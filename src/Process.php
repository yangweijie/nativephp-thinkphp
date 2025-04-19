<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;

class Process
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 进程列表
     *
     * @var array
     */
    protected $processes = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
    }

    /**
     * 运行命令
     *
     * @param string $command 命令
     * @param array $options 选项
     * @return int 进程 ID
     */
    public function run($command, array $options = [])
    {
        // 默认选项
        $defaultOptions = [
            'cwd' => null,
            'env' => null,
            'detached' => false,
            'shell' => true,
            'windowsHide' => true,
            'stdio' => 'pipe',
        ];

        $options = array_merge($defaultOptions, $options);

        // 调用 NativePHP 客户端运行命令
        $client = $this->app->make('native.client');
        $response = $client->post('process/run', [
            'command' => $command,
            'options' => $options,
        ]);

        // 生成进程 ID
        $processId = $response->json('processId') ?? count($this->processes) + 1;
        $pid = $response->json('pid');

        // 保存进程信息
        $this->processes[$processId] = [
            'command' => $command,
            'options' => $options,
            'pid' => $pid,
            'status' => 'running',
            'output' => '',
            'error' => '',
            'exitCode' => null,
            'startTime' => time(),
        ];

        // 注册进程事件监听器
        $this->registerProcessEventListeners($processId);

        return $processId;
    }

    /**
     * 注册进程事件监听器
     *
     * @param int $processId 进程 ID
     * @return void
     */
    protected function registerProcessEventListeners($processId)
    {
        // 监听进程标准输出
        $this->app->event->listen('native.process.stdout', function ($event) use ($processId) {
            if ($event['processId'] === $processId) {
                $this->processes[$processId]['output'] .= $event['data'];
            }
        });

        // 监听进程标准错误
        $this->app->event->listen('native.process.stderr', function ($event) use ($processId) {
            if ($event['processId'] === $processId) {
                $this->processes[$processId]['error'] .= $event['data'];
            }
        });

        // 监听进程退出
        $this->app->event->listen('native.process.exit', function ($event) use ($processId) {
            if ($event['processId'] === $processId) {
                $this->processes[$processId]['status'] = 'exited';
                $this->processes[$processId]['exitCode'] = $event['code'];
            }
        });
    }

    /**
     * 运行 PHP 脚本
     *
     * @param string $script
     * @param array $args
     * @param array $options
     * @return int 进程 ID
     */
    public function runPhp($script, array $args = [], array $options = [])
    {
        $command = 'php ' . escapeshellarg($script);

        foreach ($args as $arg) {
            $command .= ' ' . escapeshellarg($arg);
        }

        return $this->run($command, $options);
    }

    /**
     * 运行 ThinkPHP 命令
     *
     * @param string $command
     * @param array $args
     * @param array $options
     * @return int 进程 ID
     */
    public function runThink($command, array $args = [], array $options = [])
    {
        $thinkPath = $this->app->getRootPath() . 'think';

        $command = 'php ' . escapeshellarg($thinkPath) . ' ' . $command;

        foreach ($args as $arg) {
            $command .= ' ' . escapeshellarg($arg);
        }

        return $this->run($command, $options);
    }

    /**
     * 获取进程信息
     *
     * @param int $processId
     * @return array|null
     */
    public function get($processId)
    {
        return isset($this->processes[$processId]) ? $this->processes[$processId] : null;
    }

    /**
     * 获取所有进程
     *
     * @return array
     */
    public function all()
    {
        return $this->processes;
    }

    /**
     * 获取进程输出
     *
     * @param int $processId
     * @return string
     */
    public function getOutput($processId)
    {
        return isset($this->processes[$processId]) ? $this->processes[$processId]['output'] : '';
    }

    /**
     * 获取进程错误
     *
     * @param int $processId
     * @return string
     */
    public function getError($processId)
    {
        return isset($this->processes[$processId]) ? $this->processes[$processId]['error'] : '';
    }

    /**
     * 获取进程退出码
     *
     * @param int $processId
     * @return int|null
     */
    public function getExitCode($processId)
    {
        return isset($this->processes[$processId]) ? $this->processes[$processId]['exitCode'] : null;
    }

    /**
     * 检查进程是否正在运行
     *
     * @param int $processId
     * @return bool
     */
    public function isRunning($processId)
    {
        return isset($this->processes[$processId]) && $this->processes[$processId]['status'] === 'running';
    }

    /**
     * 向进程发送输入
     *
     * @param int $processId 进程 ID
     * @param string $input 输入内容
     * @return bool
     */
    public function write($processId, $input)
    {
        if (!isset($this->processes[$processId]) || $this->processes[$processId]['status'] !== 'running') {
            return false;
        }

        $client = $this->app->make('native.client');
        $response = $client->post('process/write', [
            'processId' => $processId,
            'input' => $input,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 向进程发送信号
     *
     * @param int $processId 进程 ID
     * @param string $signal 信号，如 'SIGINT', 'SIGTERM'
     * @return bool
     */
    public function signal($processId, $signal)
    {
        if (!isset($this->processes[$processId]) || $this->processes[$processId]['status'] !== 'running') {
            return false;
        }

        $client = $this->app->make('native.client');
        $response = $client->post('process/signal', [
            'processId' => $processId,
            'signal' => $signal,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 终止进程
     *
     * @param int $processId 进程 ID
     * @param string $signal 信号，默认为 'SIGTERM'
     * @return bool
     */
    public function kill($processId, $signal = 'SIGTERM')
    {
        if (!isset($this->processes[$processId]) || $this->processes[$processId]['status'] !== 'running') {
            return false;
        }

        $client = $this->app->make('native.client');
        $response = $client->post('process/kill', [
            'processId' => $processId,
            'signal' => $signal,
        ]);

        if ($response->json('success')) {
            $this->processes[$processId]['status'] = 'killed';
            return true;
        }

        return false;
    }

    /**
     * 等待进程结束
     *
     * @param int $processId 进程 ID
     * @param int $timeout 超时时间（秒），0 表示无限等待
     * @return bool
     */
    public function wait($processId, $timeout = 0)
    {
        if (!isset($this->processes[$processId])) {
            return false;
        }

        if ($this->processes[$processId]['status'] !== 'running') {
            return true;
        }

        $client = $this->app->make('native.client');
        $response = $client->post('process/wait', [
            'processId' => $processId,
            'timeout' => $timeout,
        ]);

        if ($response->json('success')) {
            // 更新进程状态
            if ($response->json('exited')) {
                $this->processes[$processId]['status'] = 'exited';
                $this->processes[$processId]['exitCode'] = $response->json('exitCode');
            }

            return $response->json('exited');
        }

        return false;
    }

    /**
     * 设置进程事件回调
     *
     * @param int $processId 进程 ID
     * @param string $event 事件类型，如 'stdout', 'stderr', 'exit'
     * @param callable $callback 回调函数
     * @return bool
     */
    public function on($processId, $event, $callback)
    {
        if (!isset($this->processes[$processId])) {
            return false;
        }

        $eventName = 'native.process.' . $event;

        $this->app->event->listen($eventName, function ($eventData) use ($processId, $callback) {
            if ($eventData['processId'] === $processId) {
                call_user_func($callback, $eventData);
            }
        });

        return true;
    }

    /**
     * 清理已结束的进程
     *
     * @return int 清理的进程数量
     */
    public function cleanup()
    {
        $count = 0;

        foreach ($this->processes as $processId => $process) {
            if ($process['status'] !== 'running') {
                unset($this->processes[$processId]);
                $count++;
            }
        }

        return $count;
    }

    // 重复的方法已删除

    /**
     * 获取进程信息
     *
     * @param int $processId 进程 ID
     * @return array|null
     */
    public function getInfo($processId)
    {
        return $this->processes[$processId] ?? null;
    }

    /**
     * 获取所有进程
     *
     * @return array
     */
    public function getProcesses()
    {
        return $this->processes;
    }
}
