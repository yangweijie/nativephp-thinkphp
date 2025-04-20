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

        $output->writeln('<info>启动 NativePHP 应用...</info>');

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
        $phpServer->start();

        $output->writeln('<info>ThinkPHP 服务器已启动</info>');

        // 设置环境变量
        $env = [
            'NATIVEPHP_SERVE_HOST' => $host,
            'NATIVEPHP_SERVE_PORT' => $port,
            'NATIVEPHP_SERVE_NO_RELOAD' => $enableReload ? 'false' : 'true',
            'NATIVEPHP_DEV_TOOLS' => $devTools ? 'true' : 'false',
            'PHP_CLI_SERVER_WORKERS' => '4',
        ];

        // 检查 npx 是否可用
        $npxPath = $this->findExecutable('npx');
        
        if (!$npxPath) {
            $output->writeln('<error>npx 命令未找到。请先安装 Node.js 和 npm。</error>');
            $phpServer->stop();
            return 1;
        }

        // 获取 Electron 应用程序路径
        $electronAppPath = $this->getElectronAppPath();

        if (!file_exists($electronAppPath)) {
            $output->writeln('<error>未找到 Electron 应用程序。请确保 NativePHP 已正确安装。</error>');
            $phpServer->stop();
            return 1;
        }

        // 启动 Electron 应用程序
        $output->writeln('<info>启动 Electron 应用程序...</info>');

        // 使用 npx 直接运行 electron
        $electronServer = new Process([
            $npxPath,
            'electron',
            $electronAppPath
        ]);

        $electronServer->setEnv($env);
        $electronServer->setTimeout(null);

        var_dump($electronServer->getCommandLine());

        // 设置输出回调
        $phpServer->waitUntil(function ($type, $buffer) {
            return strpos($buffer, 'ThinkPHP server started') !== false;
        });

        $electronServer->start(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        $output->writeln('<info>NativePHP 应用已启动！</info>');
        $output->writeln(sprintf('<info>应用运行在 http://%s:%s</info>', $host, $port));

        // 等待进程结束
        $electronServer->wait();
        $phpServer->stop();

        return 0;
    }

    /**
     * 查找可执行文件
     */
    protected function findExecutable(string $name): ?string
    {
        // 检查是否在 PATH 中
        $paths = explode(PATH_SEPARATOR, getenv('PATH'));
        
        // Windows 系统需要添加 .cmd 后缀
        $extensions = PHP_OS_FAMILY === 'Windows' ? ['.cmd', '.bat', '.exe', ''] : [''];
        
        foreach ($paths as $path) {
            foreach ($extensions as $extension) {
                $exec = $path . DIRECTORY_SEPARATOR . $name . $extension;
                if (is_file($exec) && is_executable($exec)) {
                    return $exec;
                }
            }
        }
        
        return null;
    }

    /**
     * 获取 Electron 应用程序路径
     */
    protected function getElectronAppPath(): string
    {
        $basePath = $this->app->getRootPath();
        $appPath = $basePath . 'vendor/nativephp/electron/app';
        
        // 检查目录是否存在
        if (!is_dir($appPath)) {
            // 如果目录不存在，创建它
            mkdir($appPath, 0755, true);
            
            // 复制 Electron 应用文件
            $sourceDir = dirname(dirname(__DIR__)) . '/src/resources/electron';
            if (is_dir($sourceDir)) {
                $this->copyDirectory($sourceDir, $appPath);
            }
        }
        
        return $appPath;
    }
    
    /**
     * 复制目录
     */
    protected function copyDirectory(string $source, string $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            
            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            
            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
        closedir($dir);
    }
}
