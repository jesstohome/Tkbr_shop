<?php


namespace App\Http\Controllers;

use App\Models\Bloc;
use App\Models\Currency;
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
            $date_range1 = explode("/", $request->date_range);
            $payment_statements = $payment_statements->where('created_at', '>=', trim($date_range1[0]));
            $payment_statements = $payment_statements->where('created_at', '<=', trim($date_range1[1]) . " 23:59:59");
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
        if ($request->business_type) {
            $business_type = $request->business_type;
            $payment_statements = $payment_statements->where('business_type', $business_type);
        }
        if ($request->min_price) {
            $min_price = $request->min_price;
            $payment_statements = $payment_statements->where('amount', '>=', $min_price);
        }
        if ($request->max_price) {
            $max_price = $request->max_price;
            $payment_statements = $payment_statements->where('amount', '<=', $max_price);
        }

        $seller_id = $request->seller_id;
        if (!empty($seller_id)) {
            $payment_statements = $payment_statements->where('seller_id', $seller_id);
        }

        $bloc = \Auth::user()->bloc;
        $bloc_id = $request->bloc_id;
        if (!empty($bloc_id)) {
            $bloc = Bloc::find($bloc_id);
            $payment_statements = $payment_statements->where('bloc_id', $bloc_id);
        }

        $staff_id = $request->staff_id;
        if (!empty($staff_id)) {
            $payment_statements = $payment_statements->where('staff_id', $staff_id);
        }

        if ($request->ids) {
            $payment_statements = $payment_statements->whereIn('id', explode(",", $request->ids));
        }

        $payment_statements = filter_by_bloc($payment_statements);

        // 统计
        $payment_statements_clone = clone $payment_statements;
        $total = $payment_statements_clone->count();
        $total_amount = $payment_statements_clone->sum('amount');
        $amount_4_currency = number_format($payment_statements_clone->sum('amount_exchanged'), 2);
        $total_seller = $payment_statements_clone->distinct('seller_id')->count();
        // 集团对应货币的金额总额
        $currency_name = '';
        if ($bloc) {
            $bloc_currency = Currency::query()->where('code', $bloc->currency_code)->first();
            $currency_name = $bloc_currency->name;
        }

        $perPage = $request->perPage ?: 15;
        $payment_statements = $payment_statements->paginate($perPage)->appends(request()->query());

        return view('backend.reports.payment_statement', compact('payment_statements', 'date_range', 'total', 'total_seller', 'total_amount', 'order_code', 'inner_order_code', 'status', 'payment_type', 'seller_id', 'business_type', 'bloc_id', 'staff_id', 'min_price', 'max_price', 'perPage', 'currency_name', 'amount_4_currency'));
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
