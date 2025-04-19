<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\HotkeyContract;

class Hotkey implements HotkeyContract
{
    /**
     * @var array<string, callable>
     */
    protected array $hotkeys = [];

    public function __construct(protected Native $native) {}

    public function register(string $accelerator, callable $callback): self
    {
        if ($this->isRegistered($accelerator)) {
            $this->unregister($accelerator);
        }

        $this->hotkeys[$accelerator] = $callback;

        // 通过事件分发器触发快捷键注册事件
        $this->native->events()->dispatch('hotkey.registered', [
            'accelerator' => $accelerator
        ]);

        return $this;
    }

    public function unregister(string $accelerator): self
    {
        if ($this->isRegistered($accelerator)) {
            unset($this->hotkeys[$accelerator]);

            // 通过事件分发器触发快捷键注销事件
            $this->native->events()->dispatch('hotkey.unregistered', [
                'accelerator' => $accelerator
            ]);
        }

        return $this;
    }

    public function unregisterAll(): self
    {
        foreach (array_keys($this->hotkeys) as $accelerator) {
            $this->unregister($accelerator);
        }

        return $this;
    }

    public function getRegistered(): array
    {
        return array_keys($this->hotkeys);
    }

    public function isRegistered(string $accelerator): bool
    {
        return isset($this->hotkeys[$accelerator]);
    }

    /**
     * 触发快捷键回调
     */
    public function trigger(string $accelerator): void
    {
        if ($this->isRegistered($accelerator)) {
            call_user_func($this->hotkeys[$accelerator]);
        }
    }
}