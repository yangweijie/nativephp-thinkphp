<?php

namespace native\thinkphp\event\Menu;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MenuItemClicked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public array $item, public array $combo = []) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
