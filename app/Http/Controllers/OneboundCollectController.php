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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OneboundCollectController extends Controller
{
    protected $hosts = [
        'https://api-gw.onebound.cn',
        'https://api-1.onebound.cn',
        'https://api-2.onebound.cn',
        'https://api-3.onebound.cn',
        'https://api-4.onebound.cn',
    ];

    // 支持搜索的平台（测试权限通常只有 taobao/1688，跨境平台需购买）
    protected $platforms = [
        'taobao'    => ['name' => '淘宝 (Taobao)',   'lang' => 'zh', 'need_nation' => false],
        '1688'      => ['name' => '1688 (阿里巴巴)', 'lang' => 'zh',  'need_nation' => false],
        'shopee'    => ['name' => 'Shopee',          'lang' => 'en',  'need_nation' => true],
        'aliexpress'=> ['name' => 'AliExpress',       'lang' => 'en',  'need_nation' => false],
        'amazon'    => ['name' => 'Amazon',           'lang' => 'en',  'need_nation' => true],
        'lazada'    => ['name' => 'Lazada',           'lang' => 'en',  'need_nation' => true],
        'ebay'      => ['name' => 'eBay',             'lang' => 'en',  'need_nation' => true],
        'walmart'   => ['name' => 'Walmart',           'lang' => 'en',  'need_nation' => false],
    ];

    // 各平台常用国家
    protected $nations = [
        'shopee' => [
            'sg'      => 'Singapore',
            'com.my'  => 'Malaysia',
            'com.ph'  => 'Philippines',
            'co.id'   => 'Indonesia',
            'co.th'   => 'Thailand',
            'vn'      => 'Vietnam',
            'tw'      => 'Taiwan',
        ],
        'lazada' => [
            'sg'      => 'Singapore',
            'com.my'  => 'Malaysia',
            'com.ph'  => 'Philippines',
            'co.id'   => 'Indonesia',
            'co.th'   => 'Thailand',
            'vn'      => 'Vietnam',
        ],
        'amazon' => [
            'com'     => 'United States',
            'co.uk'   => 'United Kingdom',
            'de'      => 'Germany',
            'fr'      => 'France',
            'ca'      => 'Canada',
            'jp'      => 'Japan',
            'com.be'  => 'Belgium',
        ],
        'ebay' => [
            'com'     => 'United States',
            'co.uk'   => 'United Kingdom',
            'de'      => 'Germany',
        ],
    ];

    protected $r = [
        'co.id'  => 0.01,
        'com.my' => 1,
        'com.ph' => 1,
        'sg'     => 1,
        'co.th'  => 1,
        'vn'     => 1,
    ];

    private function getKey() { return get_setting('wanbang_key'); }
    private function getSecret() { return get_setting('wanbang_secret'); }

    public function index(Request $request)
    {
        $hasApiKey = !empty($this->getKey());
        $categories = Category::where('level', 0)->get();
        $platforms = $this->platforms;
        $nations = $this->nations;

        return view('backend.product_collect.onebound_index', compact('hasApiKey', 'categories', 'platforms', 'nations'));
    }

    public function search(Request $request)
    {
        $platform = $request->platform ?? 'shopee';
        $keyword  = $request->keyword ?? '';
        $nation   = $request->nation ?? '';
        $page     = (int)($request->page ?? 1);

        if (empty($keyword)) {
            return response()->json(['error' => '请输入搜索关键词'], 400);
        }
        if (!isset($this->platforms[$platform])) {
            return response()->json(['error' => '不支持该平台'], 400);
        }

        $config = $this->platforms[$platform];
        if ($config['need_nation'] && empty($nation)) {
            return response()->json(['error' => '该平台需要选择国家'], 400);
        }

        $result = $this->apiRequest($platform, 'item_search', $nation, $keyword, $page);

        if (!empty($result['error'])) {
            return response()->json($result);
        }

        $products = [];
        $total = 0;

        if ($result && ($result['error_code'] ?? '') === '0000') {
            $items = $result['items']['item'] ?? [];
            if (isset($items['num_iid'])) {
                $items = [$items]; // 单条结果
            }
            $total = $result['total_results'] ?? count($items);

            foreach ($items as $item) {
                $price = $item['price'] ?? '0';
                if (is_numeric($price) && isset($this->r[$nation])) {
                    $price = $price * $this->r[$nation];
                }
                $products[] = [
                    'num_iid'   => $item['num_iid'] ?? '',
                    'title'      => $item['title'] ?? '',
                    'pic_url'    => $item['pic_url'] ?? '',
                    'price'      => $price,
                    'sales'      => $item['sales'] ?? 0,
                    'shop_title' => $item['shop_title'] ?? $item['nick'] ?? '',
                    'detail_url' => $item['detail_url'] ?? '',
                ];
            }
        }

        return response()->json([
            'products' => $products,
            'total'    => $total,
            'page'     => $page,
        ]);
    }

    public function import(Request $request)
    {
        set_time_limit(0);

        $platform   = $request->platform;
        $numIid     = $request->num_iid;
        $nation     = $request->nation ?? '';
        $categoryId = $request->category_id;

        if (empty($numIid) || empty($categoryId) || empty($platform)) {
            return response()->json(['success' => false, 'message' => '参数不完整'], 400);
        }

        $category = Category::find($categoryId);
        if (!$category) {
            return response()->json(['success' => false, 'message' => '分类不存在'], 400);
        }

        // Check duplicate
        $exists = Product::where('barcode', $numIid)->where('source', $platform)->first();
        if ($exists) {
            return response()->json(['success' => false, 'message' => '该商品已导入 (ID: ' . $exists->id . ')'], 400);
        }

        // Get product detail
        $res = $this->apiRequest($platform, 'item_get', $nation, $numIid);

        if (!$res || ($res['error_code'] ?? '') !== '0000') {
            return response()->json(['success' => false, 'message' => '获取商品详情失败: ' . ($res['error_msg'] ?? $res['error'] ?? 'API error')], 500);
        }

        $item = $res['item'];

        $basePrice = $item['price'] ?? 0;
        if (is_numeric($basePrice) && isset($this->r[$nation])) {
            $basePrice = $basePrice * $this->r[$nation];
        }

        DB::beginTransaction();
        try {
            // Download thumbnail
            $thumbnailId = null;
            if (!empty($item['pic_url'])) {
                $thumbnailId = $this->downloadImage($item['pic_url']);
            }

            // Download gallery images
            $photoIds = [];
            $midPhotos = time() . mt_rand(100, 499);
            if (!empty($item['item_imgs'])) {
                foreach ($item['item_imgs'] as $img) {
                    $imgUrl = $img['url'] ?? $img;
                    if (is_string($imgUrl) && !empty($imgUrl)) {
                        $photoId = $this->downloadImage($imgUrl, $midPhotos);
                        if ($photoId) $photoIds[] = $photoId;
                    }
                }
            }

            // Handle brand
            $brandId = $this->handleBrand($item);

            // Description
            $desc = $item['desc'] ?? '';
            if (empty($desc)) {
                $desc = $item['description'] ?? '';
            }

            // Create product
            $product = new Product;
            $product->name = $item['title'] ?? $item['name'] ?? '';
            $product->added_by = 'admin';
            $product->user_id = Auth::user()->id;
            $product->category_id = $category->id;
            $product->brand_id = $brandId;
            $product->unit = 'Pc';
            $product->min_qty = 1;
            $product->barcode = $numIid;
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
            $product->description = $desc;
            $product->meta_title = $item['title'] ?? $item['name'] ?? '';
            $product->meta_description = isset($item['desc_short']) ? $item['desc_short'] : substr(strip_tags($desc), 0, 200);
            $product->meta_img = $thumbnailId;
            $product->shipping_type = !empty($item['post_fee']) ? 'flat_rate' : 'free';
            $product->low_stock_quantity = 1;
            $product->stock_visibility_state = 'quantity';
            $product->cash_on_delivery = 1;
            $product->est_shipping_days = 3;
            $product->shipping_cost = $item['post_fee'] ?? 0;
            $product->slug = $numIid . '-' . Str::random(5);
            $product->approved = 1;
            $product->published = 0;
            $product->source = $platform;
            $product->in_storehouse = 1;
            $product->digital = 0;
            $product->save();

            // Create stock
            $totalStock = $item['stock'] ?? rand(100, 500);
            $stock = new ProductStock;
            $stock->product_id = $product->id;
            $stock->variant = '';
            $stock->price = $basePrice;
            $stock->sku = '';
            $stock->qty = $totalStock;
            $stock->image = $thumbnailId;
            $stock->save();
            $product->current_stock = $totalStock;
            $product->save();

            // Handle variants (props)
            if (!empty($item['props_list'])) {
                $this->handleVariants($product, $item, $basePrice, $thumbnailId, $nation);
            }

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => '导入成功',
                'product_id' => $product->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => '导入失败: ' . $e->getMessage()], 500);
        }
    }

    private function handleBrand($item)
    {
        $brandName = $item['brand'] ?? $item['brand_name'] ?? '';
        if (empty($brandName)) return 0;

        $brand = Brand::where('name', $brandName)->first();
        if ($brand) return $brand->id;

        $brand = new Brand;
        $brand->name = $brandName;
        $brand->meta_title = $brandName;
        $brand->meta_description = $brandName;
        $brand->slug = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $brandName)) . '-' . Str::random(5);
        $brand->logo = '';
        $brand->save();

        $bt = BrandTranslation::firstOrNew([
            'lang'     => env('DEFAULT_LANGUAGE', 'zh'),
            'brand_id' => $brand->id,
        ]);
        $bt->name = $brandName;
        $bt->save();

        return $brand->id;
    }

    private function handleVariants($product, $item, $basePrice, $thumbnailId, $nation)
    {
        $choiceOptions = [];
        foreach ($item['props_list'] as $re) {
            $parts = explode(':', $re);
            if (count($parts) < 2) continue;
            $attrName = $parts[0];
            $attrValue = $parts[1];
            if (!isset($choiceOptions[$attrName])) {
                $choiceOptions[$attrName] = ['attribute_id' => null, 'values' => []];
            }
            $choiceOptions[$attrName]['values'][] = $attrValue;
        }

        if (empty($choiceOptions)) return;

        $choiceOptions = array_values($choiceOptions);
        $attributes = [];
        foreach ($choiceOptions as $idx => $opt) {
            $attributes[] = $idx + 1;
            $opt['attribute_id'] = $idx + 1;
            $choiceOptions[$idx] = $opt;
        }

        $product->variant_product = 1;
        $product->attributes = json_encode($attributes, JSON_UNESCAPED_UNICODE);
        $product->choice_options = json_encode($choiceOptions, JSON_UNESCAPED_UNICODE);
        $product->save();

        // Create simple combinations
        $combinations = [[]];
        foreach ($choiceOptions as $opt) {
            $newCombos = [];
            foreach ($combinations as $combo) {
                foreach ($opt['values'] as $val) {
                    $newCombos[] = array_merge($combo, [$val]);
                }
            }
            $combinations = $newCombos;
        }

        $totalStock = 0;
        foreach ($combinations as $combo) {
            $variant = implode('-', $combo);
            $qty = rand(100, 500);
            $stock = new ProductStock;
            $stock->product_id = $product->id;
            $stock->variant = $variant;
            $stock->price = $basePrice;
            $stock->sku = '';
            $stock->qty = $qty;
            $stock->image = $thumbnailId;
            $stock->save();
            $totalStock += $qty;
        }
        $product->current_stock = $totalStock;
        $product->save();
    }

    private function apiRequest($platform, $action, $nation, $query, $page = 1)
    {
        $key = $this->getKey();
        $secret = $this->getSecret();

        if (empty($key) || empty($secret)) {
            return ['error' => '请先在系统设置中配置万邦 API Key 和 Secret'];
        }

        $config = $this->platforms[$platform] ?? [];
        $needNation = $config['need_nation'] ?? false;

        $lastResponse = null;
        $lastUrl = '';

        foreach ($this->hosts as $host) {
            if ($action === 'item_search') {
                $url = $host . '/' . $platform . '/item_search/?key=' . $key . '&secret=' . $secret;
                if ($needNation && !empty($nation)) {
                    $url .= '&nation=' . $nation;
                }
                $url .= '&q=' . urlencode($query) . '&page=' . $page;
            } else {
                // item_get
                $url = $host . '/' . $platform . '/item_get/?key=' . $key . '&secret=' . $secret;
                if ($needNation && !empty($nation)) {
                    $url .= '&nation=' . $nation;
                }
                $url .= '&num_iid=' . $query;
            }

            $lastUrl = $url;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Content-type:application/json;charset=UTF-8",
                "Accept:application/json",
            ]);
            $output = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            // cURL 错误
            if ($output === false) {
                $lastResponse = ['_curl_error' => $curlError, '_http_code' => $httpCode];
                continue;
            }

            $output = json_decode($output, true);
            $lastResponse = $output;

            if (!empty($output) && ($output['error_code'] ?? '') === '0000') {
                return $output;
            }

            // 搜索无结果是合理情况
            if (!empty($output) && ($output['error_code'] ?? '') === '2000') {
                return ['error_code' => '0000', 'items' => ['item' => []]];
            }
        }

        return [
            'error' => $platform . ' 查询失败',
            '_debug_url' => $lastUrl,
            '_debug_response' => json_encode($lastResponse, JSON_UNESCAPED_UNICODE),
        ];
    }

    private function downloadImage($url, $mid = null)
    {
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30]);
            $response = $client->request('GET', $url);
            $content = $response->getBody()->getContents();

            $pathInfo = parse_url($url);
            $ext = pathinfo($pathInfo['path'] ?? '', PATHINFO_EXTENSION);
            if (empty($ext) || strlen($ext) > 5) $ext = 'jpg';

            $dir = 'uploads/ob/' . date('Ymd') . '/' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
            Storage::disk('public')->put($dir, $content);

            $upload = new Upload;
            $upload->user_id = Auth::user()->id ?? 1;
            $upload->file_size = strlen($content);
            $upload->extension = $ext;
            $upload->type = 'image';
            $upload->file_original_name = basename($url);
            $upload->file_name = $dir;
            if ($mid) $upload->mid = $mid;
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
