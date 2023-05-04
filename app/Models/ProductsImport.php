<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMappedCells;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Auth;
use Carbon\Carbon;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Storage;

// 表格中的字段名不做任何处理
HeadingRowFormatter::default('none');

//class ProductsImport implements ToModel, WithHeadingRow, WithValidation
class ProductsImport implements ToCollection, WithHeadingRow, WithValidation, ToModel, SkipsEmptyRows
{
    private $rows = 0;

    // 表格的第二行是字段名
    public function headingRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        $canImport = true;
        $user = Auth::user();
        if ($canImport) {
            // 原价比例
            $original_price_ratio = (float) get_setting('original_price_ratio');
            if (empty($original_price_ratio) || $original_price_ratio < 0) {
                $original_price_ratio = 0.6;
            }

            foreach ($rows as $row) {
                // 检测 是否已存在
                $productInDb = Product::query()->where('name', $row['产品名称'])->count();
                if ($productInDb) {
                    continue;
                }

                $row = [
                    'name' => $row['产品名称'],
                    'description' => $row['产品短描述'],
                    'category_id' => is_numeric($row['分类']) ? $row['分类'] : $this->getCategoryIdByName($row['分类']),
                    'brand_id' => is_numeric($row['品牌']) ? $row['品牌'] : $this->getBrandIdByName($row['品牌']),
                    'unit' => $row['单元'],
                    'unit_price' => (float) ($row['原价'] ?? 0) * $original_price_ratio,
                    'video_link' => '',
                    'video_provider' => '',
                    'meta_title' => $row['产品名称'],
                    'meta_description' => '',
                    'thumbnail_img' => $this->getImages($row, '缩略图地址', 1),
                    'photos' => $this->getImages($row, '高清图地址', 8),
                    'current_stock' => mt_rand(999, 5000),
                    'sku' => '',
                    'slug' => Str::random(5),
                ];
                $row['description'] = $this->mergeImages2Desc($row['description'], $row['photos']);

                // 有些备注行直接过滤掉
                if (empty($row['name']) || empty($row['unit_price'])) continue;

                $approved = 1;
                if ($user->user_type == 'seller' && get_setting('product_approve_by_admin') == 1) {
                    $approved = 0;
                }

                $saveData = [
                    'name' => $row['name'],
                    'description' => $row['description'],
                    'added_by' => $user->user_type == 'seller' ? 'seller' : 'admin',
                    'user_id' => $user->user_type == 'seller' ? $user->id : User::where('user_type', 'admin')->first()->id,
                    'bloc_id' => $user->bloc_id,
                    'approved' => $approved,
                    'category_id' => $row['category_id'],
                    'brand_id' => $row['brand_id'],
                    'video_provider' => $row['video_provider'],
                    'video_link' => $row['video_link'],
                    'tags' => $row['tags'],
                    'unit_price' => $row['unit_price'],
                    'unit' => $row['unit'],
                    'meta_title' => $row['meta_title'],
                    'meta_description' => $row['meta_description'],
                    'meta_image' => $row['meta_image'],
                    'discount' => 0, // 折扣为0
                    'colors' => json_encode(array()),
                    'choice_options' => json_encode(array()),
                    'variations' => json_encode(array()),
                    'slug' => preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', strtolower($row['slug']))) . '-' . Str::random(5),
                    'thumbnail_img' => $this->downloadThumbnail($row['thumbnail_img']),
                    'photos' => $this->downloadGalleryImages($row['photos']),
                ];
                $saveData['meta_image'] = $saveData['thumbnail_img'];
                $productId = Product::create($saveData);

                ProductStock::create([
                    'product_id' => $productId->id,
                    'qty' => $row['current_stock'],
                    'price' => $row['unit_price'],
                    'sku' => $row['sku'],
                    'variant' => '',
                ]);
            }

            flash(translate('Products imported successfully'))->success();
        }
    }

    public function model(array $row)
    {
        ++$this->rows;
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function rules(): array
    {
        return [
            // Can also use callback validation rules
            'unit_price' => function ($attribute, $value, $onFailure) {
                if (!is_numeric($value)) {
                    $onFailure('Unit price is not numeric');
                }
            },
            '产品名称' => [
                'required'
            ],
        ];
    }

    public function getCategoryIdByName($name) {
        if (empty($name)) return 0;

        $category = Category::query()->where('name', $name)->first();
        if ($category) {
            return $category->id;
        }

        $category = new Category();
        $category->name = $name;
        $category->save();

        return $category->id;
    }

    public function getBrandIdByName($name) {
        if (empty($name)) return 0;

        $brand = Brand::query()->where('name', $name)->first();
        if ($brand) {
            return $brand->id;
        }

        /*$category = new Category();
        $category->name = $name;*/

        return 0;
    }

    public function getImages($row, $cellKeyName, $num) {
        $images = '';
        for($i = 1; $i <= $num; $i++) {
            if (!empty($row[$cellKeyName . $i])) {
                $images .= $row[$cellKeyName . $i] . ",";
            }
        }

        return rtrim($images, ',');
    }

    public function downloadThumbnail($url)
    {
        try {
            $upload = new Upload;
            $upload->external_link = $url;
            $upload->type = 'image';
            $upload->save();

            return $upload->id;
        } catch (\Exception $e) {
        }
        return null;
    }

    public function downloadGalleryImages($urls)
    {
        $data = array();
        foreach (explode(',', str_replace(' ', '', $urls)) as $url) {
            $data[] = $this->downloadThumbnail($url);
        }
        return implode(',', $data);
    }

    private function mergeImages2Desc($description, $photos)
    {
        $photos = is_string($photos) ? explode(",", $photos) : $photos;
        foreach ($photos as $photo) {
            $description .= "<img src='{$photo}' /><br/>";
        }

        return $description;
    }
}
