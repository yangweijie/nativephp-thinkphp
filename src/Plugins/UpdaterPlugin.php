<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Updater;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Facades\Notification;

class UpdaterPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'updater';

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
    protected $description = '自动更新插件';

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
        Logger::info('Updater plugin initialized');

        // 监听更新事件
        $this->app->event->listen('native.updater.check', function ($event) {
            $this->handleUpdateCheck($event);
        });

        $this->app->event->listen('native.updater.download', function ($event) {
            $this->handleUpdateDownload($event);
        });

        $this->app->event->listen('native.updater.install', function ($event) {
            $this->handleUpdateInstall($event);
        });

        $this->app->event->listen('native.updater.cancel', function ($event) {
            $this->handleUpdateCancel($event);
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
        Logger::info('Updater plugin started');

        // 设置更新服务器 URL
        $this->setupUpdater();

        // 检查是否需要在启动时检查更新
        $this->checkUpdateOnStartup();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Updater plugin quit');
    }

    /**
     * 设置更新器
     *
     * @return void
     */
    protected function setupUpdater(): void
    {
        // 获取配置
        $config = config('native.updater', []);

        // 设置更新服务器 URL
        if (isset($config['server_url'])) {
            Updater::setServerUrl($config['server_url']);
        }
    }

    /**
     * 在启动时检查更新
     *
     * @return void
     */
    protected function checkUpdateOnStartup(): void
    {
        // 获取配置
        $config = config('native.updater', []);

        // 检查是否需要在启动时检查更新
        if (isset($config['check_on_startup']) && $config['check_on_startup']) {
            // 延迟检查更新，避免影响应用启动速度
            $this->app->event->listen('app.ready', function () use ($config) {
                // 检查更新
                $updateInfo = Updater::check();

                // 如果有更新可用，显示通知
                if ($updateInfo && isset($updateInfo['canUpdate']) && $updateInfo['canUpdate']) {
                    // 显示更新通知
                    $this->showUpdateNotification($updateInfo);

                    // 如果配置了自动下载，则下载更新
                    if (isset($config['auto_download']) && $config['auto_download']) {
                        Updater::download($updateInfo['version']);
                    }
                }
            });
        }
    }

    /**
     * 显示更新通知
     *
     * @param array $updateInfo 更新信息
     * @return void
     */
    protected function showUpdateNotification(array $updateInfo): void
    {
        // 获取配置
        $config = config('native.updater', []);

        // 如果配置了不显示通知，则不显示
        if (isset($config['show_notification']) && !$config['show_notification']) {
            return;
        }

        // 显示通知
        /** @phpstan-ignore-next-line */
        Notification::title('有新版本可用')
            ->body("新版本 {$updateInfo['version']} 已发布，是否更新？")
            ->show();
    }

    /**
     * 处理更新检查事件
     *
     * @param array $event
     * @return void
     */
    protected function handleUpdateCheck(array $event): void
    {
        // 记录更新检查
        $config = config('native.updater', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Update check', [
                'server_url' => $event['server_url'] ?? null,
            ]);
        }
    }

    /**
     * 处理更新下载事件
     *
     * @param array $event
     * @return void
     */
    protected function handleUpdateDownload(array $event): void
    {
        // 记录更新下载
        $config = config('native.updater', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Update download', [
                'version' => $event['version'] ?? null,
            ]);
        }
    }

    /**
     * 处理更新安装事件
     *
     * @param array $event
     * @return void
     */
    protected function handleUpdateInstall(array $event): void
    {
        // 记录更新安装
        $config = config('native.updater', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Update install');
        }
    }

    /**
     * 处理更新取消事件
     *
     * @param array $event
     * @return void
     */
    protected function handleUpdateCancel(array $event): void
    {
        // 记录更新取消
        $config = config('native.updater', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Update cancel');
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
        Logger::info('Updater plugin unloaded');
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
