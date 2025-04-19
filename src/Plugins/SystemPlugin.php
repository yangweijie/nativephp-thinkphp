<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Logger;

class SystemPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'system';

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
    protected $description = '系统信息插件';

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
        Logger::info('System plugin initialized');

        // 监听系统事件
        $this->app->event->listen('native.system.open_external', function ($event) {
            $this->handleOpenExternal($event);
        });

        $this->app->event->listen('native.system.open_path', function ($event) {
            $this->handleOpenPath($event);
        });

        $this->app->event->listen('native.system.show_item_in_folder', function ($event) {
            $this->handleShowItemInFolder($event);
        });

        $this->app->event->listen('native.system.move_item_to_trash', function ($event) {
            $this->handleMoveItemToTrash($event);
        });

        $this->app->event->listen('native.system.beep', function ($event) {
            $this->handleBeep($event);
        });

        $this->app->event->listen('native.system.power', function ($event) {
            $this->handlePower($event);
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
        Logger::info('System plugin started');

        // 记录系统信息
        $this->logSystemInfo();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('System plugin quit');
    }

    /**
     * 记录系统信息
     *
     * @return void
     */
    protected function logSystemInfo(): void
    {
        // 获取配置
        $config = config('native.system', []);
        if (isset($config['log_system_info']) && $config['log_system_info']) {
            // 获取系统信息
            $os = System::getOS();
            $osVersion = System::getOSVersion();
            $arch = System::getArch();
            $hostname = System::getHostname();
            $language = System::getLanguage();

            // 记录系统信息
            Logger::info('System information', [
                'os' => $os,
                'os_version' => $osVersion,
                'arch' => $arch,
                'hostname' => $hostname,
                'language' => $language,
            ]);

            // 记录硬件信息
            $memoryInfo = System::getMemoryInfo();
            $cpuInfo = System::getCPUInfo();
            $batteryInfo = System::getBatteryInfo();

            Logger::info('Hardware information', [
                'memory' => $memoryInfo,
                'cpu' => $cpuInfo,
                'battery' => $batteryInfo,
            ]);
        }
    }

    /**
     * 处理打开外部 URL 事件
     *
     * @param array $event
     * @return void
     */
    protected function handleOpenExternal(array $event): void
    {
        // 记录打开外部 URL
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Open external URL', [
                'url' => $event['url'] ?? '',
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理打开文件或目录事件
     *
     * @param array $event
     * @return void
     */
    protected function handleOpenPath(array $event): void
    {
        // 记录打开文件或目录
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Open path', [
                'path' => $event['path'] ?? '',
            ]);
        }
    }

    /**
     * 处理在文件管理器中显示文件事件
     *
     * @param array $event
     * @return void
     */
    protected function handleShowItemInFolder(array $event): void
    {
        // 记录在文件管理器中显示文件
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Show item in folder', [
                'path' => $event['path'] ?? '',
            ]);
        }
    }

    /**
     * 处理移动文件到回收站事件
     *
     * @param array $event
     * @return void
     */
    protected function handleMoveItemToTrash(array $event): void
    {
        // 记录移动文件到回收站
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Move item to trash', [
                'path' => $event['path'] ?? '',
            ]);
        }
    }

    /**
     * 处理播放系统提示音事件
     *
     * @param array $event
     * @return void
     */
    protected function handleBeep(array $event): void
    {
        // 记录播放系统提示音
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Beep', [
                'type' => $event['type'] ?? 'info',
            ]);
        }
    }

    /**
     * 处理系统电源操作事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePower(array $event): void
    {
        // 记录系统电源操作
        $config = config('native.system', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Power operation', [
                'operation' => $event['operation'] ?? '',
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
        Logger::info('System plugin unloaded');
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
