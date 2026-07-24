<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\BrandTranslation;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\ProductTax;
use App\Models\Upload;
use Auth;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CrossImportController extends Controller
{
    // ========== 在这里填写商城A的数据库连接信息 ==========
    private $sourceConfig = [
        'host'     => '127.0.0.1',
        'port'     => '3306',
        'database' => 'shop_a_database',     // ← 改成商城A的数据库名
        'username' => 'root',                // ← 改成商城A的数据库用户名
        'password' => '',                    // ← 改成商城A的数据库密码
        'charset'  => 'utf8mb4',
        'prefix'   => '',
    ];

    private function sourceDb()
    {
        config(['database.connections.source_db' => array_merge([
            'driver'    => 'mysql',
            'collation' => 'utf8mb4_unicode_ci',
            'strict'    => false,
        ], $this->sourceConfig)]);

        return DB::connection('source_db');
    }

    // ==================== 页面入口 ====================

    public function index()
    {
        $sourceCategories = [];
        $targetCategories = Category::where('level', 0)->get();
        $configured = false;

        try {
            $all = $this->sourceDb()->table('categories')
                ->select('id', 'name', 'parent_id', 'level')
                ->orderBy('level')->orderBy('name')
                ->get();

            // 批量读取中文翻译（兼容 zh / cn / zh-CN 等）
            $catIds = $all->pluck('id')->toArray();
            $translations = $this->sourceDb()->table('category_translations')
                ->whereIn('category_id', $catIds)
                ->whereIn('lang', ['zh', 'cn', 'zh-CN', 'zh-cn', 'zh_cn'])
                ->get();
            $transMap = [];
            foreach ($translations as $t) {
                if (!empty($t->name) && empty($transMap[$t->category_id])) {
                    $transMap[$t->category_id] = $t->name;
                }
            }

            // 替换为中文名
            foreach ($all as $c) {
                if (!empty($transMap[$c->id])) {
                    $c->name = $transMap[$c->id];
                }
            }

            // 构建树形结构
            $byParent = [];
            foreach ($all as $c) {
                $pid = $c->parent_id ?? 0;
                if (!isset($byParent[$pid])) $byParent[$pid] = [];
                $byParent[$pid][] = $c;
            }
            $sourceCategories = $this->buildTree($byParent, 0);
            $configured = true;
        } catch (\Exception $e) {
            // 配置信息不匹配会进这里
        }

        return view('backend.product.cross_import', compact(
            'sourceCategories', 'targetCategories', 'configured'
        ));
    }

    private function buildTree($byParent, $parentId, $depth = 0)
    {
        $result = [];
        if (!isset($byParent[$parentId])) return $result;
        foreach ($byParent[$parentId] as $cat) {
            $cat->depth = $depth;
            $cat->prefix = $depth > 0 ? str_repeat('-- ', $depth) : '';
            $result[] = $cat;
            $result = array_merge($result, $this->buildTree($byParent, $cat->id, $depth + 1));
        }
        return $result;
    }

    // ==================== 搜索商品 ====================

    public function listProducts(Request $request)
    {
        $categoryId = $request->category_id;
        $minPrice   = $request->min_price;
        $maxPrice   = $request->max_price;
        $keyword    = $request->keyword;

        $query = $this->sourceDb()->table('products')
            ->select('id', 'name', 'category_id', 'unit_price', 'thumbnail_img',
                     'current_stock', 'brand_id', 'source', 'slug', 'created_at')
            ->whereNull('original_id')       // 只取内部商品
            ->where('published', 1);

        // 分类筛选：如果选了父分类，则包含所有子分类
        if (!empty($categoryId)) {
            $childIds = $this->getAllChildIds($categoryId);
            $childIds[] = (int)$categoryId;
            $query->whereIn('category_id', $childIds);
        }
        if (!empty($minPrice)) {
            $query->where('unit_price', '>=', (float)$minPrice);
        }
        if (!empty($maxPrice)) {
            $query->where('unit_price', '<=', (float)$maxPrice);
        }
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $products = $query->orderBy('id', 'desc')->paginate(50);

        // 附加额外信息
        foreach ($products as $p) {
            // 分类名（优先取中文翻译）
            $cat = $this->sourceDb()->table('categories')->where('id', $p->category_id)->first();
            $catName = $cat->name ?? '-';
            if ($cat) {
                $catTrans = $this->sourceDb()->table('category_translations')
                    ->where('category_id', $cat->id)
                    ->whereIn('lang', ['zh', 'cn', 'zh-CN', 'zh-cn', 'zh_cn'])
                    ->first();
                if ($catTrans && !empty($catTrans->name)) {
                    $catName = $catTrans->name;
                }
            }
            $p->category_name = $catName;

            // 品牌名
            $p->brand_name = '-';
            if ($p->brand_id) {
                $brand = $this->sourceDb()->table('brands')->where('id', $p->brand_id)->first();
                $p->brand_name = $brand->name ?? '-';
            }

            // 缩略图URL — 直接从源系统 uploads 表取 external_link
            $p->thumb_url = '';
            if ($p->thumbnail_img) {
                $up = $this->sourceDb()->table('uploads')->where('id', (int)$p->thumbnail_img)->first();
                if ($up && !empty($up->external_link)) {
                    $p->thumb_url = $up->external_link;
                }
            }
        }

        return response()->json($products);
    }

    private function getAllChildIds($parentId)
    {
        $ids = [];
        $children = $this->sourceDb()->table('categories')
            ->where('parent_id', $parentId)->pluck('id')->toArray();
        foreach ($children as $childId) {
            $ids[] = $childId;
            $ids = array_merge($ids, $this->getAllChildIds($childId));
        }
        return $ids;
    }

    // ==================== 导入 ====================

    public function import(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        $importAll        = $request->import_all;
        $ids              = $request->ids ?? [];
        $targetCategoryId = $request->target_category_id;

        if (empty($targetCategoryId)) {
            return response()->json(['success' => false, 'message' => '参数不完整'], 400);
        }

        $targetCategory = Category::find($targetCategoryId);
        if (!$targetCategory) {
            return response()->json(['success' => false, 'message' => '目标分类不存在'], 400);
        }

        // 一键导入全部：按筛选条件查出所有商品ID
        if ($importAll) {
            $query = $this->sourceDb()->table('products')
                ->select('id')
                ->whereNull('original_id')
                ->where('published', 1);

            if (!empty($request->category_id)) {
                $childIds = $this->getAllChildIds($request->category_id);
                $childIds[] = (int)$request->category_id;
                $query->whereIn('category_id', $childIds);
            }
            if (!empty($request->min_price)) {
                $query->where('unit_price', '>=', (float)$request->min_price);
            }
            if (!empty($request->max_price)) {
                $query->where('unit_price', '<=', (float)$request->max_price);
            }
            if (!empty($request->keyword)) {
                $query->where('name', 'like', '%' . $request->keyword . '%');
            }

            $ids = $query->pluck('id')->toArray();
        }

        if (empty($ids)) {
            return response()->json(['success' => false, 'message' => '没有可导入的商品'], 400);
        }

        $ids     = array_map('intval', $ids);
        $total   = count($ids);
        $success = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($ids as $idx => $sourceId) {
            $sp = $this->sourceDb()->table('products')->where('id', $sourceId)->first();
            if (!$sp) continue;

            // 按 name + category 去重
            $exists = Product::where('name', $sp->name)->where('category_id', $targetCategoryId)->first();
            if ($exists) { $skipped++; continue; }

            try {
                DB::beginTransaction();

                // 图片
                $newThumbId  = $this->copyImage($sp->thumbnail_img ?? null);
                $newPhotoIds = [];
                if (!empty($sp->photos)) {
                    foreach (explode(',', $sp->photos) as $pid) {
                        $nid = $this->copyImage(trim($pid));
                        if ($nid) $newPhotoIds[] = $nid;
                    }
                }
                $newMetaId = $this->copyImage($sp->meta_img ?? null);

                // 创建商品
                $product = new Product;
                $product->name                   = $sp->name;
                $product->added_by              = $sp->added_by ?? 'admin';
                $product->user_id               = Auth::user()->id;
                $product->category_id           = $targetCategoryId;
                $product->brand_id              = 0;   // 不引入品牌
                $product->unit                  = $sp->unit ?? 'Pc';
                $product->min_qty               = $sp->min_qty ?? 1;
                $product->tags                  = $sp->tags ?? '';
                $product->barcode               = $sp->barcode ?? '';
                $product->refundable            = $sp->refundable ?? 1;
                $product->thumbnail_img         = $newThumbId;
                $product->photos                = implode(',', $newPhotoIds);
                $product->video_provider        = $sp->video_provider ?? 'youtube';
                $product->video_link            = $sp->video_link;
                $product->unit_price            = $sp->unit_price ?? 0;
                $product->purchase_price        = $sp->purchase_price ?? 0;
                $product->discount              = $sp->discount ?? 0;
                $product->discount_type         = $sp->discount_type ?? 'amount';
                $product->discount_start_date   = $sp->discount_start_date;
                $product->discount_end_date     = $sp->discount_end_date;
                $product->earn_point            = $sp->earn_point ?? 0;
                $product->current_stock         = 9999;
                $product->external_link         = $sp->external_link;
                $product->external_link_btn     = $sp->external_link_btn;
                $product->description           = $sp->description ?? '';
                $product->meta_title            = $sp->meta_title ?? $sp->name;
                $product->meta_description      = $sp->meta_description ?? '';
                $product->meta_img              = $newMetaId;
                $product->shipping_type         = $sp->shipping_type ?? 'free';
                $product->shipping_cost         = $sp->shipping_cost ?? 0;
                $product->low_stock_quantity    = $sp->low_stock_quantity ?? 1;
                $product->stock_visibility_state= $sp->stock_visibility_state ?? 'quantity';
                $product->cash_on_delivery      = $sp->cash_on_delivery ?? 1;
                $product->est_shipping_days     = $sp->est_shipping_days ?? 3;
                $product->is_quantity_multiplied= $sp->is_quantity_multiplied ?? 0;
                $product->colors                = $sp->colors ?? '[]';
                $product->attributes            = $sp->attributes ?? '[]';
                $product->choice_options         = $sp->choice_options ?? '[]';
                $product->slug                  = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($sp->name))) . '-' . Str::random(5);
                $product->approved              = 1;
                $product->published             = 1;   // 直接上架
                $product->in_storehouse         = 1;   // 入库
                $product->featured              = $sp->featured ?? 0;
                $product->todays_deal           = $sp->todays_deal ?? 0;
                $product->variant_product       = $sp->variant_product ?? 0;
                $product->digital               = $sp->digital ?? 0;
                $product->source                = 'tes_' . ($sp->source ?? '');
                $product->bloc_id               = (int) Auth::user()->bloc_id;
                $product->staff_id              = get_staff_id();
                $product->save();

                // 翻译
                $translations = $this->sourceDb()->table('product_translations')
                    ->where('product_id', $sourceId)->get();
                foreach ($translations as $tr) {
                    DB::table('product_translations')->insert([
                        'product_id'  => $product->id,
                        'name'        => $tr->name ?? $sp->name,
                        'description' => $tr->description ?? '',
                        'lang'        => $tr->lang ?? env('DEFAULT_LANGUAGE', 'zh'),
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }

                // SKU / 变体 — 库存统一 9999
                $stocks = $this->sourceDb()->table('product_stocks')
                    ->where('product_id', $sourceId)->get();
                $totalStock = 0;
                if (count($stocks) > 0) {
                    foreach ($stocks as $st) {
                        $ps = new ProductStock;
                        $ps->product_id = $product->id;
                        $ps->variant    = $st->variant ?? '';
                        $ps->price      = $st->price ?? $product->unit_price;
                        $ps->sku        = $st->sku ?? '';
                        $ps->qty        = 9999;
                        $ps->save();
                        $totalStock += 9999;
                    }
                } else {
                    ProductStock::create([
                        'product_id' => $product->id,
                        'variant'    => '',
                        'price'      => $product->unit_price,
                        'sku'        => '',
                        'qty'        => 9999,
                    ]);
                    $totalStock = 9999;
                }
                $product->current_stock = $totalStock;
                $product->save();

                // 税费
                $taxes = $this->sourceDb()->table('product_taxes')
                    ->where('product_id', $sourceId)->get();
                foreach ($taxes as $tx) {
                    ProductTax::create([
                        'product_id' => $product->id,
                        'tax_id'     => $tx->tax_id ?? 0,
                        'tax'        => $tx->tax ?? 0,
                        'tax_type'   => $tx->tax_type ?? 'amount',
                    ]);
                }

                DB::commit();
                $success++;

                // 每 100 个释放内存 + 刷新输出，防止连接超时
                if ($success % 100 === 0) {
                    gc_collect_cycles();
                    if (ob_get_level()) ob_flush();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $errors[] = "ID {$sourceId}: " . $e->getMessage();
            }
        }

        return response()->json([
            'success'  => true,
            'imported' => $success,
            'skipped'  => $skipped,
            'total'    => $total,
            'errors'   => $errors,
        ]);
    }

    // ==================== 图片代理 ====================

    public function thumb($id)
    {
        $up = $this->sourceDb()->table('uploads')->where('id', (int)$id)->first();
        if (!$up) {
            return $this->placeholderImage();
        }

        // external_link：直接 302 重定向到源 URL（最快最稳）
        if (!empty($up->external_link)) {
            return redirect()->away($up->external_link);
        }

        if (!empty($up->file_name)) {
            $ext = $up->extension ?? 'jpg';
            $mime = in_array($ext, ['png', 'gif', 'webp', 'svg']) ? 'image/' . $ext : 'image/jpeg';

            // SOURCE_APP_PATH 直接读文件
            $srcPath = rtrim(env('SOURCE_APP_PATH', ''), '/');
            if (!empty($srcPath)) {
                $file = $srcPath . '/storage/app/public/' . ltrim($up->file_name, '/');
                if (file_exists($file)) {
                    return response(file_get_contents($file), 200)->header('Content-Type', $mime);
                }
            }

            // SOURCE_APP_URL 抓取
            $base = rtrim(env('SOURCE_APP_URL', ''), '/');
            if (!empty($base)) {
                try {
                    $imgUrl = $base . '/storage/' . ltrim($up->file_name, '/');
                    $client = new \GuzzleHttp\Client(['timeout' => 10, 'verify' => false]);
                    $content = $client->request('GET', $imgUrl)->getBody()->getContents();
                    return response($content, 200)->header('Content-Type', $mime);
                } catch (\Exception $e) {}
            }
        }

        return $this->placeholderImage();
    }

    private function placeholderImage()
    {
        // 返回 1x1 透明 gif
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        return response($gif, 200)->header('Content-Type', 'image/gif');
    }

    // ==================== 辅助方法 ====================

    private function copyBrand($sb)
    {
        $exist = Brand::where('name', $sb->name)->first();
        if ($exist) return $exist->id;

        $brand = new Brand;
        $brand->name             = $sb->name;
        $brand->meta_title       = $sb->meta_title ?? $sb->name;
        $brand->meta_description = $sb->meta_description ?? '';
        $brand->slug             = $sb->slug ?: (preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $sb->name)) . '-' . Str::random(5));
        $brand->logo             = $sb->logo ?? '';
        $brand->top              = $sb->top ?? 0;
        $brand->save();

        $trans = $this->sourceDb()->table('brand_translations')
            ->where('brand_id', $sb->id)->get();
        foreach ($trans as $st) {
            BrandTranslation::create([
                'brand_id' => $brand->id,
                'name'     => $st->name ?? $sb->name,
                'lang'     => $st->lang ?? env('DEFAULT_LANGUAGE', 'zh'),
            ]);
        }

        return $brand->id;
    }

    private function copyImage($sourceUploadId)
    {
        if (empty($sourceUploadId)) return null;

        $up = $this->sourceDb()->table('uploads')->where('id', (int)$sourceUploadId)->first();
        if (!$up) return null;

        $upload = new Upload;
        $upload->user_id            = Auth::user()->id ?? 1;
        $upload->file_size          = $up->file_size ?? 0;
        $upload->extension          = $up->extension ?? 'jpg';
        $upload->type               = $up->type ?? 'image';
        $upload->file_original_name = $up->file_original_name ?? '';

        // 有 external_link：直接复制链接（秒级，不下载图片文件）
        if (!empty($up->external_link)) {
            $upload->external_link  = $up->external_link;
            $upload->file_name      = '';
            $upload->save();
            return $upload->id;
        }

        // 无 external_link：本地图片需要复制文件
        if (!empty($up->file_name)) {
            $ext = $up->extension ?? 'jpg';
            $targetPath = 'uploads/cross/' . date('Ymd') . '/' . time() . '_' . mt_rand(100, 999) . '.' . $ext;
            $copied = false;

            // 优先从 SOURCE_APP_PATH 读文件
            $srcAppPath = env('SOURCE_APP_PATH', '');
            if (!empty($srcAppPath)) {
                $srcFile = rtrim($srcAppPath, '/') . '/storage/app/public/' . ltrim($up->file_name, '/');
                if (file_exists($srcFile)) {
                    Storage::disk('public')->put($targetPath, file_get_contents($srcFile));
                    $copied = true;
                }
            }

            // 备选：从 SOURCE_APP_URL 下载
            $srcAppUrl = rtrim(env('SOURCE_APP_URL', ''), '/');
            if (!$copied && !empty($srcAppUrl)) {
                try {
                    $imgUrl = $srcAppUrl . '/storage/' . ltrim($up->file_name, '/');
                    $content = @file_get_contents($imgUrl);
                    if ($content && strlen($content) > 100) {
                        Storage::disk('public')->put($targetPath, $content);
                        $copied = true;
                    }
                } catch (\Exception $e) {}
            }

            if ($copied) {
                $upload->file_name = $targetPath;
                $upload->file_size = strlen(Storage::disk('public')->get($targetPath));
                $upload->save();
                return $upload->id;
            }
        }

        // 图片无法获取，返回 null
        return null;
    }
}
