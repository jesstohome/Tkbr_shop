<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\BusinessSetting;
use App\Models\Message;
use App\Models\ProductQuery;
use Auth;
use Illuminate\Support\Facades\Redis;
use phpDocumentor\Fileset\Collection;
use function response;
use function strtotime;
use function translate;

class ConversationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (BusinessSetting::where('type', 'conversation_system')->first()->value == 1) {
            $conversations = Conversation::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(5);

            del_plus("new_conversation_tip:seller");
            del_plus("new_pos_conversation_tip:seller");
            return view('seller.conversations.index', compact('conversations'));
        } else {
            flash(translate('Conversation is disabled at this moment'))->warning();
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
        $conversation = Conversation::findOrFail(decrypt($id));
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->receiver_viewed = 1;
        }
        $conversation->save();
        return view('seller.conversations.show', compact('conversation'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        $conversation = Conversation::findOrFail(decrypt($request->id));
        foreach($conversation->messages as $message) {
            if( $message->user_id != Auth::user()->id ) {
                $message->updated_at = date('Y-m-d H:i:s');
                $message->save();
            }
        }
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->sender_viewed = 1;
            $conversation->save();
        } else {
            $conversation->receiver_viewed = 1;
            $conversation->save();
        }
        return view('frontend.partials.messages', compact('conversation'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function message_store(Request $request)
    {
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->receiver_viewed = "1";
        } elseif ($conversation->receiver_id == Auth::user()->id) {
            $conversation->sender_viewed = "1";
        }
        $conversation->admin_viewed = 0;
        $conversation->save();

        if ($conversation->add_by_admin) {
            hset_plus('new_pos_conversation_tip', $conversation->id, 1);
        } else {
            hset_plus('new_conversation_tip', $conversation->id, 1);
        }

        return back();
    }

    public function message_count(Request $request){
        $count = hlen_plus('new_conversation_tip:seller') || hlen_plus('new_pos_conversation_tip:seller');
        $ticket_count = Redis::get('loop_load_new_reply_audio_frontend');
        $product_review_tip = hlen_plus('new_review_tip:seller');
        $newAudio = hlen_plus('audio:new_conversation_tip:seller') || hlen_plus('audio:new_pos_conversation_tip:seller') || hlen_plus('audio:new_ticket_tip:seller') || hlen_plus('audio:new_review_tip:seller');
        if ($newAudio || $ticket_count) {
            del_plus('audio:new_conversation_tip:seller');
            del_plus('audio:new_pos_conversation_tip:seller');
            del_plus('audio:new_ticket_tip:seller');
            del_plus('audio:new_review_tip:seller');
            Redis::del('loop_load_new_reply_audio_frontend');
        }
        return response()->json([
            'result' => $count,
            'ticket_count' => $ticket_count,
            'product_review_tip' => $product_review_tip,
            'newAudio' => $newAudio || $ticket_count,
        ]);
    }

}
