<?php

namespace native\thinkphp\event\App;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OpenedFromURL implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $url) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
