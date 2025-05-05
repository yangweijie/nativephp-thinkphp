<?php

namespace native\thinkphp\event\PowerMonitor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;
use native\thinkphp\enums\PowerStatesEnum;

class PowerStateChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public PowerStatesEnum $state;

    public function __construct(string $state)
    {
        $this->state = PowerStatesEnum::from($state);
    }

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
