<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\Contract\EventDispatcherContract;

/**
 * 模拟 EventDispatcher 类，用于测试
 */
class MockEventDispatcher implements EventDispatcherContract
{
    protected $listeners = [];

    public function __construct($native = null, $app = null)
    {
    }

    /**
     * 注册事件监听器
     */
    public function listen($event, $listener = null): self
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * 触发事件
     */
    public function dispatch($event, $params = null): void
    {
        // 处理通配符监听器
        if (isset($this->listeners['*'])) {
            foreach ($this->listeners['*'] as $listener) {
                call_user_func($listener, $params, $event);
            }
        }

        // 处理具体事件监听器
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                call_user_func($listener, $params);
            }
        }
    }

    /**
     * 移除事件的所有监听器
     */
    public function remove(string $event): void
    {
        unset($this->listeners[$event]);
    }

    /**
     * 移除事件监听器
     */
    public function removeListener(string $event, ?callable $listener = null): self
    {
        if ($listener === null) {
            // 移除所有监听器
            $this->remove($event);
        } else {
            // 移除特定监听器
            if (isset($this->listeners[$event])) {
                $this->listeners[$event] = array_filter(
                    $this->listeners[$event],
                    fn($l) => $l !== $listener
                );

                if (empty($this->listeners[$event])) {
                    unset($this->listeners[$event]);
                }
            }
        }

        return $this;
    }

    /**
     * 获取事件的所有监听器
     */
    public function getListeners(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }

    /**
     * 清除所有监听器
     */
    public function clear(): self
    {
        $this->listeners = [];
        return $this;
    }
}
