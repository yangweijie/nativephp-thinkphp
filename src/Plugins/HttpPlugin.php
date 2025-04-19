<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Logger;

class HttpPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'http';

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
    protected $description = 'HTTP 客户端插件';

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
        Logger::info('Http plugin initialized');

        // 监听 HTTP 请求事件
        $this->app->event->listen('native.http.request', function ($event) {
            $this->handleHttpRequest($event);
        });

        $this->app->event->listen('native.http.response', function ($event) {
            $this->handleHttpResponse($event);
        });

        $this->app->event->listen('native.http.error', function ($event) {
            $this->handleHttpError($event);
        });

        $this->app->event->listen('native.http.download', function ($event) {
            $this->handleHttpDownload($event);
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
        Logger::info('Http plugin started');

        // 应用 HTTP 配置
        $this->applyHttpConfig();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Http plugin quit');
    }

    /**
     * 应用 HTTP 配置
     *
     * @return void
     */
    protected function applyHttpConfig(): void
    {
        // 获取配置
        $config = config('native.http', []);

        // 设置默认超时
        if (isset($config['timeout'])) {
            /** @phpstan-ignore-next-line */
            Http::timeout($config['timeout']);
        }

        // 设置是否验证 SSL 证书
        if (isset($config['verify'])) {
            /** @phpstan-ignore-next-line */
            Http::verify($config['verify']);
        }

        // 设置默认请求头
        if (isset($config['headers']) && is_array($config['headers'])) {
            foreach ($config['headers'] as $key => $value) {
                /** @phpstan-ignore-next-line */
                Http::withHeader($key, $value);
            }
        }

        // 设置默认认证
        if (isset($config['auth'])) {
            if (isset($config['auth']['type']) && $config['auth']['type'] === 'basic') {
                /** @phpstan-ignore-next-line */
                Http::withBasicAuth(
                    $config['auth']['username'] ?? '',
                    $config['auth']['password'] ?? ''
                );
            } elseif (isset($config['auth']['type']) && $config['auth']['type'] === 'bearer') {
                /** @phpstan-ignore-next-line */
                Http::withToken($config['auth']['token'] ?? '');
            }
        }
    }

    /**
     * 处理 HTTP 请求事件
     *
     * @param array $event
     * @return void
     */
    protected function handleHttpRequest(array $event): void
    {
        // 记录 HTTP 请求
        $config = config('native.http', []);
        if (isset($config['log_requests']) && $config['log_requests']) {
            Logger::info('HTTP request', [
                'method' => $event['method'] ?? 'GET',
                'url' => $event['url'] ?? '',
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理 HTTP 响应事件
     *
     * @param array $event
     * @return void
     */
    protected function handleHttpResponse(array $event): void
    {
        // 记录 HTTP 响应
        $config = config('native.http', []);
        if (isset($config['log_responses']) && $config['log_responses']) {
            Logger::info('HTTP response', [
                'status_code' => $event['status_code'] ?? 0,
                'url' => $event['url'] ?? '',
                'method' => $event['method'] ?? 'GET',
            ]);
        }
    }

    /**
     * 处理 HTTP 错误事件
     *
     * @param array $event
     * @return void
     */
    protected function handleHttpError(array $event): void
    {
        // 记录 HTTP 错误
        Logger::error('HTTP error', [
            'error' => $event['error'] ?? '',
            'url' => $event['url'] ?? '',
            'method' => $event['method'] ?? 'GET',
        ]);
    }

    /**
     * 处理 HTTP 下载事件
     *
     * @param array $event
     * @return void
     */
    protected function handleHttpDownload(array $event): void
    {
        // 记录 HTTP 下载
        $config = config('native.http', []);
        if (isset($config['log_downloads']) && $config['log_downloads']) {
            Logger::info('HTTP download', [
                'url' => $event['url'] ?? '',
                'save_path' => $event['save_path'] ?? '',
                'success' => $event['success'] ?? false,
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
        Logger::info('Http plugin unloaded');
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
