<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\ChildProcess start(string|array $cmd, string $alias, string|null $cwd = null, bool $persistent = false, array $env = []) 启动子进程
 * @method static array|null get(string $alias) 获取子进程
 * @method static array all() 获取所有子进程
 * @method static bool stop(string $alias) 停止子进程
 * @method static bool restart(string $alias) 重启子进程
 * @method static bool message(string $message, string $alias) 向子进程发送消息
 * @method static \Native\ThinkPHP\ChildProcess php(string $script, string $alias, array $args = [], string|null $cwd = null, bool $persistent = false, array $env = []) 运行 PHP 脚本
 * @method static \Native\ThinkPHP\ChildProcess artisan(string $command, string $alias, array $args = [], string|null $cwd = null, bool $persistent = false, array $env = []) 运行 ThinkPHP 命令
 * @method static bool exists(string $alias) 检查子进程是否存在
 * @method static bool isRunning(string $alias) 检查子进程是否正在运行
 * @method static int|null getPid(string $alias) 获取子进程 PID
 * @method static string|null getStatus(string $alias) 获取子进程状态
 * @method static string|null getOutput(string $alias) 获取子进程输出
 * @method static string|null getError(string $alias) 获取子进程错误
 * @method static int|null getExitCode(string $alias) 获取子进程退出码
 * @method static int cleanup() 清理已停止的子进程
 *
 * @see \Native\ThinkPHP\ChildProcess
 */
class ChildProcess extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.child_process';
    }
}
