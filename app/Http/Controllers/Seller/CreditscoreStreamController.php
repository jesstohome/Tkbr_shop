<?php

namespace App\Http\Controllers\Seller;

use App\Models\CreditscoreStream;
use Auth;

class CreditscoreStreamController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $creditscorestreams = CreditscoreStream::where('seller_id', Auth::user()->id)->paginate(9);
        return view('seller.creditscore_streams_history', compact('creditscorestreams'));
    }
}
