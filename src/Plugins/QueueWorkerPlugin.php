<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;

class QueueWorkerPlugin implements Plugin
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
        Logger::info('QueueWorker plugin initialized');

        // 监听队列工作进程事件
        $this->app->event->listen('native.queue_worker.started', function ($event) {
            $this->handleWorkerStarted($event);
        });

        $this->app->event->listen('native.queue_worker.stopped', function ($event) {
            $this->handleWorkerStopped($event);
        });

        $this->app->event->listen('native.queue_worker.restarted', function ($event) {
            $this->handleWorkerRestarted($event);
        });

        $this->app->event->listen('native.queue_worker.failed', function ($event) {
            $this->handleWorkerFailed($event);
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
        Logger::info('QueueWorker plugin started');

        // 启动自动队列工作进程
        $this->startAutoQueueWorkers();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('QueueWorker plugin quit');

        // 停止所有队列工作进程
        $this->stopAllQueueWorkers();
    }

    /**
     * 启动自动队列工作进程
     *
     * @return void
     */
    protected function startAutoQueueWorkers(): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了自动启动队列工作进程，则启动
        if (isset($config['auto_start']) && $config['auto_start']) {
            // 获取自动启动的队列工作进程
            $autoStartWorkers = $config['auto_start_workers'] ?? [];

            // 遍历自动启动的队列工作进程
            foreach ($autoStartWorkers as $worker) {
                // 获取队列工作进程参数
                $connection = $worker['connection'] ?? 'default';
                $queue = $worker['queue'] ?? 'default';
                $tries = $worker['tries'] ?? 3;
                $timeout = $worker['timeout'] ?? 60;
                $sleep = $worker['sleep'] ?? 3;
                $persistent = $worker['persistent'] ?? true;

                // 启动队列工作进程
                QueueWorker::up($connection, $queue, $tries, $timeout, $sleep, true, $persistent);
            }
        }
    }

    /**
     * 停止所有队列工作进程
     *
     * @return void
     */
    protected function stopAllQueueWorkers(): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了自动停止队列工作进程，则停止
        if (isset($config['auto_stop']) && $config['auto_stop']) {
            // 停止所有队列工作进程
            QueueWorker::downAll();
        }
    }

    /**
     * 处理队列工作进程启动事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWorkerStarted(array $event): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了记录队列工作进程事件，则记录
        if (isset($config['log_worker_events']) && $config['log_worker_events']) {
            Logger::info('Queue worker started', [
                'connection' => $event['connection'] ?? null,
                'queue' => $event['queue'] ?? null,
            ]);
        }

        // 如果配置了队列工作进程启动回调，则执行
        if (isset($config['on_worker_started']) && is_callable($config['on_worker_started'])) {
            call_user_func($config['on_worker_started'], $event);
        }
    }

    /**
     * 处理队列工作进程停止事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWorkerStopped(array $event): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了记录队列工作进程事件，则记录
        if (isset($config['log_worker_events']) && $config['log_worker_events']) {
            Logger::info('Queue worker stopped', [
                'connection' => $event['connection'] ?? null,
                'queue' => $event['queue'] ?? null,
            ]);
        }

        // 如果配置了队列工作进程停止回调，则执行
        if (isset($config['on_worker_stopped']) && is_callable($config['on_worker_stopped'])) {
            call_user_func($config['on_worker_stopped'], $event);
        }
    }

    /**
     * 处理队列工作进程重启事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWorkerRestarted(array $event): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了记录队列工作进程事件，则记录
        if (isset($config['log_worker_events']) && $config['log_worker_events']) {
            Logger::info('Queue worker restarted', [
                'connection' => $event['connection'] ?? null,
                'queue' => $event['queue'] ?? null,
            ]);
        }

        // 如果配置了队列工作进程重启回调，则执行
        if (isset($config['on_worker_restarted']) && is_callable($config['on_worker_restarted'])) {
            call_user_func($config['on_worker_restarted'], $event);
        }
    }

    /**
     * 处理队列工作进程失败事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWorkerFailed(array $event): void
    {
        // 获取配置
        $config = config('native.queue_worker', []);

        // 如果配置了记录队列工作进程事件，则记录
        if (isset($config['log_worker_events']) && $config['log_worker_events']) {
            Logger::info('Queue worker failed', [
                'connection' => $event['connection'] ?? null,
                'queue' => $event['queue'] ?? null,
                'error' => $event['error'] ?? null,
            ]);
        }

        // 如果配置了队列工作进程失败回调，则执行
        if (isset($config['on_worker_failed']) && is_callable($config['on_worker_failed'])) {
            call_user_func($config['on_worker_failed'], $event);
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
        Logger::info('QueueWorker plugin unloaded');
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
