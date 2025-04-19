<?php

namespace Native\ThinkPHP\Providers;

use think\Service;
use Native\ThinkPHP\App;
use Native\ThinkPHP\Window;
use Native\ThinkPHP\Menu;
use Native\ThinkPHP\Notification;
use Native\ThinkPHP\Dialog;
use Native\ThinkPHP\FileSystem;
use Native\ThinkPHP\System;
use Native\ThinkPHP\Clipboard;
use Native\ThinkPHP\GlobalShortcut;
use Native\ThinkPHP\Tray;
use Native\ThinkPHP\Keyboard;
use Native\ThinkPHP\Dock;
use Native\ThinkPHP\Shortcut;
use Native\ThinkPHP\Screen;
use Native\ThinkPHP\AutoUpdater;
use Native\ThinkPHP\I18n\Translator;
use Native\ThinkPHP\Theme;
use Native\ThinkPHP\Alert;
use Native\ThinkPHP\ContextMenu;
use Native\ThinkPHP\Speech;
use Native\ThinkPHP\Device;
use Native\ThinkPHP\Geolocation;
use Native\ThinkPHP\PushNotification;
use Native\ThinkPHP\Network;
use Native\ThinkPHP\Utils\Logger;
use Native\ThinkPHP\PowerMonitor;
use Native\ThinkPHP\Printer;
use Native\ThinkPHP\Database;
use Native\ThinkPHP\Shell;
use Native\ThinkPHP\Process;
use Native\ThinkPHP\ProgressBar;
use Native\ThinkPHP\Middleware\MiddlewareManager;
use Native\ThinkPHP\Plugins\PluginManager;
use Native\ThinkPHP\Documentation\DocumentationGenerator;
use Native\ThinkPHP\DeveloperTools\DeveloperTools;
use Native\ThinkPHP\DeveloperTools\PerformanceMonitor;
use Native\ThinkPHP\DeveloperTools\CrashReporter;
use Native\ThinkPHP\Testing\TestRunner;
use Native\ThinkPHP\Testing\TestHelper;
use Native\ThinkPHP\Utils\PerformanceOptimizer;

class NativeServiceProvider extends Service
{
    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        // 注册基础服务
        $this->registerBaseServices();

        // 注册高级服务
        $this->registerAdvancedServices();

        // 注册扩展服务
        $this->registerExtensionServices();

        // 注册开发者工具
        $this->registerDeveloperTools();

        // 注册测试工具
        $this->registerTestingTools();

        // 注册性能优化工具
        $this->registerPerformanceTools();

        // 注册命令
        $this->registerCommands();

        // 注册中间件
        $this->registerMiddleware();

