<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ProductTranslation;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PosSetMealCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $prices = Product::query()->whereIn('id', json_decode($data->product_ids))->pluck('unit_price')->toArray();

                $name = $data->name;
                return [
                    'id' => $data->id,
                    'stock' => $data->stock,
                    'added_times' => $data->added_times,
                    'min_price' => empty($prices) ? 0 : single_price(min($prices)),
                    'max_price' => empty($prices) ? 0 : single_price(max($prices)),
                    'name' => $data->category->getTranslation('name') . '-' . $data->name,
                    'name2' => addslashes($name),
                    'thumbnail_image' => ($data->category->icon == null) ? '' : uploaded_asset($data->category->icon),
                ];
            })->shuffle()
        ];
    }

    public function with($request)
    {
        return [
            'success' => true,
            'status' => 200
        ];
    }
}
