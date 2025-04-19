<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Clipboard;
use Native\ThinkPHP\Facades\Logger;

class ClipboardPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'clipboard';

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
    protected $description = '剪贴板插件';

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
     * 剪贴板监听器ID列表
     *
     * @var array
     */
    protected $listeners = [];

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
        // 加载配置的剪贴板监听器
        $this->loadClipboardListeners();

        // 监听剪贴板事件
        $this->app->event->listen('native.clipboard.change', function ($event) {
            $this->handleClipboardChange($event);
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
        Logger::info('Clipboard plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 移除所有剪贴板监听器
        $this->removeAllListeners();
    }

    /**
     * 加载配置的剪贴板监听器
     *
     * @return void
     */
    protected function loadClipboardListeners(): void
    {
        // 获取配置
        $config = config('native.clipboard', []);

        // 如果没有配置，则使用默认配置
        if (empty($config)) {
            return;
        }

        // 注册监听器
        if (isset($config['listeners']) && is_array($config['listeners'])) {
            foreach ($config['listeners'] as $listener) {
                if (isset($listener['callback']) && is_callable($listener['callback'])) {
                    $id = Clipboard::onChange($listener['callback']);
                    $this->listeners[] = $id;
                }
            }
        }
    }

    /**
     * 处理剪贴板变化事件
     *
     * @param array $event
     * @return void
     */
    protected function handleClipboardChange(array $event): void
    {
        // 获取配置
        $config = config('native.clipboard', []);

        // 如果配置了全局回调，则执行
        if (isset($config['on_change']) && is_callable($config['on_change'])) {
            call_user_func($config['on_change'], $event);
        }

        // 记录剪贴板变化
        if (isset($config['log_changes']) && $config['log_changes']) {
            $formats = Clipboard::formats();
            Logger::info('Clipboard content changed', [
                'formats' => $formats,
            ]);
        }
    }

    /**
     * 移除所有剪贴板监听器
     *
     * @return void
     */
    protected function removeAllListeners(): void
    {
        foreach ($this->listeners as $id) {
            Clipboard::offChange($id);
        }

        $this->listeners = [];
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 移除所有剪贴板监听器
        $this->removeAllListeners();
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
