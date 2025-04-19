<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Native\ThinkPHP\NativeServiceProvider;

class MigrateCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:migrate')
            ->setDescription('在 NativePHP 开发环境中运行数据库迁移');
    }

    protected function execute(Input $input, Output $output)
    {
        // 重写数据库
        (new NativeServiceProvider(app()))->rewriteDatabase();

        // 执行 ThinkPHP 的迁移命令
        $this->app->console->call('migrate:run');

        $output->info('数据库迁移已完成');
        return 0;
    }
}
