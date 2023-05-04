<?php


namespace App\Http\Controllers;

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

        $payment_statements = $payment_statements->paginate(15);
        return view('backend.reports.payment_statement', compact('payment_statements', 'date_range'));
    }

}
