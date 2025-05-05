<?php

namespace native\thinkphp\event\PowerMonitor;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use native\thinkphp\support\traits\Dispatchable;
use Illuminate\Queue\SerializesModels;
use native\thinkphp\enums\ThermalStatesEnum;

class ThermalStateChanged implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ThermalStatesEnum $state;

    public function __construct(string $state)
    {
        $this->state = ThermalStatesEnum::from($state);
    }

    public function broadcastOn()
    {
        return [
            new Channel('nativephp'),
        ];
    }
}
