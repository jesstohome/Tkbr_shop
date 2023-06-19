<?php

namespace App\Http\Controllers;

use App\Models\Bloc;
use App\Models\Currency;
use App\Models\Order;
use App\Models\PaymentStatement;
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Auth;
use App\Models\TicketReply;
use App\Mail\SupportMailManager;
use Illuminate\Support\Facades\Redis;
use Mail;

class SupportTicketController extends Controller
{

    // 分组类型：0为空未分组，1已上架等订单，2无成交下单中，3有成交下单中，4追单中，5无效卖家
    private $groups = [];

    public function __construct()
    {
        $this->groups = [
            /*translate('No Group'),
            translate('Waiting orders'),
            translate('No transaction in progress'),
            translate('Has transaction in progress'),
            translate('In pursuit of orders'),
            translate('Invalid seller'),*/
            '未分组',
            '已上架等订单',
            '无成交下单中',
            '有成交下单中',
            '追单中',
            '无效卖家',
        ];

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tickets = Ticket::where('user_id', Auth::user()->id)->orderBy('viewed')->orderBy('created_at', 'desc')->paginate(9);
        return view('frontend.user.support_ticket.index', compact('tickets'));
    }

    public function admin_index_4_order(Request $request) {
        $request->type = 'order';

        del_plus('new_work_order_ticket_tip');

        return $this->admin_index($request);
    }

    /**
     * 后台的工单列表
     * author: Sym
     * time: 2023-05-19 13:43
     * @param Request $request
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|mixed
     */
    public function admin_index(Request $request)
    {
        $sort_search = null;
        $tickets = Ticket::orderBy('viewed')->orderBy('updated_at', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $tickets = $tickets->where('code', 'like', '%'.$sort_search.'%');
        }

        $group = $request->group;
        if (!is_null($group) && is_numeric($group)) {
            $tickets = $tickets->where('group', $group);
        }

        $seller_id = $request->seller_id;
        $order_no = $request->order_no;
        $pay_status = $request->pay_status;
        $created_at = $request->created_at;
        $updated_at = $request->updated_at;
        if (!empty($seller_id)) {
            $tickets = $tickets->where('user_id', $seller_id);
        }
        if (!empty($order_no)) {
            $tickets = $tickets->where('order_id', Order::query()->where('code', $order_no)->value('id'));
        }
        if (!empty($pay_status)) {
            $tickets = $tickets->whereIn('order_id', Order::query()->where('payment_status', $pay_status)->pluck('id')->toArray());
        }
        if (!empty($created_at)) {
            $created_times = explode(" to ", $created_at);
            $tickets = $tickets->where('created_at', '>=', $created_times[0]);
            $tickets = $tickets->where('created_at', '<=', $created_times[1]);
        }
        if (!empty($updated_at)) {
            $reply_time = explode(" to ", $updated_at);
            $tickets = $tickets->where('updated_at', '>=', $reply_time[0]);
            $tickets = $tickets->where('updated_at', '<=', $reply_time[1]);
        }

        $type = $request->type ? $request->type : 'service';
        $tickets = $tickets->where('type', $type);

        if ($type == 'service') {
            del_plus('new_ticket_tip');
        }

        $tickets = filter_by_bloc($tickets);
        $tickets = $tickets->paginate(15);

        $groups = $this->groups;

        $view = 'backend.support.support_tickets.index';
        if ($type == 'order') {
            $view = 'backend.support.support_tickets.index_4_order';
        }
        return view($view, compact('tickets', 'sort_search', 'groups', 'group', 'seller_id', 'order_no', 'pay_status', 'created_times', 'reply_time'));
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
        //dd();
        $ticket = new Ticket;
        $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
        $ticket->user_id = Auth::user()->id;
        $ticket->bloc_id = Auth::user()->bloc_id;
        $ticket->staff_id = get_staff_id();
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;
        $ticket->files = $request->attachments;
        $ticket->client_ip = get_ip();
        $ticket->ip_location = getCountryCityByIp($ticket->client_ip);

        if($ticket->save()){
            $this->send_support_mail_to_admin($ticket);
            flash(translate('Ticket has been sent successfully'))->success();
            return redirect()->route('support_ticket.index');
        }
        else{
            flash(translate('Something went wrong'))->error();
        }


    }

