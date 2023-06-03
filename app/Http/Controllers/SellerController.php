<?php

namespace App\Http\Controllers;

use App\Mail\EmailManager;
use App\Models\EmailTask;
use App\Models\PaymentRecord;
use App\Models\ShopManage;
use App\Models\Staff;
use App\Models\Ticket;
use App\Models\TicketReply;
use Auth;
use Illuminate\Http\Request;
use App\Models\Seller;
use App\Models\User;
use App\Models\Shop;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\CreditscoreStream;

use Illuminate\Support\Facades\Hash;
use App\Models\SellerPackagePayment;
use App\Models\SellerPackage;
use App\Notifications\EmailVerificationNotification;
use Cache;
use Illuminate\Support\Facades\Log;
use function compact;
use function dd;
use function view;

class SellerController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function setbalance(Request $request)
    {

        $user_id = $request->user_id;
        $bzj = $request->bzj;
        if( $bzj < 0 )
        {
             echo json_encode(['msg'=>translate("Money Must Biger Than 0 ")]);
             exit;
        }
          $user = User::findOrFail($user_id);
          $user->balance = $bzj;
          $user->save();
          echo json_encode(['msg'=>translate("Success")]);


    }
    public function setbzj(Request $request)
    {
        $shop_id = $request->shop_id;
        $bzj = $request->bzj;
        if( $bzj < 0 )
        {
             echo json_encode(['msg'=>translate("Guarantee Money Must Biger Than 0 ")]);
             exit;
        }
          $shop = shop::findOrFail($shop_id);
          $shop->bzj_money = $bzj;
          $shop->save();
          echo json_encode(['msg'=>translate("Success")]);

    }
    public function setpid(Request $request)
    {
        $shop_id = $request->shop_id;
        $pid = $request->pid;
        $shop = Shop::findOrFail($shop_id );
        $user_id = $shop['user_id'];
        $user = User::findOrFail( $user_id );
        $user->pid = $pid;
        $user->save();
         echo json_encode(['msg'=>translate("Success")]);
    }



    public function setpackage(Request $request)
    {
             $shop_id = $request->shop_id;
             $package_id = $request->packageid;

            $shop = Shop::findOrFail($shop_id );
            $shop->seller_package_id = $package_id;
            $seller_package = SellerPackage::findOrFail( $package_id );
            $shop->product_upload_limit = $seller_package->product_upload_limit;
            $shop->package_invalid_at = date('Y-m-d', strtotime($seller->package_invalid_at . ' +' . $seller_package->duration . 'days'));
            $res = $shop->save();


            $seller_package = new SellerPackagePayment;
            $seller_package->user_id = $shop->user_id;
            $seller_package->seller_package_id =  $package_id;
            $seller_package->payment_method = 'free';
            $seller_package->payment_details = '';
            $seller_package->approval = 1;
            $seller_package->offline_payment = 0;
            $seller_package->save();

             echo json_encode(['msg'=>translate("Success")]);

    }
    public function setviews(Request $request)
    {
        $shop_id = $request->shop_id;

        $shop = shop::findOrFail($shop_id);
        $shop->view_rand_range = $request->view_rand_range ?: '';
        $shop->save();
        echo json_encode(['msg'=>translate("Success")]);

    }

     /**
     * 修改卖家信用分
     * @param Request $request
     */
    public function updatecreditscore(Request $request)
    {

        //echo json_encode(['msg'=>translate("Success")]);
        $seller_id = $request->seller_id;
        $seller_score = $request->seller_score;
        $seller_remark = $request->seller_remark;
        $users = User::where('id','=',$seller_id)->first();
        //增加明细
        $creditscore_stream = new CreditscoreStream;
        $creditscore_stream['order_id'] = 0;
        $creditscore_stream['seller_id'] = $seller_id;
        $creditscore_stream['creditscore_before'] = $users['creditscore'];
        $creditscore_stream['creditscore_after'] = $users['creditscore'] + $seller_score;
        $creditscore_stream['remark'] = $seller_remark;

        $users->creditscore = $users['creditscore'] + $seller_score;
        $users->save();
        $creditscore_stream->save();

        echo json_encode(['msg'=>translate("Success")]);
    }

    public function index(Request $request)
    {
        $sort_search = null;
        $approved = null;
        $shops = Shop::whereIn('user_id', function ($query) {
                       $query->select('id')
                       ->from(with(new User)->getTable());
                    })->latest();

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $shops = $shops->where(function ($shops) use ($user_ids) {
                $shops->whereIn('user_id', $user_ids);
            });
        }
        if ($request->approved_status != null) {
            $approved = $request->approved_status;
            $shops = $shops->where('verification_status', $approved);
        }

        if ($request->date_range) {
            $date_range = $request->date_range;
            $date_var = explode(" - ", $request->date_range);
            $start_time = $date_var[0];
            $end_time = $date_var[1];
            $shops = $shops->where('created_at', '>=', $start_time);
            $shops = $shops->where('created_at', '<=', $end_time);
        }

        $shops = filter_by_bloc($shops);
        $shops = $shops->select("shops.*")->paginate(15);

        del_plus('new_shop_created_tip');

        return view('backend.sellers.index', compact('shops', 'sort_search', 'approved', 'date_range', 'start_time', 'end_time'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.sellers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (User::where('email', $request->email)->first() != null) {
            flash(translate('Email already exists!'))->error();
            return back();
        }
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->user_type = "seller";
        $user->password = Hash::make($request->password);

        if ($user->save()) {
            if (get_setting('email_verification') != 1) {
                $user->email_verified_at = date('Y-m-d H:m:s');
            } else {
                $user->notify(new EmailVerificationNotification());
            }
            $user->save();

            $seller = new Seller;
            $seller->user_id = $user->id;

            if ($seller->save()) {
                $shop = new Shop;
                $shop->user_id = $user->id;
                $shop->slug = 'demo-shop-' . $user->id;
                $shop->save();

                flash(translate('Seller has been inserted successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }
        flash(translate('Something went wrong'))->error();
        return back();
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
        $shop = Shop::findOrFail(decrypt($id));
        return view('backend.sellers.edit', compact('shop'));
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
        $shop = Shop::findOrFail($id);
        $user = $shop->user;
        $user->name = $request->name;
        $user->email = $request->email;
        $shop->views = (int)$request->views;
        if (strlen($request->password) > 0) {
            $user->password = Hash::make($request->password);
        }
        if (strlen($request->tpwd) > 0) {
            $user->tpwd = md5($request->tpwd);
        }
        if ($user->save()) {
            if ($shop->save()) {
                flash(translate('Seller has been updated successfully'))->success();
                return redirect()->route('sellers.index');
            }
        }

        flash(translate('Something went wrong'))->error();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);
        Product::where('user_id', $shop->user_id)->delete();
        $orders = Order::where('user_id', $shop->user_id)->get();

        foreach ($orders as $key => $order) {
            OrderDetail::where('order_id', $order->id)->delete();
        }
        Order::where('user_id', $shop->user_id)->delete();

        User::destroy($shop->user->id);

        if (Shop::destroy($id)) {
            flash(translate('Seller has been deleted successfully'))->success();
            return redirect()->route('sellers.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function bulk_seller_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $shop_id) {
                $this->destroy($shop_id);
            }
        }

        return 1;
    }

    public function show_verification_request($id)
    {
        $shop = Shop::findOrFail($id);
        return view('backend.sellers.verification', compact('shop'));
    }

    public function approve_seller($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->verification_status = 1;
        if ($shop->save()) {
            Cache::forget('verified_sellers_id');
            flash(translate('Seller has been approved successfully'))->success();

            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }

    public function reject_seller($id)
    {
        $shop = Shop::findOrFail($id);
        $shop->verification_status = 0;
        $shop->verification_info = null;
        if ($shop->save()) {
            Cache::forget('verified_sellers_id');
            flash(translate('Seller verification request has been rejected successfully'))->success();
            return redirect()->route('sellers.index');
        }
        flash(translate('Something went wrong'))->error();
        return back();
    }


    public function payment_modal(Request $request)
    {

        $shop = shop::findOrFail($request->id);
        $id = $request->id;
        return view('backend.sellers.payment_modal', compact('shop','id'));
    }

    public function profile_modal(Request $request)
    {
        $shop = Shop::findOrFail($request->id);
        return view('backend.sellers.profile_modal', compact('shop'));
    }

    public function updateApproved(Request $request)
    {
        $staff = Staff::find($request->admin_ids);
        $shop = Shop::findOrFail($request->id);
        $shop->verification_status = $request->status;
        $shop->bloc_id = $staff->bloc_id;
        $shop->staff_id = $staff->id;
        $shop->user->bloc_id = $staff->bloc_id;
        $shop->user->staff_id = $staff->id;
        if ($shop->save()) {
            hdel_plus('new_shop_created_tip', $shop->id);
            Cache::forget('verified_sellers_id');

            ShopManage::query()->where('shop_id', $shop->id)->delete();
            if (!empty($request->admin_ids)) {
                $sm = new ShopManage();
                $sm->shop_id = $shop->id;
                $sm->admin_id = $staff->user_id;
                $sm->save();
            }

            if ($request->status) {
                $array['view'] = 'emails.approve_seller';
                $array['subject'] = 'Shop Approve Notice';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['content'] = '';
                $array['seller_name'] = $shop->user->name;

                $task = new EmailTask();
                $task->email = $shop->user->email;
                $task->array = json_encode($array, JSON_UNESCAPED_UNICODE);
                $task->save();
            }

            $ticket = Ticket::where('user_id', $shop->user_id)->where("type", 'service')->first();
            if (!empty($ticket)) {
                $ticket->bloc_id = $staff->bloc_id;
                $ticket->staff_id = $staff->id;
                $ticket->save();
            }

            return 1;
        }
        return 0;
    }

    public function login($id)
    {
        $shop = Shop::findOrFail(decrypt($id));
        $user  = $shop->user;
        auth()->login($user, true);

        return redirect()->route('seller.dashboard');
    }

    public function ban($id)
    {
        $shop = Shop::findOrFail($id);

        if ($shop->user->banned == 1) {
            $shop->user->banned = 0;
            flash(translate('Seller has been unbanned successfully'))->success();
        } else {
            $shop->user->banned = 1;
            flash(translate('Seller has been banned successfully'))->success();
        }

        $shop->user->save();
        return back();
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesman_index(Request $request)
    {
        $pid = Auth::user()->id;
        $sort_search = null;
        $approved = null;
        $shops = Shop::whereIn('user_id', function ($query) {
            $query->select('id')->from(with(new User)->getTable());
        })->latest();


        $userIds = User::where('user_type', 'seller')->where(function ($user) use ($pid) {
            $user->where('pid', $pid);
        })->pluck('id')->toArray();
        $shops = $shops->where(function ($shops) use ($userIds) {
            $shops->whereIn('user_id', $userIds);
        });

        if ($request->has('search')) {
            $sort_search = $request->search;
            $user_ids = User::where('user_type', 'seller')->where(function ($user) use ($sort_search) {
                $user->where('name', 'like', '%' . $sort_search . '%')->orWhere('email', 'like', '%' . $sort_search . '%');
            })->pluck('id')->toArray();
            $shops = $shops->where(function ($shops) use ($user_ids) {
                $shops->whereIn('user_id', $user_ids);
            });
        }
//        $shops = $shops->where('verification_status', 1);
        $shops = $shops->paginate(15);
        return view('salesman.sellers.index', compact('shops', 'sort_search'));
    }

    public function salesman_profile_modal(Request $request)
    {
        $shop = Shop::findOrFail($request->id);
        return view('salesman.sellers.profile_modal', compact('shop'));
    }

    /**
     * author: Sym
     * time: 2023-04-05 11:22
     * @param Request $request
     */
    public function updateHomeDisplay(Request $request) {
        $shop = Shop::findOrFail($request->id);

        $shop->home_display = (int) !empty($request->get('status'));
        $shop->save();
        echo 1;
    }

    // 向厂家付款记录/提货付款记录
    public function payment_records(Request $request) {
        $list = PaymentRecord::query()->orderByDesc('id');
        $start_time = $request->get("start_time");
        $end_time = $request->get("end_time");
        $order_no = $request->get("order_no");
        $seller_id = $request->get("seller_id");
        $buyer_id = $request->get("buyer_id");
        $payment_code = $request->get("payment_code");
        $out_order_no = $request->get("out_order_no");
        $pay_status = $request->get("pay_status");

        if ($order_no) {
            $order = Order::query()->where("code", $order_no)->first();
            if ($order) {
                $list = $list->where('order_id', $order->id);
            } else {
                $list = $list->whereRaw('1=2');
            }
        }
        if ($start_time) {
            $list = $list->where("created_at", ">=", strtotime($start_time));
        }
        if ($end_time) {
            $list = $list->where("created_at", "<=", strtotime($end_time));
        }
        if ($seller_id) {
            $list = $list->where('seller_id', $seller_id);
        }
        if ($buyer_id) {
            $list = $list->where('buyer_id', $buyer_id);
        }
        if ($payment_code) {
            $list = $list->where('payment_code', $payment_code);
        }
        if ($out_order_no) {
            $list = $list->where('out_order_no', $out_order_no);
        }
        if ($pay_status != '') {
            $list = $list->where('pay_status', $pay_status);
        }

        $list = filter_by_bloc($list);

        $list_clone = clone $list;
        $total = $list_clone->count();
        $total_amount = $list_clone->sum('amount');
        $total_seller = $list_clone->distinct("seller_id")->count();

        $list = $list->paginate(20);

        del_plus("orders_pick_up_tip");

        return view('backend.sellers.payment_records', compact('list', 'start_date', 'end_date', 'seller_id', 'buyer_id', 'payment_code', 'out_order_no', 'pay_status', 'order_no', 'total', 'total_seller', 'total_amount'));
    }

    public function update_seller_staff (Request $request) {
        $seller = User::find($request->seller_id);
        if (empty($seller)) {
            return response()->json(['success' => 0, 'msg' => '卖家不存在']);
        }
        $staff = Staff::query()->where("id", $request->staff_id)->first();
        $seller->staff_id = $staff->id;
        $seller->bloc_id = $staff->bloc_id;
        $seller->save();
        $seller->shop->staff_id = $staff->id;
        $seller->shop->bloc_id = $staff->bloc_id;
        $seller->shop->save();

        return response()->json(['success' => 1, 'msg' => '保存成功']);
    }
}
