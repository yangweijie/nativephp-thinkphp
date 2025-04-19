<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Contracts\Plugin;
use Native\ThinkPHP\Events\ChildProcess\ProcessSpawned;
use Native\ThinkPHP\Events\ChildProcess\ProcessExited;
use Native\ThinkPHP\Events\ChildProcess\MessageReceived;
use Native\ThinkPHP\Events\ChildProcess\ErrorReceived;

class ChildProcessPlugin implements Plugin
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
        Logger::info('ChildProcess plugin initialized');

        // 监听子进程事件
        $this->app->event->listen('native.child_process.spawned', function ($event) {
            $this->handleProcessSpawned($event);
        });

        $this->app->event->listen('native.child_process.exited', function ($event) {
            $this->handleProcessExited($event);
        });

        $this->app->event->listen('native.child_process.message', function ($event) {
            $this->handleMessageReceived($event);
        });

        $this->app->event->listen('native.child_process.error', function ($event) {
            $this->handleErrorReceived($event);
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
        Logger::info('ChildProcess plugin started');

        // 恢复持久化的子进程
        $this->restorePersistentProcesses();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('ChildProcess plugin quit');

        // 清理非持久化的子进程
        $this->cleanupNonPersistentProcesses();
    }

    /**
     * 恢复持久化的子进程
     *
     * @return void
     */
    protected function restorePersistentProcesses(): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了自动恢复持久化的子进程，则恢复
        if (isset($config['auto_restore_persistent']) && $config['auto_restore_persistent']) {
            // 获取所有子进程
            $processes = ChildProcess::all();

            // 遍历所有子进程
            foreach ($processes as $alias => $process) {
                // 如果子进程是持久化的且没有运行，则重启
                if (isset($process['persistent']) && $process['persistent'] && (!isset($process['status']) || $process['status'] !== 'running')) {
                    ChildProcess::restart($alias);
                }
            }
        }
    }

    /**
     * 清理非持久化的子进程
     *
     * @return void
     */
    protected function cleanupNonPersistentProcesses(): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了自动清理非持久化的子进程，则清理
        if (isset($config['auto_cleanup_non_persistent']) && $config['auto_cleanup_non_persistent']) {
            // 获取所有子进程
            $processes = ChildProcess::all();

            // 遍历所有子进程
            foreach ($processes as $alias => $process) {
                // 如果子进程不是持久化的且正在运行，则停止
                if (isset($process['persistent']) && !$process['persistent'] && isset($process['status']) && $process['status'] === 'running') {
                    ChildProcess::stop($alias);
                }
            }
        }
    }

    /**
     * 处理进程启动事件
     *
     * @param array $event
     * @return void
     */
    protected function handleProcessSpawned(array $event): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了记录子进程事件，则记录
        if (isset($config['log_process_events']) && $config['log_process_events']) {
            Logger::info('Process spawned', [
                'alias' => $event['alias'] ?? null,
                'pid' => $event['pid'] ?? null,
            ]);
        }

        // 触发进程启动事件
        $this->app->event->trigger(new ProcessSpawned(
            $event['alias'] ?? null,
            $event['pid'] ?? null
        ));
    }

    /**
     * 处理进程退出事件
     *
     * @param array $event
     * @return void
     */
    protected function handleProcessExited(array $event): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了记录子进程事件，则记录
        if (isset($config['log_process_events']) && $config['log_process_events']) {
            Logger::info('Process exited', [
                'alias' => $event['alias'] ?? null,
                'code' => $event['code'] ?? null,
            ]);
        }

        // 触发进程退出事件
        $this->app->event->trigger(new ProcessExited(
            $event['alias'] ?? null,
            $event['code'] ?? null
        ));
    }

    /**
     * 处理消息接收事件
     *
     * @param array $event
     * @return void
     */
    protected function handleMessageReceived(array $event): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了记录子进程事件，则记录
        if (isset($config['log_process_events']) && $config['log_process_events']) {
            Logger::info('Message received', [
                'alias' => $event['alias'] ?? null,
                'data' => $event['data'] ?? null,
            ]);
        }

        // 触发消息接收事件
        $this->app->event->trigger(new MessageReceived(
            $event['alias'] ?? null,
            $event['data'] ?? null
        ));
    }

    /**
     * 处理错误接收事件
     *
     * @param array $event
     * @return void
     */
    protected function handleErrorReceived(array $event): void
    {
        // 获取配置
        $config = config('native.child_process', []);

        // 如果配置了记录子进程事件，则记录
        if (isset($config['log_process_events']) && $config['log_process_events']) {
            Logger::info('Error received', [
                'alias' => $event['alias'] ?? null,
                'data' => $event['data'] ?? null,
            ]);
        }

        // 触发错误接收事件
        $this->app->event->trigger(new ErrorReceived(
            $event['alias'] ?? null,
            $event['data'] ?? null
        ));
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('ChildProcess plugin unloaded');
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
