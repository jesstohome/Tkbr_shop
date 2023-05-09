<?php

namespace App\Http\Resources;

use App\Models\ProductTranslation;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PosSetMealCollection extends ResourceCollection
{

    public function toArray($request)
    {
        return [
            'data' => $this->collection->map(function ($data) {
                $name = $data->name;
                return [
                    'id' => $data->id,
                    'stock' => $data->stock,
                    'added_times' => $data->added_times,
                    'min_price' => single_price($data->min_price),
                    'max_price' => single_price($data->max_price),
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
