<?php


namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Controller;
use App\Models\CombinedOrder;
use App\Models\Currency;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentStatement;
use App\Models\SellerWithdrawRequest;
use App\Models\Shop;
use App\Models\ShopPaymentConfig;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;

class HtpayController extends Controller
{
    protected $payment_type = 'htpay';

    // 	代付货币 // 1:印度 2印尼 3 巴西 4哥伦比亚 5墨西哥 6土耳其 7巴基斯坦 8 尼日尼亚 9 秘鲁 10南非 11孟加拉 12 肯尼亚 13加纳 14厄瓜多尔 15USDT 16乌干达 17 俄罗斯 18 委内瑞拉 19 菲律宾 20玻利维亚
    protected $payment_currency = 2;

    public function pay(Request $request) {
        if(Session::has('payment_type')){
            if(Session::get('payment_type') == 'cart_payment'){
                $combined_order = CombinedOrder::findOrFail(Session::get('combined_order_id'));
                $amount = $combined_order->grand_total;
                $user = User::find($combined_order->user_id);
            } elseif (Session::get('payment_type') == 'order_pick_up_payment') {
                $order = Order::find(Session::get('payment_data')['id']);
                $user = User::find($order->user_id);
                $amount = $order->product_storehouse_total;

                $exchange_rate = $this->getExchangeRate();

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

                $shop = $order->shop;
            }
        }

        list($pay_memberid, $sign_key) = $this->getMchId();
        $pay_bankcode = $this->getPayBankCode();

        $request_arr = [
            "pay_memberid" => $pay_memberid,//商户id 商户后台获取
            "pay_orderid"  => $paymentStatement->order_no,//商户订单号自己生成
            "pay_amount"   => number_format($amount * $exchange_rate, 2, '.', ''),//支付金额
            "pay_applydate" => date("Y-m-d H:i:s"),//支付时间
            "pay_bankcode"  => $pay_bankcode,//后台获取
            "pay_notifyurl" => route($this->payment_type . '.notify'),//异步回调地址
            "pay_callbackurl" => route($this->payment_type . '.callback'),//同步回调地址（最后通知以异步回调为准）
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
                $paymentStatement->transaction_id = $res['data']['order_id'];
                $paymentStatement->out_order_no = $res['data']['order_id'];
                $paymentStatement->save();
                return \Redirect::to($res['data']['pay_url']);
            } else {
                \Log::warning(var_export(['HtPayResult' => $res], true));
                flash($res['msg'] ?? 'Failed')->warning();
                return back();
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
    public function daifu_pay($withdrawRequest) {
        $user = User::find($withdrawRequest->user_id);
        $shop = $user->shop;

        list($pay_memberid, $sign_key) = $this->getMchId();

        $currency = Currency::query()->where('code', $withdrawRequest->currency)->first();
        if (!empty($currency->exchange_rate)) {
            $exchange_rate = $currency->exchange_rate;
        } else {
            $exchange_rate = $this->getExchangeRate();
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

        // 取shop_payment_configs表
        $shop_payment_conf = ShopPaymentConfig::query()->where("shop_id", $shop->id)->where("country_code", $withdrawRequest->cur_select_country_code)->first();
        if (empty($shop_payment_conf) || (empty($shop_payment_conf['bank_account_no']) && empty($shop_payment_conf['e_wallet_address']))) {
            return flash('卖家的当前国家的银行配置不存在')->error();
        }

        $bank_num = $shop_payment_conf->bank_account_no;
        $bank_name = $shop_payment_conf->bank_name;
        $account_name = $shop_payment_conf->bank_account_name;

        // IFSC code印度必填，其他国家没有随便填写11位数字
        $ifsc = $shop_payment_conf->bank_var1 ?: '12345678910';

        $request_data = [
            'mchid' => $pay_memberid,//商户id 商户后台获取
            'out_trade_no' => $paymentStatement->order_no,// 商户订单号自己生成
            'money' => number_format($money,2,'.',''),//代付金额
            'ifsc' => $ifsc, // IFSC code印度必填，其他国家没有随便填写11位数字
            'bank_num' => $bank_num, //银行卡号
            'bank_name' => $bank_name, //银行名称
            'account_name' => $account_name, //银行卡账户名
            'customer_email' => $user->email, //用户邮箱
            'customer_mobile' => "91829732132", //用户手机号码格式要正确
            'notify_url' => route($this->payment_type . '.notify'), //异步回调地址不带参数
            'country_id' => $this->payment_currency, //1印度 2印尼
        ];
        ksort($request_data);
        //签名字符串
        $md5str = "";
        foreach ($request_data as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }

        $request_data['pay_md5sign'] = strtoupper(md5($md5str . "key=".$sign_key ));
        $req_url = "https://www.htpayio.com/Payment_Dfpay_add.html";
        $res = curlS($req_url, $request_data);
        $res = json_decode($res, true);
        if (isset($res['status']) && $res['status'] == "success") {
            $paymentStatement->transaction_id = $res['transaction_id'];
            $paymentStatement->out_order_no = $res['transaction_id'];
            $paymentStatement->save();
            // 提交成功
            flash(translate('Payment completed'))->success();
        }else{
            // 提交失败
            $paymentStatement->status = 2;
            $paymentStatement->failure_reason = $res['msg'] ?? '';
            $paymentStatement->save();
            flash($res['msg'] ?: translate('Payment Failed'))->error();
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
            if (!empty($data)) {
                if (!empty($data['transaction_id'])) {
                    $paymentStatement = PaymentStatement::query()->where('transaction_id', $data['transaction_id'])->where('payment_type', $this->payment_type)->first();
                } elseif (!empty($data['orderid'])) {
                    $paymentStatement = PaymentStatement::query()->where('out_order_no', $data['orderid'])->where('payment_type', $this->payment_type)->first();
                }
                if ($paymentStatement) {
                    // 代付的异步通知，以status作为交易是否成功的标识，付收的异步通知以returncode作为标识
                    if (isset($data['status'])) {
                        $paymentStatement->status = $data['status'] === 'success' ? 1 : 2;
                    } else {
                        $paymentStatement->status = $data['returncode'] === '00' ? 1 : 2;
                    }
                    $paymentStatement->save();
                    if ($paymentStatement->business_type == 'pick_up') {
                        storehouseProduct_payment_done($paymentStatement->target_id, $this->payment_type);
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
                        $payment->payment_method = $this->payment_type;
                        $payment->txn_code = $data['orderid'];
                        $payment->payment_details = $data;
                        $payment->t_type = $withdrawRequest->t_type;
                        $payment->save();

                        // 更新提现状态
                        $withdrawRequest->status = $data['returncode'] === '00' ? 1 : 4;
                        $withdrawRequest->save();
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::warning('htpay-notify-exception:' . $exception->getMessage());
        }

        echo 'ok';
    }

    /**
     * 获取商户信息
     * author: Sym
     * time: 2023-05-17 11:19
     * @return array
     */
    protected function getMchId() {
        return [env('HTPAY_MEMBERID'), env('HTPAY_SECRET')];
    }

    protected function getExchangeRate() {
        return env('HTPAY_EXCHANGE_RATE');
    }

    protected function getPayBankCode()
    {
        return env('HTPAY_BANK_CODE');
    }
}
