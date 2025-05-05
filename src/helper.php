<?php

use Illuminate\Broadcasting\PendingBroadcast;
use Illuminate\Contracts\Broadcasting\Factory as BroadcastFactory;

if (! function_exists('broadcast')) {
    /**
     * Begin broadcasting an event.
     *
     * @param  mixed|null  $event
     * @return PendingBroadcast
     */
    function broadcast($event = null): PendingBroadcast
    {
        return app(BroadcastFactory::class)->event($event);
    }
}