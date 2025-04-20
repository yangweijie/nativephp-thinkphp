<?php
declare(strict_types=1);

namespace NativePHP\Think\Commands;

use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\Config;
use Symfony\Component\Process\Process;

class BuildCommand extends Command
{
    protected function configure()
    {
        $this->setName('native:build')
            ->setDescription('构建桌面应用')
            ->addOption('platform', null, Option::VALUE_OPTIONAL, '构建平台(windows/macos/linux)', PHP_OS_FAMILY)
            ->addOption('arch', null, Option::VALUE_OPTIONAL, '构建架构(x64/arm64)', php_uname('m'))
            ->addOption('target', null, Option::VALUE_OPTIONAL, '构建目标(development/production)', 'production')
            ->addOption('no-sign', null, Option::VALUE_NONE, '跳过代码签名')
            ->addOption('output', null, Option::VALUE_OPTIONAL, '输出目录')
            ->addOption('debug', 'd', Option::VALUE_NONE, '构建调试版本')
            ->addOption('release', 'r', Option::VALUE_NONE, '构建发布版本')
            ->addOption('universal', 'u', Option::VALUE_NONE, 'macOS: 构建通用二进制文件')
            ->addOption('updater', null, Option::VALUE_NONE, '启用自动更新')
            ->addOption('icon', null, Option::VALUE_OPTIONAL, '自定义应用图标路径')
            ->addOption('resources', null, Option::VALUE_OPTIONAL, '额外资源目录路径')
            ->addOption('compress', 'c', Option::VALUE_NONE, '启用资源压缩')
            ->addOption('minify', 'm', Option::VALUE_NONE, '压缩 JS/CSS 文件')
            ->addOption('static-dir', null, Option::VALUE_OPTIONAL, '静态资源目录', 'public');
    }

    protected function execute(Input $input, Output $output)
    {
        // 验证和准备构建环境
        if (!$this->checkBuildEnvironment($output)) {
            return 1;
        }

        // 验证参数
        if ($input->getOption('debug') && $input->getOption('release')) {
            $output->writeln('<error>不能同时指定 debug 和 release 选项</error>');
            return 1;
        }

        // 准备构建目录
        $buildDir = $this->prepareBuildDirectory($input, $output);
        
        // 复制资源
        $this->copyApplicationResources($input, $output, $buildDir);
        
        // 处理静态资源
        $this->processStaticAssets($buildDir, $input, $output);

        // 生成构建配置
        $this->generateBuildConfig($input, $output, $buildDir);
        
        // 执行构建
        return $this->runBuild($input, $output, $buildDir);
    }

    public function checkBuildEnvironment(Output $output): bool
    {
        $requirements = [
            'node' => 'Node.js',
            'npm' => 'NPM',
            'cargo' => 'Rust/Tauri'
        ];
        
        foreach ($requirements as $cmd => $name) {
            $process = new Process([$cmd, '--version']);
            $process->run();
            
            if (!$process->isSuccessful()) {
                $output->writeln("<error>{$name} 未安装，请先安装 {$name}</error>");
                return false;
            }
        }
        
        return true;
    }

    protected function prepareBuildDirectory(Input $input, Output $output): string
    {
        $buildDir = $input->getOption('output') ?? $this->app->getRuntimePath() . 'build';
        
        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }
        
