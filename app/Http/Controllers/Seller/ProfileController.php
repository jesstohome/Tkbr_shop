<?php

namespace App\Http\Controllers\Seller;

use App\Http\Requests\SellerProfileRequest;
use App\Models\Shop;
use App\Models\ShopPaymentConfig;
use App\Models\User;
use Auth;
use Hash;
use phpDocumentor\Reflection\Types\Self_;

class ProfileController extends Controller
{
    public static $e_wallet_names = ['OVO', 'DANA', 'GOPAY', 'SHOPEEPAY'];

    public static $online_bank_names = [
        'BCA' => 'Bank BCA',
        'BRI' => 'Bank BRI',
        'MANDIRI' => 'BANK MANDIRI',
            // 'BNI' => 'BANK BNI 46',
         'CIMB' => 'BANK CIMB NIAGA',
        'PERMATA' => 'BANK PERMATA',
            // 'BJB' => 'Bank BJB',
            // 'DANAMON' => 'BANK DANAMON INDONESIA',
        'BTN' => 'Bank BTN',
            // 'MAYBANK' => 'BANK MAYBANK INDONESIA',
        'SINARMAS' => 'BANK SINARMAS',
        'PANIN' => 'BANK PANIN',
            // 'BNI_SYR' => 'BANK BNI SYARIAH',
            // 'MANDIRI_SYR' => 'BANK SYARIAH MANDIRI',
            // 'DKI' => 'BPD DKI JAKARTA',
        'MEGA' => 'BANK MEGA',
        'BTPN' => 'Bank BTPN',
            // 'BRI_SYR' => 'BANK BRI SYARIAH',
            // 'MUAMALAT' => 'BANK MUAMALAT INDONESIA',
        'OCBC' => 'BANK OCBC NISP',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->addresses;

        $e_wallet_names = self::$e_wallet_names;
        $online_bank_names = self::$online_bank_names;

        // 付款配置
        $payment_config = ShopPaymentConfig::query()->where('shop_id', $user->shop->id)->get()->keyBy('country_code');

        return view('seller.profile.index', compact('user','addresses', 'e_wallet_names', 'online_bank_names', 'payment_config'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellerProfileRequest $request , $id)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;

        if($request->new_password != null && ($request->new_password == $request->confirm_password)){
            $user->password = Hash::make($request->new_password);
        }

        $user->avatar_original = $request->photo;

        $shop = $user->shop;


        if($shop){
            // 检测是否已被其他账号绑定
            if (!empty($request->bank_acc_no)) {
                $hasOther = Shop::query()->where("bank_acc_no", $request->bank_acc_no)
                    ->where("id", "!=", $shop->id)->count();
                if ($hasOther) {
                    flash(translate('This bank account has already been bound. Please bind to another bank account!'))->error();
                    return back();
                }
            }
            if (!empty($request->e_wallet_address)) {
                $hasOther = Shop::query()->where("e_wallet_address", $request->e_wallet_address)
                    ->where("id", "!=", $shop->id)->count();
                if ($hasOther) {
                    flash(translate('This wallet account has already been bound. Please bind to another wallet account!'))->error();
                    return back();
                }
            }

            $shop->cur_payment_country_code = $request->cur_payment_country_code;
            $shop->cash_on_delivery_status = $request->cash_on_delivery_status;
            $shop->bank_payment_status = $request->bank_payment_status;
            $shop->bank_name = $request->bank_name;
            $shop->bank_acc_name = $request->bank_acc_name;
            $shop->bank_acc_no = $request->bank_acc_no;
            $shop->bank_routing_no = $request->bank_routing_no;
            $shop->usdt_address = $request->usdt_address;
            $shop->usdt_payment_status = $request->usdt_payment_status;
            $shop->usdt_type = $request->usdt_type;
            $shop->e_wallet = $request->e_wallet;
            $shop->e_wallet_name = $request->e_wallet_name;
            $shop->e_wallet_address = $request->e_wallet_address;
            $shop->online_bank = $request->online_bank;
            $shop->online_bank_name = $request->online_bank_name;
            $shop->online_bank_account_name = $request->online_bank_account_name;
            $shop->online_bank_no = $request->online_bank_no;
//            $shop->online_ervice = $request->online_ervice;
            $shop->save();

            // 按国家保存付款配置
            $cur_code = strtolower($shop->cur_payment_country_code);
            $pay_c_codes = [$cur_code];
            if (!empty($request->bank_switch[$cur_code]) || !empty($request->e_wallet_switch[$cur_code])) {
                foreach ($pay_c_codes as $pay_c_code) {
                    $payment_config = ShopPaymentConfig::query()->where('shop_id', $shop->id)->where('country_code', $pay_c_code)->first();
                    if (empty($payment_config)) {
                        // add
                        $payment_config = new ShopPaymentConfig();
                        $payment_config->shop_id = $shop->id;
                    }

                    $payment_config->country_code = $pay_c_code;
                    $payment_config->bank_switch = $request->bank_switch[$pay_c_code] ?: 0;
                    $payment_config->bank_code = $request->bank_code ? $request->bank_code[$pay_c_code] : '';
                    $payment_config->bank_no = $request->bank_no[$pay_c_code];
                    $payment_config->bank_name = $request->bank_name[$pay_c_code];
                    $payment_config->bank_account_no = $request->bank_account_no[$pay_c_code];
                    $payment_config->bank_account_name = $request->bank_account_name[$pay_c_code];

                    $payment_config->bank_var1 = $request->bank_var1 ? $request->bank_var1[$pay_c_code] : '';
                    // 印度的 ifsc 11位 数字字母，第5位必须是0
                    if ($pay_c_code == 'in' && !empty($payment_config->bank_var1)) {
                        if (mb_strlen($payment_config->bank_var1) != 11 || mb_substr($payment_config->bank_var1, 4, 1) != 0) {
                            flash(translate('IFSC is wrong!'))->error();
                            return back();
                        }
                    }

                    $payment_config->bank_var2 = $request->bank_var2 ? $request->bank_var2[$pay_c_code] : '';

                    $payment_config->e_wallet_switch = $request->e_wallet_switch[$pay_c_code] ?: 0;
                    $payment_config->e_wallet_name = $request->e_wallet_name[$pay_c_code];
                    $payment_config->e_wallet_address = $request->e_wallet_address[$pay_c_code];

                    $payment_config->save();
                }
            }
        }

        $user->save();

        flash(translate('Your Profile has been updated successfully!'))->success();
        return back();
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function online_service_update(SellerProfileRequest $request , $id)
    {
        if(env('DEMO_MODE') == 'On'){
            flash(translate('Sorry! the action is not permitted in demo '))->error();
            return back();
        }

        $user = User::findOrFail($id);
        $shop = $user->shop;
        if($shop){
            $shop->online_ervice = $request->online_ervice;
            $shop->save();
        }
        $user->save();

        flash(translate('Online Service has been updated successfully!'))->success();
        return back();
    }
}
