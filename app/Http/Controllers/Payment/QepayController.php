<?php


namespace App\Http\Controllers\Payment;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Seller\ProfileController;
use App\Models\CombinedOrder;
use App\Models\CustomerPackage;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentStatement;
use App\Models\SellerPackage;
use App\Models\SellerSpreadPackage;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use App\Models\Wallet;
use App\Utility\SignApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Session;

class QepayController extends Controller
{
    private $payment_type = 'qepay';

    public function pay(Request $request) {
        try {
            $exchange_rate = getExchangeRate();

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

            $trade_amount = number_format($amount, 2, '.', '') * $exchange_rate;
            $order_date = date('Y-m-d H:i:s');

            // 网银通道必填，其他类型一定不能填该参数
            $bank_code = '';
            if ($pay_type == 200) {
                /*$user = User::find($order->seller_id);
                $shop = $user->shop;

                // Name对应银行CODE
                $online_bank_names = ProfileController::$online_bank_names;
                $online_bank_names = array_flip($online_bank_names);
                $bank_code = isset($online_bank_names[$shop->online_bank_name]) ? $online_bank_names[$shop->online_bank_name] : $shop->online_bank_name;*/
            }


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

    /**
     * 代付，充值到钱包
     * author: Sym
     * time: 2023-05-04 14:48
     */
    public function daifu_pay ($withdrawRequest) {
        $mch_id = env('QEPAY_MCH_ID');
        $merchant_key = env('QEPAY_DAIFU_MCH_KEY');

        $exchange_rate = getExchangeRate();
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

        $user = User::find($withdrawRequest->user_id);
        $shop = $user->shop;

        // Name对应银行CODE
        $online_bank_names = ProfileController::$online_bank_names;
        $online_bank_names = array_flip($online_bank_names);
        $bank_code = isset($online_bank_names[$shop->online_bank_name]) ? $online_bank_names[$shop->online_bank_name] : $shop->online_bank_name;

        $apply_date = date('Y-m-d H:i:s');
        $mch_transferId = $paymentStatement->order_no;
        $receive_account = $shop->online_bank_no;
        $receive_name = $shop->online_bank_account_name;
        $transfer_amount = $money;
        $sign_type='MD5';

        $signStr = "";
        $signStr = $signStr."apply_date=".$apply_date."&";

        if($bank_code != ""){
            $signStr = $signStr . "bank_code=" . $bank_code . "&";
        }

        $signStr = $signStr."mch_id=".$mch_id."&";

        $signStr = $signStr."mch_transferId=".$mch_transferId."&";

        $signStr = $signStr."receive_account=".$receive_account."&";

        $signStr = $signStr."receive_name=".$receive_name."&";

        $signStr = $signStr."transfer_amount=".$transfer_amount;


        $reqUrl = "https://payment.qeapay.com/pay/transfer";

        $signAPI = new signapi();

        $sign = $signAPI->sign($signStr, $merchant_key);

        $postdata = array(
            'apply_date'=>$apply_date,
            'bank_code'=>$bank_code,
            'mch_id'=>$mch_id,
            'mch_transferId'=>$mch_transferId,
            'receive_account'=>$receive_account,
            'receive_name'=>$receive_name,
            'transfer_amount'=>$transfer_amount,
            'sign_type'=>$sign_type,
            'sign'=>$sign
        );

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
        curl_close($ch);

        \Log::debug(var_export(['daifu_pay_request_arr' => $postdata, 'res' => $response, $curl_info], true));
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
            flash($res['errorMsg'] ?: translate('Payment Failed'))->error();
        }
    }

    public function notify(Request $request) {
        $data = $request->post();
        \Log::info(var_export(['QepayNotifyData' => $data, 'time' => date('Y-m-d H:i:s')], true));

        try {
            if (!empty($data)) {
                // 正常回调带 mchId时，执行回调签名校验
                if (!empty($data["mchId"])) {
                    if (!$this->signValidate($data)) {
                        \Log::info(var_export(['Signature error', 'time' => date('Y-m-d H:i:s')], true));
                        exit('Signature error');
                    }
                }

                if ($data['oriAmount'] != $data['amount']) {
                    \Log::warning(var_export(['实际支付金额不准确' => $data], true));
                    exit('实际支付金额不准确');
                }

                $out_order_no = $data['orderNo'];
                if (!empty($out_order_no)) {
                    $paymentStatement = PaymentStatement::query()->where('out_order_no', $out_order_no)->where('payment_type', $this->payment_type)->first();
                }
                if ($paymentStatement) {
                    $paymentStatement->status = $data['tradeResult'] == 1 ? 1 : 2;
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
                        $payment->txn_code = $out_order_no;
                        $payment->payment_details = $data;
                        $payment->t_type = $withdrawRequest->t_type;
                        $payment->save();

                        // 更新提现状态
                        $withdrawRequest->status = $data['tradeResult'] == 1 ? 1 : 4;
                        $withdrawRequest->save();
                    }
                }

                exit('success');
            }
        } catch (\Exception $exception) {
            Log::warning('qepay-notify-exception:' . $exception->getMessage());
        }

        exit('error') ;
    }

    /**
     * 回调校验
     * author: Sym
     * time: 2023-05-07 19:39
     * @param $data
     * @return bool
     */
    private function signValidate($data) {
        $merchant_key = env('QEPAY_ZHIFU_MCH_KEY');

        $amount = $data["amount"];

        $mchId = $data["mchId"];

        $mchOrderNo = $data["mchOrderNo"];

        $merRetMsg = $data["merRetMsg"];

        $orderDate = $data["orderDate"];

        $orderNo = $data["orderNo"];

        $oriAmount = $data["oriAmount"];

        $tradeResult = $data["tradeResult"];

        $signType = $data["signType"];

        $sign = $data["sign"];


        $signStr = "";
        $signStr = $signStr."amount=".$amount."&";
        $signStr = $signStr."mchId=".$mchId."&";
        $signStr = $signStr."mchOrderNo=".$mchOrderNo."&";
        $signStr = $signStr."merRetMsg=".$merRetMsg."&";
        $signStr = $signStr."orderDate=".$orderDate."&";
        $signStr = $signStr."orderNo=".$orderNo."&";
        $signStr = $signStr."oriAmount=".$oriAmount."&";
        $signStr = $signStr."tradeResult=".$tradeResult;

        $signAPI = new SignApi();

        return $signAPI->validateSignByKey($signStr,$merchant_key,$sign);
    }
}