        return $buildDir;
    }

    public function generateBuildConfig(Input $input, Output $output, string $buildDir): void
    {
        $config = [
            'build' => [
                'platform' => $input->getOption('platform'),
                'arch' => $input->getOption('arch'),
                'target' => $input->getOption('target'),
                'sign' => !$input->getOption('no-sign'),
                'debug' => $input->getOption('debug'),
                'release' => $input->getOption('release') ?? true,
                'universal' => $input->getOption('universal'),
                'updater' => $input->getOption('updater'),
            ],
            'app' => Config::get('native.app'),
            'window' => Config::get('native.window'),
            'updater' => Config::get('native.updater'),
        ];

        file_put_contents(
            $buildDir . '/build.json',
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
    }

    protected function copyApplicationResources(Input $input, Output $output, string $buildDir): void
    {
        // 复制基础资源
        $this->copyResources($buildDir);
        
        // 复制图标
        if ($iconPath = $input->getOption('icon')) {
            if (file_exists($iconPath)) {
                copy($iconPath, $buildDir . '/resources/icon.' . pathinfo($iconPath, PATHINFO_EXTENSION));
                $output->writeln('<info>已复制应用图标</info>');
            }
        }
        
        // 复制额外资源
        if ($resourcesPath = $input->getOption('resources')) {
            if (is_dir($resourcesPath)) {
                $this->copyDirectory($resourcesPath, $buildDir . '/resources');
                $output->writeln('<info>已复制额外资源</info>');
            }
        }
    }

    protected function processStaticAssets(string $buildDir, Input $input, Output $output): void 
    {
        $staticDir = $input->getOption('static-dir');
        $shouldCompress = $input->getOption('compress');
        $shouldMinify = $input->getOption('minify');

        if (!is_dir($staticDir)) {
            return;
        }

        $output->writeln('<info>处理静态资源...</info>');

        // 复制静态资源
        $this->copyDirectory($staticDir, $buildDir . '/resources/static');

        if ($shouldMinify) {
            $this->minifyAssets($buildDir . '/resources/static', $output);
        }

        if ($shouldCompress) {
            $this->compressAssets($buildDir . '/resources/static', $output);
        }
    }

    protected function minifyAssets(string $dir, Output $output): void
    {
        $process = new Process(['npm', 'install', 'terser', 'clean-css-cli', '--save-dev']);
        $process->run();

        // 压缩 JS 文件
        $jsFiles = glob($dir . '/**/*.js');
        foreach ($jsFiles as $file) {
            $process = new Process([
                'npx',
                'terser',
                $file,
                '--compress',
                '--mangle',
                '--output',
                $file
            ]);
            $process->run();
        }

        // 压缩 CSS 文件
        $cssFiles = glob($dir . '/**/*.css');
        foreach ($cssFiles as $file) {
            $process = new Process([
                'npx',
                'cleancss',
                '-o',
                $file,
                $file
            ]);
            $process->run();
        }

        $output->writeln('<info>资源压缩完成</info>');
    }

    protected function compressAssets(string $dir, Output $output): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $path = $file->getRealPath();
                if (pathinfo($path, PATHINFO_EXTENSION) !== 'gz') {
                    $compressed = gzencode(file_get_contents($path), 9);
                    file_put_contents($path . '.gz', $compressed);
                }
            }
        }

        $output->writeln('<info>资源压缩打包完成</info>');
    }

    protected function runBuild(Input $input, Output $output, string $buildDir): int
    {
        $platform = $input->getOption('platform');
        $target = $input->getOption('target');
        $universal = $input->getOption('universal');
        
        // 构建命令参数
        $args = ['cargo', 'tauri', 'build'];
        
        if ($target === 'development' || $input->getOption('debug')) {
            $args[] = '--debug';
        }
        
        // 处理目标平台
        if ($platform) {
            $args[] = '--target';
            $args[] = $this->getTargetTriple($platform);
        }
        
        // 处理 macOS 通用二进制
        if ($universal && $platform === 'macos') {
            $args[] = '--target';
            $args[] = 'universal-apple-darwin';
        }
        
        // 环境变量
        $env = array_merge($_ENV, [
            'RUST_BACKTRACE' => '1',
            'TAURI_DEPS_DIR' => $this->app->getRuntimePath() . 'tauri-deps',
            'TAURI_UPDATER_ENABLED' => $input->getOption('updater') ? '1' : '0'
        ]);

        // 执行构建
        $process = new Process($args, $buildDir, $env);
        $process->setTimeout(null);
        
        $process->run(function ($type, $buffer) use ($output) {
            $output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $this->handleBuildSuccess($input, $output, $buildDir);
            return 0;
        }

        $output->writeln('<error>构建失败</error>');
        $output->writeln($process->getErrorOutput());
        return 1;
    }

    public function getTargetTriple(string $platform): string
    {
        return match ($platform) {
            'windows' => 'x86_64-pc-windows-msvc',
            'macos' => 'x86_64-apple-darwin',
            'linux' => 'x86_64-unknown-linux-gnu',
            default => throw new \InvalidArgumentException('不支持的构建平台: ' . $platform),
        };
    }

    protected function handleBuildSuccess(Input $input, Output $output, string $buildDir): void
    {
        $output->writeln('<info>构建完成</info>');
        
        $platform = $input->getOption('platform');
        if ($platform && $buildDir) {
            $pattern = match ($platform) {
                'windows' => '*.exe',
                'macos' => '*.app',
                'linux' => '*.AppImage',
                default => '*'
            };
            
            $files = glob($buildDir . '/target/release/' . $pattern);
            if (!empty($files)) {
                $output->writeln('<info>构建产物：</info>');
                foreach ($files as $file) {
                    $output->writeln("- " . basename($file));
                }
            }
        }
    }

    protected function copyResources(string $buildDir): void
    {
        $resourcesPath = __DIR__ . '/../resources/electron';
        if (is_dir($resourcesPath)) {
            $this->copyDirectory($resourcesPath, $buildDir . '/resources');
        }
    }

    protected function copyDirectory(string $source, string $destination): void
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