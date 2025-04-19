<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use Symfony\Component\Process\Process;

class ServeCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:serve')
            ->setDescription('启动开发服务器')
            ->addOption('host', null, Option::VALUE_OPTIONAL, '监听主机', '127.0.0.1')
            ->addOption('port', 'p', Option::VALUE_OPTIONAL, '监听端口', 8000)
            ->addOption('no-reload', null, Option::VALUE_NONE, '禁用热重载')
            ->addOption('dev-tools', null, Option::VALUE_NONE, '启用开发者工具');
    }

    protected function execute(Input $input, Output $output)
    {
        $host = $input->getOption('host');
        $port = $input->getOption('port');
        $enableReload = !$input->getOption('no-reload');
        $devTools = $input->getOption('dev-tools');

        // 启动 ThinkPHP 服务器
        $phpServer = new Process([
            PHP_BINARY,
            'think',
            'run',
            '--host',
            $host,
            '--port',
            $port
        ]);

        $phpServer->setWorkingDirectory($this->app->getRootPath());
        $phpServer->setTimeout(null);

        // 启动 Tauri 开发服务器
        $tauriServer = new Process([
            'cargo',
            'tauri',
            'dev',
            '--',
            '--port',
            $port
        ]);

        $tauriServer->setWorkingDirectory($this->app->getRootPath() . '/src-tauri');
        $tauriServer->setTimeout(null);

        // 配置环境变量
        $env = [
            'RUST_BACKTRACE' => '1',
            'TAURI_DEPS_DIR' => $this->app->getRuntimePath() . 'tauri-deps',
            'PHP_CLI_SERVER_WORKERS' => '4',
        ];

        if ($devTools) {
            $env['TAURI_DEV_TOOLS'] = '1';
        }

        $phpServer->setEnv($env);
        $tauriServer->setEnv($env);

        // 设置输出回调
        $phpServer->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $tauriServer->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        return 0;
    }
}