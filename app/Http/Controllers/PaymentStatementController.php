<?php


namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentStatement;
use Illuminate\Http\Request;

/**
 * 付款对账单
 * Class PaymentStatementController
 * @package App\Http\Controllers
 */
class PaymentStatementController extends Controller
{
    public function index(Request $request) {
        $date_range = null;

        $payment_statements = PaymentStatement::orderBy('id', 'desc');
        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_range1 = explode(" / ", $request->date_range);
            $payment_statements = $payment_statements->where('created_at', '>=', $date_range1[0]);
            $payment_statements = $payment_statements->where('created_at', '<=', $date_range1[1] . " 23:59:59");
        }

        if ($request->order_code) {
            $order_code = $request->order_code;
            $order = Order::query()->where('code', $request->order_code)->first();
            if ($order) {
                $payment_statements = $payment_statements->where('target_id', $order->id)->where("business_type", 'pick_up');
            } else {
                $payment_statements = $payment_statements->whereRaw("1=2");
            }
        }
        if ($request->inner_order_code) {
            $inner_order_code = $request->inner_order_code;
            $payment_statements = $payment_statements->where('order_no', $request->inner_order_code);
        }
        if (!is_null($request->status) && is_numeric($request->status)) {
            $status = $request->status;
            $payment_statements = $payment_statements->where('status', $request->status);
        }
        if ($request->payment_type) {
            $payment_type = $request->payment_type;
            $payment_statements = $payment_statements->where('payment_type', $request->payment_type);
        }

        $seller_id = $request->seller_id;
        if (!empty($seller_id)) {
            $payment_statements = $payment_statements->where('seller_id', $seller_id);
        }

        $payment_statements = filter_by_bloc($payment_statements);

        // 统计
        $payment_statements_clone = clone $payment_statements;
        $total = $payment_statements_clone->count();
        $total_amount = $payment_statements_clone->sum('amount');
        $total_seller = $payment_statements_clone->distinct('seller_id')->count();

        $payment_statements = $payment_statements->paginate(15);
        return view('backend.reports.payment_statement', compact('payment_statements', 'date_range', 'total', 'total_seller', 'total_amount', 'order_code', 'inner_order_code', 'status', 'payment_type', 'seller_id'));
    }

    /**
     * 更新备注
     * author: Sym
     * time: 2023-05-14 12:59
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_remark(Request $request) {
        $payment_statement = PaymentStatement::find($request->id);
        $payment_statement->remark = $request->remark;
        if ($payment_statement->save()) {
            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0]);

    }

}