        // 注册插件
        $this->registerPlugins();
    }

    /**
     * 注册基础服务
     *
     * @return void
     */
    protected function registerBaseServices()
    {
        // 注册应用程序
        $this->app->bind('native.app', function () {
            return new App($this->app);
        });

        // 注册窗口管理
        $this->app->bind('native.window', function () {
            return new Window($this->app);
        });

        // 注册菜单管理
        $this->app->bind('native.menu', function () {
            return new Menu($this->app);
        });

        // 注册通知管理
        $this->app->bind('native.notification', function () {
            return new Notification($this->app);
        });

        // 注册对话框管理
        $this->app->bind('native.dialog', function () {
            return new Dialog($this->app);
        });

        // 注册文件系统管理
        $this->app->bind('native.filesystem', function () {
            return new FileSystem($this->app);
        });

        // 注册系统管理
        $this->app->bind('native.system', function () {
            return new System($this->app);
        });
    }

    /**
     * 注册高级服务
     *
     * @return void
     */
    protected function registerAdvancedServices()
    {
        // 注册剪贴板管理
        $this->app->bind('native.clipboard', function () {
            return new Clipboard($this->app);
        });

        // 注册全局快捷键
        $this->app->bind('native.global_shortcut', function () {
            return new GlobalShortcut($this->app);
        });

        // 注册系统托盘
        $this->app->bind('native.tray', function () {
            return new Tray($this->app);
        });

        // 注册键盘管理
        $this->app->bind('native.keyboard', function () {
            return new Keyboard($this->app);
        });

        // 注册 Dock 管理
        $this->app->bind('native.dock', function () {
            return new Dock($this->app);
        });

        // 注册桌面快捷方式
        $this->app->bind('native.shortcut', function () {
            return new Shortcut($this->app);
        });

        // 注册屏幕捕获
        $this->app->bind('native.screen', function () {
            return new Screen($this->app);
        });

        // 注册自动更新
        $this->app->bind('native.auto_updater', function () {
            return new AutoUpdater($this->app);
        });

        // 注册国际化
        $this->app->bind('native.i18n', function () {
            return new Translator($this->app);
        });

        // 注册主题系统
        $this->app->bind('native.theme', function () {
            return new Theme($this->app);
        });

        // 注册警告框
        $this->app->bind('native.alert', function () {
            return new Alert($this->app);
        });

        // 注册上下文菜单
        $this->app->bind('native.context_menu', function () {
            return new ContextMenu($this->app);
        });
    }

    /**
     * 注册扩展服务
     *
     * @return void
     */
    protected function registerExtensionServices()
    {
        // 注册语音识别和合成
        $this->app->bind('native.speech', function () {
            return new Speech($this->app);
        });

        // 注册设备管理
        $this->app->bind('native.device', function () {
            return new Device($this->app);
        });

        // 注册地理位置服务
        $this->app->bind('native.geolocation', function () {
            return new Geolocation($this->app);
        });

        // 注册推送通知服务
        $this->app->bind('native.push_notification', function () {
            return new PushNotification($this->app);
        });

        // 注册网络管理
        $this->app->bind('native.network', function () {
            return new Network($this->app);
        });

        // 注册日志工具
        $this->app->bind('native.logger', function () {
            return new Logger($this->app);
        });

        // 注册电源监控
        $this->app->bind('native.power_monitor', function () {
            return new PowerMonitor($this->app);
        });

        // 注册打印机管理
        $this->app->bind('native.printer', function () {
            return new Printer($this->app);
        });

        // 注册数据库管理
        $this->app->bind('native.database', function () {
            return new Database($this->app);
        });

        // 注册命令行工具
        $this->app->bind('native.shell', function () {
            return new Shell($this->app);
        });

        // 注册进程管理
        $this->app->bind('native.process', function () {
            return new Process($this->app);
        });

        // 注册进度条管理
        $this->app->bind('native.progress_bar', function () {
            return new ProgressBar($this->app);
        });
    }

    /**
     * 注册开发者工具
     *
     * @return void
     */
    protected function registerDeveloperTools()
    {
        // 注册中间件管理器
        $this->app->bind('native.middleware_manager', function () {
            return new MiddlewareManager($this->app);
        });

        // 注册插件管理器
        $this->app->bind('native.plugin_manager', function () {
            return new PluginManager($this->app);
        });

        // 注册文档生成器
        $this->app->bind('native.documentation_generator', function () {
            return new DocumentationGenerator($this->app);
        });

        // 注册开发者工具
        $this->app->bind('native.developer_tools', function () {
            return new DeveloperTools($this->app);
        });

        // 注册性能监控
        $this->app->bind('native.performance_monitor', function () {
            return new PerformanceMonitor($this->app);
        });

        // 注册崩溃报告
        $this->app->bind('native.crash_reporter', function () {
            return new CrashReporter($this->app);
        });
    }

    /**
     * 注册测试工具
     *
     * @return void
     */
    protected function registerTestingTools()
    {
        // 注册测试运行器
        $this->app->bind('native.test_runner', function () {
            return new TestRunner($this->app);
        });

        // 注册测试助手
        $this->app->bind('native.test_helper', function () {
            return new TestHelper($this->app);
        });
    }

    /**
     * 注册性能优化工具
     *
     * @return void
     */
    protected function registerPerformanceTools()
    {
        // 注册性能优化器
        $this->app->bind('native.performance_optimizer', function () {
            return new PerformanceOptimizer($this->app);
        });
    }

    /**
     * 注册命令
     *
     * @return void
     */
    protected function registerCommands()
    {
        $this->commands([
            \Native\ThinkPHP\Commands\InitCommand::class,
            \Native\ThinkPHP\Commands\ServeCommand::class,
            \Native\ThinkPHP\Commands\BuildCommand::class,
            \Native\ThinkPHP\Commands\GenerateDocsCommand::class,
            \Native\ThinkPHP\Commands\OptimizeCommand::class,
            \Native\ThinkPHP\Commands\TestCommand::class,
            \Native\ThinkPHP\Commands\UnitTestCommand::class,
            \Native\ThinkPHP\Commands\FunctionalTestCommand::class,
            \Native\ThinkPHP\Commands\IntegrationTestCommand::class,
        ]);
    }

    /**
     * 注册中间件
     *
     * @return void
     */
    protected function registerMiddleware()
    {
        // 获取中间件管理器
        $middlewareManager = $this->app->make('native.middleware_manager');

        // 注册中间件
        $middlewareManager->add([
            \Native\ThinkPHP\Middleware\NativeMiddleware::class,
            \Native\ThinkPHP\Middleware\CorsMiddleware::class,
            \Native\ThinkPHP\Middleware\SecurityMiddleware::class,
            \Native\ThinkPHP\Middleware\CacheMiddleware::class,
            \Native\ThinkPHP\Middleware\LogMiddleware::class,
        ]);
    }

    /**
     * 注册插件
     *
     * @return void
     */
    protected function registerPlugins()
    {
        // 获取插件管理器
        $pluginManager = $this->app->make('native.plugin_manager');

        // 注册插件
        $pluginManager->register([
            \Native\ThinkPHP\Plugins\DevToolsPlugin::class,
            \Native\ThinkPHP\Plugins\PerformancePlugin::class,
            \Native\ThinkPHP\Plugins\SecurityPlugin::class,
            \Native\ThinkPHP\Plugins\I18nPlugin::class,
            \Native\ThinkPHP\Plugins\ThemePlugin::class,
        ]);
    }

    /**
     * 启动服务
     *
     * @return void
     */
    public function boot()
    {
        // 发布配置文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../config/native.php' => $this->app->getConfigPath() . 'native.php',
        ], 'config');

        // 发布资源文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../resources' => $this->app->getRootPath() . 'public/vendor/native',
        ], 'resources');

        // 发布视图文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../views' => $this->app->getRootPath() . 'view/vendor/native',
        ], 'views');

        // 发布语言文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../lang' => $this->app->getRootPath() . 'lang',
        ], 'lang');

        // 发布数据库迁移文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../database/migrations' => $this->app->getRootPath() . 'database/migrations',
        ], 'migrations');

        // 发布种子文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../database/seeds' => $this->app->getRootPath() . 'database/seeds',
        ], 'seeds');

        // 发布测试文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../tests' => $this->app->getRootPath() . 'tests',
        ], 'tests');

        // 发布示例文件
        /** @phpstan-ignore-next-line */
        $this->publishes([
            __DIR__ . '/../examples' => $this->app->getRootPath() . 'examples',
        ], 'examples');

        // 加载路由
        $this->loadRoutesFrom(__DIR__ . '/../routes/native.php');

        // 加载视图
        /** @phpstan-ignore-next-line */
        $this->loadViewsFrom(__DIR__ . '/../views', 'native');

        // 加载语言
        /** @phpstan-ignore-next-line */
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'native');

        // 加载数据库迁移
        /** @phpstan-ignore-next-line */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // 启动插件
        $this->app->make('native.plugin_manager')->boot();
    }
}