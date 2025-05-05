<?php

namespace native\thinkphp\event\Windows;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WindowResized implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $id, public int $width, public int $height)
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
