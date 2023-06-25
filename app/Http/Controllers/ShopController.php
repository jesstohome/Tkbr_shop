<?php

namespace App\Http\Controllers;

use App\Models\ShopManage;
use App\Models\Staff;
use App\Models\Upload;
use Cookie;
use Faker\Factory;
use Illuminate\Http\Request;
use App\Models\Shop;
use App\Models\User;
use App\Models\SellerPackage;
use App\Models\BusinessSetting;
use App\Models\SellerPackagePayment;
use Auth;
use Hash;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Monolog\Logger;
use function dd;
use function view;

class ShopController extends Controller
{

    public function __construct() {
        $this->middleware('user', [ 'only' => [ 'index' ] ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $shop = Auth::user()->shop;

        $ad_js_cache_key = sprintf('show_ad_js:%s', $shop->id);
        $show_ad_js = \Cache::get($ad_js_cache_key);
        if ($show_ad_js) {
            \Cache::delete($ad_js_cache_key);
        }

        return view('seller.shop', compact('shop', 'show_ad_js'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( Request $request ) {
        if ( Auth::check() )
        {
            if ( Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff')
            {
                return view('backend.sellers.seller_form');
            }
            else if ( Auth::user()->user_type == 'salesman' )
            {
                return view('salesman.sellers.seller_form');
            }
            else if ( Auth::user()->user_type == 'customer' )
            {
                flash(translate('Customer can not be a seller'))->error();
                return back();
            }
            else if ( Auth::user()->user_type == 'seller' )
            {
                flash(translate('This user already a seller'))->error();
                return back();
            }

        }
        else
        {
            $invitation_code = '';
            if ( $request->has('invitation_code') )
            {
                $invitation_code = $request->invitation_code;
                Cookie::queue('invitation_code', $request->invitation_code, 720);
            }

            $staff_invitation_code = '';
            if ( $request->has('staff_invitation_code') )
            {
                $staff_invitation_code = $request->staff_invitation_code;
                Cookie::queue('staff_invitation_code', $request->staff_invitation_code, 720);
            }


            return view('frontend.seller_form', compact('invitation_code', 'staff_invitation_code'));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return mixed
     */
    public function store( Request $request ) {

         $package = SellerPackage::where(['is_default' => 1 ])->first();
         $package_id = $package['id'];

        $user = NULL;
        if (get_setting('seller_reg_id_card_switch')) {
            if ( $request->identity_card_front == NULL )
            {
                if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Identity Card Front Not Allow Empty!')]);
                flash(translate('Identity Card Front Not Allow Empty!'))->error();
                return back();
            }
            if ( $request->identity_card_back == NULL )
            {
                if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Identity Card Back Not Allow Empty!')]);
                flash(translate('Identity Card Back Not Allow Empty!'))->error();
                return back();
            }
        }

        $staff_id = get_staff_id();
        if ( !Auth::check() )
        {
            $bloc_id = 0;

            if ($request->get('staff_invite_code')) {
                $staff = Staff::query()->where('invite_code', $request->get('staff_invite_code'))->first();
                if (empty($staff)) {
                    if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Sorry! Invitation code error.')]);
                    flash(translate('Sorry! Invitation code error.'))->error();
                    return back();
                }
            }

            if ( User::where('email', $request->email)->first() != NULL )
            {
                if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Email already exists!')]);
                flash(translate('Email already exists!'))->error();
                return back();
            }

            // 检测用户名是否已经存在
            if (!empty($request->name)) {
                $has_name = User::query()->where('name', $request->name)->count();
                if ($has_name) {

                    if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Name already exists!')]);

                    flash(translate('Name already exists!'))->error();
                    return back();
                }
            }

            // 检测店铺名是否已经存在
            $shop_name = $request->shop_name ?: $request->name;
            if (!empty($shop_name)) {
                $has_name = Shop::query()->where('name', $shop_name)->count();
                if ($has_name) {
                    if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Shop Name already exists!')]);

                    flash(translate('Shop Name already exists!'))->error();
                    return back();
                }
            }

            if ( $request->password == $request->password_confirmation )
            {
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->user_type = "seller";
                $user->creditscore = "60";
                $user->password = Hash::make($request->password);
                $user->save();
            }
            else
            {
                if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Sorry! Password did not match!')]);
                flash(translate('Sorry! Password did not match.'))->error();
                return back();
            }
        }
        else
        {
            // 归属集团Id
            $bloc_id = Auth::user()->bloc_id;

            //如果有登录用户
            if ( Auth::user()->user_type == 'admin' ||  Auth::user()->user_type == 'staff')
            {
                //管理后台
                if ( User::where('email', $request->email)->first() != NULL )
                {
                    flash(translate('Email already exists!'))->error();
                    return back();
                }

                if ( $request->password == $request->password_confirmation )
                {
                    $user = new User;
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->user_type = "seller";
                    $user->is_virtual = "1";
                    $user->password = Hash::make($request->password);
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                }
                else
                {
                    flash(translate('Sorry! Password did not match.'))->error();
                    return back();
                }
            }
            else if ( Auth::user()->user_type == 'salesman' )
            {
                //推销员
                if ( User::where('email', $request->email)->first() != NULL )
                {
                    flash(translate('Email already exists!'))->error();
                    return back();
                }
                if ( $request->password == $request->password_confirmation )
                {
                    $user = new User;
                    $user->name = $request->name;
                    $user->email = $request->email;
                    $user->user_type = "seller";
                    $user->is_virtual = "1";
                    $user->bloc_id = Auth::user()->bloc_id;
                    $user->pid = Auth::user()->id;
                    $user->password = Hash::make($request->password);
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                }
                else
                {
                    flash(translate('Sorry! Password did not match.'))->error();
                    return back();
                }
            }
            else
            {
                $user = Auth::user();
                $user->user_type = "seller";
                $user->save();
            }
        }

        if ( Shop::where('user_id', $user->id)->first() == NULL )
        {
            $shop = new Shop;
            $shop->user_id = $user->id;
            $shop->name = $request->shop_name ?? $request->name;
            $shop->address = $request->address;
            $shop->slug = preg_replace('/\s+/', '-', $shop->name) . '-' . $user->id;
            $user->identity_card_front = $request->identity_card_front ?? 0;
            $user->identity_card_back = $request->identity_card_back ?? 0;
            $user->certtype = $request->certtype;
            $shop->seller_package_id = $package_id;
            $shop->rating = 5; // 默认5星

            // 先判断是否是由内部员工邀请码申请的
            $staff_user_id = Auth::check() ? Auth::user()->id : 0;
            if ($request->get('staff_invite_code')) {
                $staff = Staff::query()->where('invite_code', $request->get('staff_invite_code'))->first();
                if (!empty($staff)) {
                    $staff_id = $staff->id;
                    $bloc_id = $staff->bloc_id;
                    if (!empty($staff->user_id)) {
                        $staff_user_id = $staff->user_id;
                        $user->pid = $staff->user_id;
                    }
                }

            } elseif ( $request->get('invitation_code') ) {
                // 再判断是否是通过其他卖家分销邀请码申请
                $user->pid = $request->get('invitation_code');

                // 卖家A推广邀请的下级卖家B， 默认和卖家A 属于相同的员工账号负责
                $leadSeller = User::find($user->pid);
                if (!empty($leadSeller)) {
                    $staff_id = $leadSeller->staff_id;
                    $bloc_id = $leadSeller->bloc_id;
                    $staff_user_id = ShopManage::query()->where('shop_id', $leadSeller->shop->id)->value("admin_id");
                }
            }
            if (get_setting('seller_reg_id_card_switch')) {
                Upload::where('user_id', 0)->whereIn('id', [ $user->identity_card_front, $user->identity_card_back ])->update([ 'user_id' => $user->id ]);
            }

            $user->bloc_id = $bloc_id;
            $user->staff_id = $staff_id;
            $user->save();

            $shop->bloc_id = $bloc_id;
            $shop->staff_id = $staff_id;
            if ( $shop->save() )
            {
                // 绑定负责人
                if ($staff_user_id) {
                    $sm = new ShopManage();
                    $sm->shop_id = $shop->id;
                    $sm->admin_id = $staff_user_id;
                    $sm->save();
                }
                #####################################

                $shop->seller_package_id = $package_id;
                $seller_package = SellerPackage::findOrFail( $package_id );
                $shop->product_upload_limit = $seller_package->product_upload_limit;
                $shop->package_invalid_at = date('Y-m-d', strtotime($seller->package_invalid_at . ' +' . $seller_package->duration . 'days'));
                $shop->save();

                $seller_package = new SellerPackagePayment;
                $seller_package->user_id = $user->id;
                $seller_package->seller_package_id =  $package_id;
                $seller_package->payment_method = 'free';
                $seller_package->payment_details = '';
                $seller_package->approval = 1;
                $seller_package->offline_payment = 0;
                $seller_package->save();


                #####################################




                if ( Auth::check() )
                {
                    if ( Auth::user()->user_type == 'admin' )
                    {//管理后台
                        flash(translate('Virtual Seller has been created successfully!'))->success();
                        return redirect()->route('sellers.index');
                    }
                    else if ( Auth::user()->user_type == 'salesman' )
                    {//推销员
                        flash(translate('Virtual Seller has been created successfully!'))->success();
                        return redirect()->route('salesman.sellers_index');
                    }
                }
                else
                {
                    auth()->login($user, false);

                    \Cache::set(sprintf('show_ad_js:%s', $shop->id), 1);

                    // 这里不需要了，店铺审核的时候再发送
                    // ticket_say_hello();
                }
                if ( BusinessSetting::where('type', 'email_verification')->first()->value != 1 )
                {
                    $user->email_verified_at = date('Y-m-d H:m:s');
                    $user->save();
                }
                else
                {
                    $user->notify(new EmailVerificationNotification());
                }

                // redis cache red tips
                hset_plus('new_shop_created_tip', $shop->id, 1, $shop->staff_id);

                if ($request->ajax()) return response()->json(['success' => 1, 'msg' => translate('Your Shop has been created successfully!')]);
                flash(translate('Your Shop has been created successfully!'))->success();
                return redirect()->route('shops.index');
            }
            else
            {
                $user->user_type == 'customer';
                $user->save();
            }
        }

        if ($request->ajax()) return response()->json(['success' => 0, 'msg' => translate('Sorry! Something went wrong.')]);
        flash(translate('Sorry! Something went wrong.'))->error();
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id ) {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit( $id ) {
        //
    }

    public function destroy( $id ) {
        //
    }

    /**
     * 批量创建虚拟店铺
     * author: Sym
     * time: 2023-06-04 14:44
     */
    public function create_virtual_sellers(Request $request) {
        try
        {
            \DB::beginTransaction();

            $bloc_id = 0;
            $staff_id = 0;
            $package = SellerPackage::where(['is_default' => 1 ])->first();
            $package_id = $package['id'];

            $max = \intval($request->input('max')) < 1 ? 1 : ( \intval($request->input('max')) > 100 ? 100 : \intval($request->input('max')) );
            for ( $i = 0; $i < $max; $i++ ) {
                $faker = Factory::create();
                $user = new User();
                $user->name = $faker->name;
                $user->is_virtual_user = 1;
                $user->bloc_id = $bloc_id;
                $user->staff_id = $staff_id;
                $user->email = $faker->email;
                $user->email_verified_at = \date('Y-m-d H:i:s');
                $user->balance = 0;
                $user->user_type = "seller";
                $user->creditscore = "60";
                $user->saveOrFail();

                $shop = new Shop;
                $shop->bloc_id = $bloc_id;
                $shop->staff_id = $staff_id;
                $shop->user_id = $user->id;
                $shop->name = $user->name . " Shop";
                $shop->address = '';
                $shop->slug = preg_replace('/\s+/', '-', $user->name) . '-' . $user->id;
                $shop->seller_package_id = $package_id;
                $shop->verification_status = 1;
                $shop->save();

                $seller_package = new SellerPackagePayment;
                $seller_package->user_id = $user->id;
                $seller_package->seller_package_id =  $package_id;
                $seller_package->payment_method = 'free';
                $seller_package->payment_details = '';
                $seller_package->approval = 1;
                $seller_package->offline_payment = 0;
                $seller_package->save();
            }
            \DB::commit();
            return 1;
        } catch ( \Throwable $ex ) {
            \DB::rollBack();
            throw $ex;
        }
        return 0;
    }
}
