<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\PowerMonitor as PowerMonitorFacade;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;
use think\facade\Cache;

class PowerMonitor extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取系统电源状态
        $powerState = PowerMonitorFacade::getPowerState();
        
        // 获取系统空闲时间
        $idleTime = PowerMonitorFacade::getSystemIdleTime();
        
        // 获取系统是否空闲
        $isIdle = PowerMonitorFacade::isSystemIdle(60);
        
        // 获取系统是否锁定
        $isLocked = PowerMonitorFacade::isSystemLocked();
        
        // 获取系统是否在屏幕保护状态
        $isOnScreenSaver = PowerMonitorFacade::isSystemOnScreenSaver();
        
        // 获取电池电量
        $batteryLevel = PowerMonitorFacade::getBatteryLevel();
        
        // 获取电池是否正在充电
        $isBatteryCharging = PowerMonitorFacade::isBatteryCharging();
        
        // 获取电池剩余时间
        $batteryTimeRemaining = PowerMonitorFacade::getBatteryTimeRemaining();
        
        // 获取系统是否处于低电量模式
        $isLowPowerMode = PowerMonitorFacade::isLowPowerMode();
        
        // 获取系统是否处于睡眠模式
        $isSleepMode = PowerMonitorFacade::isSleepMode();
        
        // 获取系统是否处于休眠模式
        $isHibernateMode = PowerMonitorFacade::isHibernateMode();
        
        // 获取系统电源信息
        $powerInfo = PowerMonitorFacade::getPowerInfo();
        
        // 获取系统电源设置
        $powerSettings = PowerMonitorFacade::getPowerSettings();
        
        // 获取电源事件日志
        $powerEvents = Cache::get('power_events', []);
        
        return View::fetch('power_monitor/index', [
            'powerState' => $powerState,
            'idleTime' => $idleTime,
            'isIdle' => $isIdle,
            'isLocked' => $isLocked,
            'isOnScreenSaver' => $isOnScreenSaver,
            'batteryLevel' => $batteryLevel,
            'isBatteryCharging' => $isBatteryCharging,
            'batteryTimeRemaining' => $batteryTimeRemaining,
            'isLowPowerMode' => $isLowPowerMode,
            'isSleepMode' => $isSleepMode,
            'isHibernateMode' => $isHibernateMode,
            'powerInfo' => $powerInfo,
            'powerSettings' => $powerSettings,
            'powerEvents' => $powerEvents,
        ]);
    }
    
    /**
     * 注册电源事件监听器
     *
     * @return \think\Response
     */
    public function registerEvents()
    {
        // 清空事件日志
        Cache::set('power_events', []);
        
        // 注册系统挂起事件
        PowerMonitorFacade::onSuspend(function ($event) {
            $this->logPowerEvent('suspend', '系统即将挂起');
        });
        
        // 注册系统恢复事件
        PowerMonitorFacade::onResume(function ($event) {
            $this->logPowerEvent('resume', '系统已恢复');
        });
        
        // 注册系统锁定事件
        PowerMonitorFacade::onLock(function ($event) {
            $this->logPowerEvent('lock', '系统已锁定');
        });
        
        // 注册系统解锁事件
        PowerMonitorFacade::onUnlock(function ($event) {
            $this->logPowerEvent('unlock', '系统已解锁');
        });
        
        // 注册系统电源状态变化事件
        PowerMonitorFacade::onPowerStateChange(function ($event) {
            $this->logPowerEvent('power-state-change', '系统电源状态变化: ' . $event['state']);
        });
        
        // 注册屏幕保护开始事件
        PowerMonitorFacade::onScreenSaverStart(function ($event) {
            $this->logPowerEvent('screen-saver-start', '屏幕保护已开始');
        });
        
        // 注册屏幕保护停止事件
        PowerMonitorFacade::onScreenSaverStop(function ($event) {
            $this->logPowerEvent('screen-saver-stop', '屏幕保护已停止');
        });
        
        // 注册电池电量变化事件
        PowerMonitorFacade::onBatteryLevelChange(function ($event) {
            $this->logPowerEvent('battery-level-change', '电池电量变化: ' . $event['level'] . '%');
        });
        
        // 注册电池充电状态变化事件
        PowerMonitorFacade::onBatteryChargingChange(function ($event) {
            $this->logPowerEvent('battery-charging-change', '电池充电状态变化: ' . ($event['charging'] ? '正在充电' : '未充电'));
        });
        
        // 注册系统空闲状态变化事件
        PowerMonitorFacade::onIdleStateChange(function ($event) {
            $this->logPowerEvent('idle-state-change', '系统空闲状态变化: ' . ($event['idle'] ? '空闲' : '活动'));
        }, 60);
        
        // 注册系统即将关机事件
        PowerMonitorFacade::onShutdown(function ($event) {
            $this->logPowerEvent('shutdown', '系统即将关机');
        });
        
        // 注册系统即将重启事件
        PowerMonitorFacade::onReboot(function ($event) {
            $this->logPowerEvent('reboot', '系统即将重启');
        });
        
        // 注册系统显示器添加事件
        PowerMonitorFacade::onDisplayAdded(function ($event) {
            $this->logPowerEvent('display-added', '显示器已添加');
        });
        
        // 注册系统显示器移除事件
        PowerMonitorFacade::onDisplayRemoved(function ($event) {
            $this->logPowerEvent('display-removed', '显示器已移除');
        });
        
        // 注册系统显示器布局变化事件
        PowerMonitorFacade::onDisplayLayoutChange(function ($event) {
            $this->logPowerEvent('display-layout-change', '显示器布局已变化');
        });
        
        return json(['success' => true, 'message' => '电源事件监听器已注册']);
    }
    
    /**
     * 取消电源事件监听器
     *
     * @return \think\Response
     */
    public function unregisterEvents()
    {
        // 取消所有事件监听器
        PowerMonitorFacade::offAll();
        
        // 清空事件日志
        Cache::set('power_events', []);
        
        return json(['success' => true, 'message' => '电源事件监听器已取消']);
    }
    
    /**
     * 获取电源事件日志
     *
     * @return \think\Response
     */
    public function getEvents()
    {
        $powerEvents = Cache::get('power_events', []);
        
        return json(['success' => true, 'events' => $powerEvents]);
    }
    
    /**
     * 清空电源事件日志
     *
     * @return \think\Response
     */
    public function clearEvents()
    {
        Cache::set('power_events', []);
        
        return json(['success' => true, 'message' => '电源事件日志已清空']);
    }
    
    /**
     * 记录电源事件
     *
     * @param string $type 事件类型
     * @param string $message 事件消息
     * @return void
     */
    protected function logPowerEvent($type, $message)
    {
        $powerEvents = Cache::get('power_events', []);
        
        $powerEvents[] = [
            'type' => $type,
            'message' => $message,
            'time' => date('Y-m-d H:i:s'),
        ];
        
        // 限制事件日志数量
        if (count($powerEvents) > 100) {
            $powerEvents = array_slice($powerEvents, -100);
        }
        
        Cache::set('power_events', $powerEvents);
        
        // 发送通知
        Notification::send('电源事件', $message);
    }
}
