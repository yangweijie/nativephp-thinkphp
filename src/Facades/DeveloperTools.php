<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static void enable() 启用开发者工具
 * @method static void disable() 禁用开发者工具
 * @method static void openPerformanceMonitor() 打开性能监控窗口
 * @method static void openCrashReporter() 打开崩溃报告窗口
 * @method static void openDebugConsole() 打开调试控制台窗口
 * @method static \Native\ThinkPHP\DeveloperTools\PerformanceMonitor getPerformanceMonitor() 获取性能监控器
 * @method static \Native\ThinkPHP\DeveloperTools\CrashReporter getCrashReporter() 获取崩溃报告器
 *
 * @see \Native\ThinkPHP\DeveloperTools\DeveloperTools
 */
class DeveloperTools extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.developer_tools';
    }
}