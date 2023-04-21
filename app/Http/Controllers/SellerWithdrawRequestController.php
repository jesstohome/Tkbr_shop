<?php

namespace App\Http\Controllers;

use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use Auth;
use function compact;
use function count;
use function dd;
use function get_setting;
use function view;

class SellerWithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('t_type',1)->latest();
        $seller_withdraw_requests = filter_by_bloc($seller_withdraw_requests);
        $seller_withdraw_requests = $seller_withdraw_requests->paginate(15);

        return view('backend.sellers.seller_withdraw_requests.index', compact('seller_withdraw_requests'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index_by_user( Request $request)
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('user_id',$request->user_id )->get();

       # $seller_withdraw_requests =  SellerWithdrawRequest::latest()->paginate(15);
        return view('backend.sellers.seller_withdraw_requests_all_by_user.index', compact('seller_withdraw_requests'));
    }

    public function index_by_customer( Request $request)
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('t_type',2)->latest();
        $seller_withdraw_requests = filter_by_bloc($seller_withdraw_requests);
        $seller_withdraw_requests = $seller_withdraw_requests->paginate(15);


        # $seller_withdraw_requests =  SellerWithdrawRequest::latest()->paginate(15);
        return view('backend.sellers.seller_withdraw_requests_all_by_user.index2', compact('seller_withdraw_requests'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if ($request->amount > $user->balance) {
            flash(translate('You do not have enough balance to send withdraw request'))->error();
            return back();
        }
        $seller_withdraw_request = new SellerWithdrawRequest;
        $seller_withdraw_request->user_id = $user->id;
        $seller_withdraw_request->amount = $request->amount;
        $seller_withdraw_request->message = $request->message;
        $seller_withdraw_request->status = '0';
        $seller_withdraw_request->viewed = '0';
        if ($seller_withdraw_request->save()) {
            flash(translate('Request has been sent successfully'))->success();
            return redirect()->route('withdraw_requests.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function payment_modal(Request $request)
    {
        $user = User::findOrFail($request->id);

        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
         $user2 = $user;
        $seller_withdraw_request_id = $request->seller_withdraw_request_id;
        return view('backend.sellers.seller_withdraw_requests.payment_modal', compact('user','seller_withdraw_request','seller_withdraw_request_id','user2'));
    }
    public function refuse_modal(Request $request)
    {
        $user = User::findOrFail($request->id);
        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
        $id = $request->seller_withdraw_request_id;
        return view('backend.sellers.seller_withdraw_requests.refuse_modal', compact('user','seller_withdraw_request','id'));
    }

    public function message_modal(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::findOrFail($request->id);
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.partials.withdraw_message_modal', compact('seller_withdraw_request'));
        }
        elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.sellers.seller_withdraw_requests.withdraw_message_modal', compact('seller_withdraw_request'));
        }
        elseif (Auth::user()->user_type == 'salesman' ) {
            return view('salesman.seller_withdraw_requests.withdraw_message_modal', compact('seller_withdraw_request'));
        }
    }

    public function history_modal(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::findOrFail($request->id);
        $seller_id = $seller_withdraw_request->user_id;

        $size = 500;
        $orders = Order::orderBy('id', 'desc')->where('seller_id', $seller_id)->latest()->paginate($size);
        $wallets_recharge = Wallet::where('offline_payment', 1)->where("user_id", $seller_id)->latest()->paginate($size);
        $wallets_withdraw = SellerWithdrawRequest::where("user_id", $seller_id)->where('type', 1)->latest()->paginate($size);
//        $commission_records = CommissionHistory::where("seller_id", $seller_id)->latest()->paginate($size);

        $shops = [];
        //总商家数
        $users_1 = User::where('pid', '=', $seller_id)->where('user_type', '=', 'seller')->get();//下一级商家
        foreach ( $users_1 as $user_1 )
        {
            $users_2 = User::where('pid', '=', $user_1->id)->where('user_type', '=', 'seller')->get();//下二级商家
            //查询所有订单
            $orders_1 = $orders = Order::where('seller_id', $user_1->id)->get();
            $one = [
                'shop_name' => $user_1->shop->name,
                'order_number' => count($orders_1),
                'brokerage' =>0,
                'level' => '一级',
            ];
            foreach ( $orders_1 as $order_1 )
            {
                $one['brokerage'] += $order_1->grand_total*get_setting('commission_ratio_level_1')/100;
            }
            $shops[] = $one;

            foreach ( $users_2 as $user_2 )
            {
                $users_3 = User::where('pid', '=', $user_2->id)->where('user_type', '=', 'seller')->get();//下二级商家
                //查询所有订单
                $orders_2 = $orders = Order::where('seller_id', $user_2->id)->get();
                $two = [
                    'shop_name' => $user_2->shop->name,
                    'order_number' => count($orders_2),
                    'brokerage' =>0,
                    'level' => '二级',
                ];
                foreach ( $orders_2 as $order_2 )
                {
                    $two['brokerage'] += $order_2->grand_total*get_setting('commission_ratio_level_2')/100;
                }
                $shops[] = $two;


                foreach ( $users_3 as $user_3 )
                {
                    //查询所有订单
                    $orders_3 = $orders = Order::where('seller_id', $user_3->id)->get();
                    $three = [
                        'shop_name' => $user_3->shop->name,
                        'order_number' => count($orders_3),
                        'brokerage' =>0,
                        'level' => '三级',
                    ];
                    foreach ( $orders_3 as $order_3 )
                    {
                        $three['brokerage'] += $order_3->grand_total*get_setting('commission_ratio_level_3')/100;
                    }
                    $shops[] = $three;
                }
            }
        }

        return view('backend.sellers.seller_withdraw_requests.withdraw_history_modal', compact('orders', 'wallets_recharge', 'wallets_withdraw', 'commission_records', 'shops'));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function salesman_index()
    {
        $userIds = [];
        $users_1 = User::where('pid', '=', Auth::user()->id)->where('user_type', '=', 'seller')->get();//下一级商家
        foreach ( $users_1 as $user_1 )
        {
            $userIds[] = $user_1->id;
            $users_2 = User::where('pid', '=', $user_1->id)->where('user_type', '=', 'seller')->get();//下二级商家
            foreach ( $users_2 as $user_2 )
            {
                $userIds[] = $user_2->id;
                $users_3 = User::where('pid', '=', $user_2->id)->where('user_type', '=', 'seller')->get();//下二级商家
                foreach ( $users_3 as $user_3 )
                {
                    $userIds[] = $user_3->id;
                }
            }
        }
        $seller_withdraw_requests = SellerWithdrawRequest::latest()->whereIn('user_id', $userIds)->paginate(15);
        return view('salesman.seller_withdraw_requests.index', compact('seller_withdraw_requests'));
    }
}
