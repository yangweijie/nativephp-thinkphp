<?php

namespace Native\ThinkPHP\Utils;

use think\App as ThinkApp;
use Native\ThinkPHP\Facades\FileSystem;

class Logger
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 日志文件路径
     *
     * @var string
     */
    protected $logFile;

    /**
     * 日志级别
     *
     * @var array
     */
    protected $levels = [
        'debug' => 0,
        'info' => 1,
        'notice' => 2,
        'warning' => 3,
        'error' => 4,
        'critical' => 5,
        'alert' => 6,
        'emergency' => 7,
    ];

    /**
     * 当前日志级别
     *
     * @var int
     */
    protected $currentLevel = 0;

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param string|null $logFile
     * @param string $level
     */
    public function __construct(ThinkApp $app, $logFile = null, $level = 'debug')
    {
        $this->app = $app;
        $this->logFile = $logFile ?: $this->getDefaultLogFile();
        $this->setLevel($level);

        // 确保日志目录存在
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            FileSystem::makeDirectory($dir, 0755, true);
        }
    }

    /**
     * 获取默认日志文件路径
     *
     * @return string
     */
    protected function getDefaultLogFile()
    {
        return $this->app->getRuntimePath() . 'logs/native_' . date('Y-m-d') . '.log';
    }

    /**
     * 设置日志级别
     *
     * @param string $level
     * @return $this
     */
    public function setLevel($level)
    {
        if (isset($this->levels[$level])) {
            $this->currentLevel = $this->levels[$level];
        }

        return $this;
    }

    /**
     * 设置日志文件路径
     *
     * @param string $logFile
     * @return $this
     */
    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;

        // 确保日志目录存在
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            FileSystem::makeDirectory($dir, 0755, true);
        }

        return $this;
    }

    /**
     * 获取日志文件路径
     *
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFile;
    }

    /**
     * 写入日志
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function log($level, $message, array $context = [])
    {
        if (!isset($this->levels[$level]) || $this->levels[$level] < $this->currentLevel) {
            return false;
        }

        $time = date('Y-m-d H:i:s');
        $levelUpper = strtoupper($level);

        // 替换上下文变量
        $message = $this->interpolate($message, $context);

        // 格式化日志内容
        $content = "[{$time}] [{$levelUpper}] {$message}";

        // 添加上下文信息
        if (!empty($context)) {
            $content .= ' ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        $content .= PHP_EOL;

        // 写入日志文件
        return FileSystem::append($this->logFile, $content);
    }

    /**
     * 替换上下文变量
     *
     * @param string $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = [])
    {
        // 构建替换数组
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val) || is_bool($val) || is_null($val)) {
                $replace['{' . $key . '}'] = $val;
            } elseif (is_array($val) || is_object($val)) {
                $replace['{' . $key . '}'] = json_encode($val);
            }
        }

        // 替换消息中的变量
        return strtr($message, $replace);
    }

    /**
     * 记录调试信息
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function debug($message, array $context = [])
    {
        return $this->log('debug', $message, $context);
    }

    /**
     * 记录信息
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function info($message, array $context = [])
    {
        return $this->log('info', $message, $context);
    }

    /**
     * 记录通知
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function notice($message, array $context = [])
    {
        return $this->log('notice', $message, $context);
    }

    /**
     * 记录警告
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function warning($message, array $context = [])
    {
        return $this->log('warning', $message, $context);
    }

    /**
     * 记录错误
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function error($message, array $context = [])
    {
        return $this->log('error', $message, $context);
    }

    /**
     * 记录严重错误
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function critical($message, array $context = [])
    {
        return $this->log('critical', $message, $context);
    }

    /**
     * 记录警报
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function alert($message, array $context = [])
    {
        return $this->log('alert', $message, $context);
    }

    /**
     * 记录紧急情况
     *
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function emergency($message, array $context = [])
    {
        return $this->log('emergency', $message, $context);
    }

    /**
     * 清空日志文件
     *
     * @return bool
     */
    public function clear()
    {
        return FileSystem::write($this->logFile, '');
    }

    /**
     * 获取日志内容
     *
     * @param int $lines
     * @return string
     */
    public function get($lines = 0)
    {
        if (!FileSystem::exists($this->logFile)) {
            return '';
        }

        $content = FileSystem::read($this->logFile);

        if ($lines <= 0) {
            return $content;
        }

        $contentLines = explode(PHP_EOL, $content);
        $contentLines = array_filter($contentLines);
        $contentLines = array_slice($contentLines, -$lines);

        return implode(PHP_EOL, $contentLines);
    }

    /**
     * 获取日志文件大小
     *
     * @return int
     */
    public function size()
    {
        if (!FileSystem::exists($this->logFile)) {
            return 0;
        }

        return FileSystem::size($this->logFile);
    }

    /**
     * 轮换日志文件
     *
     * @param int $maxSize
     * @param int $maxFiles
     * @return bool
     */
    public function rotate($maxSize = 10485760, $maxFiles = 5)
    {
        if (!FileSystem::exists($this->logFile) || $this->size() < $maxSize) {
            return false;
        }

        // 备份当前日志文件
        $backupFile = $this->logFile . '.' . date('YmdHis');
        FileSystem::copy($this->logFile, $backupFile);

        // 清空当前日志文件
        $this->clear();

        // 删除多余的备份文件
        $dir = dirname($this->logFile);
        $pattern = basename($this->logFile) . '.*';
        $files = glob($dir . '/' . $pattern);

        if (count($files) > $maxFiles) {
            // 按修改时间排序
            usort($files, function ($a, $b) {
                return filemtime($a) - filemtime($b);
            });

            // 删除最旧的文件
            $filesToDelete = array_slice($files, 0, count($files) - $maxFiles);
            foreach ($filesToDelete as $file) {
                FileSystem::delete($file);
            }
        }

        return true;
    }
}
