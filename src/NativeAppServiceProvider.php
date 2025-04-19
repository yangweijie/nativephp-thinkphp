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
    }

    public function boot()
    {
        // 初始化 Bridge
        $this->app->make('native')->bridge()->register();

        // 发布配置
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/native.php' => $this->app->getConfigPath() . 'native.php',
            ], 'native-config');
        }
    }
}