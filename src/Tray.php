<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\TrayContract;

class Tray implements TrayContract
{
    protected array $config = [];
    protected ?Menu $trayMenu = null;
    protected bool $created = false;

    public function __construct(protected Native $native)
    {
        $this->config = $this->native->getConfig('tray', []);
    }

    public function icon(string $path): self
    {
        $this->config['icon_path'] = $path;
        $this->emitChange();
        return $this;
    }

    public function label(string $label): self
    {
        $this->config['label'] = $label;
        $this->emitChange();
        return $this;
    }

    public function tooltip(string $text): self
    {
        $this->config['tooltip'] = $text;
        $this->emitChange();
        return $this;
    }

    public function menu(callable $callback): self
    {
        if (!$this->trayMenu) {
            $this->trayMenu = new Menu($this->native);
        }

        $callback($this->trayMenu);
        $this->emitChange();
        return $this;
    }

    public function create(): self
    {
        if (!$this->created) {
            $this->created = true;
            $this->native->events()->dispatch('tray.created', $this->getConfig());
        }
        return $this;
    }

    public function destroy(): self
    {
        if ($this->created) {
            $this->created = false;
            $this->native->events()->dispatch('tray.destroyed');
        }
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
        $config = $this->config;
        
        if ($this->trayMenu) {
            $config['menu_items'] = $this->trayMenu->getConfig();
        }
        
        return $config;
    }

    protected function emitChange(): void
    {
        if ($this->created) {
            $this->native->events()->dispatch('tray.changed', $this->getConfig());
        }
    }
}