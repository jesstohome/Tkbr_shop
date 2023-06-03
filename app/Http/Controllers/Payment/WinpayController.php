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

            $version = '1.0';
            $mch_id = env('QEPAY_MCH_ID');
            $merchant_key = env('QEPAY_ZHIFU_MCH_KEY');// 支付秘钥
            $notify_url = route('qepay.notify');
            $mch_order_no = $paymentStatement->order_no;
            /**
             * 支付类型
             * 200	印尼网银B2C
             * 202	印尼OVO钱包
             */
            $pay_type = 200;

            $trade_amount = number_format($amount * $exchange_rate, 2, '.', '');
            $order_date = date('Y-m-d H:i:s');

            // 网银通道必填，其他类型一定不能填该参数
            $bank_code = '';

            $goods_name = $paymentStatement->order_no;
            $sign_type = 'MD5';
            $mch_return_msg = '';

            $signStr = "";
            if($bank_code != ""){
                $signStr = $signStr."bank_code=".$bank_code."&";
            }

            $signStr = $signStr."goods_name=".$goods_name."&";
            $signStr = $signStr."mch_id=".$mch_id."&";
            $signStr = $signStr."mch_order_no=".$mch_order_no."&";
            if($mch_return_msg != ""){
                $signStr = $signStr."mch_return_msg=".$mch_return_msg."&";
            }
            $signStr = $signStr."notify_url=".$notify_url."&";
            $signStr = $signStr."order_date=".$order_date."&";
            if($page_url != ""){
                $signStr = $signStr."page_url=".$page_url."&";
            }
            $signStr = $signStr."pay_type=".$pay_type."&";
            $signStr = $signStr."trade_amount=".$trade_amount."&";
            $signStr = $signStr."version=".$version;
            $signAPI = new SignApi();
            $sign = $signAPI->sign($signStr,$merchant_key);

            $postdata=array(
                'goods_name'=>$goods_name,
                'mch_id'=>$mch_id,
                'mch_order_no'=>$mch_order_no,
                'notify_url'=>$notify_url,
                'order_date'=>$order_date,
                'pay_type'=>$pay_type,
                'trade_amount'=>$trade_amount,
                'version' => $version,
                /** 下面这些参数有填写才需要提交，不填写的不需要提交也不需要参与签名 */
                /**'bank_code'=>$bank_code,
                'mch_return_msg'=>$mch_return_msg,
                'page_url'=>$page_url,*/
                'sign_type'=>$sign_type,
                'sign'=>$sign);
            if (!empty($bank_code)) {
                $postdata['bank_code'] = $bank_code;
            }

            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,"https://payment.qeapay.com/pay/web"); //支付请求地址
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/x-www-form-urlencoded'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postdata));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response=curl_exec($ch);
            $curl_info = curl_getinfo($ch);

            //$res=simplexml_load_string($response);

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
