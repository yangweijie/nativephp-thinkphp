<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static int run(string $command, array $options = []) 运行命令
 * @method static int runPhp(string $script, array $args = [], array $options = []) 运行 PHP 脚本
 * @method static int runThink(string $command, array $args = [], array $options = []) 运行 ThinkPHP 命令
 * @method static array|null get(int $processId) 获取进程信息
 * @method static array all() 获取所有进程
 * @method static string getOutput(int $processId) 获取进程输出
 * @method static string getError(int $processId) 获取进程错误
 * @method static int|null getExitCode(int $processId) 获取进程退出码
 * @method static bool isRunning(int $processId) 检查进程是否正在运行
 * @method static bool write(int $processId, string $input) 向进程发送输入
 * @method static bool signal(int $processId, string $signal) 向进程发送信号
 * @method static bool kill(int $processId, string $signal = 'SIGTERM') 终止进程
 * @method static bool wait(int $processId, int $timeout = 0) 等待进程结束
 * @method static bool on(int $processId, string $event, callable $callback) 设置进程事件回调
 * @method static int cleanup() 清理已结束的进程
 * @method static array|null getInfo(int $processId) 获取进程信息
 * @method static array getProcesses() 获取所有进程
 *
 * @see \Native\ThinkPHP\Process
 */
class Process extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.process';
    }
}
