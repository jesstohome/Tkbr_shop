<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RedPointerTips implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * 要放置事件的队列的名称。
     *
     * @var string
     */
    public $broadcastQueue = 'default';

    public $data = [];

    public $received_user_id = 0;

    /**
     * Create a new event instance.
     * @param $data
     * @param int $received_user_id
     */
    public function __construct($data, $received_user_id = 0)
    {
        $this->data = $data;
        $this->received_user_id = $received_user_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('red-pointer.' . $this->received_user_id);
    }

    /**
     * 获取广播数据
     *
     * @return array
     */
    public function broadcastWith()
    {
        return ['user' => Auth()->user(), 'data' => $this->data];
    }
}
