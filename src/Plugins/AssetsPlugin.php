<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Assets;
use Native\ThinkPHP\Facades\Logger;

class AssetsPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'assets';

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
    protected $description = '资源管理插件';

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
        Logger::info('Assets plugin initialized');

        // 注册资源路由
        $this->registerAssetsRoutes();
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Assets plugin started');

        // 创建资源目录
        $this->createAssetsDirectory();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Assets plugin quit');
    }

    /**
     * 创建资源目录
     *
     * @return void
     */
    protected function createAssetsDirectory(): void
    {
        $assetsDirectory = $this->app->getRootPath() . 'resources/native/assets';

        if (!is_dir($assetsDirectory)) {
            mkdir($assetsDirectory, 0755, true);
            Logger::info('Assets directory created: ' . $assetsDirectory);
        }
    }

    /**
     * 注册资源路由
     *
     * @return void
     */
    protected function registerAssetsRoutes(): void
    {
        // 获取路由对象
        $route = $this->app->make('think\Route');

        // 注册资源路由
        $route->get('resources/native/assets/<path>', function ($path) {
            // 检查资源是否存在
            if (!Assets::exists($path)) {
                header('HTTP/1.0 404 Not Found');
                return 'Resource not found';
            }

            // 获取资源内容
            $content = Assets::get($path);

            // 获取资源 MIME 类型
            $mimeType = Assets::mimeType($path) ?: 'application/octet-stream';

            // 返回资源
            // 直接返回内容，并设置正确的 Content-Type
            header('Content-Type: ' . $mimeType);
            return $content;
        })->pattern(['path' => '.*']);
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('Assets plugin unloaded');
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
