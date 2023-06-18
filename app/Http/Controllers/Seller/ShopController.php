<?php

namespace App\Http\Controllers\Seller;

use App\Models\BusinessSetting;
use App\Models\Product;
use App\Models\Staff;
use Illuminate\Http\Request;
use App\Models\Shop;
use Auth;

class ShopController extends Controller
{
    public function index()
    {
        $shop = Auth::user()->shop;

        $invite_code = '';
        if (!empty($pid)) {
            $invite_code = Staff::query()->where('user_id', Auth::user()->pid)->value('invite_code');
        }

        return view('seller.shop', compact('shop', 'invite_code'));
    }

    public function update(Request $request)
    {
        $shop = Shop::find($request->shop_id);

        if ($request->has('name')) {
            if ($request->has('shipping_cost')) {
                $shop->shipping_cost = $request->shipping_cost;
            }

            $shop_name = $request->name;
            if (!empty($shop_name)) {
                $has_name = Shop::query()->where('name', $shop_name)->where('id', '!=', $shop->id)->count();
                if ($has_name) {
                    flash(translate('Shop Name already exists!'))->error();
                    return back();
                }
            }

            $shop->name             = $request->name;
            $shop->address          = $request->address ?? '';
            $shop->phone            = $request->phone;
            $shop->slug             = preg_replace('/\s+/', '-', $request->name) . '-' . $shop->user->id;
            $shop->meta_title       = $request->meta_title;
            $shop->meta_description = $request->meta_description;
            $shop->logo             = $request->logo;
        }

        if ($request->has('delivery_pickup_longitude') && $request->has('delivery_pickup_latitude')) {

            $shop->delivery_pickup_longitude    = $request->delivery_pickup_longitude;
            $shop->delivery_pickup_latitude     = $request->delivery_pickup_latitude;
        } elseif (
            $request->has('facebook') ||
            $request->has('google') ||
            $request->has('twitter') ||
            $request->has('youtube') ||
            $request->has('instagram')
        ) {
            $shop->facebook = $request->facebook;
            $shop->instagram = $request->instagram;
            $shop->google = $request->google;
            $shop->twitter = $request->twitter;
            $shop->youtube = $request->youtube;
        } else {
            $shop->sliders = $request->sliders;
        }

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function online_service_update(Request $request)
    {
        $shop = Shop::find($request->shop_id);
        $shop->online_ervice = $request->online_ervice;

        if ($shop->save()) {
            flash(translate('Your Shop has been updated successfully!'))->success();
            return back();
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function verify_form ()
    {
        if (Auth::user()->shop->verification_info == null) {
            $shop = Auth::user()->shop;
            return view('seller.verify_form', compact('shop'));
        } else {
            flash(translate('Sorry! You have sent verification request already.'))->error();
            return back();
        }
    }

    public function verify_form_store(Request $request)
    {
        $data = array();
        $i = 0;
        foreach (json_decode(BusinessSetting::where('type', 'verification_form')->first()->value) as $key => $element) {
            $item = array();
            if ($element->type == 'text') {
                $item['type'] = 'text';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'select' || $element->type == 'radio') {
                $item['type'] = 'select';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i];
            } elseif ($element->type == 'multi_select') {
                $item['type'] = 'multi_select';
                $item['label'] = $element->label;
                $item['value'] = json_encode($request['element_' . $i]);
            } elseif ($element->type == 'file') {
                $item['type'] = 'file';
                $item['label'] = $element->label;
                $item['value'] = $request['element_' . $i]->store('uploads/verification_form');
            }
            array_push($data, $item);
            $i++;
        }
        $shop = Auth::user()->shop;
        $shop->verification_info = json_encode($data);
        if ($shop->save()) {
            flash(translate('Your shop verification request has been submitted successfully!'))->success();
            return redirect()->route('seller.dashboard');
        }

        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    public function show()
    {
    }

    public function rand_add_views() {
        $shop = Auth::user()->shop;

        // 没有产品不需要增加
        $productTotal = Product::query()->where("user_id", $shop->user_id)->count();
        if (empty($productTotal)) {
            return response()->json();
        }

        $shop->views += 1;
        $shop->save();

        return response()->json();
    }
}
