<?php

namespace native\thinkphp;


use native\thinkphp\client\Client;
use native\thinkphp\contract\GlobalShortcut as GlobalShortcutContract;

class GlobalShortcut implements GlobalShortcutContract
{
    protected string $key;

    protected string $event;

    public function __construct(protected Client $client) {}

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }

    public function register(): void
    {
        $this->client->post('global-shortcuts', [
            'key' => $this->key,
            'event' => $this->event,
        ]);
    }

    public function unregister(): void
    {
        $this->client->delete('global-shortcuts', [
            'key' => $this->key,
        ]);
    }
}
