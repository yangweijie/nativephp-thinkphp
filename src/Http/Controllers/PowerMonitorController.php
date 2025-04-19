<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\PowerMonitor;

class PowerMonitorController
{
    /**
     * 获取系统空闲时间
     *
     * @return Response
     */
    public function getIdleTime()
    {
        $idleTime = PowerMonitor::getSystemIdleTime();
        
        return json([
            'idle_time' => $idleTime,
        ]);
    }
    
    /**
     * 获取系统是否锁定
     *
     * @return Response
     */
    public function isLocked()
    {
        $locked = PowerMonitor::isSystemLocked();
        
        return json([
            'locked' => $locked,
        ]);
    }
    
    /**
     * 获取系统是否在屏幕保护状态
     *
     * @return Response
     */
    public function isOnScreenSaver()
    {
        $onScreenSaver = PowerMonitor::isSystemOnScreenSaver();
        
        return json([
            'on_screen_saver' => $onScreenSaver,
        ]);
    }
    
    /**
     * 获取系统电源状态
     *
     * @return Response
     */
    public function getPowerState()
    {
        $state = PowerMonitor::getPowerState();
        
        return json([
            'state' => $state,
        ]);
    }
    
    /**
     * 获取电池电量
     *
     * @return Response
     */
    public function getBatteryLevel()
    {
        $level = PowerMonitor::getBatteryLevel();
        
        return json([
            'level' => $level,
        ]);
    }
    
    /**
     * 获取电池是否正在充电
     *
     * @return Response
     */
    public function isBatteryCharging()
    {
        $charging = PowerMonitor::isBatteryCharging();
        
        return json([
            'charging' => $charging,
        ]);
    }
    
    /**
     * 获取电池剩余时间
     *
     * @return Response
     */
    public function getBatteryTimeRemaining()
    {
        $timeRemaining = PowerMonitor::getBatteryTimeRemaining();
        
        return json([
            'time_remaining' => $timeRemaining,
        ]);
    }
    
    /**
     * 监听电源事件
     *
     * @param Request $request
     * @return Response
     */
    public function on(Request $request)
    {
        $event = $request->param('event');
        $id = $request->param('id');
        
        // 根据事件类型注册监听器
        switch ($event) {
            case 'suspend':
                PowerMonitor::onSuspend(function() use ($id) {
                    event('native.power.suspend', $id);
                });
                break;
            case 'resume':
                PowerMonitor::onResume(function() use ($id) {
                    event('native.power.resume', $id);
                });
                break;
            case 'lock-screen':
                PowerMonitor::onLock(function() use ($id) {
                    event('native.power.lock', $id);
                });
                break;
            case 'unlock-screen':
                PowerMonitor::onUnlock(function() use ($id) {
                    event('native.power.unlock', $id);
                });
                break;
            case 'power-state-change':
                PowerMonitor::onPowerStateChange(function($state) use ($id) {
                    event('native.power.state-change', [$id, $state]);
                });
                break;
            default:
                return json([
                    'success' => false,
                    'error' => 'Invalid event type',
                ]);
        }
        
        return json([
            'success' => true,
            'id' => $id,
        ]);
    }
    
    /**
     * 移除电源事件监听器
     *
     * @param Request $request
     * @return Response
     */
    public function off(Request $request)
    {
        $id = $request->param('id');
        
        // 移除监听器
        $success = PowerMonitor::off($id);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 移除所有电源事件监听器
     *
     * @return Response
     */
    public function offAll()
    {
        // 移除所有监听器
        PowerMonitor::offAll();
        
        return json([
            'success' => true,
        ]);
    }
}
