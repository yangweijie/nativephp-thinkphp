<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\DeveloperTools;

class DevToolsPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'dev-tools';

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
    protected $description = '开发者工具插件';

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
            'window.create' => [$this, 'onWindowCreate'],
            'window.close' => [$this, 'onWindowClose'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 启用开发者工具
        DeveloperTools::enable();

        // 注册开发者菜单
        $this->registerDeveloperMenu();
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录应用启动事件
        \Native\ThinkPHP\Facades\Logger::info('Application started');

        // 启动性能监控
        DeveloperTools::getPerformanceMonitor()->start();
    }

    /**
     * 窗口创建事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowCreate(array $window): void
    {
        // 记录窗口创建事件
        \Native\ThinkPHP\Facades\Logger::info('Window created', $window);
    }

    /**
     * 窗口关闭事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowClose(array $window): void
    {
        // 记录窗口关闭事件
        \Native\ThinkPHP\Facades\Logger::info('Window closed', $window);
    }

    /**
     * 注册开发者菜单
     *
     * @return void
     */
    protected function registerDeveloperMenu(): void
    {
        // 创建开发者菜单
        \Native\ThinkPHP\Facades\Menu::create()
            ->submenu('开发者工具', function ($menu) {
                $menu->add('性能监控', function () {
                    DeveloperTools::openPerformanceMonitor();
                });
                $menu->add('崩溃报告', function () {
                    DeveloperTools::openCrashReporter();
                });
                $menu->add('调试控制台', function () {
                    DeveloperTools::openDebugConsole();
                });
                $menu->separator();
                $menu->add('重新加载', function () {
                    \Native\ThinkPHP\Facades\Window::reload();
                });
                $menu->add('开发者工具', function () {
                    /** @phpstan-ignore-next-line */
                    \Native\ThinkPHP\Facades\Window::openDevTools();
                });
            })
            ->setApplicationMenu();
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 禁用开发者工具
        DeveloperTools::disable();

        // 停止性能监控
        DeveloperTools::getPerformanceMonitor()->stop();
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