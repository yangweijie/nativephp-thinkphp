<?php

namespace Native\ThinkPHP\DeveloperTools;

use think\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;

class DeveloperTools
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 是否启用开发者工具
     *
     * @var bool
     */
    protected $enabled = false;

    /**
     * 性能监控器
     *
     * @var \Native\ThinkPHP\DeveloperTools\PerformanceMonitor
     */
    protected $performanceMonitor;

    /**
     * 崩溃报告器
     *
     * @var \Native\ThinkPHP\DeveloperTools\CrashReporter
     */
    protected $crashReporter;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->performanceMonitor = new PerformanceMonitor($app);
        $this->crashReporter = new CrashReporter($app);
    }

    /**
     * 启用开发者工具
     *
     * @return void
     */
    public function enable(): void
    {
        if ($this->enabled) {
            return;
        }

        // 启用性能监控
        $this->performanceMonitor->start();

        // 启用崩溃报告
        $this->crashReporter->register();

        // 添加开发者工具菜单
        $this->addDeveloperMenu();

        $this->enabled = true;
    }

    /**
     * 禁用开发者工具
     *
     * @return void
     */
    public function disable(): void
    {
        if (!$this->enabled) {
            return;
        }

        // 停止性能监控
        $this->performanceMonitor->stop();

        // 禁用崩溃报告
        $this->crashReporter->unregister();

        // 移除开发者工具菜单
        $this->removeDeveloperMenu();

        $this->enabled = false;
    }

    /**
     * 添加开发者工具菜单
     *
     * @return void
     */
    protected function addDeveloperMenu(): void
    {
        // 获取当前应用菜单
        $menu = Menu::create();

        // 添加开发者工具菜单
        $menu->submenu('开发者工具', function ($submenu) {
            $submenu->add('性能监控', function () {
                $this->openPerformanceMonitor();
            });
            $submenu->add('崩溃报告', function () {
                $this->openCrashReporter();
            });
            $submenu->add('调试控制台', function () {
                $this->openDebugConsole();
            });
            $submenu->separator();
            $submenu->add('重新加载', function () {
                Window::reload();
            });
            $submenu->add('开发者工具', function () {
                /** @phpstan-ignore-next-line */
                Window::openDevTools();
            });
        });

        // 设置应用菜单
        $menu->setApplicationMenu();
    }

    /**
     * 移除开发者工具菜单
     *
     * @return void
     */
    protected function removeDeveloperMenu(): void
    {
        // 获取当前应用菜单
        $menu = Menu::create();

        // 设置应用菜单（不包含开发者工具菜单）
        $menu->setApplicationMenu();
    }

    /**
     * 打开性能监控窗口
     *
     * @return void
     */
    public function openPerformanceMonitor(): void
    {
        Window::open('/developer-tools/performance', [
            'title' => '性能监控',
            'width' => 800,
            'height' => 600,
            'resizable' => true,
        ]);
    }

    /**
     * 打开崩溃报告窗口
     *
     * @return void
     */
    public function openCrashReporter(): void
    {
        Window::open('/developer-tools/crash-reporter', [
            'title' => '崩溃报告',
            'width' => 800,
            'height' => 600,
            'resizable' => true,
        ]);
    }

    /**
     * 打开调试控制台窗口
     *
     * @return void
     */
    public function openDebugConsole(): void
    {
        Window::open('/developer-tools/debug-console', [
            'title' => '调试控制台',
            'width' => 800,
            'height' => 600,
            'resizable' => true,
        ]);
    }

    /**
     * 获取性能监控器
     *
     * @return \Native\ThinkPHP\DeveloperTools\PerformanceMonitor
     */
    public function getPerformanceMonitor(): PerformanceMonitor
    {
        return $this->performanceMonitor;
    }

    /**
     * 获取崩溃报告器
     *
     * @return \Native\ThinkPHP\DeveloperTools\CrashReporter
     */
    public function getCrashReporter(): CrashReporter
    {
        return $this->crashReporter;
    }
}