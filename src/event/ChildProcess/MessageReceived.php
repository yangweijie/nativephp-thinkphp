<?php

namespace native\thinkphp\event\ChildProcess;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public string $alias, public mixed $data) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
