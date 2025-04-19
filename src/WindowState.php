<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\WindowStateContract;

class WindowState implements WindowStateContract
{
    protected array $states = [];

    public function __construct(protected WindowManager $manager)
    {
    }

    public function save(string $label): array
    {
        $window = $this->manager->getWindow($label);
        $options = $window->getOptions();

        $state = [
            'width' => $options['width'] ?? null,
            'height' => $options['height'] ?? null,
            'x' => $options['x'] ?? null,
            'y' => $options['y'] ?? null,
            'maximized' => $options['maximized'] ?? false,
            'minimized' => $options['minimized'] ?? false,
            'fullscreen' => $options['fullscreen'] ?? false,
            'alwaysOnTop' => $options['alwaysOnTop'] ?? false,
            'visible' => $options['visible'] ?? true,
            'resizable' => $options['resizable'] ?? true,
            'decorations' => $options['decorations'] ?? true,
        ];

        $this->states[$label] = $state;
        return $state;
    }

    public function get(string $label): ?array
    {
        return $this->states[$label] ?? null;
    }

    public function restore(string $label, array $state): self
    {
        $window = $this->manager->getWindow($label);

        if (isset($state['width'])) {
            $window->width($state['width']);
        }
        if (isset($state['height'])) {
            $window->height($state['height']);
        }
        if (isset($state['x'])) {
            $window->x($state['x']);
        }
        if (isset($state['y'])) {
            $window->y($state['y']);
        }

        if ($state['maximized']) {
            $window->maximize();
        }
        if ($state['minimized']) {
            $window->minimize();
        }
        if ($state['fullscreen']) {
            $window->fullscreen();
        }

        $window->alwaysOnTop($state['alwaysOnTop'])
            ->resizable($state['resizable'])
            ->decorations($state['decorations']);

        if ($state['visible']) {
            $window->show();
        } else {
            $window->hide();
        }

        return $this;
    }

    public function clear(string $label): self
    {
        unset($this->states[$label]);
        return $this;
    }

    public function has(string $label): bool
    {
        return isset($this->states[$label]);
    }

    public function all(): array
    {
        return $this->states;
    }

    public function clearAll(): self
    {
        $this->states = [];
        return $this;
    }
}