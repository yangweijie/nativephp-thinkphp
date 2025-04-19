<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Logger;

class NotificationPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'notification';

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
    protected $description = '通知插件';

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
        Logger::info('Notification plugin initialized');

        // 监听通知事件
        $this->app->event->listen('native.notification.show', function ($event) {
            $this->handleNotificationShow($event);
        });

        $this->app->event->listen('native.notification.click', function ($event) {
            $this->handleNotificationClick($event);
        });

        $this->app->event->listen('native.notification.close', function ($event) {
            $this->handleNotificationClose($event);
        });

        $this->app->event->listen('native.notification.action', function ($event) {
            $this->handleNotificationAction($event);
        });

        $this->app->event->listen('native.notification.reply', function ($event) {
            $this->handleNotificationReply($event);
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
        Logger::info('Notification plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Notification plugin quit');
    }

    /**
     * 处理通知显示事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNotificationShow(array $event): void
    {
        // 记录通知显示
        $config = config('native.notification', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Notification shown', [
                'title' => $event['title'] ?? null,
                'body' => $event['body'] ?? null,
                'reference' => $event['reference'] ?? null,
            ]);
        }
    }

    /**
     * 处理通知点击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNotificationClick(array $event): void
    {
        // 记录通知点击
        $config = config('native.notification', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Notification clicked', [
                'reference' => $event['reference'] ?? null,
            ]);
        }
    }

    /**
     * 处理通知关闭事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNotificationClose(array $event): void
    {
        // 记录通知关闭
        $config = config('native.notification', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Notification closed', [
                'reference' => $event['reference'] ?? null,
            ]);
        }
    }

    /**
     * 处理通知操作事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNotificationAction(array $event): void
    {
        // 记录通知操作
        $config = config('native.notification', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Notification action', [
                'reference' => $event['reference'] ?? null,
                'action' => $event['action'] ?? null,
            ]);
        }
    }

    /**
     * 处理通知回复事件
     *
     * @param array $event
     * @return void
     */
    protected function handleNotificationReply(array $event): void
    {
        // 记录通知回复
        $config = config('native.notification', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Notification reply', [
                'reference' => $event['reference'] ?? null,
                'reply' => $event['reply'] ?? null,
            ]);
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
        Logger::info('Notification plugin unloaded');
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
