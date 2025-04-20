<?php

namespace NativePHP\Think;

use think\Service;
use NativePHP\Think\Commands\InstallCommand;
use NativePHP\Think\Commands\ServeCommand;
use NativePHP\Think\Commands\UpdaterCommand;

class NativeServiceProvider extends Service
{
    public function register()
    {
        // 注册单例
        $this->app->bind('native', function () {
            return new Native($this->app);
        });

        // 注册更新管理器
        $this->app->bind('native.updater', function () {
            return new UpdateManager($this->app);
        });
    }

    public function boot()
    {
        // 注册命令
        $this->commands([
            InstallCommand::class,
            ServeCommand::class,
            UpdaterCommand::class,
        ]);

        // 注册更新检查中间件
        $this->registerMiddleware();
    }

    protected function registerMiddleware()
    {
        $this->app->middleware->add(Middleware\CheckForUpdates::class);
    }
}
