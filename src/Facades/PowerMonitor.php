<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static int getSystemIdleTime() 获取系统空闲时间（秒）
 * @method static bool isSystemIdle(int $threshold = 60) 获取系统是否空闲
 * @method static bool isSystemLocked() 获取系统是否锁定
 * @method static bool isSystemOnScreenSaver() 获取系统是否在屏幕保护状态
 * @method static string getPowerState() 获取系统电源状态
 * @method static float getBatteryLevel() 获取电池电量
 * @method static bool isBatteryCharging() 获取电池是否正在充电
 * @method static int getBatteryTimeRemaining() 获取电池剩余时间（分钟）
 * @method static bool isLowPowerMode() 获取系统是否处于低电量模式
 * @method static bool isSleepMode() 获取系统是否处于睡眠模式
 * @method static bool isHibernateMode() 获取系统是否处于休眠模式
 * @method static array getPowerInfo() 获取系统电源信息
 * @method static array getPowerSettings() 获取系统电源设置
 * @method static string onSuspend(callable $callback) 监听系统挂起事件
 * @method static string onResume(callable $callback) 监听系统恢复事件
 * @method static string onLock(callable $callback) 监听系统锁定事件
 * @method static string onUnlock(callable $callback) 监听系统解锁事件
 * @method static string onPowerStateChange(callable $callback) 监听系统电源状态变化事件
 * @method static string onScreenSaverStart(callable $callback) 监听屏幕保护开始事件
 * @method static string onScreenSaverStop(callable $callback) 监听屏幕保护停止事件
 * @method static string onBatteryLevelChange(callable $callback) 监听电池电量变化事件
 * @method static string onBatteryChargingChange(callable $callback) 监听电池充电状态变化事件
 * @method static string onIdleStateChange(callable $callback, int $threshold = 60) 监听系统空闲状态变化事件
 * @method static string onShutdown(callable $callback) 监听系统即将关机事件
 * @method static string onReboot(callable $callback) 监听系统即将重启事件
 * @method static string onDisplayAdded(callable $callback) 监听系统显示器添加事件
 * @method static string onDisplayRemoved(callable $callback) 监听系统显示器移除事件
 * @method static string onDisplayLayoutChange(callable $callback) 监听系统显示器布局变化事件
 * @method static bool off(string $id) 移除事件监听器
 * @method static bool offAll() 移除所有事件监听器
 *
 * @see \Native\ThinkPHP\PowerMonitor
 */
class PowerMonitor extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.powerMonitor';
    }
}
