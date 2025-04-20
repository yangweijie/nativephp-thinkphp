<?php

namespace NativePHP\Think;

use think\App;

class Native
{
    protected array $config;
    protected ?Bridge $bridge = null;
    protected ?WindowManager $windowManager = null;
    protected ?WindowPresets $windowPresets = null;
    protected ?WindowLayoutPresets $windowLayoutPresets = null;
    protected ?WindowState $windowState = null;
    protected ?WindowGroupStateManager $windowGroupStateManager = null;
    protected ?Menu $menu = null;
    protected ?Tray $tray = null;
    protected ?Hotkey $hotkey = null;
    protected ?Ipc $ipc = null;
    protected ?EventDispatcher $events = null;

    public function __construct(protected App $app)
    {
        // 在测试环境中，可能没有 config 对象
        if (method_exists($this->app, 'config') && $this->app->config) {
            $this->config = $this->app->config->get('native', []);
        } else {
            $this->config = [];
        }
    }

    public function getConfig(string $key, mixed $default = null): mixed
    {
        return array_get($this->config, $key, $default);
    }

    public function window(?string $label = null): Window
    {
        return $this->windowManager()->create($label);
    }

    public function windowManager(): WindowManager
    {
        if (!$this->windowManager) {
            $this->windowManager = new WindowManager($this);
        }
        return $this->windowManager;
    }

    public function windowPresets(): WindowPresets
    {
        if (!$this->windowPresets) {
            $this->windowPresets = new WindowPresets($this);
        }
        return $this->windowPresets;
    }

    public function windowLayoutPresets(): WindowLayoutPresets
    {
        if (!$this->windowLayoutPresets) {
            $this->windowLayoutPresets = new WindowLayoutPresets($this->windowManager());
        }
        return $this->windowLayoutPresets;
    }

    public function windowState(): WindowState
    {
        if (!$this->windowState) {
            $this->windowState = new WindowState($this->windowManager());
        }
        return $this->windowState;
    }

    /**
     * 获取窗口组状态管理器
     */
    public function windowGroupStateManager(): WindowGroupStateManager
    {
        if (!$this->windowGroupStateManager) {
            $this->windowGroupStateManager = new WindowGroupStateManager($this->windowManager());
        }
        return $this->windowGroupStateManager;
    }

    public function menu(): Menu
    {
        if (!$this->menu) {
            $this->menu = new Menu($this);
        }
        return $this->menu;
    }

    public function tray(): Tray
    {
        if (!$this->tray) {
            $this->tray = new Tray($this);
        }
        return $this->tray;
    }

    public function hotkey(): Hotkey
    {
        if (!$this->hotkey) {
            $this->hotkey = new Hotkey($this);
        }
        return $this->hotkey;
    }

    public function ipc(): Ipc
    {
        if (!$this->ipc) {
            $this->ipc = new Ipc($this);
        }
        return $this->ipc;
    }

    public function events(): EventDispatcher
    {
        if (!$this->events) {
            $this->events = new EventDispatcher($this);
        }
        return $this->events;
    }

    public function bridge(): Bridge
    {
        if (!$this->bridge) {
            $this->bridge = new Bridge($this);
        }
        return $this->bridge;
    }

    /**
     * 退出应用
     */
    public function exit(int $code = 0): void
    {
        $this->bridge()->emit('app:exit', ['code' => $code]);
    }
}