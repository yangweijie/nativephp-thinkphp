<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Utils\Event on(string $event, callable $callback, int $priority = 0) 添加事件监听器
 * @method static \Native\ThinkPHP\Utils\Event once(string $event, callable $callback, int $priority = 0) 添加一次性事件监听器
 * @method static \Native\ThinkPHP\Utils\Event off(string $event, callable $callback = null) 移除事件监听器
 * @method static array emit(string $event, mixed ...$args) 触发事件
 * @method static int listenerCount(string|null $event = null) 获取事件监听器数量
 * @method static array eventNames() 获取事件列表
 * @method static array listeners(string $event) 获取事件监听器
 * @method static \Native\ThinkPHP\Utils\Event removeAllListeners() 移除所有事件监听器
 * 
 * @see \Native\ThinkPHP\Utils\Event
 */
class Event extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.event';
    }
}
