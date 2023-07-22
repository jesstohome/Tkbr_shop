<?php

namespace App\Services;

use App\Models\ProductStock;
use App\Utility\ProductUtility;
use Combinations;

class ProductStockService
{
    public function store(array $data, $product)
    {
        $collection = collect($data);

        $options = ProductUtility::get_attribute_options($collection);

        //Generates the combinations of customer choice options
        $combinations = Combinations::makeCombinations($options);

        $variant = '';
        if (count($combinations[0]) > 0) {
            $product->variant_product = 1;
            $product->save();
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);
                $field1 = $str;
                $field2 = str_replace('.', '_', $str);
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->variant = $str;
                $product_stock->price = request('price_' . $field1, request('price_' . $field2, 0));
                $product_stock->sku = request('sku_' . $field1, request('sku_' . $field2, ''));
                $product_stock->qty = request('qty_' . $field1, request('qty_' . $field2, 0));
                $product_stock->image = request('img_' . $field1, request('img_' . $field2, ''));
                $product_stock->save();
            }
        } else {
            unset($collection['colors_active'], $collection['colors'], $collection['choice_no']);
            $qty = $collection['current_stock'];
            $price = $collection['unit_price'];
            unset($collection['current_stock']);

            $data = $collection->merge(compact('variant', 'qty', 'price'))->toArray();

            ProductStock::create($data);
        }
    }

    public function product_duplicate_store($product_stocks, $product_new, $maxProfit = 0)
    {
        foreach ($product_stocks as $key => $stock) {
            $product_stock = new ProductStock;
            $product_stock->product_id = $product_new->id;
            $product_stock->variant = $stock->variant;
            $product_stock->sku = $stock->sku;
            $product_stock->qty = $stock->qty;
            $product_stock->price = $stock->price;

            // 利润计算
            if (!empty($maxProfit)) {
                $product_stock->price = $stock->price + $stock->price * $maxProfit;
            }

            $product_stock->save();
        }
    }
}