    public function send_support_mail_to_admin($ticket){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.admin_show', encrypt($ticket->id));
        $array['sender'] = $ticket->user->name;
        $array['details'] = $ticket->details;

        // dd($array);
        // dd(User::where('user_type', 'admin')->first()->email);
        try {
            Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            // dd($e->getMessage());
        }
    }

    public function send_support_reply_email_to_user($ticket, $tkt_reply){
        $array['view'] = 'emails.support';
        $array['subject'] = 'Support ticket Code is:- '.$ticket->code;
        $array['from'] = env('MAIL_FROM_ADDRESS');
        $array['content'] = 'Hi. A ticket has been created. Please check the ticket.';
        $array['link'] = route('support_ticket.show', encrypt($ticket->id));
        $array['sender'] = $tkt_reply->user->name;
        $array['details'] = $tkt_reply->reply;

        try {
            Mail::to($ticket->user->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            //dd($e->getMessage());
        }
    }

    public function admin_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = Auth::user()->id;
        $ticket_reply->reply = $request->reply ?: '';
        $ticket_reply->files = $request->attachments;
        $ticket_reply->ticket->client_viewed = 0;
        $ticket_reply->ticket->status = $request->status;
        $ticket_reply->ticket->save();

        if($ticket_reply->save()) {
            Redis::set('loop_load_new_reply_audio_frontend', 1);


            // 保存下，更新下最新时间
            $ticket = $ticket_reply->ticket;
            $ticket->updated_at = time();
            $ticket->save();

            if ($request->ajax()) {
                $list = appendTicketFiles([$ticket_reply]);
                return response()->json(['success' => 1, 'list' => $list]);
            }

            flash(translate('Reply has been sent successfully'))->success();
            return back();
        }
        else{
            if ($request->ajax()) {
                return response()->json(['success' => 0, 'msg' => translate('Something went wrong')]);
            }
            flash(translate('Something went wrong'))->error();
        }
    }

    public function seller_store(Request $request)
    {
        $ticket_reply = new TicketReply;
        $ticket_reply->ticket_id = $request->ticket_id;
        $ticket_reply->user_id = $request->user_id;
        $ticket_reply->reply = $request->reply;
        $ticket_reply->files = $request->attachments;
        $ticket_reply->ticket->viewed = 0;
        $ticket_reply->ticket->status = 'pending';
        $ticket_reply->ticket->save();
        if($ticket_reply->save()){

            flash(translate('Reply has been sent successfully'))->success();
            return back();
        }
        else{
            flash(translate('Something went wrong'))->error();
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
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->client_viewed = 1;
        $ticket->save();

        $ticket_replies = $ticket->ticketreplies->where('recall', 0);
        TicketReply::query()->whereIn('id', $ticket_replies->where("read", 0)->pluck("id"))->update(['read' => 1]);

        $view = 'backend.support.support_tickets.show';
        if ($ticket->order_id) {
            $view = 'backend.support.support_tickets.show_4_order';
        }
        return view($view, compact('ticket','ticket_replies'));
    }

    public function admin_show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->viewed = 1;
        $ticket->save();

        $ticket_replies = $ticket->ticketreplies->where('recall', 0);
        TicketReply::query()->whereIn('id', $ticket_replies->where("read", 0)->pluck("id"))->update(['read' => 1]);

        $order = $ticket->order;
        if ($order) {
            $currency = Currency::query()->where("code", $order->pickup_currency)->first();
        }

        $view = 'backend.support.support_tickets.show';
        if ($ticket->order_id) {
            $view = 'backend.support.support_tickets.show_4_order';
        }
        return view($view, compact('ticket', 'ticket_replies', 'currency'));
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

    public function load_new_reply(Request $request) {
        return load_new_reply($request);
    }

    public function change_group(Request $request) {
        $ticket = Ticket::find($request->post('id'));
        if ($ticket) {
            $ticket->group = (int) $request->post('group');
            $ticket->save();

            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0]);
    }

