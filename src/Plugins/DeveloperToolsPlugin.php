<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\DeveloperTools;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;

class DeveloperToolsPlugin implements Plugin
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
        Logger::info('DeveloperTools plugin initialized');
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('DeveloperTools plugin started');

        // 初始化开发者工具
        $this->initializeDeveloperTools();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('DeveloperTools plugin quit');

        // 禁用开发者工具
        DeveloperTools::disable();
    }

    /**
     * 初始化开发者工具
     *
     * @return void
     */
    protected function initializeDeveloperTools(): void
    {
        // 获取配置
        $config = config('native.developer', []);

        // 如果配置了显示开发者工具，则启用
        if (isset($config['show_devtools']) && $config['show_devtools']) {
            DeveloperTools::enable();
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
        Logger::info('DeveloperTools plugin unloaded');

        // 禁用开发者工具
        DeveloperTools::disable();
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
