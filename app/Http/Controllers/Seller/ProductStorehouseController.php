<?php

namespace App\Http\Controllers\Seller;

use App\Http\Resources\PosProductCollection;
use App\Http\Resources\PosSetMealCollection;
use App\Models\Product;
use App\Models\ProductSetMeal;
use App\Models\ProductStock;
use App\Models\Review;
use App\Models\Seller;
use App\Models\SellerPackage;
use App\Services\ProductStockService;
use App\Services\ProductTaxService;
use App\Utility\CategoryUtility;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function back;
use function collect;
use function count;
use function dd;
use function flash;
use function translate;

class ProductStorehouseController extends Controller
{
    protected $productTaxService;
    protected $productStockService;

    public function __construct(
        ProductTaxService   $productTaxService,
        ProductStockService $productStockService
    )
    {
        $this->productTaxService = $productTaxService;
        $this->productStockService = $productStockService;
    }

    public function index()
    {
        $package = SellerPackage::query()->where('is_default', 1)->first();
        return view('seller.product_storehouse.index', compact('package'));
    }

    public function searchProduct(Request $request)
    {
        // 排除已复制产品
        $alreadyCopyIds = Product::query()
            ->where('user_id', Auth::user()->id)
            ->where('published', 1)
            ->whereNotNull('original_id')
            ->pluck('original_id')
            ->toArray();

        // 排除已被其他商家上架的产品
        $limitShop = (int) get_setting('warehouse_product_merchant_limit');
        $othersAlreadyCopyIds = Product::query()
            ->where('published', 1)
            ->whereNotNull('original_id')
            ->groupBy('original_id')
            ->selectRaw("original_id, count(*) total")
            ->having("total", ">=", $limitShop)
            ->pluck('original_id')
            ->toArray();

        if (!empty($othersAlreadyCopyIds)) {
            $alreadyCopyIds = array_merge($alreadyCopyIds, $othersAlreadyCopyIds);
        }

        $products = ProductStock::join('products', 'product_stocks.product_id', '=', 'products.id')
            ->where('products.in_storehouse', 1)
            ->whereNotIn('products.id', $alreadyCopyIds)
            ->select('products.*', 'product_stocks.id as stock_id', 'product_stocks.variant', 'product_stocks.price as stock_price', 'product_stocks.qty as stock_qty', 'product_stocks.image as stock_image')
            ->groupBy("products.id")
            ->orderBy('products.unit_price', 'desc');


        if ($request->category != null) {
            $arr = explode('-', $request->category);
            if ($arr[0] == 'category') {
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $products = $products->whereIn('products.category_id', $category_ids);
            }
        }

        if ($request->brand != null) {
            $products = $products->where('products.brand_id', $request->brand);
        }

        if ($request->keyword != null) {
            $products = $products->where('products.name', 'like', '%' . $request->keyword . '%')->orWhere('products.barcode', $request->keyword);
        }

        $stocks = new PosProductCollection($products->paginate(16));
        $stocks->appends(['keyword' => $request->keyword, 'category' => $request->category, 'brand' => $request->brand]);
        return $stocks;
    }

    public function searchSetMeal(Request $request) {
        $list = ProductSetMeal::whereRaw('stock > added_times')->orderBy('id', 'desc');

        if ($request->category != null) {
            $arr = explode('-', $request->category);
            if ($arr[0] == 'category') {
                $category_ids = CategoryUtility::children_ids($arr[1]);
                $category_ids[] = $arr[1];
                $list = $list->whereIn('category_id', $category_ids);
            }
        }

        $list = new PosSetMealCollection($list->paginate(100));
        $list->appends(['category' => $request->category]);
        return $list;
    }

