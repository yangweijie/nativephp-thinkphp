<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class PowerMonitor
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 事件监听器
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 获取系统空闲时间（秒）
     *
     * @return int
     */
    public function getSystemIdleTime()
    {
        $response = $this->client->get('power-monitor/idle-time');
        return (int) $response->json('idle_time');
    }

    /**
     * 获取系统是否空闲
     *
     * @param int $threshold 空闲阈值（秒）
     * @return bool
     */
    public function isSystemIdle($threshold = 60)
    {
        $idleTime = $this->getSystemIdleTime();
        return $idleTime >= $threshold;
    }

    /**
     * 获取系统是否锁定
     *
     * @return bool
     */
    public function isSystemLocked()
    {
        $response = $this->client->get('power-monitor/is-locked');
        return (bool) $response->json('locked');
    }

    /**
     * 获取系统是否在屏幕保护状态
     *
     * @return bool
     */
    public function isSystemOnScreenSaver()
    {
        $response = $this->client->get('power-monitor/is-on-screen-saver');
        return (bool) $response->json('on_screen_saver');
    }

    /**
     * 获取系统电源状态
     *
     * @return string 'on-ac', 'on-battery', 'charging', 'discharging', 'unknown'
     */
    public function getPowerState()
    {
        $response = $this->client->get('power-monitor/power-state');
        return $response->json('state') ?? 'unknown';
    }

    /**
     * 获取电池电量
     *
     * @return float 0.0 到 1.0 之间的值，表示电池电量百分比
     */
    public function getBatteryLevel()
    {
        $response = $this->client->get('power-monitor/battery-level');
        return (float) $response->json('level');
    }

    /**
     * 获取电池是否正在充电
     *
     * @return bool
     */
    public function isBatteryCharging()
    {
        $response = $this->client->get('power-monitor/is-battery-charging');
        return (bool) $response->json('charging');
    }

    /**
     * 获取电池剩余时间（分钟）
     *
     * @return int
     */
    public function getBatteryTimeRemaining()
    {
        $response = $this->client->get('power-monitor/battery-time-remaining');
        return (int) $response->json('time_remaining');
    }

    /**
     * 监听系统挂起事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onSuspend($callback)
    {
        $id = md5('suspend' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'suspend',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'suspend',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统恢复事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onResume($callback)
    {
        $id = md5('resume' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'resume',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'resume',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统锁定事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onLock($callback)
    {
        $id = md5('lock-screen' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'lock-screen',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'lock-screen',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统解锁事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onUnlock($callback)
    {
        $id = md5('unlock-screen' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'unlock-screen',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'unlock-screen',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统电源状态变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onPowerStateChange($callback)
    {
        $id = md5('power-state-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'power-state-change',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'power-state-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 移除事件监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function off($id)
    {
        if (!isset($this->listeners[$id])) {
            return false;
        }

        $event = $this->listeners[$id]['event'];

        $response = $this->client->post('power-monitor/off', [
            'event' => $event,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->listeners[$id]);
            return true;
        }

        return false;
    }

    /**
     * 监听屏幕保护开始事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onScreenSaverStart($callback)
    {
        $id = md5('screen-saver-start' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'screen-saver-start',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'screen-saver-start',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听屏幕保护停止事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onScreenSaverStop($callback)
    {
        $id = md5('screen-saver-stop' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'screen-saver-stop',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'screen-saver-stop',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听电池电量变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onBatteryLevelChange($callback)
    {
        $id = md5('battery-level-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'battery-level-change',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'battery-level-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听电池充电状态变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onBatteryChargingChange($callback)
    {
        $id = md5('battery-charging-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'battery-charging-change',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'battery-charging-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统空闲状态变化事件
     *
     * @param callable $callback
     * @param int $threshold 空闲阈值（秒）
     * @return string 监听器ID
     */
    public function onIdleStateChange($callback, $threshold = 60)
    {
        $id = md5('idle-state-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'idle-state-change',
            'callback' => $callback,
            'threshold' => $threshold,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'idle-state-change',
            'id' => $id,
            'threshold' => $threshold,
        ]);

        return $id;
    }

    /**
     * 监听系统即将关机事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onShutdown($callback)
    {
        $id = md5('shutdown' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'shutdown',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'shutdown',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统即将重启事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onReboot($callback)
    {
        $id = md5('reboot' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'reboot',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'reboot',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 获取系统是否处于低电量模式
     *
     * @return bool
     */
    public function isLowPowerMode()
    {
        $response = $this->client->get('power-monitor/is-low-power-mode');
        return (bool) $response->json('low_power_mode');
    }

    /**
     * 获取系统是否处于睡眠模式
     *
     * @return bool
     */
    public function isSleepMode()
    {
        $response = $this->client->get('power-monitor/is-sleep-mode');
        return (bool) $response->json('sleep_mode');
    }

    /**
     * 获取系统是否处于休眠模式
     *
     * @return bool
     */
    public function isHibernateMode()
    {
        $response = $this->client->get('power-monitor/is-hibernate-mode');
        return (bool) $response->json('hibernate_mode');
    }

    /**
     * 移除所有事件监听器
     *
     * @return bool
     */
    public function offAll()
    {
        $response = $this->client->post('power-monitor/off-all');
        $this->listeners = [];

        return (bool) $response->json('success');
    }

    /**
     * 监听系统显示器添加事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onDisplayAdded($callback)
    {
        $id = md5('display-added' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'display-added',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'display-added',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统显示器移除事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onDisplayRemoved($callback)
    {
        $id = md5('display-removed' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'display-removed',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'display-removed',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听系统显示器布局变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onDisplayLayoutChange($callback)
    {
        $id = md5('display-layout-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'display-layout-change',
            'callback' => $callback,
        ];

        $this->client->post('power-monitor/on', [
            'event' => 'display-layout-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 获取系统电源信息
     *
     * @return array
     */
    public function getPowerInfo()
    {
        $response = $this->client->get('power-monitor/power-info');
        return $response->json('info') ?? [];
    }

    /**
     * 获取系统电源设置
     *
     * @return array
     */
    public function getPowerSettings()
    {
        $response = $this->client->get('power-monitor/power-settings');
        return $response->json('settings') ?? [];
    }
}
