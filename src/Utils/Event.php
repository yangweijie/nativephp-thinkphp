<?php

namespace Native\ThinkPHP\Utils;

class Event
{
    /**
     * 事件监听器
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 一次性事件监听器
     *
     * @var array
     */
    protected $onceListeners = [];

    /**
     * 添加事件监听器
     *
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @return $this
     */
    public function on($event, callable $callback, $priority = 0)
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        // 按优先级排序
        $this->sortListeners($event);

        return $this;
    }

    /**
     * 添加一次性事件监听器
     *
     * @param string $event
     * @param callable $callback
     * @param int $priority
     * @return $this
     */
    public function once($event, callable $callback, $priority = 0)
    {
        if (!isset($this->onceListeners[$event])) {
            $this->onceListeners[$event] = [];
        }

        $this->onceListeners[$event][] = [
            'callback' => $callback,
            'priority' => $priority,
        ];

        // 按优先级排序
        $this->sortOnceListeners($event);

        return $this;
    }

    /**
     * 移除事件监听器
     *
     * @param string $event
     * @param callable|null $callback
     * @return $this
     */
    public function off($event, callable $callback = null)
    {
        // 如果没有指定回调函数，则移除所有监听器
        if ($callback === null) {
            unset($this->listeners[$event]);
            unset($this->onceListeners[$event]);
            return $this;
        }

        // 移除普通监听器
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $key => $listener) {
                if ($listener['callback'] === $callback) {
                    unset($this->listeners[$event][$key]);
                }
            }

            // 重新索引数组
            $this->listeners[$event] = array_values($this->listeners[$event]);

            // 如果没有监听器了，则移除事件
            if (empty($this->listeners[$event])) {
                unset($this->listeners[$event]);
            }
        }

        // 移除一次性监听器
        if (isset($this->onceListeners[$event])) {
            foreach ($this->onceListeners[$event] as $key => $listener) {
                if ($listener['callback'] === $callback) {
                    unset($this->onceListeners[$event][$key]);
                }
            }

            // 重新索引数组
            $this->onceListeners[$event] = array_values($this->onceListeners[$event]);

            // 如果没有监听器了，则移除事件
            if (empty($this->onceListeners[$event])) {
                unset($this->onceListeners[$event]);
            }
        }

        return $this;
    }

    /**
     * 触发事件
     *
     * @param string $event
     * @param mixed ...$args
     * @return array
     */
    public function emit($event, ...$args)
    {
        $results = [];

        // 触发普通监听器
        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $results[] = call_user_func_array($listener['callback'], $args);
            }
        }

        // 触发一次性监听器
        if (isset($this->onceListeners[$event])) {
            foreach ($this->onceListeners[$event] as $listener) {
                $results[] = call_user_func_array($listener['callback'], $args);
            }

            // 移除一次性监听器
            unset($this->onceListeners[$event]);
        }

        return $results;
    }

    /**
     * 触发事件对象
     *
     * @param object $event 事件对象
     * @return array
     */
    public function emitEvent($event)
    {
        /** @phpstan-ignore-next-line */
        if (!is_object($event)) {
            throw new \InvalidArgumentException('事件必须是一个对象');
        }

        // 获取事件类型
        $eventType = null;

        if ($event instanceof \Native\ThinkPHP\Events\WindowEvent) {
            $eventType = 'window.' . $event->type;
        } elseif ($event instanceof \Native\ThinkPHP\Events\AppEvent) {
            $eventType = 'app.' . $event->type;
        } elseif ($event instanceof \Native\ThinkPHP\Events\NotificationEvent) {
            $eventType = 'notification.' . $event->type;
        } elseif ($event instanceof \Native\ThinkPHP\Events\MenuEvent) {
            $eventType = 'menu.' . $event->type;
        } else {
            // 如果事件对象没有定义类型，则使用类名作为事件类型
            $eventType = get_class($event);
        }

        // 触发事件
        return $this->emit($eventType, $event);
    }

    /**
     * 获取事件监听器数量
     *
     * @param string|null $event
     * @return int
     */
    public function listenerCount($event = null)
    {
        if ($event === null) {
            $count = 0;

            foreach ($this->listeners as $eventListeners) {
                $count += count($eventListeners);
            }

            foreach ($this->onceListeners as $eventListeners) {
                $count += count($eventListeners);
            }

            return $count;
        }

        $count = 0;

        if (isset($this->listeners[$event])) {
            $count += count($this->listeners[$event]);
        }

        if (isset($this->onceListeners[$event])) {
            $count += count($this->onceListeners[$event]);
        }

        return $count;
    }

    /**
     * 获取事件列表
     *
     * @return array
     */
    public function eventNames()
    {
        return array_unique(array_merge(array_keys($this->listeners), array_keys($this->onceListeners)));
    }

    /**
     * 获取事件监听器
     *
     * @param string $event
     * @return array
     */
    public function listeners($event)
    {
        $listeners = [];

        if (isset($this->listeners[$event])) {
            foreach ($this->listeners[$event] as $listener) {
                $listeners[] = $listener['callback'];
            }
        }

        if (isset($this->onceListeners[$event])) {
            foreach ($this->onceListeners[$event] as $listener) {
                $listeners[] = $listener['callback'];
            }
        }

        return $listeners;
    }

    /**
     * 移除所有事件监听器
     *
     * @return $this
     */
    public function removeAllListeners()
    {
        $this->listeners = [];
        $this->onceListeners = [];

        return $this;
    }

    /**
     * 按优先级排序监听器
     *
     * @param string $event
     * @return void
     */
    protected function sortListeners($event)
    {
        if (!isset($this->listeners[$event])) {
            return;
        }

        usort($this->listeners[$event], function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }

    /**
     * 按优先级排序一次性监听器
     *
     * @param string $event
     * @return void
     */
    protected function sortOnceListeners($event)
    {
        if (!isset($this->onceListeners[$event])) {
            return;
        }

        usort($this->onceListeners[$event], function ($a, $b) {
            return $b['priority'] - $a['priority'];
        });
    }
}
