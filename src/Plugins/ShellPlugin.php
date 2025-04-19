<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;

class ShellPlugin implements Plugin
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [
        'app.start' => 'onAppStart',
        'app.quit' => 'onAppQuit',
    ];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Shell plugin initialized');

        // 监听 Shell 事件
        $this->app->event->listen('native.shell.open_item', function ($event) {
            $this->handleOpenItem($event);
        });

        $this->app->event->listen('native.shell.show_item_in_folder', function ($event) {
            $this->handleShowItemInFolder($event);
        });

        $this->app->event->listen('native.shell.trash_item', function ($event) {
            $this->handleTrashItem($event);
        });

        $this->app->event->listen('native.shell.open_external', function ($event) {
            $this->handleOpenExternal($event);
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
        Logger::info('Shell plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Shell plugin quit');
    }

    /**
     * 处理打开文件事件
     *
     * @param array $event
     * @return void
     */
    protected function handleOpenItem(array $event): void
    {
        // 获取配置
        $config = config('native.shell', []);

        // 如果配置了记录 Shell 事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Shell open item', [
                'path' => $event['path'] ?? null,
            ]);
        }

        // 如果配置了打开文件回调，则执行
        if (isset($config['on_open_item']) && is_callable($config['on_open_item'])) {
            call_user_func($config['on_open_item'], $event);
        }
    }

    /**
     * 处理在文件夹中显示文件事件
     *
     * @param array $event
     * @return void
     */
    protected function handleShowItemInFolder(array $event): void
    {
        // 获取配置
        $config = config('native.shell', []);

        // 如果配置了记录 Shell 事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Shell show item in folder', [
                'path' => $event['path'] ?? null,
            ]);
        }

        // 如果配置了在文件夹中显示文件回调，则执行
        if (isset($config['on_show_item_in_folder']) && is_callable($config['on_show_item_in_folder'])) {
            call_user_func($config['on_show_item_in_folder'], $event);
        }
    }

    /**
     * 处理将文件移动到回收站事件
     *
     * @param array $event
     * @return void
     */
    protected function handleTrashItem(array $event): void
    {
        // 获取配置
        $config = config('native.shell', []);

        // 如果配置了记录 Shell 事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Shell trash item', [
                'path' => $event['path'] ?? null,
            ]);
        }

        // 如果配置了将文件移动到回收站回调，则执行
        if (isset($config['on_trash_item']) && is_callable($config['on_trash_item'])) {
            call_user_func($config['on_trash_item'], $event);
        }
    }

    /**
     * 处理使用外部程序打开 URL 事件
     *
     * @param array $event
     * @return void
     */
    protected function handleOpenExternal(array $event): void
    {
        // 获取配置
        $config = config('native.shell', []);

        // 如果配置了记录 Shell 事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Shell open external', [
                'url' => $event['url'] ?? null,
            ]);
        }

        // 如果配置了使用外部程序打开 URL 回调，则执行
        if (isset($config['on_open_external']) && is_callable($config['on_open_external'])) {
            call_user_func($config['on_open_external'], $event);
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
        Logger::info('Shell plugin unloaded');
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
