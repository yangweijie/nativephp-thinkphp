<?php

namespace NativePHP\Think;

use think\Service;
use NativePHP\Think\Commands\InstallCommand;
use NativePHP\Think\Commands\ServeCommand;

class NativeServiceProvider extends Service
{
    public function register()
    {
        // 注册单例
        $this->app->bind('native', function () {
            return new Native($this->app);
        });
    }

    public function boot()
    {
        // 注册命令
        $this->commands([
            InstallCommand::class,
            ServeCommand::class,
        ]);
    }
}
