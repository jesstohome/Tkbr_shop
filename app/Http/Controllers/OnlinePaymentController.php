<?php


namespace App\Http\Controllers;


use App\Models\Order;
use Illuminate\Http\Request;

class OnlinePaymentController extends Controller
{
    // 在线支付货款
    public function paymentForStorehouseProductAmount(Request $request) {
        $orderId = $request->get('order_id');
        $payment_code = $request->get('payment_code');
        $order = Order::findOrFail($orderId);

        if (!$order || $order->product_storehouse_total <= 0) {
            flash(translate('Something went wrong!'))->error();
            return back();
        }
        if ($order->product_storehouse_status == 1) {
            flash(translate('Payment completed'))->error();
            return back();
        }

        $request->session()->put('payment_type', 'order_pick_up_payment');
        $request->session()->put('payment_data', $order);

        $decorator = __NAMESPACE__ . '\\Payment\\' . str_replace(' ', '', ucwords(str_replace('_', ' ', $payment_code))) . "Controller";
        if (class_exists($decorator)) {
            return (new $decorator)->pay($request);
        }

        \Log::debug(var_export(['decorator' => $decorator, 'Unknown Payment'], true));
        flash(translate('Unknown Payment'))->error();
        return back();
    }

}
