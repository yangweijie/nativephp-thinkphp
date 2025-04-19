<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool register(string $accelerator, callable $callback) 注册全局快捷键
 * @method static bool isRegistered(string $accelerator) 检查快捷键是否已注册
 * @method static bool unregister(string $accelerator) 注销指定的快捷键
 * @method static void unregisterAll() 注销所有快捷键
 * @method static array getShortcuts() 获取所有已注册的快捷键
 * 
 * @see \Native\ThinkPHP\GlobalShortcut
 */
class GlobalShortcut extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.global_shortcut';
    }
}
