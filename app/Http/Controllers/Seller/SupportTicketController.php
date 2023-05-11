<?php

namespace App\Http\Controllers\Seller;

use App\Mail\SupportMailManager;
use App\Models\Upload;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Auth;
use Mail;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ticket = Ticket::where('user_id', Auth::user()->id)->first();
        if (empty($ticket)) {
            $ticket = new Ticket;
            $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
            $ticket->user_id = Auth::user()->id;
            $ticket->bloc_id = Auth::user()->bloc_id;
            $ticket->staff_id = get_staff_id();
            $ticket->subject = 'Tiktok Shop Serve';
            $ticket->viewed = 0;
            $ticket->status = 'pending';
            $ticket->details = '';
            $ticket->files = '';

            if($ticket->save()) {
                $ticket_reply = new TicketReply;
                $ticket_reply->ticket_id = $ticket->id;
                $ticket_reply->user_id = Auth::user()->id;
                $ticket_reply->reply = translate('Hello');
                $ticket_reply->files = '';
                $ticket_reply->save();

                return redirect()->route('seller.support_ticket.show', encrypt($ticket->id));
            } else{
                flash(translate('Something went wrong'))->error();
            }
        }

        return redirect()->route('seller.support_ticket.show', encrypt($ticket->id));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $ticket = new Ticket;
        $ticket->code = max(100000, (Ticket::latest()->first() != null ? Ticket::latest()->first()->code + 1 : 0)).date('s');
        $ticket->user_id = Auth::user()->id;
        $ticket->bloc_id = Auth::user()->bloc_id;
        $ticket->staff_id = get_staff_id();
        $ticket->subject = $request->subject;
        $ticket->details = $request->details;
        $ticket->files = $request->attachments;

        if($ticket->save()){
//            $this->send_support_mail_to_admin($ticket); // 工单不用邮件
            flash(translate('Ticket has been sent successfully'))->success();
            return redirect()->route('seller.support_ticket.index');
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

        try {
            Mail::to(User::where('user_type', 'admin')->first()->email)->queue(new SupportMailManager($array));
        } catch (\Exception $e) {
            // dd($e->getMessage());
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
        foreach ($ticket_replies as $ticket_reply) {
            $ticket_reply->read = 1;
            $ticket_reply->save();
        }
        return view('seller.support_ticket.show', compact('ticket','ticket_replies'));
    }

    public function ticket_reply_store(Request $request)
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

    public function load_new_reply(Request $request) {
        return load_new_reply($request);
    }
}
