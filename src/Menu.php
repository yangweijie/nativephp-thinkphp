<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\MenuContract;

class Menu implements MenuContract
{
    protected array $items = [];
    protected array $config = [];
    protected array $disabledItems = [];

    public function __construct(protected Native $native) {}

    public function add(string $label, array $options = []): self
    {
        $this->items[] = array_merge([
            'type' => 'normal',
            'label' => $label,
        ], $options);

        $this->emitChange();
        return $this;
    }

    public function addSubmenu(string $label, callable $callback): self
    {
        $submenu = new self($this->native);
        $callback($submenu);

        $this->items[] = [
            'type' => 'submenu',
            'label' => $label,
            'submenu' => $submenu->items
        ];

        $this->emitChange();
        return $this;
    }

    public function addSeparator(): self
    {
        $this->items[] = ['type' => 'separator'];
        $this->emitChange();
        return $this;
    }

    public function configure(array $options): self
    {
        $this->config = array_merge($this->config, $options);
        $this->emitChange();
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function clear(): self
    {
        $this->items = [];
        $this->emitChange();
        return $this;
    }

    public function disable(string $label): self
    {
        $this->disabledItems[$label] = true;
        $this->updateItemState($label, true);
        return $this;
    }

    public function enable(string $label): self
    {
        unset($this->disabledItems[$label]);
        $this->updateItemState($label, false);
        return $this;
    }

    public function toggle(string $label): self
    {
        if (isset($this->disabledItems[$label])) {
            return $this->enable($label);
        }
        return $this->disable($label);
    }

    protected function updateItemState(string $label, bool $disabled): void
    {
        foreach ($this->items as &$item) {
            if ($item['label'] === $label) {
                $item['enabled'] = !$disabled;
            }
            if (isset($item['submenu'])) {
                foreach ($item['submenu'] as &$subItem) {
                    if ($subItem['label'] === $label) {
                        $subItem['enabled'] = !$disabled;
                    }
                }
            }
        }
        $this->emitChange();
    }

    protected function emitChange(): void
    {
        $this->native->events()->dispatch('menu.changed', [
            'items' => $this->items,
            'config' => $this->config
        ]);
    }
}