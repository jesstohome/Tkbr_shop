<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\User;
use Auth;
use App\Models\TicketReply;
use App\Mail\SupportMailManager;
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

    public function admin_index(Request $request)
    {
        $sort_search =null;
        $tickets = Ticket::orderBy('viewed')->orderBy('id', 'desc');
        if ($request->has('search')){
            $sort_search = $request->search;
            $tickets = $tickets->where('code', 'like', '%'.$sort_search.'%');
        }

        $group = $request->group;
        if (!is_null($group) && is_numeric($group)) {
            $tickets = $tickets->where('group', $group);
        }

        $tickets = filter_by_bloc($tickets);
        $tickets = $tickets->paginate(15);

        $groups = $this->groups;
        return view('backend.support.support_tickets.index', compact('tickets', 'sort_search', 'groups', 'group'));
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

        if($ticket_reply->save()){
            \Cache::set('loop_load_new_reply_audio_frontend', 1);

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

        $ticket_replies = $ticket->ticketreplies;
        TicketReply::query()->whereIn('id', $ticket_replies->where("read", 0)->pluck("id"))->update(['read' => 1]);

        return view('frontend.user.support_ticket.show', compact('ticket','ticket_replies'));
    }

    public function admin_show($id)
    {
        $ticket = Ticket::findOrFail(decrypt($id));
        $ticket->viewed = 1;
        $ticket->save();

        $ticket_replies = $ticket->ticketreplies;
        TicketReply::query()->whereIn('id', $ticket_replies->where("read", 0)->pluck("id"))->update(['read' => 1]);

        $in_chat_page = true;
        return view('backend.support.support_tickets.show', compact('ticket', 'in_chat_page'));
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
}
