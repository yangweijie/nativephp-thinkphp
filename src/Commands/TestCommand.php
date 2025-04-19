<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;

class TestCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:test';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '运行 NativePHP for ThinkPHP 测试';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('开始运行 NativePHP for ThinkPHP 测试...');

        // 创建测试目录
        $testDir = runtime_path() . 'tests';
        if (!is_dir($testDir)) {
            mkdir($testDir, 0755, true);
        }

        // 运行单元测试
        $output->writeln('');
        $output->writeln('<info>运行单元测试...</info>');
        $this->getApplication()->find('native:test:unit')->run($input, $output);

        // 运行功能测试
        $output->writeln('');
        $output->writeln('<info>运行功能测试...</info>');
        $this->getApplication()->find('native:test:functional')->run($input, $output);

        // 运行集成测试
        $output->writeln('');
        $output->writeln('<info>运行集成测试...</info>');
        $this->getApplication()->find('native:test:integration')->run($input, $output);

        $output->writeln('');
        $output->writeln('<info>所有测试已完成！</info>');
        $output->writeln('测试报告已生成：');
        $output->writeln('- 单元测试报告：' . $testDir . '/unit_test_report.html');
        $output->writeln('- 功能测试报告：' . $testDir . '/functional_test_report.html');
        $output->writeln('- 集成测试报告：' . $testDir . '/integration_test_report.html');

        return 0;
    }

    /**
     * 获取应用实例
     *
     * @return mixed
     */
    protected function getApplication()
    {
        /** @phpstan-ignore-next-line */
        return $this->getConsole()->getApplication();
    }

    /**
     * 获取控制台实例
     *
     * @return \think\Console
     */
    // @phpstan-ignore-next-line
    public function getConsole()
    {
        return $this->app->console;
    }
}