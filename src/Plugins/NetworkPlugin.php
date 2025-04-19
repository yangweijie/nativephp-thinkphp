<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Network;
use Native\ThinkPHP\Facades\Logger;

class NetworkPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'network';

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
    protected $description = '网络管理插件';

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
     * 网络状态检查定时器 ID
     *
     * @var int|null
     */
    protected $statusCheckTimer = null;

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
        Logger::info('Network plugin initialized');

        // 监听网络事件
        $this->app->event->listen('native.network.status_change', function ($event) {
            $this->handleNetworkStatusChange($event);
        });

        $this->app->event->listen('native.network.online', function ($event) {
            $this->handleNetworkOnline($event);
        });

        $this->app->event->listen('native.network.offline', function ($event) {
            $this->handleNetworkOffline($event);
        });

        $this->app->event->listen('native.network.connection_type_change', function ($event) {
            $this->handleNetworkConnectionTypeChange($event);
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
        Logger::info('Network plugin started');

        // 初始化网络状态检查
        $this->initNetworkStatusCheck();

        // 记录网络信息
        $this->logNetworkInfo();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 停止网络状态检查
        $this->stopNetworkStatusCheck();

        // 记录插件卸载
        Logger::info('Network plugin quit');
    }

    /**
     * 初始化网络状态检查
     *
     * @return void
     */
    protected function initNetworkStatusCheck(): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了自动检查网络状态，则启动定时器
        if (isset($config['auto_check_status']) && $config['auto_check_status']) {
            // 获取检查间隔
            $interval = $config['check_interval'] ?? 60;

            // 启动定时器
            $this->startNetworkStatusCheck($interval);
        }
    }

    /**
     * 启动网络状态检查
     *
     * @param int $interval
     * @return void
     */
    protected function startNetworkStatusCheck(int $interval): void
    {
        // 如果定时器已经启动，先停止
        $this->stopNetworkStatusCheck();

        // 启动定时器
        $this->statusCheckTimer = swoole_timer_tick($interval * 1000, function () {
            // 检查网络状态
            /** @phpstan-ignore-next-line */
            Network::checkStatus();
        });

        // 立即检查一次网络状态
        /** @phpstan-ignore-next-line */
        Network::checkStatus();
    }

    /**
     * 停止网络状态检查
     *
     * @return void
     */
    protected function stopNetworkStatusCheck(): void
    {
        // 如果定时器已经启动，停止
        if ($this->statusCheckTimer !== null) {
            swoole_timer_clear($this->statusCheckTimer);
            $this->statusCheckTimer = null;
        }
    }

    /**
     * 记录网络信息
     *
     * @return void
     */
    protected function logNetworkInfo(): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了记录网络信息，则记录
        if (isset($config['log_network_info']) && $config['log_network_info']) {
            // 获取网络信息
            /** @phpstan-ignore-next-line */
            $networkInfo = Network::getNetworkInfo();

            // 记录网络信息
            Logger::info('Network information', $networkInfo);
        }
    }

    /**
     * 处理网络状态变化事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNetworkStatusChange(array $event): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了记录网络事件，则记录
        if (isset($config['log_network_events']) && $config['log_network_events']) {
            Logger::info('Network status change', [
                'status' => $event['status'] ?? null,
                'type' => $event['type'] ?? null,
                'oldStatus' => $event['oldStatus'] ?? null,
                'oldType' => $event['oldType'] ?? null,
            ]);
        }

        // 如果配置了网络状态变化回调，则执行
        if (isset($config['on_status_change']) && is_callable($config['on_status_change'])) {
            call_user_func($config['on_status_change'], $event);
        }
    }

    /**
     * 处理网络上线事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNetworkOnline(array $event): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了记录网络事件，则记录
        if (isset($config['log_network_events']) && $config['log_network_events']) {
            Logger::info('Network online', [
                'type' => $event['type'] ?? null,
            ]);
        }

        // 如果配置了网络上线回调，则执行
        if (isset($config['on_online']) && is_callable($config['on_online'])) {
            call_user_func($config['on_online'], $event);
        }
    }

    /**
     * 处理网络离线事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNetworkOffline(array $event): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了记录网络事件，则记录
        if (isset($config['log_network_events']) && $config['log_network_events']) {
            Logger::info('Network offline');
        }

        // 如果配置了网络离线回调，则执行
        if (isset($config['on_offline']) && is_callable($config['on_offline'])) {
            call_user_func($config['on_offline'], $event);
        }
    }

    /**
     * 处理网络连接类型变化事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNetworkConnectionTypeChange(array $event): void
    {
        // 获取配置
        $config = config('native.network', []);

        // 如果配置了记录网络事件，则记录
        if (isset($config['log_network_events']) && $config['log_network_events']) {
            Logger::info('Network connection type change', [
                'type' => $event['type'] ?? null,
                'oldType' => $event['oldType'] ?? null,
            ]);
        }

        // 如果配置了网络连接类型变化回调，则执行
        if (isset($config['on_connection_type_change']) && is_callable($config['on_connection_type_change'])) {
            call_user_func($config['on_connection_type_change'], $event);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 停止网络状态检查
        $this->stopNetworkStatusCheck();

        // 移除所有网络事件监听器
        Network::offAll();

        // 记录插件卸载
        Logger::info('Network plugin unloaded');
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
