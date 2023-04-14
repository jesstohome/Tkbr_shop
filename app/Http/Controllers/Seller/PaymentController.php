<?php

namespace App\Http\Controllers\Seller;

use App\Models\Payment;
use App\Models\WalletExpenseLog;
use Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payments = Payment::where('seller_id', Auth::user()->id)->paginate(9);
        $walletExpenseList = WalletExpenseLog::orderBy('id', 'desc')->where('user_id',Auth::user()->id)->paginate(15);
        return view('seller.payment_history', compact('payments', 'walletExpenseList'));
    }
}
