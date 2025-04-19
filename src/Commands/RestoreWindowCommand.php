<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use think\console\input\Argument;

class RestoreWindowCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:restore-window')
            ->setDescription('恢复窗口状态')
            ->addArgument('label', Argument::REQUIRED, '窗口标识')
            ->addOption('all', 'a', Option::VALUE_NONE, '恢复所有窗口状态')
            ->addOption('clear', 'c', Option::VALUE_NONE, '清除保存的状态');
    }

    protected function execute(Input $input, Output $output)
    {
        $label = $input->getArgument('label');

        if ($input->getOption('all')) {
            $states = $this->app->native->windowState()->all();
            foreach ($states as $windowLabel => $state) {
                $this->restoreWindow($windowLabel, $output);
            }
            return 0;
        }

        if ($input->getOption('clear')) {
            if ($this->app->native->windowState()->clear($label)) {
                $output->writeln("<info>窗口 '{$label}' 的保存状态已清除</info>");
            }
            return 0;
        }

        $this->restoreWindow($label, $output);
        return 0;
    }

    protected function restoreWindow(string $label, Output $output): void
    {
        if ($this->app->native->windowState()->autoRestore($label)) {
            $output->writeln("<info>窗口 '{$label}' 状态已恢复</info>");
        } else {
            $output->writeln("<error>未找到窗口 '{$label}' 的保存状态</error>");
        }
    }
}