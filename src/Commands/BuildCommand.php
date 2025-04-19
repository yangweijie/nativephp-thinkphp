<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use think\console\input\Argument;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:build')
            ->setDescription('构建桌面应用')
            ->addArgument('platform', Argument::OPTIONAL, '构建平台 (windows/macos/linux)', 'all')
            ->addOption('debug', 'd', Option::VALUE_NONE, '构建调试版本')
            ->addOption('release', 'r', Option::VALUE_NONE, '构建发布版本')
            ->addOption('universal', 'u', Option::VALUE_NONE, 'macOS: 构建通用二进制文件')
            ->addOption('updater', null, Option::VALUE_NONE, '启用自动更新');
    }

    protected function execute(Input $input, Output $output)
    {
        $platform = $input->getArgument('platform');
        $isDebug = $input->getOption('debug');
        $isRelease = $input->getOption('release');
        $isUniversal = $input->getOption('universal');
        $enableUpdater = $input->getOption('updater');

        // 验证参数
        if ($isDebug && $isRelease) {
            $output->writeln('<error>不能同时指定 debug 和 release 选项</error>');
            return 1;
        }

        if (!$isDebug && !$isRelease) {
            $isRelease = true; // 默认构建发布版本
        }

        // 构建命令参数
        $args = ['cargo', 'tauri', 'build'];

        if ($platform !== 'all') {
            $args[] = '--target';
            $args[] = $this->getTargetTriple($platform);
        }

        if ($isDebug) {
            $args[] = '--debug';
        }

        if ($isUniversal && $platform === 'macos') {
            $args[] = '--target';
            $args[] = 'universal-apple-darwin';
        }

        // 环境变量
        $env = [
            'RUST_BACKTRACE' => '1',
            'TAURI_DEPS_DIR' => $this->app->getRuntimePath() . 'tauri-deps',
            'TAURI_UPDATER_ENABLED' => $enableUpdater ? '1' : '0',
        ];

        // 执行构建
        $output->writeln('<info>开始构建应用程序...</info>');

        $process = new Process($args);
        $process->setWorkingDirectory($this->app->getRootPath() . '/src-tauri');
        $process->setTimeout(null);
        $process->setEnv($env);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $output->writeln('<info>应用程序构建完成！</info>');
            return 0;
        }

        $output->writeln('<error>构建失败！</error>');
        return 1;
    }

    protected function getTargetTriple(string $platform): string
    {
        return match ($platform) {
            'windows' => 'x86_64-pc-windows-msvc',
            'macos' => 'x86_64-apple-darwin',
            'linux' => 'x86_64-unknown-linux-gnu',
            default => throw new \InvalidArgumentException('不支持的构建平台: ' . $platform),
        };
    }
}