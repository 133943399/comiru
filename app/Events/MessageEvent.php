<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $text;
    public $date;
    public $name;

    public function __construct($user_id,$msg = '')
    {
        $this->user_id = $user_id;
        $this->text = ['text' => $msg];
        $this->date = date('Y-m-d H:i:m');
        $this->name = \Auth::user()->name;
    }

    public function broadcastOn()
    {
        return new Channel("user_".$this->user_id);
    }
}
