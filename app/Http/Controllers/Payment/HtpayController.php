<?php


namespace App\Http\Controllers\Payment;


use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\PaymentStatement;
use App\Models\SellerPackage;
use App\Models\SellerSpreadPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Session;

class HtpayController extends Controller
{

    public function pay(Request $request) {
        $pay_memberid = env('HTPAY_MEMBERID');
        $sign_key = env('HTPAY_SECRET');
        $pay_bankcode = env('HTPAY_BANK_CODE', 904);

        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = $combined_order->grand_total;
                $user = User::find($combined_order->user_id);
            }
            elseif (Session::get('payment_type') == 'wallet_payment') {
                $amount = Session::get('payment_data')['amount'];
            }
            elseif (Session::get('payment_type') == 'customer_package_payment') {
                $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);
                $amount = $customer_package->amount;
            }
            elseif (Session::get('payment_type') == 'seller_package_payment') {
                $seller_package = SellerPackage::findOrFail(Session::get('payment_data')['seller_package_id']);
                $amount = $seller_package->amount;
            }
            elseif (Session::get('payment_type') == 'seller_spread_package_payment') {
                $seller_package = SellerSpreadPackage::findOrFail(Session::get('payment_data')['seller_spread_package_id']);
                $amount = $seller_package->amount;
            } elseif (Session::get('payment_type') == 'order_pick_up_payment') {
                $order = Order::find(Session::get('payment_data')['id']);
                $user = User::find($order->user_id);
                $amount = $order->product_storehouse_total;

                $paymentStatement = new PaymentStatement();
                $paymentStatement->bloc_id = $order->bloc_id;
                $paymentStatement->staff_id = $order->staff_id;
                $paymentStatement->seller_id = $order->seller_id;
                $paymentStatement->customer_id = $order->user_id;
                $paymentStatement->payment_type = 'htpay';
                $paymentStatement->order_no = date('YmdHis') . rand(10000, 99999);
                $paymentStatement->out_order_no = '';
                $paymentStatement->amount = $amount;
                $paymentStatement->business_type = 'pick_up';
                $paymentStatement->target_id = $order->id;
                $paymentStatement->status = 0;
                $paymentStatement->save();

                Session::put('payment_statement_id', $paymentStatement->id);

            }
        }

        // 默认1美元对换14670印尼盾
        $exchange_rate = env('HTPAY_EXCHANGE_RATE', 14670);
        if ($exchange_rate <= 0) {
            $exchange_rate = 14670;
        }

        $request_arr = [
            "pay_memberid" => $pay_memberid,//商户id 商户后台获取
            "pay_orderid"  => $paymentStatement->order_no,//商户订单号自己生成
            "pay_amount"   => number_format($amount, 2, '.', '') * $exchange_rate,//支付金额
            "pay_applydate" => date("Y-m-d H:i:s"),//支付时间
            "pay_bankcode"  => $pay_bankcode,//后台获取
            "pay_notifyurl" => route('htpay.notify'),//异步回调地址
            "pay_callbackurl" => route('htpay.callback'),//同步回调地址（最后通知以异步回调为准）
        ];
        ksort($request_arr);
        //签名字符串
        $md5str = "";
        foreach ($request_arr as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        //签名key商户后台获取
        $sign = strtoupper(md5($md5str . "key=" . $sign_key));
        $request_arr['pay_md5sign'] = $sign;
        $request_arr['email'] = $user->email ?: '';
        $request_arr['customer_id'] = $user->id; //下游用户id
        $request_arr['customer_name'] = $user->name; //下游用户姓名
        $request_arr['customer_phone'] = '91829732132'; //下游用户手机
        $request_arr['returnType'] = 'json'; //下游用户手机

        // https://www.htpayio.com/Pay_Index.html
        try {
            $res = http_post('https://www.htpayio.com/Pay_Index.html', $request_arr);
            \Log::debug(var_export(['pay_request_arr' => $request_arr, 'res' => $res], true));
            $res = json_decode($res, true);
            if (!empty($res['data']['pay_url'])) {
                $paymentStatement->out_order_no = $res['data']['order_id'];
                $paymentStatement->save();
                return \Redirect::to($res['data']['pay_url']);
            } else {
                \Log::warning(var_export(['HtPayResult' => $res], true));
            }

        }catch (\Exception $ex) {
            flash(translate('Something was wrong'))->error();
            \Log::error(var_export(['PayFailed' => $ex->getMessage(), $ex->getTraceAsString()], true));
            return redirect()->route('home');
        }
    }

    // 页面跳转通知
    public function callback() {
        $data = $request->post();
        $payment_statement_id = Session::get('payment_statement_id');
        \Log::info(var_export(['payment_statement_id' => $payment_statement_id, $payment_statement_id, 'HtPayNotifyData' => $data, 'time' => date('Y-m-d H:i:s')], true));



        echo 'ok';
    }

    // 服务端通知
    public function notify(Request $request) {
        $data = $request->post();
        \Log::info(var_export(['HtPayNotifyData' => $data, 'time' => date('Y-m-d H:i:s')], true));

        echo 'ok';
    }
}
