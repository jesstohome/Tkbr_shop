<?php


namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Seller\ProfileController;
use App\Models\CombinedOrder;
use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentStatement;
use App\Models\ShopPaymentConfig;
use App\Models\User;
use App\Models\Wallet;
use App\Utility\SignApi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

/**
 * winpay
 * Class WinpayController
 * @package App\Http\Controllers\Payment
 */
class WinpayController extends Controller
{
    private $payment_type = 'winpay';
    private $mch_id = '';
    private $merchant_key = '';
    private $sign_type = 'MD5';

    public function __construct()
    {
        $this->mch_id = env('WINPAY_MEMBERID');
        $this->merchant_key = env('WINPAY_SECRET');// 支付秘钥
    }

    public function pay(Request $request) {
        try {
            if(Session::has('payment_type')){
                if(Session::get('payment_type') == 'cart_payment'){
                    $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                    $amount = $combined_order->grand_total;
                    $user = User::find($combined_order->user_id);
                } elseif (Session::get('payment_type') == 'order_pick_up_payment') {
                    $order = Order::find(Session::get('payment_data')['id']);
                    $user = User::find($order->user_id);
                    $amount = $order->product_storehouse_total;

                    $exchange_rate = env('QEPAY_EXCHANGE_RATE');

                    $paymentStatement = new PaymentStatement();
                    $paymentStatement->bloc_id = $order->bloc_id;
                    $paymentStatement->staff_id = $order->staff_id;
                    $paymentStatement->seller_id = $order->seller_id;
                    $paymentStatement->customer_id = $order->user_id;
                    $paymentStatement->payment_type = $this->payment_type;
                    $paymentStatement->order_no = date('YmdHis') . rand(10000, 99999);
                    $paymentStatement->out_order_no = '';
                    $paymentStatement->amount = $amount;
                    $paymentStatement->amount_exchanged = $amount * $exchange_rate;
                    $paymentStatement->business_type = 'pick_up';
                    $paymentStatement->target_id = $order->id;
                    $paymentStatement->status = 0;
                    $paymentStatement->save();
                    Session::put('payment_statement_id', $paymentStatement->id);

                    // 客户提出需要往钱包收支明细加上此次提货记录
                    $wallet = new Wallet();
                    $wallet->payment_statement_id = $paymentStatement->id;
                    $wallet->user_id = $order->seller_id;
                    $wallet->amount = $amount;
                    $wallet->payment_method = $this->payment_type;
                    $wallet->payment_details = '';
                    $wallet->approval = 0;
                    $wallet->offline_payment = 0;
                    $wallet->reciept = '';
                    $wallet->type = 3;
                    $wallet->target_id = $order->id ?? 0;
                    $wallet->save();

                }
            }

            $mch_id = $this->mch_id;
            $notify_url = route('winpay.notify');

            $trade_amount = number_format($amount * $exchange_rate, 2, '.', '');
            $sign_type = 'MD5';
            $now = time();
            $params = [
                'merchant_ref' => $paymentStatement->order_no,
                'product' => 'IndiaH5',
                'amount' => $trade_amount,
            ];
            $paramsJson = empty($params) ? '' : json_encode($params, JSON_UNESCAPED_UNICODE);
            $sign = $this->sign($paramsJson, $now);
            $postdata = array(
                'merchant_no' => $mch_id,
                'timestamp'=> $now,
                'sign_type' => $sign_type,
                'sign' => $sign,
                'params' => $paramsJson
            );

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://api.winpay.club/api/gateway/pay"); //支付请求地址
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response=curl_exec($ch);
            $curl_info = curl_getinfo($ch);
            curl_close($ch);

            \Log::debug(var_export(['pay_request_arr' => $postdata, 'res' => $response, $curl_info], true));
            if (!empty($response)) {
                $res = json_decode($response, true);
                \Log::debug(var_export(['res' => $res], true));
                if (!empty($res) && $res['code'] == 200 && !empty($res['params'])) {
                    $resultData = is_array($res['params']) ? $res['params'] : json_decode($res['params'], true);
                    $paymentStatement->out_order_no = $resultData['system_ref'];
                    $paymentStatement->save();
                    return \Redirect::to($resultData['payurl']);
                } else {
                    \Log::warning(var_export(['WinPayResult' => $res], true));
                }
            }
        } catch (\Exception $exception) {
            flash(translate('Something was wrong'))->error();
            \Log::error(var_export(['PayFailed' => $exception->getMessage(), $exception->getTraceAsString()], true));
            return redirect()->route('home');
        }
    }

