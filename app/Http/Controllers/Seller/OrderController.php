<?php

namespace App\Http\Controllers\Seller;

use App\Models\Currency;
use App\Models\Order;
use App\Models\ProductStock;
use App\Models\SmsTemplate;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WalletExpenseLog;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use Auth;
use DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use function dd;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('id', 'desc')
            ->where('seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();
        $orders = $orders->where('created_at', '<=', date('Y-m-d H:i:s'));
        if ($request->product_storehouse_status != null) {
            $orders = $orders->where('product_storehouse_status', $request->product_storehouse_status);
            $product_storehouse_status = $request->product_storehouse_status;
        }
        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }
        return view('seller.orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search', 'product_storehouse_status'));
    }

    public function show( $id ) {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('city', $order_shipping_address->city)
            ->where('user_type', 'delivery_boy')
            ->get();

        $tpwd = Auth::user()->tpwd;
        $express = '';
        if ( $order->express_info )
        {
            $express = json_decode($order->express_info);
        }

        error_reporting(0);


        $order->viewed = 1;
        $order->save();

        $bloc = Auth::user()->bloc;
        $currency = Currency::query()->where('code', $bloc->currency_code)->first();
        $currency_name = $currency->name;

        return view('seller.orders.show', compact('order', 'delivery_boys', 'express', 'tpwd', 'bloc', 'currency_name'));
    }

    public function buy_package(Request $request)
    {

    }

    /**
     * 创建工单付款
     * author: Sym
     * time: 2023-05-19 11:15
     */
    public function createWorkOrderPayment(Request $request) {
        $order = Order::find($request->order_id);
        if (empty($order)) {
            error(translate('Order does not exist'));
            return back();
        }

        $product = $order->details[0]->product ?? [];

        $ticket = Ticket::query()->where('type', 'order')->where('order_id', $request->order_id)->first();
        if (empty($ticket)) {
            $ticket = new Ticket();
            $ticket->type = 'order';
            $ticket->bloc_id = $order->bloc_id ?: Auth::user()->bloc_id;
            $ticket->staff_id = $order->staff_id ?: Auth::user()->staff_id;
            $ticket->order_id = $order->id;
            $ticket->user_id = $order->seller_id;
            $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
            $ticket->subject = '';
            $ticket->viewed = 0;
            $ticket->status = 'pending';
            $ticket->details = '';
            $ticket->files = '';
            $ticket->save();

            hset_plus('new_work_order_ticket_tip', $ticket->id, 1, $ticket->staff_id);
        }

        $ticket_replies = $ticket->ticketreplies;
        foreach ($ticket_replies as $ticket_reply) {
            $ticket_reply->read = 1;
            $ticket_reply->save();
        }

        return view('seller.support_ticket.work_order_show', compact('order', 'ticket', 'ticket_replies', 'product'));
    }

    // 钱包余额支付货款
    public function paymentForStorehouseProductAmount(Request $request)
    {
        if (!$request->filled('order_id')) return response()->json(['success' => 0, 'message' => translate('Something went wrong!')]);

        $orderId = decrypt($request->order_id);
        $order = Order::findOrFail($orderId);
//        dd($order);
        if (!$order || $order->product_storehouse_total <= 0) return response()->json(['success' => 0, 'message' => translate('Something went wrong!')]);
        if ($order->product_storehouse_status == 1) return response()->json(['success' => 0, 'message' => translate('Payment completed')]);

        DB::beginTransaction();
        $shop = $order->shop;
        $user = $shop->user;

        if ($user->balance >= $order->product_storehouse_total) {
            $user->balance -= $order->product_storehouse_total;
            $user->save();

            // 记录钱包支出日志
            $walletExpenseLog = new WalletExpenseLog();
            $walletExpenseLog->user_id = $user->id;
            $walletExpenseLog->amount = $order->product_storehouse_total;
            $walletExpenseLog->type = 'pick up';
            $walletExpenseLog->save();

            storehouseProduct_payment_done($orderId, 'wallet');

            DB::commit();

            return response()->json(['success' => 1, 'message' => translate('Payment completed')]);
        }
        DB::rollBack();
        return response()->json(['success' => 0, 'type' => 'balance_insufficient', 'message' => translate('Insufficient balance')]);
    }

    // Update Delivery Status
    public function update_delivery_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if ($request->status == 'cancelled' && $order->payment_type == 'wallet' && $order->picking_switch==1) {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

//        if ($request->status == 'delivered') {
//            product_storehouse_order_free_up($order);
//        }

        foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
            $orderDetail->delivery_status = $request->status;
            $orderDetail->save();

            if ($request->status == 'cancelled') {
                $variant = $orderDetail->variation;
                if ($orderDetail->variation == null) {
                    $variant = '';
                }

                $product_stock = ProductStock::where('product_id', $orderDetail->product_id)
                    ->where('variant', $variant)
                    ->first();

                if ($product_stock != null) {
                    $product_stock->qty += $orderDetail->quantity;
                    $product_stock->save();
                }
            }
        }

        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            if (Auth::user()->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }

        return 1;
    }

    // Update Payment Status
    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
            $orderDetail->payment_status = $request->status;
            $orderDetail->save();
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            calculateCommissionAffilationClubPoint($order);
        }

        //sends Notifications to user
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }
        return 1;
    }

    /**
     * 未查看数量
     * author: Sym
     * time: 2023-04-09 11:56
     */
    public function get_not_view_count() {
        $orders = DB::table('orders')
            ->orderBy('id', 'desc')
            ->where('seller_id', Auth::user()->id)
            ->where('viewed', 0)
            ->select('orders.id')
            ->distinct();
        $orders = $orders->where('created_at', '<=', date('Y-m-d H:i:s'));

        $new_order_audio = hlen_plus("audio:new_order_tip") > 0;
        if ($new_order_audio) {
            del_plus("audio:new_order_tip");
        }

        return response()->json([
            'new_order_audio' => $new_order_audio,
            'result' => $orders->count(),
        ]);
    }
}
