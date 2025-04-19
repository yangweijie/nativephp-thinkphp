<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Native\ThinkPHP\NativeServiceProvider;

class SeedDatabaseCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:db:seed')
            ->setDescription('在 NativePHP 开发环境中运行数据库填充器');
    }

    protected function execute(Input $input, Output $output)
    {
        // 重写数据库
        (new NativeServiceProvider(app()))->rewriteDatabase();

        // 执行 ThinkPHP 的填充命令
        $this->app->console->call('seed:run');

        $output->info('数据库填充已完成');
        return 0;
    }
}
