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

        $category_ids = $list->pluck("category_id")->toArray();
        $product_map = Product::query()->whereIn("category_id", $category_ids)->where('in_storehouse', 1)->selectRaw("category_id, count(*) as total")->groupBy("category_id")->get();

        $product_map = $product_map->pluck("total", "category_id")->toArray();
        $used_product_ids = [];
        foreach ($list->get() as $key => $item) {
            $product_ids = is_string($item->product_ids) ? json_decode($item->product_ids, true) : $item->product_ids;
            if (!isset($used_product_ids[$item->category_id])) {
                $used_product_ids[$item->category_id] = $product_ids;
            } else {
                $used_product_ids[$item->category_id] = array_merge($used_product_ids[$item->category_id], $product_ids);
            }
            $used_product_ids[$item->category_id] = array_unique($used_product_ids[$item->category_id]);
        }

        $list = $list->paginate(30);

        foreach ($list as $key => $item) {
            $total_num = (int) empty($product_map[$item->category_id]) ? 0 : $product_map[$item->category_id];
            $list[$key]->uninclude_product_total = max(0, $total_num - count($used_product_ids[$item->category_id] ?? []));
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
        $gen_type = (int) $request->post('gen_type', 1);
        $meal_name = trim((string) $request->post('name', ''));

        if (empty($category_id)) {
            flash(translate('Something went wrong'))->error();
            return back();
        }

        $has_count = ProductSetMeal::query()->where('category_id', $category_id)->count();

        // 分类下仓库商品(自营 + 仓库内 + 非卖家铺货)
        $warehouseQuery = function () use ($category_id) {
            return Product::query()
                ->where("added_by", 'admin')
                ->where("in_storehouse", 1)
                ->whereNull("original_id")
                ->where("category_id", $category_id);
        };

        // ========== 方式二：按价格区间(一个区间生成一个套餐，区间本身保证不重复) ==========
        if ($gen_type == 2) {
            $range_from = trim((string) $request->post('price_range_from', ''));
            $range_to = trim((string) $request->post('price_range_to', ''));
            if (!is_numeric($range_from) || !is_numeric($range_to) || (float) $range_from > (float) $range_to) {
                flash('价格区间无效')->error();
                return back();
            }
            $product_ids = $warehouseQuery()
                ->whereBetween('unit_price', [(float) $range_from, (float) $range_to])
                ->orderBy('id', 'desc')
                ->pluck('id')->toArray();

            if (empty($product_ids)) {
                flash('该价格区间内没有可用商品')->error();
                return back();
            }

            $this->createSetMeal($category_id, $product_ids, 2, $range_from, $range_to, $has_count, null, null, $meal_name);
            flash('套餐添加成功')->success();
            return redirect()->route('product_set_meal.index');
        }

        // ========== 方式三：按排序取数(第N到第M个，区间本身保证不重复) ==========
        if ($gen_type == 3) {
            $range_from = trim((string) $request->post('sort_range_from', ''));
            $range_to = trim((string) $request->post('sort_range_to', ''));
            if (!is_numeric($range_from) || !is_numeric($range_to) || (int) $range_from < 1 || (int) $range_to < (int) $range_from) {
                flash('排序区间无效')->error();
                return back();
            }
            $allIds = $warehouseQuery()->orderBy('id', 'desc')->pluck('id')->toArray();
            $start = (int) $range_from - 1;
            $length = (int) $range_to - (int) $range_from + 1;
            $product_ids = array_slice($allIds, $start, $length);

            if (empty($product_ids)) {
                flash('该排序区间内没有可用商品')->error();
                return back();
            }

            $this->createSetMeal($category_id, $product_ids, 3, $range_from, $range_to, $has_count, null, null, $meal_name);
            flash('套餐添加成功')->success();
            return redirect()->route('product_set_meal.index');
        }

        // ========== 方式一：随机(原有逻辑) ==========
        $num = $request->post('num');
        $min_product_num = $request->post('min_product_num');
        $max_product_num = $request->post('max_product_num');
        if ($num > 0 && $min_product_num > 0 && $max_product_num >= $min_product_num) {
            // 此分类下所有的产品ID
            $all_product_ids = $warehouseQuery()->pluck('id')->toArray();
            // 套餐已使用的产品ID合并进来，用于计数均衡
            $usedIds = [];
            $meal_products = ProductSetMeal::query()->where('category_id', $category_id)->select("product_ids")->get();
            foreach ($meal_products as $meal_product) {
                $_ids = is_string($meal_product->product_ids) ? json_decode($meal_product->product_ids, true) : $meal_product->product_ids;
                if (!empty($_ids)) {
                    $usedIds = array_merge($usedIds, $_ids);
                }
            }
            $usedIds = array_values(array_unique($usedIds));
            foreach ($usedIds as $usedId) {
                $all_product_ids[] = $usedId;
            }

            $id_count_maps = [];
            foreach ($all_product_ids as $all_product_id) {
                if (!isset($id_count_maps[$all_product_id])) {
                    $id_count_maps[$all_product_id] = 0;
                } else {
                    $id_count_maps[$all_product_id]++;
                }
            }

            for ($i = 1; $i <= $num; $i++) {
                // 按已使用数量，从小到大排序
                asort($id_count_maps);

                // 排序好的取产品ID
                $sortedIds = array_keys($id_count_maps);

                $product_num = mt_rand($min_product_num, $max_product_num);
                $selected = array_slice($sortedIds, 0, $product_num);

                // 新使用的产品，累计使用次数
                foreach ($selected as $pid) {
                    $id_count_maps[$pid]++;
                }

                $nameForMeal = $meal_name !== '' ? $meal_name . '-' . $i : null;
                $this->createSetMeal($category_id, $selected, 1, null, null, $has_count + $i - 1, $min_product_num, $max_product_num, $nameForMeal);
            }

            flash(translate('Set Meal has been inserted successfully'))->success();
            return redirect()->route('product_set_meal.index');
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * 创建套餐记录
     */
    private function createSetMeal($category_id, $product_ids, $gen_type, $range_from, $range_to, $name_index, $minNum = null, $maxNum = null, $name = null)
    {
        $productSetMeal = new ProductSetMeal();
        $productSetMeal->bloc_id = \Auth::user()->bloc_id;
        $productSetMeal->staff_id = \Auth::user()->staff_id;
        $productSetMeal->category_id = $category_id;
        $productSetMeal->min_product_num = $minNum ?: count($product_ids);
        $productSetMeal->max_product_num = $maxNum ?: count($product_ids);
        $productSetMeal->product_ids = json_encode($product_ids, JSON_UNESCAPED_UNICODE);
        $productSetMeal->min_price = Product::query()->whereIn('id', $product_ids)->min('unit_price');
        $productSetMeal->max_price = Product::query()->whereIn('id', $product_ids)->max('unit_price');
        $productSetMeal->stock = 5000;
        $productSetMeal->gen_type = $gen_type;
        $productSetMeal->range_from = $range_from;
        $productSetMeal->range_to = $range_to;
        $productSetMeal->name = ($name !== null && $name !== '') ? $name : chr(65 + $name_index);
        $productSetMeal->save();

        return $productSetMeal;
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
            if ($request->filled('name')) {
                // 自定义套餐名称
                $productSetMeal->name = trim($request->name);
            } elseif ($category_id != $productSetMeal->category_id) {
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

    /**
     * 批量删除套餐
     */
    public function bulk_delete(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids) || !is_array($ids)) {
            return 0;
        }
        ProductSetMeal::whereIn('id', $ids)->delete();
        return 1;
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

    /**
     * 当前分类仓库商品数量(自营 + 仓库内 + 非卖家铺货)
     */
    public function get_category_product_count(Request $request) {
        $category_id = $request->get('category_id');
        $count = Product::query()
            ->where('added_by', 'admin')
            ->where('in_storehouse', 1)
            ->whereNull('original_id')
            ->where('category_id', $category_id)
            ->count();

        return $count;
    }
}
