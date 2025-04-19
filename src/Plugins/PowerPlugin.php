<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\PowerMonitor;
use Native\ThinkPHP\Facades\Logger;

class PowerPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'power';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '电源管理插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 事件监听器 ID 列表
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Power plugin initialized');

        // 监听电源事件
        $this->app->event->listen('native.power.suspend', function ($id) {
            $this->handleSuspend($id);
        });

        $this->app->event->listen('native.power.resume', function ($id) {
            $this->handleResume($id);
        });

        $this->app->event->listen('native.power.lock', function ($id) {
            $this->handleLock($id);
        });

        $this->app->event->listen('native.power.unlock', function ($id) {
            $this->handleUnlock($id);
        });

        $this->app->event->listen('native.power.state-change', function ($id, $state) {
            $this->handlePowerStateChange($id, $state);
        });

        $this->app->event->listen('native.power.battery-level-change', function ($id, $level) {
            $this->handleBatteryLevelChange($id, $level);
        });

        $this->app->event->listen('native.power.battery-charging-change', function ($id, $charging) {
            $this->handleBatteryChargingChange($id, $charging);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Power plugin started');

        // 注册电源事件监听器
        $this->registerPowerEventListeners();

        // 记录系统电源信息
        $this->logPowerInfo();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 移除所有电源事件监听器
        PowerMonitor::offAll();

        // 记录插件卸载
        Logger::info('Power plugin quit');
    }

    /**
     * 注册电源事件监听器
     *
     * @return void
     */
    protected function registerPowerEventListeners(): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 注册系统挂起事件监听器
        if (isset($config['listen_suspend']) && $config['listen_suspend']) {
            $this->listeners[] = PowerMonitor::onSuspend(function () {
                $this->app->event->trigger('native.power.suspend', ['id' => 'system']);
            });
        }

        // 注册系统恢复事件监听器
        if (isset($config['listen_resume']) && $config['listen_resume']) {
            $this->listeners[] = PowerMonitor::onResume(function () {
                $this->app->event->trigger('native.power.resume', ['id' => 'system']);
            });
        }

        // 注册系统锁定事件监听器
        if (isset($config['listen_lock']) && $config['listen_lock']) {
            $this->listeners[] = PowerMonitor::onLock(function () {
                $this->app->event->trigger('native.power.lock', ['id' => 'system']);
            });
        }

        // 注册系统解锁事件监听器
        if (isset($config['listen_unlock']) && $config['listen_unlock']) {
            $this->listeners[] = PowerMonitor::onUnlock(function () {
                $this->app->event->trigger('native.power.unlock', ['id' => 'system']);
            });
        }

        // 注册系统电源状态变化事件监听器
        if (isset($config['listen_power_state_change']) && $config['listen_power_state_change']) {
            $this->listeners[] = PowerMonitor::onPowerStateChange(function ($state) {
                $this->app->event->trigger('native.power.state-change', ['id' => 'system', 'state' => $state]);
            });
        }

        // 注册电池电量变化事件监听器
        if (isset($config['listen_battery_level_change']) && $config['listen_battery_level_change']) {
            $this->listeners[] = PowerMonitor::onBatteryLevelChange(function ($level) {
                $this->app->event->trigger('native.power.battery-level-change', ['id' => 'system', 'level' => $level]);
            });
        }

        // 注册电池充电状态变化事件监听器
        if (isset($config['listen_battery_charging_change']) && $config['listen_battery_charging_change']) {
            $this->listeners[] = PowerMonitor::onBatteryChargingChange(function ($charging) {
                $this->app->event->trigger('native.power.battery-charging-change', ['id' => 'system', 'charging' => $charging]);
            });
        }

        // 注册系统空闲状态变化事件监听器
        if (isset($config['listen_idle_state_change']) && $config['listen_idle_state_change']) {
            $threshold = $config['idle_threshold'] ?? 60;
            $this->listeners[] = PowerMonitor::onIdleStateChange(function ($idle) {
                $this->app->event->trigger('native.power.idle-state-change', ['id' => 'system', 'idle' => $idle]);
            }, $threshold);
        }
    }

    /**
     * 记录系统电源信息
     *
     * @return void
     */
    protected function logPowerInfo(): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源信息，则记录
        if (isset($config['log_power_info']) && $config['log_power_info']) {
            // 获取系统电源信息
            $powerState = PowerMonitor::getPowerState();
            $batteryLevel = PowerMonitor::getBatteryLevel();
            $batteryCharging = PowerMonitor::isBatteryCharging();
            $batteryTimeRemaining = PowerMonitor::getBatteryTimeRemaining();
            $lowPowerMode = PowerMonitor::isLowPowerMode();

            // 记录系统电源信息
            Logger::info('System power info', [
                'power_state' => $powerState,
                'battery_level' => $batteryLevel,
                'battery_charging' => $batteryCharging,
                'battery_time_remaining' => $batteryTimeRemaining,
                'low_power_mode' => $lowPowerMode,
            ]);
        }
    }

    /**
     * 处理系统挂起事件
     *
     * @param string $id
     * @return void
     */
    protected function handleSuspend(string $id): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('System suspend', [
                'id' => $id,
            ]);
        }

        // 如果配置了系统挂起回调，则执行
        if (isset($config['on_suspend']) && is_callable($config['on_suspend'])) {
            call_user_func($config['on_suspend']);
        }
    }

    /**
     * 处理系统恢复事件
     *
     * @param string $id
     * @return void
     */
    protected function handleResume(string $id): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('System resume', [
                'id' => $id,
            ]);
        }

        // 如果配置了系统恢复回调，则执行
        if (isset($config['on_resume']) && is_callable($config['on_resume'])) {
            call_user_func($config['on_resume']);
        }
    }

    /**
     * 处理系统锁定事件
     *
     * @param string $id
     * @return void
     */
    protected function handleLock(string $id): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('System lock', [
                'id' => $id,
            ]);
        }

        // 如果配置了系统锁定回调，则执行
        if (isset($config['on_lock']) && is_callable($config['on_lock'])) {
            call_user_func($config['on_lock']);
        }
    }

    /**
     * 处理系统解锁事件
     *
     * @param string $id
     * @return void
     */
    protected function handleUnlock(string $id): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('System unlock', [
                'id' => $id,
            ]);
        }

        // 如果配置了系统解锁回调，则执行
        if (isset($config['on_unlock']) && is_callable($config['on_unlock'])) {
            call_user_func($config['on_unlock']);
        }
    }

    /**
     * 处理系统电源状态变化事件
     *
     * @param string $id
     * @param string $state
     * @return void
     */
    protected function handlePowerStateChange(string $id, string $state): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('System power state change', [
                'id' => $id,
                'state' => $state,
            ]);
        }

        // 如果配置了系统电源状态变化回调，则执行
        if (isset($config['on_power_state_change']) && is_callable($config['on_power_state_change'])) {
            call_user_func($config['on_power_state_change'], $state);
        }
    }

    /**
     * 处理电池电量变化事件
     *
     * @param string $id
     * @param float $level
     * @return void
     */
    protected function handleBatteryLevelChange(string $id, float $level): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('Battery level change', [
                'id' => $id,
                'level' => $level,
            ]);
        }

        // 如果配置了电池电量变化回调，则执行
        if (isset($config['on_battery_level_change']) && is_callable($config['on_battery_level_change'])) {
            call_user_func($config['on_battery_level_change'], $level);
        }
    }

    /**
     * 处理电池充电状态变化事件
     *
     * @param string $id
     * @param bool $charging
     * @return void
     */
    protected function handleBatteryChargingChange(string $id, bool $charging): void
    {
        // 获取配置
        $config = config('native.power', []);

        // 如果配置了记录系统电源事件，则记录
        if (isset($config['log_power_events']) && $config['log_power_events']) {
            Logger::info('Battery charging change', [
                'id' => $id,
                'charging' => $charging,
            ]);
        }

        // 如果配置了电池充电状态变化回调，则执行
        if (isset($config['on_battery_charging_change']) && is_callable($config['on_battery_charging_change'])) {
            call_user_func($config['on_battery_charging_change'], $charging);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 移除所有电源事件监听器
        PowerMonitor::offAll();

        // 记录插件卸载
        Logger::info('Power plugin unloaded');
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}
