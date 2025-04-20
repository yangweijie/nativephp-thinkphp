<?php

namespace NativePHP\Think;

use think\Service;

class NativeAppServiceProvider extends Service
{
    public function register()
    {
        // 注册配置
        $this->app->config->load(__DIR__ . '/config/native.php', 'native');

        // 注册单例
        $this->app->bind('native', Native::class);

        // 注册命令
        $this->commands([
            Commands\BuildCommand::class,
            Commands\CreateWindowCommand::class,
            Commands\CreateWindowGroupCommand::class,
            Commands\InstallCommand::class,
            Commands\RestoreWindowCommand::class,
            Commands\ServeCommand::class,
        ]);

        // 绑定接口到实现
        $this->app->bind(Contract\WindowContract::class, Window::class);
        $this->app->bind(Contract\WindowGroupContract::class, WindowGroup::class);
        $this->app->bind(Contract\WindowStateContract::class, WindowState::class);
        $this->app->bind(Contract\MenuContract::class, Menu::class);
        $this->app->bind(Contract\TrayContract::class, Tray::class);
        $this->app->bind(Contract\HotkeyContract::class, Hotkey::class);
        $this->app->bind(Contract\IpcContract::class, Ipc::class);
        $this->app->bind(Contract\EventDispatcherContract::class, EventDispatcher::class);

        // 注册更新管理器
        $this->app->bind('native.updater', function () {
            return new UpdateManager($this->app);
        });
    }

    public function boot()
    {
        // 初始化 Bridge
        $this->app->make('native')->bridge()->register();

        // 注册窗口分组相关事件监听
        $this->app->event->listen('window.group.created', function($data) {
            // 保存分组状态
            $group = $this->app->make('native')->windowManager()->getGroup($data['name']);
            if ($group) {
                $group->saveState();
            }
        });

        $this->app->event->listen('window.group.restored', function($data) {
            // 加载分组状态
            $group = $this->app->make('native')->windowManager()->getGroup($data['name']);
            if ($group) {
                $group->loadState();
            }
        });

        // 注册命令
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\BuildCommand::class,
                Commands\ServeCommand::class,
                Commands\InstallCommand::class,
                Commands\UpdaterCommand::class, // 添加更新器命令
                Commands\CreateWindowCommand::class,
                Commands\CreateWindowGroupCommand::class,
                Commands\RestoreWindowCommand::class,
            ]);
        }
    }

    /**
     * 发布资源
     * ThinkPHP 兼容方法，模拟 Laravel 的 publishes 方法
     */
    protected function publishes(array $paths, $groups = null)
    {
        // 在 ThinkPHP 中，我们直接在 InstallCommand 中处理资源发布
        // 这个方法只是为了兼容性而存在，实际上不做任何事情
    }
}
