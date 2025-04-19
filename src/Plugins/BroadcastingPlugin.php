<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\Broadcasting;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;

class BroadcastingPlugin implements Plugin
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [
        'app.start' => 'onAppStart',
        'app.quit' => 'onAppQuit',
    ];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Broadcasting plugin initialized');

        // 监听广播事件
        $this->app->event->listen('native.broadcasting.message', function ($event) {
            $this->handleBroadcastMessage($event);
        });

        $this->app->event->listen('native.broadcasting.channel_created', function ($event) {
            $this->handleChannelCreated($event);
        });

        $this->app->event->listen('native.broadcasting.channel_deleted', function ($event) {
            $this->handleChannelDeleted($event);
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
        Logger::info('Broadcasting plugin started');

        // 初始化频道
        $this->initializeChannels();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Broadcasting plugin quit');

        // 清理所有频道
        $this->cleanupChannels();
    }

    /**
     * 初始化频道
     *
     * @return void
     */
    protected function initializeChannels(): void
    {
        // 获取配置
        $config = config('native.broadcasting', []);

        // 如果配置了默认频道，则创建
        if (isset($config['default_channels']) && is_array($config['default_channels'])) {
            foreach ($config['default_channels'] as $channel) {
                Broadcasting::createChannel($channel);
            }
        }
    }

    /**
     * 清理所有频道
     *
     * @return void
     */
    protected function cleanupChannels(): void
    {
        // 获取所有频道
        $channels = Broadcasting::getChannels();

        // 清理所有频道
        foreach ($channels as $channel) {
            Broadcasting::clearChannel($channel);
        }
    }

    /**
     * 处理广播消息事件
     *
     * @param array $event
     * @return void
     */
    protected function handleBroadcastMessage(array $event): void
    {
        // 获取配置
        $config = config('native.broadcasting', []);

        // 如果配置了记录广播事件，则记录
        if (isset($config['log_broadcast_events']) && $config['log_broadcast_events']) {
            Logger::info('Broadcast message', [
                'channel' => $event['channel'] ?? null,
                'event' => $event['event'] ?? null,
                'data' => $event['data'] ?? null,
            ]);
        }

        // 如果配置了广播消息回调，则执行
        if (isset($config['on_broadcast_message']) && is_callable($config['on_broadcast_message'])) {
            call_user_func($config['on_broadcast_message'], $event);
        }
    }

    /**
     * 处理频道创建事件
     *
     * @param array $event
     * @return void
     */
    protected function handleChannelCreated(array $event): void
    {
        // 获取配置
        $config = config('native.broadcasting', []);

        // 如果配置了记录广播事件，则记录
        if (isset($config['log_broadcast_events']) && $config['log_broadcast_events']) {
            Logger::info('Channel created', [
                'channel' => $event['channel'] ?? null,
            ]);
        }

        // 如果配置了频道创建回调，则执行
        if (isset($config['on_channel_created']) && is_callable($config['on_channel_created'])) {
            call_user_func($config['on_channel_created'], $event);
        }
    }

    /**
     * 处理频道删除事件
     *
     * @param array $event
     * @return void
     */
    protected function handleChannelDeleted(array $event): void
    {
        // 获取配置
        $config = config('native.broadcasting', []);

        // 如果配置了记录广播事件，则记录
        if (isset($config['log_broadcast_events']) && $config['log_broadcast_events']) {
            Logger::info('Channel deleted', [
                'channel' => $event['channel'] ?? null,
            ]);
        }

        // 如果配置了频道删除回调，则执行
        if (isset($config['on_channel_deleted']) && is_callable($config['on_channel_deleted'])) {
            call_user_func($config['on_channel_deleted'], $event);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('Broadcasting plugin unloaded');
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        $hooks = [];
        foreach ($this->hooks as $hook => $method) {
            $hooks[$hook] = [$this, $method];
        }
        return $hooks;
    }
}
