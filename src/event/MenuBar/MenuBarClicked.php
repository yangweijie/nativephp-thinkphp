<?php

namespace native\thinkphp\event\MenuBar;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuBarClicked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $combo, public array $bounds, public array $position) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