    public function update_tag_name(Request $request) {
        $ticket = Ticket::find($request->post('id'));
        if ($ticket) {
            $ticket->tag_name = $request->post('tag_name');
            $ticket->save();

            return response()->json(['success' => 1]);
        }

        return response()->json(['success' => 0]);
    }

    /**
     * 工单确认付款
     * author: Sym
     * time: 2023-05-20 13:29
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm_payment(Request $request) {
        $order = Order::find($request->id);
        if (empty($order)) {
            return response()->json(['success' => 0, 'msg' => '订单不存在']);
        }

        if ($order->product_storehouse_status) {
            return response()->json(['success' => 0, 'msg' => '该订单已提过货']);
        }

        $payment_type = 'work_order';
        $amount = $order->product_storehouse_total;

        \DB::beginTransaction();
        try {
            $exchange_rate = 0;
            $bloc = Bloc::find($order->shop->bloc_id);
            if (!empty($bloc->currency_code)) {
                $currency = Currency::query()->where('code', $bloc->currency_code)->first();
                if (!empty($currency)) {
                    $exchange_rate = $currency->exchange_rate;
                }
            }

            $paymentStatement = new PaymentStatement();
            $paymentStatement->bloc_id = $order->bloc_id;
            $paymentStatement->staff_id = $order->staff_id;
            $paymentStatement->seller_id = $order->seller_id;
            $paymentStatement->customer_id = $order->user_id;
            $paymentStatement->payment_type = $payment_type;
            $paymentStatement->order_no = date('YmdHis') . rand(10000, 99999);
            $paymentStatement->out_order_no = '';
            $paymentStatement->amount = $amount;
            $paymentStatement->amount_exchanged = $amount;
            $paymentStatement->exchange_rate = $exchange_rate;
            $paymentStatement->amount_exchanged = $amount * $exchange_rate;
            $paymentStatement->business_type = 'pick_up';
            $paymentStatement->target_id = $order->id;
            $paymentStatement->status = 1;
            $paymentStatement->save();
            \Session::put('payment_statement_id', $paymentStatement->id);

            // 客户提出需要往钱包收支明细加上此次提货记录
            $wallet = new Wallet();
            $wallet->payment_statement_id = $paymentStatement->id;
            $wallet->user_id = $order->seller_id;
            $wallet->amount = $amount;
            $wallet->payment_method = $payment_type;
            $wallet->payment_details = '';
            $wallet->approval = 1;
            $wallet->offline_payment = 1;
            $wallet->reciept = '';
            $wallet->type = 3;
            $wallet->target_id = $order->id ?? 0;
            $wallet->save();

            if (storehouseProduct_payment_done($order->id, 'work_order')) {
                \DB::commit();
                return response()->json(['success' => 1, 'msg' => '操作提货成功']);
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
        }

        return response()->json(['success' => 0, 'msg' => '失败']);
    }

    /**
     * 保存卖家的备注信息
     * author: Sym
     * time: 2023-05-31 14:52
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_remark(Request $request) {
        $seller = User::find($request->seller_id);
        if (empty($seller)) {
            return response()->json(['success' => 0, 'msg' => '卖家不存在']);
        }

        $seller->remark = $request->remark;
        $seller->save();
        return response()->json(['success' => 1, 'msg' => '保存成功']);
    }

    /**
     * 撤回消息
     * @param Request $request
     * @return int
     */
    public function remove_message(Request $request) {
        if ($request->id) {
            TicketReply::query()->where('id', $request->id)->update(['recall' => 1]);

            return 1;
        }

        return 0;
    }
}
