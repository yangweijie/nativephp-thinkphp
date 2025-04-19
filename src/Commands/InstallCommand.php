<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:install')
            ->setDescription('安装 NativePHP 所需的依赖')
            ->addOption('force', 'f', Option::VALUE_NONE, '强制重新安装')
            ->addOption('no-tauri', null, Option::VALUE_NONE, '跳过 Tauri 安装');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>开始安装 NativePHP...</info>');

        // 发布配置
        $this->publishConfig($output);

        // 安装 Tauri
        if (!$input->getOption('no-tauri')) {
            $this->installTauri($output, $input->getOption('force'));
        }

        // 配置目录权限
        $this->configurePermissions($output);

        $output->writeln('<info>NativePHP 安装完成！</info>');
        return 0;
    }

    protected function publishConfig(Output $output)
    {
        $output->writeln('发布配置文件...');
        
        $configPath = $this->app->getConfigPath() . 'native.php';
        if (!file_exists($configPath)) {
            copy(__DIR__ . '/../config/native.php', $configPath);
            $output->writeln('<info>配置文件已创建: config/native.php</info>');
        }
    }

    protected function installTauri(Output $output, bool $force = false)
    {
        $output->writeln('检查 Tauri CLI...');

        // 检查是否已安装
        $checkCmd = 'cargo tauri --version';
        $hasError = system($checkCmd . ' 2>/dev/null', $returnCode) === false || $returnCode !== 0;

        if ($hasError || $force) {
            $output->writeln('安装 Tauri CLI...');
            
            // 安装 Rust
            if (!$this->isRustInstalled()) {
                $output->writeln('安装 Rust...');
                system('curl --proto \'=https\' --tlsv1.2 -sSf https://sh.rustup.rs | sh -s -- -y');
                $output->writeln('<info>Rust 安装完成</info>');
            }

            // 安装 Tauri CLI
            system('cargo install tauri-cli');
            $output->writeln('<info>Tauri CLI 安装完成</info>');
        } else {
            $output->writeln('<info>Tauri CLI 已安装</info>');
        }
    }

    protected function isRustInstalled(): bool
    {
        return system('which rustc >/dev/null 2>&1') !== false;
    }

    protected function configurePermissions(Output $output)
    {
        $output->writeln('配置目录权限...');

        $paths = [
            $this->app->getRuntimePath(),
            $this->app->getRootPath() . 'src-tauri',
        ];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                chmod($path, 0755);
                $output->writeln("<info>已设置目录权限: {$path}</info>");
            }
        }
    }
}