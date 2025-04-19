<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string register(string $accelerator, callable|string $callback) 注册快捷键
 * @method static bool unregister(string $id) 注销快捷键
 * @method static void unregisterAll() 注销所有快捷键
 * @method static bool isRegistered(string $accelerator) 检查快捷键是否已注册
 * @method static array getRegisteredShortcuts() 获取所有已注册的快捷键
 * @method static string registerGlobal(string $accelerator, callable|string $callback) 注册全局快捷键
 * @method static bool unregisterGlobal(string $id) 注销全局快捷键
 * @method static void unregisterAllGlobal() 注销所有全局快捷键
 * @method static bool isGlobalRegistered(string $accelerator) 检查快捷键是否已注册为全局快捷键
 * @method static array getRegisteredGlobalShortcuts() 获取所有已注册的全局快捷键
 * @method static bool sendKey(string $key, array $modifiers = []) 模拟按键
 * @method static bool sendText(string $sequence) 模拟按键序列
 * @method static bool tapKey(string $key, array $modifiers = []) 模拟按下并松开按键
 * @method static bool keyDown(string $key, array $modifiers = []) 模拟按下按键
 * @method static bool keyUp(string $key, array $modifiers = []) 模拟松开按键
 * @method static string on(string $event, callable $callback) 监听键盘事件
 * @method static bool off(string $id) 移除键盘事件监听器
 * @method static array getLayout() 获取键盘布局
 * @method static bool setLayout(string $layout) 设置键盘布局
 *
 * @see \Native\ThinkPHP\Keyboard
 */
class Keyboard extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.keyboard';
    }
}
