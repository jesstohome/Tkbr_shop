<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductSetMeal;
use Illuminate\Http\Request;

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
        $list = ProductSetMeal::query()->join("categories c", "product_set_meal.category_id", "=", "c.id");
        if (!empty($category_id)) {
            $list = $list->where('category_id', $category_id);
        }

        $list = $list->paginate(30);
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
        $product_ids = $request->product_ids;
        if(!empty($category_id)) {
            $productSetMeal = new ProductSetMeal();
            $productSetMeal->bloc_id = \Auth::user()->bloc_id;
            $productSetMeal->staff_id = \Auth::user()->staff_id;
            $productSetMeal->category_id = $category_id;
            $productSetMeal->product_ids = is_array($product_ids) ? json_encode($product_ids, JSON_UNESCAPED_UNICODE) : $product_ids;
            $productSetMeal->min_price = Product::query()->whereIn('id', $product_ids)->min('unit_price');
            $productSetMeal->max_price = Product::query()->whereIn('id', $product_ids)->max('unit_price');
            $productSetMeal->stock = $request->post('stock');
            $productSetMeal->name = chr(65 + ProductSetMeal::query()->where('category_id', $category_id)->count());
            $productSetMeal->save();

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
}
