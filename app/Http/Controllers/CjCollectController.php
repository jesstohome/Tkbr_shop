<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\Upload;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CjCollectController extends Controller
{
    protected $baseUrl = 'https://developers.cjdropshipping.com/api2.0/v1';

    private function getAccessToken()
    {
        $apiKey = get_setting('cj_api_key');
        if (empty($apiKey)) {
            return ['error' => '请先在系统设置中配置 CJ API Key'];
        }

        $cacheKey = 'cj_access_token';
        $tokenData = Cache::get($cacheKey);

        if ($tokenData && isset($tokenData['accessToken'])) {
            if (strtotime($tokenData['accessTokenExpiryDate']) > time() + 3600) {
                return $tokenData;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/authentication/getAccessToken');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['apiKey' => $apiKey]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if (empty($result['data']['accessToken'])) {
            return ['error' => 'CJ 认证失败: ' . ($result['message'] ?? '未知错误，请检查 API Key 是否正确')];
        }

        $tokenData = $result['data'];
        Cache::put($cacheKey, $tokenData, 86400 * 14);

        return $tokenData;
    }

    private function apiGet($endpoint, $params = [])
    {
        $tokenData = $this->getAccessToken();
        if (isset($tokenData['error'])) {
            return $tokenData;
        }

        $url = $this->baseUrl . $endpoint;
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'CJ-Access-Token: ' . $tokenData['accessToken'],
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }

    public function index(Request $request)
    {
        $categories = Category::where('level', 0)->get();
        $hasApiKey = !empty(get_setting('cj_api_key'));

        return view('backend.product_collect.cj_index', compact('categories', 'hasApiKey'));
    }

    public function search(Request $request)
    {
        $keyword = $request->keyword ?? '';
        $page = (int)($request->page ?? 1);
        $size = (int)($request->size ?? 20);

        $params = [
            'keyWord' => $keyword,
            'page'    => $page,
            'size'    => $size,
            'sort'    => 'desc',
            'orderBy' => 2,
        ];

        if ($request->startSellPrice) {
            $params['startSellPrice'] = $request->startSellPrice;
        }
        if ($request->endSellPrice) {
            $params['endSellPrice'] = $request->endSellPrice;
        }

        $result = $this->apiGet('/product/listV2', $params);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], 500);
        }

        if (empty($result['data']['content']) || empty($result['data']['content'][0]['productList'])) {
            return response()->json(['products' => [], 'total' => 0]);
        }

        $products = [];
        foreach ($result['data']['content'][0]['productList'] as $item) {
            $products[] = [
                'pid'           => $item['id'] ?? '',
                'name'          => $item['nameEn'] ?? '',
                'sku'           => $item['sku'] ?? '',
                'image'         => $item['bigImage'] ?? '',
                'sellPrice'     => $item['sellPrice'] ?? '',
                'nowPrice'      => $item['nowPrice'] ?? '',
                'listedNum'     => $item['listedNum'] ?? 0,
                'supplierName'  => $item['supplierName'] ?? '',
                'categoryName'  => $item['threeCategoryName'] ?? '',
                'inventoryNum'  => $item['warehouseInventoryNum'] ?? 0,
                'addMarkStatus' => $item['addMarkStatus'] ?? 0,
            ];
        }

        return response()->json([
            'products' => $products,
            'total'    => $result['data']['totalRecords'] ?? count($products),
            'page'     => $page,
            'pages'    => $result['data']['totalPages'] ?? 1,
        ]);
    }

    public function import(Request $request)
    {
        set_time_limit(0);

        $pid = $request->pid;
        $categoryId = $request->category_id;

        if (empty($pid) || empty($categoryId)) {
            return response()->json(['success' => false, 'message' => '参数不完整'], 400);
        }

        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['success' => false, 'message' => '分类不存在'], 400);
        }

        // Check if already imported
        $exists = Product::where('barcode', $pid)->where('added_by', 'admin')->first();
        if ($exists) {
            return response()->json(['success' => false, 'message' => '该商品已导入过 (ID: ' . $exists->id . ')'], 400);
        }

        // Get product detail
        $detail = $this->apiGet('/product/query', ['pid' => $pid]);
        if (isset($detail['error'])) {
            return response()->json(['success' => false, 'message' => $detail['error']], 500);
        }
        if (empty($detail['data'])) {
            return response()->json(['success' => false, 'message' => '获取商品详情失败'], 500);
        }

        $productData = $detail['data'];

        // Get variants
        $variantsResult = $this->apiGet('/product/variant/query', ['pid' => $pid]);
        $variants = [];
        if (!isset($variantsResult['error']) && !empty($variantsResult['data'])) {
            $variants = $variantsResult['data'];
        }

        DB::beginTransaction();
        try {
            // Download main image
            $thumbnailId = null;
            if (!empty($productData['bigImage'])) {
                $thumbnailId = $this->downloadImage($productData['bigImage']);
            }

            // Download gallery images
            $photoIds = [];
            if (!empty($productData['images'])) {
                foreach ($productData['images'] as $img) {
                    $imgUrl = $img['url'] ?? $img;
                    if (is_string($imgUrl) && !empty($imgUrl)) {
                        $photoId = $this->downloadImage($imgUrl);
                        if ($photoId) {
                            $photoIds[] = $photoId;
                        }
                    }
                }
            }

            // Handle brand
            $brandId = 0;
            $brandName = $productData['brandName'] ?? '';
            if (!empty($brandName)) {
                $brand = Brand::where('name', $brandName)->first();
                if ($brand) {
                    $brandId = $brand->id;
                } else {
                    $brand = new Brand;
                    $brand->name = $brandName;
                    $brand->meta_title = $brandName;
                    $brand->meta_description = $brandName;
                    $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $brandName)) . '-' . Str::random(5);
                    $brand->logo = '';
                    $brand->save();

                    $brandTranslation = BrandTranslation::firstOrNew([
                        'lang'     => env('DEFAULT_LANGUAGE', 'zh'),
                        'brand_id' => $brand->id,
                    ]);
                    $brandTranslation->name = $brandName;
                    $brandTranslation->save();

                    $brandId = $brand->id;
                }
            }

            // Build description
            $description = $productData['description'] ?? '';
            if (!empty($productData['keyFeatures'])) {
                $description .= '<br/><br/><h4>Key Features:</h4><ul>';
                foreach ($productData['keyFeatures'] as $feature) {
                    $description .= '<li>' . $feature . '</li>';
                }
                $description .= '</ul>';
            }

            $basePrice = $productData['sellPrice'] ?? 0;

            // Create product
            $product = new Product;
            $product->name = $productData['nameEn'] ?? $productData['name'] ?? '';
            $product->added_by = 'admin';
            $product->user_id = Auth::user()->id;
            $product->category_id = $category->id;
            $product->brand_id = $brandId;
            $product->unit = 'Pc';
            $product->min_qty = 1;
            $product->tags = '';
            $product->barcode = $pid;
            $product->refundable = 1;
            $product->thumbnail_img = $thumbnailId;
            $product->photos = implode(',', $photoIds);
            $product->video_provider = 'youtube';
            $product->video_link = null;
            $product->unit_price = $basePrice;
            $product->discount = 0;
            $product->discount_type = 'amount';
            $product->earn_point = 0;
            $product->current_stock = 0;
            $product->external_link = null;
            $product->external_link_btn = null;
            $product->description = $description;
            $product->meta_title = $productData['nameEn'] ?? $productData['name'] ?? '';
            $product->meta_description = substr(strip_tags($description), 0, 200);
            $product->meta_img = $thumbnailId;
            $product->shipping_type = 'free';
            $product->low_stock_quantity = 1;
            $product->stock_visibility_state = 'quantity';
            $product->cash_on_delivery = 1;
            $product->est_shipping_days = 3;
            $product->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($product->name))) . '-' . Str::random(5);
            $product->approved = 1;
            $product->published = 0;
            $product->source = 'cjdropshipping';
            $product->in_storehouse = 1;
            $product->digital = 0;
            $product->save();

            // Create variants as product stocks
            if (!empty($variants)) {
                $product->variant_product = 1;
                $product->save();

                $totalStock = 0;
                foreach ($variants as $variant) {
                    $stock = new ProductStock;
                    $stock->product_id = $product->id;
                    $stock->variant = $variant['variantNameEn'] ?? $variant['variant'] ?? '';
                    $stock->price = $variant['sellPrice'] ?? $variant['variantSellPrice'] ?? $basePrice;
                    $stock->sku = $variant['variantSku'] ?? $variant['sku'] ?? '';
                    $stock->qty = $variant['stock'] ?? $variant['inventory'] ?? rand(100, 500);
                    $stock->image = $thumbnailId;
                    $stock->save();
                    $totalStock += $stock->qty;
                }

                if ($totalStock > 0) {
                    $product->current_stock = $totalStock;
                }
            } else {
                $totalStock = $productData['warehouseInventoryNum'] ?? rand(100, 500);
                $stock = new ProductStock;
                $stock->product_id = $product->id;
                $stock->variant = '';
                $stock->price = $basePrice;
                $stock->sku = $productData['sku'] ?? '';
                $stock->qty = $totalStock;
                $stock->image = $thumbnailId;
                $stock->save();
                $product->current_stock = $totalStock;
            }
            $product->save();

            DB::commit();

            return response()->json([
                'success'     => true,
                'message'     => '导入成功',
                'product_id'  => $product->id,
                'product_url' => route('products.admin'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => '导入失败: ' . $e->getMessage()], 500);
        }
    }

    private function downloadImage($url)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->request('GET', $url);
            $content = $response->getBody()->getContents();

            $pathInfo = parse_url($url);
            $ext = pathinfo($pathInfo['path'] ?? '', PATHINFO_EXTENSION);
            if (empty($ext) || strlen($ext) > 5) {
                $ext = 'jpg';
            }

            $dir = 'uploads/cj/' . date('Ymd') . '/' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
            Storage::disk('public')->put($dir, $content);

            $upload = new Upload;
            $upload->user_id = Auth::user()->id ?? 1;
            $upload->file_size = strlen($content);
            $upload->extension = $ext;
            $upload->type = 'image';
            $upload->file_original_name = basename($url);
            $upload->file_name = $dir;
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
