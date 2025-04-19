<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Logger;

class FileSystemPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'filesystem';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '文件系统插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('FileSystem plugin initialized');

        // 监听文件系统事件
        $this->app->event->listen('native.filesystem.read', function ($event) {
            $this->handleFileSystemRead($event);
        });

        $this->app->event->listen('native.filesystem.write', function ($event) {
            $this->handleFileSystemWrite($event);
        });

        $this->app->event->listen('native.filesystem.delete', function ($event) {
            $this->handleFileSystemDelete($event);
        });

        $this->app->event->listen('native.filesystem.copy', function ($event) {
            $this->handleFileSystemCopy($event);
        });

        $this->app->event->listen('native.filesystem.move', function ($event) {
            $this->handleFileSystemMove($event);
        });

        $this->app->event->listen('native.filesystem.make-directory', function ($event) {
            $this->handleFileSystemMakeDirectory($event);
        });

        $this->app->event->listen('native.filesystem.delete-directory', function ($event) {
            $this->handleFileSystemDeleteDirectory($event);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('FileSystem plugin started');

        // 创建应用数据目录
        $this->createAppDataDirectory();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('FileSystem plugin quit');
    }

    /**
     * 创建应用数据目录
     *
     * @return void
     */
    protected function createAppDataDirectory(): void
    {
        // 获取配置
        $config = config('native.filesystem', []);
        $appDataPath = $config['app_data_path'] ?? null;

        if (!$appDataPath) {
            // 如果没有配置应用数据目录，则使用默认目录
            $appName = config('native.name', 'nativephp');
            $appName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $appName));
            $appDataPath = $this->getDefaultAppDataPath($appName);
        }

        // 创建应用数据目录
        if (!FileSystem::exists($appDataPath)) {
            FileSystem::makeDirectory($appDataPath, 0755, true);
            Logger::info('Created app data directory', ['path' => $appDataPath]);
        }

        // 将应用数据目录保存到配置中
        /** @phpstan-ignore-next-line */
        config(['native.filesystem.app_data_path' => $appDataPath]);
    }

    /**
     * 获取默认应用数据目录
     *
     * @param string $appName
     * @return string
     */
    protected function getDefaultAppDataPath($appName): string
    {
        // 获取系统用户数据目录
        $userDataPath = '';

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows
            $userDataPath = getenv('APPDATA');
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            // macOS
            $userDataPath = getenv('HOME') . '/Library/Application Support';
        } else {
            // Linux
            $userDataPath = getenv('HOME') . '/.config';
        }

        return $userDataPath . DIRECTORY_SEPARATOR . $appName;
    }

    /**
     * 处理文件系统读取事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemRead(array $event): void
    {
        // 记录文件读取
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('File read', [
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理文件系统写入事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemWrite(array $event): void
    {
        // 记录文件写入
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('File write', [
                'path' => $event['path'] ?? null,
                'size' => isset($event['content']) ? strlen($event['content']) : null,
            ]);
        }
    }

    /**
     * 处理文件系统删除事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemDelete(array $event): void
    {
        // 记录文件删除
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('File delete', [
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理文件系统复制事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemCopy(array $event): void
    {
        // 记录文件复制
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('File copy', [
                'source' => $event['source'] ?? null,
                'destination' => $event['destination'] ?? null,
            ]);
        }
    }

    /**
     * 处理文件系统移动事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemMove(array $event): void
    {
        // 记录文件移动
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('File move', [
                'source' => $event['source'] ?? null,
                'destination' => $event['destination'] ?? null,
            ]);
        }
    }

    /**
     * 处理文件系统创建目录事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemMakeDirectory(array $event): void
    {
        // 记录目录创建
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Directory create', [
                'path' => $event['path'] ?? null,
                'recursive' => $event['recursive'] ?? false,
            ]);
        }
    }

    /**
     * 处理文件系统删除目录事件
     *
     * @param array $event
     * @return void
     */
    protected function handleFileSystemDeleteDirectory(array $event): void
    {
        // 记录目录删除
        $config = config('native.filesystem', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Directory delete', [
                'path' => $event['path'] ?? null,
                'recursive' => $event['recursive'] ?? false,
            ]);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('FileSystem plugin unloaded');
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}
