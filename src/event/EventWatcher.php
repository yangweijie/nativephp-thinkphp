<?php

namespace native\thinkphp\event;

use Illuminate\Support\Facades\Event;
use native\thinkphp\client\Client;

class EventWatcher
{
    public function __construct(protected Client $client) {}

    public function register(): void
    {
        Event::listen('*', function (string $eventName, array $data) {
            $event = $data[0] ?? (object) null;

            if (! method_exists($event, 'broadcastOn')) {
                return;
            }

            $channels = $event->broadcastOn();

            // Only events dispatched on the nativephp channel
            if (! in_array('nativephp', $channels)) {
                return;
            }

            // Only post custom events to broadcasting endpoint
            if (str_starts_with($eventName, 'native\\thinkphp\\event')) {
                return;
            }

            $this->client->post('broadcast', [
                'event' => "\\{$eventName}",
                'payload' => $event,
            ]);
        });
    }
}
