<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ShopPaymentConfig;
use App\Models\Wallet;
use App\Models\WalletExpenseLog;
use Illuminate\Http\Request;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use App\Models\Shop;
use Auth;

class SellerWithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $balance = $user->balance;
        $userId = $user->id;
        $seller_withdraw_requests = SellerWithdrawRequest::where('user_id', $userId)->latest()->paginate(9)
            ->appends(['opage' => \request()->opage, 'rpage' => \request()->rpage]);

        $freezeOrders = Order::query()->where('seller_id', $userId)
            ->where(function ($query) {
                $query->whereNotNull('freeze_expired_at')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNull('freeze_expired_at')->where('product_storehouse_status', 0);
                    });
            })
            ->latest()
            ->paginate(9, ['*'], 'opage')
            ->appends(['page' => \request()->page, 'rpage' => \request()->rpage]);

        $rechargeList = Wallet::query()->where('user_id', $userId)->latest()->paginate(9, ['*'], 'rpage')
            ->appends(['page' => \request()->page, 'opage' => \request()->opage]);
        $shop = $user->shop;

        $shop_payment_config = ShopPaymentConfig::query()->where('shop_id', $shop->id)->where("country_code", $shop->cur_payment_country_code)->first();

        $paymentList = Payment::orderBy('created_at', 'desc')->where('t_type',1)->where('seller_id',Auth::user()->id)->paginate(15);

        $walletExpenseList = WalletExpenseLog::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->paginate(15);

        $auto_show_recharge = $request->get('auto_show_recharge', 0);
        return view('seller.money_withdraw_requests.index', compact('paymentList','seller_withdraw_requests', 'freezeOrders', 'rechargeList', 'balance', 'shop', 'auto_show_recharge', 'walletExpenseList', 'shop_payment_config'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $user = Auth::user();

        $type = $request->type;


        if( $type == 1 )
        {
                if ($request->amount > $user->balance) {
                    flash(translate('You do not have enough balance to send withdraw request'))->error();
                    return back();
                }

                $minimum_seller_amount_withdraw = (float) get_setting('minimum_seller_amount_withdraw');
                if ($request->amount < $minimum_seller_amount_withdraw) {
                    flash(sprintf(translate('The minimum withdrawal amount is %s dollar'), $minimum_seller_amount_withdraw))->error();
                    return back();
                }
                $exits = SellerWithdrawRequest::where('status', '0')->where('type',1)->where('user_id', $user->id)->count();

                if ($exits !== 0) {
                    flash(translate('withdraw exited'))->error();
                    return back();
                }
                $seller_withdraw_request = new SellerWithdrawRequest;
                $seller_withdraw_request->user_id = $user->id;
                $seller_withdraw_request->bloc_id = $user->bloc_id;
                $seller_withdraw_request->staff_id = $user->staff_id;
                $seller_withdraw_request->cur_select_country_code = $request->country_code;
                $seller_withdraw_request->amount = $request->amount;
                $seller_withdraw_request->message = $request->message;
                $seller_withdraw_request->status = '0';
                $seller_withdraw_request->viewed = '0';
                $seller_withdraw_request->w_type = $request->w_type;

                if ($seller_withdraw_request->save()) {//扣除余额
                    $userModel = User::find($user->id);
                    $userModel->balance = $user->balance-$request->amount;
                    $userModel->save();

                    // 记录支出日志
                    $walletExpenseLog = new WalletExpenseLog();
                    $walletExpenseLog->user_id = $user->id;
                    $walletExpenseLog->amount = $request->amount;
                    $walletExpenseLog->type = 'withdrawal';
                    $walletExpenseLog->save();

                    hset_plus('new_withdraw_tip', $seller_withdraw_request->id, 1, $seller_withdraw_request->staff_id);

                    flash(translate('Request has been sent successfully'))->success();
                    return redirect()->route('seller.money_withdraw_requests.index');
                } else {
                    flash(translate('Something went wrong'))->error();
                    return back();
                }
        }
        elseif ( $type == 2 )
        {

            if ($request->amount > $user->shop->bzj_money) {
                    flash(translate('You do not have enough guarantee balance to send withdraw request'))->error();
                    return back();
                }
                $exits = SellerWithdrawRequest::where('status', '0')->where('type',2)->where('user_id', $user->id)->count();

                if ($exits !== 0)
                {
                    flash(translate('withdraw exited'))->error();
                    return back();
                }
                $seller_withdraw_request = new SellerWithdrawRequest;
                $seller_withdraw_request->user_id = $user->id;
                $seller_withdraw_request->bloc_id = $user->bloc_id;
                $seller_withdraw_request->staff_id = $user->staff_id;
                $seller_withdraw_request->amount = $request->amount;
                $seller_withdraw_request->message = $request->message;
                $seller_withdraw_request->status = '0';
                $seller_withdraw_request->viewed = '0';
                $seller_withdraw_request->type = 2;
                $seller_withdraw_request->w_type =  $request->w_type;

                if ($seller_withdraw_request->save()) {//扣除余额
                    $userModel = Shop::find($user->shop->id);
                    $userModel->bzj_money = $userModel->bzj_money-$request->amount;
                    $userModel->save();

                    hset_plus('new_withdraw_tip', $seller_withdraw_request->id, 1, $seller_withdraw_request->staff_id);

                    flash(translate('Request has been sent successfully'))->success();
                    return redirect()->route('seller.money_withdraw_requests.index');
                } else {
                    flash(translate('Something went wrong'))->error();
                    return back();
                }


        }
    }

    public function store234(Request $request)
    {
        $user = Auth::user();

        $type = $request->type;
        if( $type == 1 )
        {
                if ($request->amount > $user->balance) {
                    flash(translate('You do not have enough balance to send withdraw request'))->error();
                    return back();
                }
                $exits = SellerWithdrawRequest::where('status', '0')->where('type',1)->where('user_id', $user->id)->count();

                if ($exits !== 0) {
                    flash(translate('withdraw exited'))->error();
                    return back();
                }
                $seller_withdraw_request = new SellerWithdrawRequest;
                $seller_withdraw_request->user_id = $user->id;
                $seller_withdraw_request->amount = $request->amount;
                $seller_withdraw_request->message = $request->message;
                $seller_withdraw_request->status = '0';
                $seller_withdraw_request->viewed = '0';
                if ($seller_withdraw_request->save()) {//扣除余额
                    $userModel = User::find($user->id);
                    $userModel->balance = $user->balance-$request->amount;
                    $userModel->save();
                    flash(translate('Request has been sent successfully'))->success();
                    return redirect()->route('seller.money_withdraw_requests.index');
                } else {
                    flash(translate('Something went wrong'))->error();
                    return back();
                }
        }
        elseif ( $type == 2 )
        {

            if ($request->amount > $user->shop->bzj_money) {
                    flash(translate('You do not have enough guarantee balance to send withdraw request'))->error();
                    return back();
                }
                $exits = SellerWithdrawRequest::where('status', '0')->where('type',2)->where('user_id', $user->id)->count();

                if ($exits !== 0)
                {
                    flash(translate('withdraw exited'))->error();
                    return back();
                }
                $seller_withdraw_request = new SellerWithdrawRequest;
                $seller_withdraw_request->user_id = $user->id;
                $seller_withdraw_request->amount = $request->amount;
                $seller_withdraw_request->message = $request->message;
                $seller_withdraw_request->status = '0';
                $seller_withdraw_request->viewed = '0';
                $seller_withdraw_request->type = 2;
                if ($seller_withdraw_request->save()) {//扣除余额
                    $userModel = Shop::find($user->shop->id);
                    $userModel->bzj_money = $userModel->bzj_money-$request->amount;
                    $userModel->save();
                    flash(translate('Request has been sent successfully'))->success();
                    return redirect()->route('seller.money_withdraw_requests.index');
                } else {
                    flash(translate('Something went wrong'))->error();
                    return back();
                }


        }
    }

    /**
     * 变更国家
     * author: Sym
     * time: 2023-05-20 09:40
     * @param Request $request
     * @return string
     */
    public function change_country(Request $request) {
        $html = '';
        $shop_payment_config = ShopPaymentConfig::query()->where("country_code", $request->code)->where('shop_id', Auth::user()->shop->id)->first();
        if (!empty($shop_payment_config)) {
            // wallet
            if ($request->type == 5) {
                $html = join(" ", [$shop_payment_config->e_wallet_name, $shop_payment_config->e_wallet_address]);
            } elseif($request->type == 2) {
                // bank
                $html = join(" ", [$shop_payment_config->bank_name, $shop_payment_config->bank_no, $shop_payment_config->bank_account_name]);
            }
        }

        return $html;
    }
}
