<?php

namespace native\thinkphp\event\Notifications;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationClicked implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
