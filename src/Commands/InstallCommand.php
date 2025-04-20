<?php

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\console\input\Option;
use Symfony\Component\Process\Process;

class InstallCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:install')
            ->setDescription('安装 NativePHP 所需的依赖')
            ->addOption('force', 'f', Option::VALUE_NONE, '强制重新安装')
            ->addOption('no-electron', null, Option::VALUE_NONE, '跳过 Electron 安装');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>开始安装 NativePHP...</info>');

        // 检查依赖
        if (!$this->checkDependencies($output)) {
            return 1;
        }

        // 安装 Node.js 依赖
        $this->installNodeDependencies($output);

        // 发布配置
        $this->publishConfig($output);

        // 安装 Electron
        if (!$input->getOption('no-electron')) {
            $this->installElectron($output, $input->getOption('force'));
        }

        // 配置目录权限
        $this->configurePermissions($output);

        $output->writeln('<info>NativePHP 安装完成！</info>');
        return 0;
    }

    protected function checkDependencies(Output $output): bool
    {
        $dependencies = [
            'php' => '>=7.2.5',
            'node' => '>=14.0.0',
            'npm' => '>=6.0.0',
            'electron' => '>=22.0.0'
        ];

        foreach ($dependencies as $dep => $version) {
            $process = new Process([$dep, '--version']);
            $process->run();

            if (!$process->isSuccessful()) {
                $output->writeln("<error>{$dep} 未安装</error>");
                return false;
            }

            $installedVersion = trim($process->getOutput());
            if (!$this->checkVersion($installedVersion, $version)) {
                $output->writeln("<error>{$dep} 版本需要 {$version}，当前版本 {$installedVersion}</error>");
                return false;
            }
        }

        return true;
    }

    protected function checkVersion(string $installed, string $required): bool
    {
        $installed = preg_replace('/[^0-9.]/', '', $installed);
        $required = str_replace('>=', '', $required);

        return version_compare($installed, $required, '>=');
    }

    protected function installNodeDependencies(Output $output): void
    {
        $output->writeln('安装 Node.js 依赖...');

        $dependencies = [
            'electron' => '^22.0.0',
            'electron-builder' => '^24.0.0',
            'electron-updater' => '^6.0.0',
            'electron-log' => '^5.0.0',
            'terser' => '^5.0.0',
            'clean-css-cli' => '^5.0.0'
        ];

        // 更新 package.json
        $packagePath = $this->app->getRootPath() . 'package.json';
        $package = file_exists($packagePath) ? json_decode(file_get_contents($packagePath), true) : [];

        $package['devDependencies'] = array_merge(
            $package['devDependencies'] ?? [],
            $dependencies
        );

        file_put_contents(
            $packagePath,
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // 安装依赖
        $process = new Process(['npm', 'install']);
        $process->setWorkingDirectory($this->app->getRootPath());
        $process->setTimeout(null);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });
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

    protected function installElectron(Output $output, bool $force = false)
    {
        $output->writeln('检查 Electron...');

        $electronPath = $this->getElectronPath();
        $electronExists = file_exists($electronPath);

        if (!$electronExists || $force) {
            $output->writeln('安装 Electron...');

            // 检查 Node.js 和 npm
            if (!$this->isNodeInstalled()) {
                $output->writeln('<error>未找到 Node.js。请先安装 Node.js 和 npm。</error>');
                return false;
            }

            // 安装 Electron
            $this->installElectronPackage($output);

            // 安装 NativePHP Electron 应用
            $this->installNativePhpElectronApp($output);

            $output->writeln('<info>Electron 安装完成</info>');
        } else {
            $output->writeln('<info>Electron 已安装</info>');
        }

        return true;
    }

    protected function isNodeInstalled(): bool
    {
        $process = new Process(['node', '--version']);
        $process->run();
        return $process->isSuccessful();
    }

    protected function installElectronPackage(Output $output)
    {
        $output->writeln('安装 Electron 包...');

        $process = new Process(['npm', 'install', '--save-dev', 'electron@latest']);
        $process->setWorkingDirectory($this->app->getRootPath());
        $process->setTimeout(null);

        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        // 创建符号链接到 vendor/bin
        $this->createElectronSymlink($output);
    }

    protected function createElectronSymlink(Output $output)
    {
        $output->writeln('创建 Electron 符号链接...');

        $vendorBinPath = $this->app->getRootPath() . 'vendor/bin';
        if (!is_dir($vendorBinPath)) {
            mkdir($vendorBinPath, 0755, true);
        }

        $electronBinPath = $vendorBinPath . '/electron';
        $nodeBinPath = $this->app->getRootPath() . 'node_modules/.bin/electron';

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows 上创建批处理文件
            $electronCmdPath = $vendorBinPath . '/electron.cmd';
            $nodeCmdPath = $this->app->getRootPath() . 'node_modules/.bin/electron.cmd';

            if (file_exists($nodeCmdPath)) {
                if (file_exists($electronCmdPath)) {
                    unlink($electronCmdPath);
                }
                copy($nodeCmdPath, $electronCmdPath);
            }
        } else {
            // Unix 系统上创建符号链接
            if (file_exists($nodeBinPath)) {
                if (file_exists($electronBinPath)) {
                    unlink($electronBinPath);
                }
                symlink($nodeBinPath, $electronBinPath);
                chmod($electronBinPath, 0755);
            }
        }
    }

    protected function installNativePhpElectronApp(Output $output)
    {
        $output->writeln('安装 NativePHP Electron 应用...');

        $electronAppPath = $this->app->getRootPath() . 'vendor/nativephp/electron';
        if (!is_dir($electronAppPath)) {
            mkdir($electronAppPath, 0755, true);
        }

        $appPath = $electronAppPath . '/app';
        if (!is_dir($appPath)) {
            mkdir($appPath, 0755, true);
        }

        // 复制 Electron 应用文件
        $this->copyElectronAppFiles($appPath, $output);
    }

    protected function copyElectronAppFiles(string $appPath, Output $output)
    {
        // 复制 Electron 应用文件
        $sourceDir = __DIR__ . '/../resources/electron';

        // 如果目录不存在，尝试其他路径
        if (!is_dir($sourceDir)) {
            $sourceDir = dirname(dirname(__DIR__)) . '/src/resources/electron';
        }

        // 再次检查目录是否存在
        if (!is_dir($sourceDir)) {
            $output->writeln('<error>未找到 Electron 应用源文件。</error>');
            $output->writeln('<info>尝试过的路径：' . __DIR__ . '/../resources/electron</info>');
            $output->writeln('<info>尝试过的路径：' . dirname(dirname(__DIR__)) . '/src/resources/electron</info>');
            return;
        }

        $output->writeln('<info>使用源目录：' . $sourceDir . '</info>');
        $this->copyDirectory($sourceDir, $appPath);
        $output->writeln('<info>Electron 应用文件已复制</info>');
    }

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

    protected function getElectronPath(): string
    {
        $basePath = $this->app->getRootPath();

        if (PHP_OS_FAMILY === 'Windows') {
            return $basePath . 'vendor\bin\electron.cmd';
        }

        return $basePath . 'vendor/bin/electron';
    }

    protected function configurePermissions(Output $output)
    {
        $output->writeln('配置目录权限...');

        $paths = [
            $this->app->getRuntimePath(),
            $this->app->getRootPath() . 'vendor/nativephp/electron/app',
        ];

        foreach ($paths as $path) {
            if (is_dir($path)) {
                chmod($path, 0755);
                $output->writeln("<info>已设置目录权限: {$path}</info>");
            }
        }
    }
}