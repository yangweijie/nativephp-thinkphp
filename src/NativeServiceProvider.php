<?php

namespace Native\ThinkPHP;

use think\Service;
use Native\ThinkPHP\Alert;
use Native\ThinkPHP\App;
use Native\ThinkPHP\Window;
use Native\ThinkPHP\Menu;
use Native\ThinkPHP\Notification;
use Native\ThinkPHP\Clipboard;
use Native\ThinkPHP\ContextMenu;
use Native\ThinkPHP\GlobalShortcut;
use Native\ThinkPHP\Tray;
use Native\ThinkPHP\Dialog;
use Native\ThinkPHP\FileSystem;
use Native\ThinkPHP\System;
use Native\ThinkPHP\Screen;
use Native\ThinkPHP\Updater;
use Native\ThinkPHP\Http;
use Native\ThinkPHP\Console\CommandServiceProvider;
use Native\ThinkPHP\Database;
use Native\ThinkPHP\Settings;
use Native\ThinkPHP\Process;
use Native\ThinkPHP\ProgressBar;
use Native\ThinkPHP\Printer;
use Native\ThinkPHP\Shell;
use Native\ThinkPHP\I18n\Translator;
use Native\ThinkPHP\Speech;
use Native\ThinkPHP\Device;
use Native\ThinkPHP\Geolocation;
use Native\ThinkPHP\PushNotification;
use Native\ThinkPHP\Utils\Logger;
use Native\ThinkPHP\Utils\Cache;
use Native\ThinkPHP\Utils\Event;
use Native\ThinkPHP\Utils\Config;
use Native\ThinkPHP\Client\Client;
use Native\ThinkPHP\Keyboard;
use Native\ThinkPHP\PowerMonitor;
use Native\ThinkPHP\Network;
use Native\ThinkPHP\Dock;
use Native\ThinkPHP\AutoUpdater;
use Native\ThinkPHP\Shortcut;
use Native\ThinkPHP\Broadcasting;
use Native\ThinkPHP\Theme;
use Native\ThinkPHP\ChildProcess;
use Native\ThinkPHP\QueueWorker;
use Native\ThinkPHP\Cache\CacheFactory;
use Native\ThinkPHP\Contracts\CacheAdapter;
use Native\ThinkPHP\Components\LanguageSwitcher;
use Native\ThinkPHP\Components\ThemeSwitcher;
use Native\ThinkPHP\Commands\InitCommand;
use Native\ThinkPHP\Commands\GenerateDocsCommand;
use Native\ThinkPHP\Commands\ServeCommand;
use Native\ThinkPHP\Commands\BuildCommand;
use Native\ThinkPHP\Commands\DebugCommand;
use Native\ThinkPHP\Commands\FreshCommand;
use Native\ThinkPHP\Commands\MigrateCommand;
use Native\ThinkPHP\Commands\SeedDatabaseCommand;

class NativeServiceProvider extends Service
{
    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        // 注册配置
        $this->registerConfig();

        // 注册命令
        $this->registerCommands();

