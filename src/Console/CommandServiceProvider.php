<?php

namespace Native\ThinkPHP\Console;

use think\Service;

class CommandServiceProvider extends Service
{
    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        // 注册命令
        $this->commands([
            InitCommand::class,
            BuildCommand::class,
            ServeCommand::class,
        ]);
    }

    /**
     * 启动服务
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
