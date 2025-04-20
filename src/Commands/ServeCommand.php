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
            ->addOption('dev-tools', null, Option::VALUE_NONE, '启用开发者工具')
            ->addOption('debug-port', null, Option::VALUE_OPTIONAL, '调试端口', 9229)
            ->addOption('debug-brk', null, Option::VALUE_NONE, '在首行断点处暂停')
            ->addOption('inspect', null, Option::VALUE_NONE, '启用检查器')
            ->addOption('memory-limit', null, Option::VALUE_OPTIONAL, '内存限制', '128M')
            ->addOption('watch', 'w', Option::VALUE_OPTIONAL, '监视文件变化的目录', 'app,config,view');
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
        $env = array_merge($_ENV, [
            'NATIVEPHP_SERVE_HOST' => $host,
            'NATIVEPHP_SERVE_PORT' => $port,
            'NATIVEPHP_SERVE_NO_RELOAD' => $enableReload ? 'false' : 'true',
            'NATIVEPHP_DEV_TOOLS' => $devTools ? 'true' : 'false',
            'NATIVEPHP_DEBUG_PORT' => $input->getOption('debug-port'),
            'NATIVEPHP_DEBUG_BRK' => $input->getOption('debug-brk') ? 'true' : 'false',
            'NATIVEPHP_INSPECT' => $input->getOption('inspect') ? 'true' : 'false',
            'PHP_CLI_SERVER_WORKERS' => '4',
        ]);

        // 配置监视目录
        $watchDirs = explode(',', $input->getOption('watch'));
        foreach ($watchDirs as $dir) {
            $this->watchDirectory(trim($dir), $output);
        }

        // 设置内存限制
        ini_set('memory_limit', $input->getOption('memory-limit'));

        // 初始化调试工具
        $debug = $this->app->make('native.debug');

        // 启动调试工具
        if ($devTools) {
            $debug->getDevTools()->register();
            $output->writeln('<info>开发者工具已启用</info>');
        }

        // 启动调试器
        if ($input->getOption('inspect')) {
            $debug->getInspector()->start();
            $output->writeln(sprintf(
                '<info>调试器已启动在 %s</info>',
                $debug->getInspector()->getUrl()
            ));
        }

        // 启动性能分析
        $profiler = new PerformanceProfiler($this->app->isDebug());
        $testRunner = new TestRunner($this->app, $profiler);

        // 设置测试监视
        if ($input->getOption('watch')) {
            $testRunner->watch([], function ($results) use ($output) {
                $output->writeln('<info>检测到测试文件变化，重新运行测试...</info>');
                $this->displayTestResults($results, $output);
            });
        }

        // 启动开发服务器
        return $this->startServer($input, $output);
    }

    protected function displayTestResults(array $results, Output $output): void
    {
        if (isset($results['results']['summary'])) {
            $summary = $results['results']['summary'];
            $output->writeln(sprintf(
                '<info>测试完成: %d 个测试, %d 个断言</info>',
                $summary['tests'],
                $summary['assertions']
            ));
        }

        if (!empty($results['results']['failures'])) {
            $output->writeln('<error>失败的测试:</error>');
            foreach ($results['results']['failures'] as $failure) {
                $output->writeln("  - {$failure}");
            }
        }

        if (!empty($results['results']['skipped'])) {
            $output->writeln('<comment>跳过的测试:</comment>');
            foreach ($results['results']['skipped'] as $skipped) {
                $output->writeln("  - {$skipped}");
            }
        }

        if (isset($results['metrics'])) {
            $output->writeln(sprintf(
                '<info>性能指标: 执行时间 %.2f ms, 内存使用 %.2f MB</info>',
                $results['metrics']['duration'],
                $results['metrics']['memory_usage'] / 1024 / 1024
            ));
        }
    }

    protected function watchDirectory(string $dir, Output $output): void
    {
        $path = $this->app->getRootPath() . $dir;
        if (!is_dir($path)) {
            return;
        }

        $watcher = new class($path, $output) {
            protected $path;
            protected $output;
            protected $lastCheck;
            protected $files = [];

            public function __construct($path, $output)
            {
                $this->path = $path;
                $this->output = $output;
                $this->lastCheck = time();
                $this->files = $this->scanDirectory($path);
            }

            public function check()
            {
                $currentFiles = $this->scanDirectory($this->path);
                $changes = array_diff(array_keys($currentFiles), array_keys($this->files));
                
                foreach ($changes as $file) {
                    $this->output->writeln(sprintf(
                        '<info>检测到文件变化: %s</info>',
                        str_replace($this->path, '', $file)
                    ));
                }

                $this->files = $currentFiles;
                $this->lastCheck = time();
            }

            protected function scanDirectory($path): array
            {
                $files = [];
                $iterator = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path)
                );

                foreach ($iterator as $file) {
                    if ($file->isFile()) {
                        $files[$file->getRealPath()] = $file->getMTime();
                    }
                }

                return $files;
            }
        };

        // 每秒检查一次文件变化
        register_tick_function([$watcher, 'check']);
    }

    protected function startInspector(Input $input, Output $output): void
    {
        $port = $input->getOption('debug-port');
        $process = new Process([
            'node',
            '--inspect' . ($input->getOption('debug-brk') ? '-brk' : ''),
            '=' . $port
        ]);

        $process->start();
        $output->writeln(sprintf(
            '<info>调试器已启动在端口 %d</info>',
            $port
        ));
    }

    protected function handleError(\Throwable $e): void
    {
        $this->output->writeln(sprintf(
            '<error>错误: %s</error>',
            $e->getMessage()
        ));

        if ($this->output->isVerbose()) {
            $this->output->writeln($e->getTraceAsString());
        }

        // 发送错误通知
        if (class_exists('\think\facade\Notice')) {
            \think\facade\Notice::error($e->getMessage());
        }
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
