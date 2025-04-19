<?php

namespace Native\ThinkPHP\Plugins;

use think\App as ThinkApp;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Logger;

class AppPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'app';

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
    protected $description = '应用程序管理插件';

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
    public function __construct(ThinkApp $app, array $config = [])
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
        Logger::info('App plugin initialized');

        // 监听应用事件
        $this->app->event->listen('native.app.quit', function () {
            $this->handleAppQuit();
        });

        $this->app->event->listen('native.app.restart', function () {
            $this->handleAppRestart();
        });

        $this->app->event->listen('native.app.focus', function () {
            $this->handleAppFocus();
        });

        $this->app->event->listen('native.app.hide', function () {
            $this->handleAppHide();
        });

        $this->app->event->listen('native.app.badge_count', function ($count) {
            $this->handleAppBadgeCount($count);
        });

        $this->app->event->listen('native.app.add_recent_document', function ($path) {
            $this->handleAppAddRecentDocument($path);
        });

        $this->app->event->listen('native.app.clear_recent_documents', function () {
            $this->handleAppClearRecentDocuments();
        });

        $this->app->event->listen('native.app.open_at_login', function ($open) {
            $this->handleAppOpenAtLogin($open);
        });

        $this->app->event->listen('native.app.minimize', function () {
            $this->handleAppMinimize();
        });

        $this->app->event->listen('native.app.maximize', function () {
            $this->handleAppMaximize();
        });

        $this->app->event->listen('native.app.restore', function () {
            $this->handleAppRestore();
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
        Logger::info('App plugin started');

        // 记录应用信息
        $this->logAppInfo();

        // 设置应用徽章计数
        $this->setInitialBadgeCount();

        // 设置应用开机自启动
        $this->setInitialOpenAtLogin();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('App plugin quit');
    }

    /**
     * 记录应用信息
     *
     * @return void
     */
    protected function logAppInfo(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用信息，则记录
        if (isset($config['log_app_info']) && $config['log_app_info']) {
            // 获取应用信息
            $name = App::name();
            $id = App::id();
            $version = App::version();
            $rootPath = App::getRootPath();
            $appPath = App::getAppPath();
            $publicPath = App::getPublicPath();
            $runtimePath = App::getRuntimePath();
            $isRunningBundled = App::isRunningBundled();
            $openAtLogin = App::openAtLogin();

            // 记录应用信息
            Logger::info('App information', [
                'name' => $name,
                'id' => $id,
                'version' => $version,
                'root_path' => $rootPath,
                'app_path' => $appPath,
                'public_path' => $publicPath,
                'runtime_path' => $runtimePath,
                'is_running_bundled' => $isRunningBundled,
                'open_at_login' => $openAtLogin,
            ]);
        }
    }

    /**
     * 设置初始徽章计数
     *
     * @return void
     */
    protected function setInitialBadgeCount(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了初始徽章计数，则设置
        if (isset($config['initial_badge_count'])) {
            App::badgeCount($config['initial_badge_count']);
        }
    }

    /**
     * 设置初始开机自启动
     *
     * @return void
     */
    protected function setInitialOpenAtLogin(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了初始开机自启动，则设置
        if (isset($config['initial_open_at_login'])) {
            App::openAtLogin($config['initial_open_at_login']);
        }
    }

    /**
     * 处理应用退出事件
     *
     * @return void
     */
    protected function handleAppQuit(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App quit');
        }

        // 如果配置了应用退出回调，则执行
        if (isset($config['on_quit']) && is_callable($config['on_quit'])) {
            call_user_func($config['on_quit']);
        }
    }

    /**
     * 处理应用重启事件
     *
     * @return void
     */
    protected function handleAppRestart(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App restart');
        }

        // 如果配置了应用重启回调，则执行
        if (isset($config['on_restart']) && is_callable($config['on_restart'])) {
            call_user_func($config['on_restart']);
        }
    }

    /**
     * 处理应用聚焦事件
     *
     * @return void
     */
    protected function handleAppFocus(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App focus');
        }

        // 如果配置了应用聚焦回调，则执行
        if (isset($config['on_focus']) && is_callable($config['on_focus'])) {
            call_user_func($config['on_focus']);
        }
    }

    /**
     * 处理应用隐藏事件
     *
     * @return void
     */
    protected function handleAppHide(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App hide');
        }

        // 如果配置了应用隐藏回调，则执行
        if (isset($config['on_hide']) && is_callable($config['on_hide'])) {
            call_user_func($config['on_hide']);
        }
    }

    /**
     * 处理应用徽章计数事件
     *
     * @param int $count
     * @return void
     */
    protected function handleAppBadgeCount(int $count): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App badge count', [
                'count' => $count,
            ]);
        }

        // 如果配置了应用徽章计数回调，则执行
        if (isset($config['on_badge_count']) && is_callable($config['on_badge_count'])) {
            call_user_func($config['on_badge_count'], $count);
        }
    }

    /**
     * 处理应用添加最近文档事件
     *
     * @param string $path
     * @return void
     */
    protected function handleAppAddRecentDocument(string $path): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App add recent document', [
                'path' => $path,
            ]);
        }

        // 如果配置了应用添加最近文档回调，则执行
        if (isset($config['on_add_recent_document']) && is_callable($config['on_add_recent_document'])) {
            call_user_func($config['on_add_recent_document'], $path);
        }
    }

    /**
     * 处理应用清除最近文档事件
     *
     * @return void
     */
    protected function handleAppClearRecentDocuments(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App clear recent documents');
        }

        // 如果配置了应用清除最近文档回调，则执行
        if (isset($config['on_clear_recent_documents']) && is_callable($config['on_clear_recent_documents'])) {
            call_user_func($config['on_clear_recent_documents']);
        }
    }

    /**
     * 处理应用开机自启动事件
     *
     * @param bool $open
     * @return void
     */
    protected function handleAppOpenAtLogin(bool $open): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App open at login', [
                'open' => $open,
            ]);
        }

        // 如果配置了应用开机自启动回调，则执行
        if (isset($config['on_open_at_login']) && is_callable($config['on_open_at_login'])) {
            call_user_func($config['on_open_at_login'], $open);
        }
    }

    /**
     * 处理应用最小化事件
     *
     * @return void
     */
    protected function handleAppMinimize(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App minimize');
        }

        // 如果配置了应用最小化回调，则执行
        if (isset($config['on_minimize']) && is_callable($config['on_minimize'])) {
            call_user_func($config['on_minimize']);
        }
    }

    /**
     * 处理应用最大化事件
     *
     * @return void
     */
    protected function handleAppMaximize(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App maximize');
        }

        // 如果配置了应用最大化回调，则执行
        if (isset($config['on_maximize']) && is_callable($config['on_maximize'])) {
            call_user_func($config['on_maximize']);
        }
    }

    /**
     * 处理应用恢复事件
     *
     * @return void
     */
    protected function handleAppRestore(): void
    {
        // 获取配置
        $config = config('native.app', []);

        // 如果配置了记录应用事件，则记录
        if (isset($config['log_app_events']) && $config['log_app_events']) {
            Logger::info('App restore');
        }

        // 如果配置了应用恢复回调，则执行
        if (isset($config['on_restore']) && is_callable($config['on_restore'])) {
            call_user_func($config['on_restore']);
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
        Logger::info('App plugin unloaded');
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
