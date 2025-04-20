<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\console\input\Argument;

class CreateWindowGroupCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:create-group')
            ->setDescription('创建一个窗口分组')
            ->addArgument('name', Argument::REQUIRED, '分组名称')
            ->addOption('preset', 'p', Option::VALUE_OPTIONAL, '使用预设配置', 'default')
            ->addOption('layout', 'l', Option::VALUE_OPTIONAL, '布局方式 (horizontal/vertical/grid/custom)', 'horizontal')
            ->addOption('windows', 'w', Option::VALUE_OPTIONAL, '要创建的窗口列表，JSON格式', '[]');
    }

    protected function execute(Input $input, Output $output)
    {
        $name = $input->getArgument('name');
        $preset = $input->getOption('preset');
        $layout = $input->getOption('layout');
        $windows = json_decode($input->getOption('windows'), true) ?: [];

        try {
            // 获取预设配置
            $config = $this->app->config->get("native.window_groups.{$preset}");
            if (!$config && $preset !== 'default') {
                $output->writeln("<error>找不到预设配置 '{$preset}'</error>");
                return 1;
            }

            // 创建窗口分组
            $group = $this->app->native->windowManager()->createGroup($name);

            // 创建配置的窗口
            foreach ($windows as $label => $options) {
                $window = $this->app->native->window($label);
                foreach ($options as $key => $value) {
                    if (method_exists($window, $key)) {
                        $window->$key($value);
                    }
                }
                $group->add($label);
            }

            // 应用布局
            switch ($layout) {
                case 'horizontal':
                    $group->arrangeHorizontal();
                    break;
                case 'vertical':
                    $group->arrangeVertical();
                    break;
                case 'grid':
                    $group->arrangeGrid();
                    break;
                case 'custom':
                    if (isset($config['layout_callback']) && is_callable($config['layout_callback'])) {
                        call_user_func($config['layout_callback'], $group);
                    }
                    break;
            }

            $output->writeln("<info>窗口分组 '{$name}' 创建成功！</info>");
            
            // 触发分组创建事件
            $this->app->native->events()->dispatch('window.group.created', [
                'name' => $name,
                'windows' => $windows,
                'layout' => $layout
            ]);

            return 0;
        } catch (\Exception $e) {
            $output->writeln("<error>创建窗口分组失败: {$e->getMessage()}</error>");
            return 1;
        }
    }
}