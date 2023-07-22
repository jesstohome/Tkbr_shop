<?php

namespace App\Http\Controllers;

use App\Events\RedPointerTips;
use Illuminate\Http\Request;
use App\Models\Message;
use Auth;
use Illuminate\Support\Facades\Redis;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $message = new Message;
        $message->conversation_id = $request->conversation_id;
        $message->user_id = Auth::user()->id;
        $message->message = $request->message;
        $message->save();
        $conversation = $message->conversation;
        if ($conversation->sender_id == Auth::user()->id) {
            $conversation->receiver_viewed ="1";
        }
        elseif($conversation->receiver_id == Auth::user()->id) {
            $conversation->sender_viewed ="1";
        }
        $conversation->is_tip = 0;
        $conversation->save();

        $product = $conversation->product;

        hset_plus('new_review_tip', $request->conversation_id, 1, $conversation->staff_id);
        hset_plus('new_pos_conversation_tip', $conversation->id, 1, $product->staff_id ?? $conversation->staff_id, $product->user_id ?? 0);
        if (!$conversation->add_by_admin) {
            // 卖家端红点
            hset_plus('new_conversation_tip', $conversation->id, 1, $product->staff_id ?? $conversation->staff_id, $product->user_id ?? 0);
        }

        broadcast(new RedPointerTips([
            'product_review_tip' => 1,
            'new_conversations' => 1,
            'newAudio' => 1,
        ], $product->user_id))->toOthers();

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
}
