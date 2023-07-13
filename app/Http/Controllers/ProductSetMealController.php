<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSetMeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductSetMealController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $category_id = $request->get('category_id');
        $list = ProductSetMeal::query();

        if (!empty($category_id)) {
            $list = $list->where('category_id', $category_id);
        }

        $list = $list->paginate(30);
        $category_ids = $list->pluck("category_id")->toArray();
        $product_map = Product::query()->whereIn("category_id", $category_ids)->where('in_storehouse', 1)->selectRaw("category_id, count(*) as total")->groupBy("category_id")->get();

        $product_map = $product_map->pluck("total", "category_id")->toArray();
        foreach ($list as $key => $item) {
            $total_num = (int) empty($product_map[$item->category_id]) ? 0 : $product_map[$item->category_id];
            $list[$key]->uninclude_product_total = $total_num - count(json_decode($item->product_ids, true));
        }
        return view('backend.product_storehouse.set_meal.index', compact('list'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.product_storehouse.set_meal.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $category_id = $request->post('category_id');
        $num = $request->post('num');
        $min_product_num = $request->post('min_product_num');
        $max_product_num = $request->post('max_product_num');
        if(!empty($category_id) && $num > 0 && $min_product_num > 0 && $max_product_num >= $min_product_num) {
            $has_count = ProductSetMeal::query()->where('category_id', $category_id)->count();

            // 此分类下所有的产品ID
            $all_product_ids = Product::query()
                ->where("added_by", 'admin')
                ->where("in_storehouse", 1)
                ->where("category_id", $category_id)
                ->pluck('id')->toArray();
            // 此分类下，套餐已经使用的产品ID
            $meal_products = ProductSetMeal::query()->where('category_id', $category_id);
            $meal_products = filter_by_bloc($meal_products);
            $meal_products = $meal_products->select("product_ids")->get();
            foreach ($meal_products as $meal_product) {
                $_product_ids = is_string($meal_product->product_ids) ? json_decode($meal_product->product_ids, true) : $meal_product->product_ids;
                if (!empty($_product_ids)) {
                    $all_product_ids = array_merge($all_product_ids, $_product_ids);
                }
            }

            $id_count_maps = [];
            foreach ($all_product_ids as $all_product_id) {
                if (!isset($id_count_maps[$all_product_id])) {
                    $id_count_maps[$all_product_id] = 0;
                } else {
                    $id_count_maps[$all_product_id]++;
                }
            }

            for($i = 1; $i <= $num; $i++) {
                // 按已使用数量，从小到大排序
                asort($id_count_maps);

                // 排序好的取产品ID
                $product_ids = array_keys($id_count_maps);

                // 排除已在套餐内的产品
                $product_num = mt_rand($min_product_num, $max_product_num);
                $product_ids = array_slice($product_ids, 0, $product_num);

                // Log::debug(var_export([$i, $id_count_maps, $product_ids], true));

                // 新使用的产品，累计使用次数
                foreach ($product_ids as $product_id) {
                    $id_count_maps[$product_id]++;
                }

                $productSetMeal = new ProductSetMeal();
                $productSetMeal->bloc_id = \Auth::user()->bloc_id;
                $productSetMeal->staff_id = \Auth::user()->staff_id;
                $productSetMeal->category_id = $category_id;
                $productSetMeal->min_product_num = $min_product_num;
                $productSetMeal->max_product_num = $max_product_num;
                $productSetMeal->product_ids = json_encode($product_ids, JSON_UNESCAPED_UNICODE);
                $productSetMeal->min_price = Product::query()->whereIn('id', $product_ids)->min('unit_price');
                $productSetMeal->max_price = Product::query()->whereIn('id', $product_ids)->max('unit_price');
                $productSetMeal->stock = 5000;
                $productSetMeal->name = chr(65 + $i - 1 + $has_count);
                $productSetMeal->save();
            }

            flash(translate('Set Meal has been inserted successfully'))->success();
            return redirect()->route('product_set_meal.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $row = ProductSetMeal::findOrFail($id);
        $row->product_ids = (array) json_decode($row->product_ids, true);
        return view('backend.product_storehouse.set_meal.edit', compact('row'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $category_id = $request->category_id;
        $product_ids = $request->product_ids;
        $productSetMeal = ProductSetMeal::find($id);
        if (empty($id) || empty($productSetMeal)) {
            flash(translate('Something went wrong'))->error();
            return back();
        }

        if(!empty($category_id)) {
            if ($category_id != $productSetMeal->category_id) {
                $productSetMeal->name = chr(65 + ProductSetMeal::query()->where('category_id', $category_id)->where('id', '!=', $id)->count());
            }

            $productSetMeal->category_id = $category_id;
            $productSetMeal->product_ids = is_array($product_ids) ? json_encode($product_ids, JSON_UNESCAPED_UNICODE) : $product_ids;
            $productSetMeal->min_price = Product::query()->whereIn('id', $product_ids)->min('unit_price');
            $productSetMeal->max_price = Product::query()->whereIn('id', $product_ids)->max('unit_price');
            $productSetMeal->stock = $request->stock;

            $productSetMeal->save();

            flash(translate('Set Meal has been updated successfully'))->success();
            return redirect()->route('product_set_meal.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        ProductSetMeal::destroy($id);
        flash(translate('Set Meal has been deleted successfully'))->success();
        return redirect()->route('product_set_meal.index');
    }

    public function products(Request $request) {
        $category_id = $request->post('category_id');
        return view('backend.product_storehouse.set_meal.product_select', compact('category_id'));
    }

    /**
     * 当前分类已有套餐数量
     * @param Request $request
     * @return mixed
     */
    public function get_has_nums(Request $request) {
        $category_id = $request->get('category_id');
        $meal = ProductSetMeal::query()->where("category_id", $category_id);
        $meal = filter_by_bloc($meal);

        return $meal->count();
    }
}
