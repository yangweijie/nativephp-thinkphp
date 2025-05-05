<?php

namespace native\thinkphp\event\Settings;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SettingChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public string $key, public mixed $value) {}

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
