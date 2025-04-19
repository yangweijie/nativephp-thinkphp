<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use think\facade\Config;
use Native\ThinkPHP\Utils\PerformanceOptimizer;

class OptimizeCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:optimize';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '优化 NativePHP 应用性能';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int|null
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('正在优化 NativePHP 应用性能...');

        // 获取优化配置
        $config = Config::get('native.optimize', []);

        // 创建性能优化器
        $optimizer = new PerformanceOptimizer(App::getInstance(), $config);

        // 执行优化
        $result = $optimizer->optimize();

        // 显示优化报告
        $report = $optimizer->getReport();
        
        if ($result) {
            $output->writeln('<info>优化完成！</info>');
            
            // 显示优化项
            if (!empty($report['optimizations'])) {
                $output->writeln('<info>优化项：</info>');
                foreach ($report['optimizations'] as $optimization) {
                    $output->writeln('- ' . $optimization['message']);
                }
            }
            
            // 显示耗时
            if (isset($report['duration'])) {
                $output->writeln('<info>耗时：' . round($report['duration'], 2) . ' 秒</info>');
            }
        } else {
            $output->writeln('<error>优化失败！</error>');
            
            // 显示错误
            if (!empty($report['errors'])) {
                $output->writeln('<error>错误：</error>');
                foreach ($report['errors'] as $error) {
                    $output->writeln('- ' . $error['message']);
                }
            }
        }

        return 0;
    }
}
