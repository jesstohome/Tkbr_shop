<?php


namespace App\Http\Controllers\Payment;

use App\Models\CombinedOrder;
use App\Models\Order;
use App\Models\PaymentStatement;
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

            $mch_id = env('WINPAY_MEMBERID');
            $merchant_key = env('WINPAY_SECRET');// 支付秘钥
            $notify_url = route('winapy.notify');

            $trade_amount = number_format($amount * $exchange_rate, 2, '.', '');
            $sign_type = 'MD5';

            $now = time();
            $params = [
                'merchant_ref' => $paymentStatement->order_no,
                'product' => 'IndiaH5',
                'amount' => $trade_amount,
            ];
            $paramsJson = empty($params) ? '' : json_encode($params, JSON_UNESCAPED_UNICODE);
            $sign = md5($mch_id . $paramsJson . $sign_type . $now . $merchant_key);
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
                if (!empty($res) && $res['tradeResult'] == 1 && !empty($res['payInfo'])) {
                    $paymentStatement->out_order_no = $res['orderNo'];
                    $paymentStatement->save();
                    return \Redirect::to($res['payInfo']);
                } else {
                    \Log::warning(var_export(['QePayResult' => $res], true));
                }
            }
        } catch (\Exception $exception) {
            flash(translate('Something was wrong'))->error();
            \Log::error(var_export(['PayFailed' => $ex->getMessage(), $ex->getTraceAsString()], true));
            return redirect()->route('home');
        }
    }

    public function daifu_pay ($withdrawRequest) {

    }

    public function notify(Request $request) {

    }
}
