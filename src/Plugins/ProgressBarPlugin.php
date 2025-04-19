<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\ProgressBar;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;

class ProgressBarPlugin implements Plugin
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
        Logger::info('ProgressBar plugin initialized');

        // 监听进度条事件
        $this->app->event->listen('native.progress_bar.start', function ($event) {
            $this->handleProgressBarStart($event);
        });

        $this->app->event->listen('native.progress_bar.advance', function ($event) {
            $this->handleProgressBarAdvance($event);
        });

        $this->app->event->listen('native.progress_bar.finish', function ($event) {
            $this->handleProgressBarFinish($event);
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
        Logger::info('ProgressBar plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('ProgressBar plugin quit');
    }

    /**
     * 处理进度条开始事件
     *
     * @param array $event
     * @return void
     */
    protected function handleProgressBarStart(array $event): void
    {
        // 获取配置
        $config = config('native.progress_bar', []);

        // 如果配置了记录进度条事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Progress bar started', [
                'id' => $event['id'] ?? null,
                'max_steps' => $event['max_steps'] ?? null,
            ]);
        }

        // 如果配置了进度条开始回调，则执行
        if (isset($config['on_start']) && is_callable($config['on_start'])) {
            call_user_func($config['on_start'], $event);
        }
    }

    /**
     * 处理进度条前进事件
     *
     * @param array $event
     * @return void
     */
    protected function handleProgressBarAdvance(array $event): void
    {
        // 获取配置
        $config = config('native.progress_bar', []);

        // 如果配置了记录进度条事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Progress bar advanced', [
                'id' => $event['id'] ?? null,
                'step' => $event['step'] ?? null,
                'percent' => $event['percent'] ?? null,
            ]);
        }

        // 如果配置了进度条前进回调，则执行
        if (isset($config['on_advance']) && is_callable($config['on_advance'])) {
            call_user_func($config['on_advance'], $event);
        }
    }

    /**
     * 处理进度条完成事件
     *
     * @param array $event
     * @return void
     */
    protected function handleProgressBarFinish(array $event): void
    {
        // 获取配置
        $config = config('native.progress_bar', []);

        // 如果配置了记录进度条事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Progress bar finished', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了进度条完成回调，则执行
        if (isset($config['on_finish']) && is_callable($config['on_finish'])) {
            call_user_func($config['on_finish'], $event);
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
        Logger::info('ProgressBar plugin unloaded');
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
