<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool up(string $connection = 'default', string $queue = 'default', int $tries = 3, int $timeout = 60, int $sleep = 3, bool $force = false, bool $persistent = true) 启动队列工作进程
 * @method static bool down(string $connection = 'default', string $queue = 'default') 停止队列工作进程
 * @method static bool restart(string $connection = 'default', string $queue = 'default', int $tries = 3, int $timeout = 60, int $sleep = 3, bool $persistent = true) 重启队列工作进程
 * @method static string|null status(string $connection = 'default', string $queue = 'default') 获取队列工作进程状态
 * @method static array all() 获取所有队列工作进程
 * @method static array|null get(string $connection = 'default', string $queue = 'default') 获取队列工作进程
 * @method static int cleanup() 清理所有队列工作进程
 * @method static int downAll() 停止所有队列工作进程
 * @method static int restartAll() 重启所有队列工作进程
 * @method static bool exists(string $connection = 'default', string $queue = 'default') 检查队列工作进程是否存在
 * @method static bool isRunning(string $connection = 'default', string $queue = 'default') 检查队列工作进程是否正在运行
 * @method static int|null getPid(string $connection = 'default', string $queue = 'default') 获取队列工作进程 PID
 * @method static string|null getOutput(string $connection = 'default', string $queue = 'default') 获取队列工作进程输出
 * @method static string|null getError(string $connection = 'default', string $queue = 'default') 获取队列工作进程错误
 * @method static int|null getExitCode(string $connection = 'default', string $queue = 'default') 获取队列工作进程退出码
 *
 * @see \Native\ThinkPHP\QueueWorker
 */
class QueueWorker extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.queue_worker';
    }
}