    public function daifu_pay ($withdrawRequest) {
        $mch_id = env('WINPAY_MEMBERID');
        $merchant_key = env('WINPAY_SECRET');

        $user = User::find($withdrawRequest->user_id);
        $shop = $user->shop;

        $currency = Currency::query()->where('code', $withdrawRequest->currency)->first();
        if (!empty($currency->exchange_rate)) {
            $exchange_rate = $currency->exchange_rate;
        } else {
            $exchange_rate = env('QEPAY_EXCHANGE_RATE');
        }

        $money = $withdrawRequest->amount * $exchange_rate;

        $paymentStatement = new PaymentStatement();
        $paymentStatement->bloc_id = $withdrawRequest->bloc_id;
        $paymentStatement->staff_id = $withdrawRequest->staff_id;
        $paymentStatement->seller_id = $withdrawRequest->user_id;
        $paymentStatement->customer_id = 0;
        $paymentStatement->payment_type = $this->payment_type;
        $paymentStatement->order_no = date('YmdHis') . rand(10000, 99999);
        $paymentStatement->out_order_no = '';
        $paymentStatement->amount = $withdrawRequest->amount;
        $paymentStatement->amount_exchanged = $money;
        $paymentStatement->business_type = 'withdraw';
        $paymentStatement->target_id = $withdrawRequest->id;
        $paymentStatement->status = 0;
        $paymentStatement->save();

        $shop_payment_conf = ShopPaymentConfig::query()->where("shop_id", $shop->id)->where("country_code", $withdrawRequest->cur_select_country_code)->first();
        if (empty($shop_payment_conf) || (empty($shop_payment_conf['bank_account_no']) && empty($shop_payment_conf['e_wallet_address']))) {
            return flash('卖家的当前国家的银行配置不存在')->error();
        }

        $bank_name = $shop_payment_conf->bank_name;
        // Name对应银行CODE
        $online_bank_names = ProfileController::$online_bank_names;


        $now = time();
        $params = [
            'merchant_ref' => $paymentStatement->order_no,
            'product' => 'IndiaH5',
            'amount' => $money,
        ];
        $paramsJson = empty($params) ? '' : json_encode($params, JSON_UNESCAPED_UNICODE);
        $sign = $this->sign($paramsJson, $now);
        $postdata = array(
            'merchant_no' => $mch_id,
            'timestamp'=> $now,
            'sign_type' => $this->sign_type,
            'sign' => $sign,
            'params' => $paramsJson
        );

        $reqUrl = "https://payment.qeapay.com/pay/transfer";
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$reqUrl);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        /*if (env('APP_ENV') === 'local') {
            \Log::debug('使用代理');
            curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:19180"); //代理服务器地址
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //使用http代理模式
        }*/

        $response = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        \Log::debug(var_export(['daifu_pay_request_arr' => $postdata, 'res' => $response, $curl_error, $curl_info], true));
        $res = json_decode($response, true);
        if (isset($res['respCode']) && $res['respCode'] == "SUCCESS") {
            $paymentStatement->status = $res['tradeResult'];
            $paymentStatement->out_order_no = $res['tradeNo'];
            $paymentStatement->save();
            // 提交成功
            flash(translate('Payment completed'))->success();
        }else{
            // 提交失败
            $paymentStatement->status = 2;
            $paymentStatement->failure_reason = $res['errorMsg'] ?? '';
            $paymentStatement->save();

            if ($res['errorMsg'] == 'Payment is under temporary maintenance') {
                $res['errorMsg'] = 'The payment channel is busy. Please try again later';
            }
            flash($res['errorMsg'] ?: translate('Payment Failed'))->error();
        }
    }

    public function notify(Request $request) {
        $data = $request->post();
        \Log::info(var_export(['WinPayNotifyData' => $data, 'time' => date('Y-m-d H:i:s')], true));
    }

    private function sign($paramsJson, $now) {
        $paramsJson = json_encode($paramsJson, JSON_UNESCAPED_UNICODE);
        return md5($this->mch_id . $paramsJson . $this->sign_type . $now . $this->merchant_key);
    }
}
