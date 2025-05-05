<?php

namespace native\thinkphp\event\Windows;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WindowHidden implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $id)
    {
        //
    }

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
