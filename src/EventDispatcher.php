<?php

namespace NativePHP\Think;

use think\Event;
use NativePHP\Think\Contract\EventDispatcherContract;

class EventDispatcher extends Event implements EventDispatcherContract
{
    /**
     * @var array<string, array<callable>>
     */
    protected array $nativeListeners = [];

    /**
     * @param Native|null $native Native instance
     * @param \think\App|null $app App instance (for testing)
     */
    public function __construct(protected ?Native $native = null, ?\think\App $app = null)
    {
        // In tests, we might not have access to the app() function
        // or we might not need a full app instance
        if ($app !== null) {
            parent::__construct($app);
        } else if (function_exists('\app')) {
            parent::__construct(\app());
        } else {
            // 在测试环境中，如果没有 App 实例，创建一个空的监听器数组
            $this->listener = [];
        }
    }

    public function listen(string $event, $listener, bool $first = false): self
    {
        $this->nativeListeners[$event][] = $listener;
        parent::listen($event, $listener);
        return $this;
    }

    public function dispatch($event, $params = null): void
    {
        $payload = $params;

        // 处理通配符监听器
        if (isset($this->nativeListeners['*'])) {
            foreach ($this->nativeListeners['*'] as $listener) {
                call_user_func($listener, $payload, $event);
            }
        }

        // 处理具体事件监听器
        if (isset($this->nativeListeners[$event])) {
            foreach ($this->nativeListeners[$event] as $listener) {
                call_user_func($listener, $payload);
            }
        }

        // 触发ThinkPHP事件系统
        try {
            parent::trigger($event, $payload);
        } catch (\Throwable $e) {
            // 在测试环境中，可能没有父类实例
        }
    }

    /**
     * 移除事件的所有监听器
     *
     * @param string $event 事件名称
     * @return void
     */
    public function remove(string $event): void
    {
        unset($this->nativeListeners[$event]);
        parent::remove($event);
    }

    /**
     * 移除事件监听器
     *
     * 此方法用于实现接口要求，同时保持与父类兼容
     *
     * @param string $event 事件名称
     * @param callable|null $listener 监听器
     * @return EventDispatcherContract
     */
    public function removeListener(string $event, ?callable $listener = null): EventDispatcherContract
    {
        if ($listener === null) {
            // Remove all listeners for this event
            $this->remove($event);
        } else {
            // Remove specific listener
            if (isset($this->nativeListeners[$event])) {
                $this->nativeListeners[$event] = array_filter(
                    $this->nativeListeners[$event],
                    fn($l) => $l !== $listener
                );

                if (empty($this->nativeListeners[$event])) {
                    unset($this->nativeListeners[$event]);
                }
            }
        }

        return $this;
    }

    public function getListeners(string $event): array
    {
        $nativeListeners = $this->nativeListeners[$event] ?? [];

        // 在测试环境中，可能没有父类实例
        try {
            $parentListeners = method_exists(parent::class, 'get') ? parent::get($event) : [];
            return array_merge($nativeListeners, $parentListeners);
        } catch (\Throwable $e) {
            return $nativeListeners;
        }
    }

    public function clear(): self
    {
        $this->nativeListeners = [];

        // 尝试调用父类的 clear 方法，如果存在
        try {
            if (method_exists(parent::class, 'clear')) {
                parent::clear();
            } else {
                // 如果父类没有 clear 方法，尝试清除父类的 listener 属性
                if (property_exists(parent::class, 'listener')) {
                    $this->listener = [];
                }
            }
        } catch (\Throwable $e) {
            // 在测试环境中，可能没有父类实例
        }

        return $this;
    }
}