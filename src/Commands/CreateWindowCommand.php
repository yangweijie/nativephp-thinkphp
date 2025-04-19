<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\console\input\Argument;

class CreateWindowCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:create-window')
            ->setDescription('创建一个新的应用窗口')
            ->addArgument('label', Argument::REQUIRED, '窗口标识')
            ->addOption('preset', 'p', Option::VALUE_OPTIONAL, '使用预设模板', 'default')
            ->addOption('width', 'w', Option::VALUE_OPTIONAL, '窗口宽度')
            ->addOption('height', 'h', Option::VALUE_OPTIONAL, '窗口高度')
            ->addOption('title', 't', Option::VALUE_OPTIONAL, '窗口标题')
            ->addOption('center', 'c', Option::VALUE_NONE, '是否居中显示');
    }

    protected function execute(Input $input, Output $output)
    {
        $label = $input->getArgument('label');
        $preset = $input->getOption('preset');

        try {
            $window = $this->app->native->windowManager()->createFromPreset($preset, $label);

            // 应用自定义选项
            if ($width = $input->getOption('width')) {
                $window->width((int)$width);
            }
            
            if ($height = $input->getOption('height')) {
                $window->height((int)$height);
            }
            
            if ($title = $input->getOption('title')) {
                $window->title($title);
            }
            
            if ($input->getOption('center')) {
                $window->center();
            }

            $output->writeln("<info>窗口 '{$label}' 创建成功！</info>");
            
            // 触发窗口创建事件
            $this->app->native->events()->dispatch('window.created', [
                'label' => $label,
                'options' => $window->getOptions()
            ]);

            return 0;
        } catch (\Exception $e) {
            $output->writeln("<error>创建窗口失败: {$e->getMessage()}</error>");
            return 1;
        }
    }
}