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

class Message
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $msg;

    public function __construct(User $user,$message)
    {
        $this->user = $user;
        $this->msg = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('test');
    }
}
