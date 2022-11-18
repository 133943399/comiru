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
use Illuminate\Support\Facades\Auth;

class NoticeEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $text;
    public $date;
    public $name;

    public function __construct($msg = '')
    {
        $this->user_id = 0;
        $this->text = ['text' => $msg];
        $this->date = date('Y-m-d H:i:m');
        $this->name = '公告通知';
    }

    public function broadcastOn()
    {
        return new Channel("notice");
    }
}
