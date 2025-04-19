<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Utils\Logger setLevel(string $level) 设置日志级别
 * @method static \Native\ThinkPHP\Utils\Logger setLogFile(string $logFile) 设置日志文件路径
 * @method static string getLogFile() 获取日志文件路径
 * @method static bool log(string $level, string $message, array $context = []) 写入日志
 * @method static bool debug(string $message, array $context = []) 记录调试信息
 * @method static bool info(string $message, array $context = []) 记录信息
 * @method static bool notice(string $message, array $context = []) 记录通知
 * @method static bool warning(string $message, array $context = []) 记录警告
 * @method static bool error(string $message, array $context = []) 记录错误
 * @method static bool critical(string $message, array $context = []) 记录严重错误
 * @method static bool alert(string $message, array $context = []) 记录警报
 * @method static bool emergency(string $message, array $context = []) 记录紧急情况
 * @method static bool clear() 清空日志文件
 * @method static string get(int $lines = 0) 获取日志内容
 * @method static int size() 获取日志文件大小
 * @method static bool rotate(int $maxSize = 10485760, int $maxFiles = 5) 轮换日志文件
 * 
 * @see \Native\ThinkPHP\Utils\Logger
 */
class Logger extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.logger';
    }
}
