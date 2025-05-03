<?php

namespace native\thinkphp\menu;


use JsonSerializable;

use native\thinkphp\contract\MenuItem;
use native\thinkphp\client\Client;
use native\thinkphp\support\traits\Conditionable;

class Menu implements JsonSerializable, MenuItem
{
    use Conditionable;

    protected array $items = [];

    protected string $label = '';

    public function __construct(protected Client $client) {}

    public function register(): void
    {
        $items = $this->toArray()['submenu'];

        $this->client->post('menu', [
            'items' => $items,
        ]);
    }

    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function add(MenuItem $item): self
    {
        $this->items[] = $item;

        return $this;
    }

    public function toArray(): array
    {
        $items = collect($this->items)
            ->map(fn (MenuItem $item) => $item->toArray())
            ->toArray();

        return [
            'label' => $this->label,
            'submenu' => $items,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
