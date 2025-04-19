<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use Native\ThinkPHP\NativeServiceProvider;

class FreshCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:migrate:fresh')
            ->setDescription('删除所有表并在 NativePHP 开发环境中重新运行所有迁移');
    }

    protected function execute(Input $input, Output $output)
    {
        $nativeServiceProvider = new NativeServiceProvider(app());
        $nativeServiceProvider->removeDatabase();
        $nativeServiceProvider->rewriteDatabase();

        // 执行 ThinkPHP 的迁移命令
        $this->app->console->call('migrate:fresh');

        $output->info('数据库已重置并重新迁移');
        return 0;
    }
}