    public function addProduct(Request $request)
    {
        $userId = Auth::user()->id;
        $bloc_id = Auth::user()->bloc_id;
        $staff_id = get_staff_id();
        if (!$request->all && !$request->product_ids) return response()->json(['success' => 0, 'message' => translate('Please select a product')]);

        if (!empty($request->set_meal_id)) {
            $setMeal = ProductSetMeal::query()->where('id', $request->set_meal_id)->first();
            if (empty($setMeal) || $setMeal->added_times >= $setMeal->stock) {
                return response()->json(['success' => 0, 'msg' => translate('Insufficient number of set meal')]);
            }
        }

        // 排除已复制产品
        $alreadyCopyIds = Product::query()
            ->where('user_id', $userId)
            ->whereNotNull('original_id')
            ->pluck('original_id')
            ->toArray();

        if ($request->all) {
            $productIds = Product::query()
                ->where('added_by', 'admin')
                ->where('in_storehouse', 1)
                ->pluck('id')
                ->toArray();

        } else {
            $productIds = $request->product_ids;
        }

        // 排除已复制产品ID
        $productIds = array_filter($productIds, function ($v) use ($alreadyCopyIds) {
            return !in_array($v, $alreadyCopyIds);
        }, ARRAY_FILTER_USE_BOTH);
        $shop = Auth::user()->shop;
        if ($shop->verification_status==0) return response()->json(['success' => 0, 'message' => translate('Shop under review.')]);

        $package = SellerPackage::query()->where('is_default', 1)->first();
        if (
            $package->product_upload_limit < ($shop->user->products()->count() + count($productIds))
        ) {
            return response()->json(['success' => 0, 'message' => sprintf(translate('Up to %d products can be uploaded'), $package->product_upload_limit)]);
        }

        try {
            DB::beginTransaction();
            // 获取最大利润
            $sellerPackage = Auth::user()->shop->seller_package;
            if (!$sellerPackage) {
                return response()->json(['success' => 0, 'message' => translate('Please upgrade your package.')]);
            }
            $maxProfit = $sellerPackage->max_profit / 100;

            // 判断每件商品最多能让N个卖家同时上架在店铺上
            $hasLimitShopProduct = false;
            $limitShop = (int) get_setting('warehouse_product_merchant_limit');

            // 有指定套餐，使用次数加1
            if (!empty($request->set_meal_id)) {
                ProductSetMeal::query()->where('id', $request->set_meal_id)->increment('added_times');
            }

            // 循环复制产品
            foreach ($productIds as $productId) {
                if ($limitShop) {
                    $alreadyShopNum = Product::query()->where('original_id', $productId)->count();
                    if ($alreadyShopNum >= $limitShop) {
                        $hasLimitShopProduct = true;
                        continue;
                    }
                }

                $product = Product::find($productId);
                $profitPrice = $product->unit_price * $maxProfit;

                $product_new = $product->replicate();
                $product_new->slug = $product_new->slug . '-' . Str::random(5);
                $product_new->added_by = 'seller';
                $product_new->user_id = $userId;
                $product_new->bloc_id = $bloc_id;
                $product_new->staff_id = $staff_id;
                $product_new->unit_price = $product->unit_price + $profitPrice;
                $product_new->original_id = $productId;
                $product_new->published = 1;
                $product_new->in_storehouse = 0;
                $product_new->save();
                /*店铺铺货 评论权限*/
                if ($shop->comment_permission == 1){
                    foreach ( $product->reviews as $review )
                    {
                        $reviewModel = new Review;
                        $reviewModel->product_id = $product_new->id;
                        $reviewModel->user_id = $review->user_id;
                        $reviewModel->rating = $review->rating;
                        $reviewModel->comment = $review->comment;
                        $reviewModel->viewed = '0';
                        $reviewModel->save();
                    }
                }


                // 循环复制产品翻译
                foreach ($product->product_translations as $productTranslation) {
                    $productTranslationNew = $productTranslation->replicate();
                    $productTranslationNew->product_id = $product_new->id;
                    $productTranslationNew->save();
                }

                //Product Stock
                $this->productStockService->product_duplicate_store($product->stocks, $product_new, $maxProfit);

                //VAT & Tax
                $this->productTaxService->product_duplicate_store($product->taxes, $product_new);
            }

            DB::commit();
            return response()->json(['success' => $hasLimitShopProduct ? 2 : 1]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['success' => 0]);
        }
    }

    public function get_products_by_set_meal(Request $request) {
        $setMeal = ProductSetMeal::find($request->id);
        if (empty($setMeal)) {
            return response()->json(['success' => 0]);
        }

        $userId = Auth::user()->id;

        $product_ids = is_string($setMeal->product_ids) ? json_decode($setMeal->product_ids, true) : $setMeal->product_ids;
        // 排除已复制产品
        $alreadyCopyIds = Product::query()
            ->where('user_id', $userId)
            ->whereNotNull('original_id')
            ->pluck('original_id')
            ->toArray();

        // 排除已复制产品ID
        $product_ids = array_filter($product_ids, function ($v) use ($alreadyCopyIds) {
            return !in_array($v, $alreadyCopyIds);
        }, ARRAY_FILTER_USE_BOTH);
        if (empty($product_ids)) {
            return response()->json(['success' => 1, 'products' => [], 'msg' => translate('All products in the current package have been added')]);
        }

        $products = Product::query()->whereIn('id', $product_ids)->select(["id", "name", "unit_price"])->get();
        foreach ($products as $product) {
            $product->unit_price = single_price($product->unit_price);
        }

        return response()->json(['success' => 1, 'products' => $products, 'set_meal_name' => $setMeal->category->getTranslation('name') . '-' . $setMeal->name]);
    }
}