        // 注册绑定
        $this->registerBindings();
    }

    /**
     * 启动服务
     *
     * @return void
     */
    public function boot()
    {
        // 注册路由
        $this->registerRoutes();

        // 加载插件
        $this->loadPlugins();
    }

    /**
     * 加载插件
     *
     * @return void
     */
    protected function loadPlugins()
    {
        // 在测试环境中禁用插件加载
        if (defined('PHPUNIT_RUNNING')) {
            return;
        }

        // 如果配置中禁用了插件加载
        if ($this->app->config->get('native.plugins.enabled') === false) {
            return;
        }
        // 获取插件管理器
        $pluginManager = $this->app->make('native.plugin_manager');

        // 加载国际化插件
        $pluginManager->load('I18n');

        // 加载主题插件
        $pluginManager->load('Theme');

        // 加载全局快捷键插件
        $pluginManager->load('GlobalShortcut');

        // 加载系统托盘插件
        $pluginManager->load('Tray');

        // 加载剪贴板插件
        $pluginManager->load('Clipboard');

        // 加载上下文菜单插件
        $pluginManager->load('ContextMenu');

        // 加载对话框插件
        $pluginManager->load('Dialog');

        // 加载文件系统插件
        $pluginManager->load('FileSystem');

        // 加载 HTTP 客户端插件
        $pluginManager->load('Http');

        // 加载系统信息插件
        $pluginManager->load('System');

        // 加载屏幕捕获插件
        $pluginManager->load('Screen');

        // 加载自动更新插件
        $pluginManager->load('Updater');

        // 加载键盘插件
        $pluginManager->load('Keyboard');

        // 加载打印机插件
        $pluginManager->load('Printer');

        // 加载桌面快捷方式插件
        $pluginManager->load('Shortcut');

        // 加载语音识别和合成插件
        $pluginManager->load('Speech');

        // 加载通知插件
        $pluginManager->load('Notification');

        // 加载电源管理插件
        $pluginManager->load('Power');

        // 加载应用程序菜单插件
        $pluginManager->load('Menu');

        // 加载窗口管理插件
        $pluginManager->load('Window');

        // 加载应用程序管理插件
        $pluginManager->load('App');

        // 加载资源管理插件
        $pluginManager->load('Assets');

        // 加载数据库管理插件
        $pluginManager->load('Database');

        // 加载安全性管理插件
        $pluginManager->load('Security');

        // 加载网络管理插件
        $pluginManager->load('Network');

        // 加载广播插件
        $pluginManager->load('Broadcasting');

        // 加载子进程管理插件
        $pluginManager->load('ChildProcess');

        // 加载队列工作器插件
        $pluginManager->load('QueueWorker');

        // 加载进度条插件
        $pluginManager->load('ProgressBar');

        // 加载 Shell 命令执行插件
        $pluginManager->load('Shell');

        // 加载资源管理插件
        $pluginManager->load('Assets');

        // 加载安全性管理插件
        $pluginManager->load('Security');

        // 加载开发者工具插件
        $pluginManager->load('DeveloperTools');
    }

    /**
     * 注册配置
     *
     * @return void
     */
    protected function registerConfig()
    {
        // 合并配置文件
        if (defined('PHPUNIT_RUNNING')) {
            // 在测试环境中使用 set 方法
            $config = require __DIR__ . '/../config/native.php';
            $this->app->config->set($config, 'native');
        } else if (method_exists($this->app->config, 'load')) {
            // 如果存在 load 方法，使用 load 方法
            $this->app->config->load(__DIR__ . '/../config/native.php', 'native');
        } else {
            // 否则使用 set 方法
            $config = require __DIR__ . '/../config/native.php';
            $this->app->config->set($config, 'native');
        }
    }

    /**
     * 注册命令
     *
     * @return void
     */
    protected function registerCommands()
    {
        // 注册命令行服务提供者
        $this->app->register(CommandServiceProvider::class);
    }

    /**
     * 注册绑定
     *
     * @return void
     */
    protected function registerBindings()
    {
        // 注册核心服务绑定
        $this->app->bind('native.alert', function () {
            return new Alert($this->app);
        });

        $this->app->bind('native.app', function () {
            return new App($this->app);
        });

        $this->app->bind('native.window', function () {
            return new Window($this->app);
        });

        $this->app->bind('native.menu', function () {
            return new Menu($this->app);
        });

        $this->app->bind('native.notification', function () {
            return new Notification($this->app);
        });

        // 注册插件管理器
        $this->app->bind('native.plugin_manager', function () {
            return new Plugins\PluginManager($this->app);
        });

        // 注册国际化翻译器
        $this->app->bind('native.translator', function () {
            return new I18n\Translator($this->app);
        });

        // 注册主题
        $this->app->bind('native.theme', function () {
            return new Theme($this->app);
        });

        // 注册主题切换器
        $this->app->bind('native.theme_switcher', function () {
            return new Components\ThemeSwitcher($this->app);
        });

        $this->app->bind('native.clipboard', function () {
            return new Clipboard($this->app);
        });

        $this->app->bind('native.context_menu', function () {
            return new ContextMenu($this->app);
        });

        $this->app->bind('native.global_shortcut', function () {
            return new GlobalShortcut($this->app);
        });

        $this->app->bind('native.dialog', function () {
            return new Dialog($this->app);
        });

        $this->app->bind('native.filesystem', function () {
            return new FileSystem($this->app);
        });

        $this->app->bind('native.http', function () {
            return new Http($this->app);
        });

        $this->app->bind('native.system', function () {
            return new System($this->app);
        });

        $this->app->bind('native.screen', function () {
            return new Screen($this->app);
        });

        $this->app->bind('native.updater', function () {
            return new Updater($this->app);
        });

        $this->app->bind('native.keyboard', function () {
            return new Keyboard($this->app);
        });

        $this->app->bind('native.shortcut', function () {
            return new Shortcut($this->app);
        });

        $this->app->bind('native.speech', function () {
            return new Speech($this->app);
        });

        $this->app->bind('native.powerMonitor', function () {
            return new PowerMonitor($this->app);
        });

        $this->app->bind('native.assets', function () {
            return new Assets($this->app);
        });

        $this->app->bind('native.security', function () {
            return new Security($this->app);
        });

        $this->app->bind('native.tray', function () {
            return new Tray($this->app);
        });

        $this->app->bind('native.database', function () {
            return new Database($this->app);
        });

        $this->app->bind('native.settings', function () {
            return new Settings($this->app);
        });

        $this->app->bind('native.shell', function () {
            return new Shell($this->app);
        });

        $this->app->bind('native.translator', function () {
            return new Translator($this->app);
        });

        $this->app->bind('native.process', function () {
            return new Process($this->app);
        });

        $this->app->bind('native.progress_bar', function () {
            return new ProgressBar($this->app);
        });

        $this->app->bind('native.printer', function () {
            return new Printer($this->app);
        });

        $this->app->bind('native.speech', function () {
            return new Speech($this->app);
        });

        $this->app->bind('native.device', function () {
            return new Device($this->app);
        });

        $this->app->bind('native.geolocation', function () {
            return new Geolocation($this->app);
        });

        $this->app->bind('native.push_notification', function () {
            return new PushNotification($this->app);
        });

        // 注册工具类
        $this->app->bind('native.logger', function () {
            return new Logger($this->app);
        });

        $this->app->bind('native.cache', function () {
            return new Cache($this->app);
        });

        $this->app->bind('native.event', function () {
            return new Event();
        });

        $this->app->bind('native.config', function () {
            return new Config($this->app);
        });

        // 注册新增服务
        $this->app->bind('native.dock', function () {
            return new Dock($this->app);
        });

        $this->app->bind('native.client', function () {
            return new Client();
        });

        $this->app->bind('native.auto-updater', function () {
            return new AutoUpdater($this->app);
        });

        $this->app->bind('native.language_switcher', function () {
            return new LanguageSwitcher($this->app);
        });

        $this->app->bind('native.cache', function () {
            $config = $this->app->config->get('native.cache', []);
            $driver = isset($config['driver']) ? $config['driver'] : 'memory';
            return CacheFactory::create($driver, $config, $this->app);
        });

        $this->app->bind('native.child_process', function () {
            return new ChildProcess($this->app, $this->app->make('native.cache'));
        });

        $this->app->bind('native.queue_worker', function () {
            return new QueueWorker($this->app, $this->app->make('native.cache'));
        });

        $this->app->bind('native.broadcasting', function () {
            return new Broadcasting($this->app);
        });

        $this->app->bind('native.network', function () {
            return new Network($this->app);
        });
    }

    /**
     * 注册路由
     *
     * @param \Closure $closure
     * @return void
     */
    protected function registerRoutes(\Closure $closure = null)
    {
        // 在测试环境中不加载路由
        if (defined('PHPUNIT_RUNNING')) {
            return;
        }

        // 加载路由
        $routesPath = __DIR__ . '/Http/routes.php';
        if (file_exists($routesPath)) {
            include $routesPath;
        }

        // 执行闭包
        if ($closure instanceof \Closure) {
            $closure();
        }
    }

    /**
     * 移除数据库
     *
     * @return void
     */
    public function removeDatabase()
    {
        $databasePath = $this->app->getRuntimePath() . 'database/native.sqlite';
        if (file_exists($databasePath)) {
            unlink($databasePath);
        }
    }

    /**
     * 重写数据库
     *
     * @return void
     */
    public function rewriteDatabase()
    {
        $databasePath = $this->app->getRuntimePath() . 'database/native.sqlite';
        $databaseDir = dirname($databasePath);

        if (!is_dir($databaseDir)) {
            mkdir($databaseDir, 0755, true);
        }

        file_put_contents($databasePath, '');
    }
}
