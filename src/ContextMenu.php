<?php

namespace native\thinkphp;


use native\thinkphp\client\Client;
use native\thinkphp\menu\Menu;

class ContextMenu
{
    public function __construct(protected Client $client) {}

    public function register(Menu $menu): void
    {
        $items = $menu->toArray()['submenu'];

        $this->client->post('context', [
            'entries' => $items,
        ]);
    }

    public function remove(): void
    {
        $this->client->delete('context');
    }
}
