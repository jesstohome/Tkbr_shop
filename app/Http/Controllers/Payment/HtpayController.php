<?php


namespace App\Http\Controllers\Payment;


use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentStatement;
use App\Models\SellerPackage;
use App\Models\SellerSpreadPackage;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

        $exchange_rate = $this->getExchangeRate();

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

    /**
     * 代付，充值到钱包
     * author: Sym
     * time: 2023-05-04 14:48
     */
    public function daifu_pay ($withdrawRequest) {
        $pay_memberid = env('HTPAY_MEMBERID');
        $sign_key = env('HTPAY_SECRET');

        $exchange_rate = $this->getExchangeRate();
        $money = $withdrawRequest->amount * $exchange_rate;

        $paymentStatement = new PaymentStatement();
        $paymentStatement->bloc_id = $withdrawRequest->bloc_id;
        $paymentStatement->staff_id = $withdrawRequest->staff_id;
        $paymentStatement->seller_id = $withdrawRequest->user_id;
        $paymentStatement->customer_id = 0;
        $paymentStatement->payment_type = 'htpay';
        $paymentStatement->order_no = date('YmdHis') . rand(10000, 99999);
        $paymentStatement->out_order_no = '';
        $paymentStatement->amount = $money;
        $paymentStatement->business_type = 'withdraw';
        $paymentStatement->target_id = $withdrawRequest->id;
        $paymentStatement->status = 0;
        $paymentStatement->save();

        $user = User::find($withdrawRequest->user_id);
        $shop = $user->shop;

        $request_data = [
            'mchid' => $pay_memberid,//商户id 商户后台获取
            'out_trade_no' => $paymentStatement->order_no,// 商户订单号自己生成
            'money' => number_format($money,2,'.',''),//代付金额
            'ifsc' => '12345678910', // IFSC code印度必填，其他国家没有随便填写11位数字
            'bank_num' => $shop->bank_acc_no ?: $user->bank_acc_no, //银行卡号
            'account_name' => $shop->bank_acc_name ?: $user->bank_acc_name, //银行卡账户名
            'customer_email' => $user->email, //用户邮箱
            'customer_mobile' => "", //用户手机号码格式要正确
            'notify_url' => route('htpay.notify'), //异步回调地址不带参数
            'bank_name' => $shop->bank_name ?: $user->bank_name, //银行名称
            'country_id' => "2" //1印度 2印尼
        ];
        ksort($request_data);
        //签名字符串
        $md5str = "";
        foreach ($request_data as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }

        $request_data['pay_md5sign'] = strtoupper(md5($md5str . "key=".$sign_key ));
        $req_url = "https://www.htpayio.com/Payment_Dfpay_add.html";
        if (env('APP_ENV') == 'local') {
            $req_url = "https://test.littleshopstudio.com/htpay-api-df";
        }
        $res = curlS($req_url, $request_data);
        if (isset($res['status']) && $res['status'] == "success") {
            // 提交成功
            flash(translate('Payment completed'))->success();
        }else{
            // 提交失败
            flash(translate('Payment Failed'))->error();
        }
    }

    // 页面跳转通知
    public function callback() {
        $data = $request->post();
        $payment_statement_id = Session::get('payment_statement_id');
        \Log::info(var_export(['payment_statement_id' => $payment_statement_id, $payment_statement_id, 'HtPayCallbackData' => $data, 'time' => date('Y-m-d H:i:s')], true));

        return redirect()->route('home');
    }

    // 服务端通知
    public function notify(Request $request) {
        $data = $request->post();
        \Log::info(var_export(['HtPayNotifyData' => $data, 'time' => date('Y-m-d H:i:s')], true));

        try {
            if (!empty($data) && $data['returncode'] === '00') {
                $paymentStatement = PaymentStatement::query()->where('out_order_no', $data['orderid'])->where('payment_type', 'htpay')->first();
                if ($paymentStatement) {
                    $paymentStatement->status = 1;
                    $paymentStatement->save();
                    if ($paymentStatement->business_type == 'pick_up') {
                        storehouseProduct_payment_done($paymentStatement->target_id, 'htpay');
                    } elseif ($paymentStatement->business_type == 'shopping') {
                        $combined_order_id = $paymentStatement->target_id;
                        $combined_order = CombinedOrder::findOrFail($combined_order_id);

                        foreach ($combined_order->orders as $key => $order) {
                            $order = Order::findOrFail($order->id);
                            $order->payment_status = 'paid';
                            $order->payment_details = $data;
                            $order->save();

                            hset_plus("new_order_tip", $order->id, 1, $order->staff_id);
                            calculateCommissionAffilationClubPoint($order);
                        }

                        Session::put('combined_order_id', $combined_order_id);
                    } elseif ($paymentStatement->business_type == 'withdraw') {
                        $withdrawRequest = SellerWithdrawRequest::find($paymentStatement->target_id);
                        $user = User::find($paymentStatement->user_id);
                        $payment = new Payment;
                        $payment->seller_id = $user->id;
                        $payment->bloc_id = $user->bloc_id;
                        $payment->staff_id = $user->staff_id;
                        $payment->amount = $data['amount'];
                        $payment->payment_method = 'htpay';
                        $payment->txn_code = $data['orderid'];
                        $payment->payment_details = $data;
                        $payment->t_type = $withdrawRequest->t_type;
                        $payment->save();
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::warning('htpay-notify-exception:' . $exception->getMessage());
        }

        echo 'ok';
    }

    private function getExchangeRate() {
        // 默认1美元对换14670印尼盾
        $exchange_rate = env('HTPAY_EXCHANGE_RATE', 14670);
        if ($exchange_rate <= 0) {
            $exchange_rate = 14670;
        }

        return $exchange_rate;
    }
}
