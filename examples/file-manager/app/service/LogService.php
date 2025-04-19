<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;

class LogService
{
    /**
     * 日志级别
     */
    const LEVEL_INFO = 'INFO';
    const LEVEL_WARNING = 'WARNING';
    const LEVEL_ERROR = 'ERROR';
    const LEVEL_DEBUG = 'DEBUG';
    
    /**
     * 日志文件路径
     *
     * @var string
     */
    protected $logFile;
    
    /**
     * 是否启用日志
     *
     * @var bool
     */
    protected $enabled = true;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $appDataPath = $this->getAppDataPath();
        $this->logFile = $appDataPath . DIRECTORY_SEPARATOR . 'file_manager.log';
        
        // 确保日志目录存在
        if (!is_dir($appDataPath)) {
            FileSystem::makeDirectory($appDataPath, 0755, true);
        }
    }
    
    /**
     * 获取应用数据目录
     *
     * @return string
     */
    protected function getAppDataPath()
    {
        $homePath = System::getHomePath();
        return $homePath . DIRECTORY_SEPARATOR . '.nativephp' . DIRECTORY_SEPARATOR . 'file-manager';
    }
    
    /**
     * 写入日志
     *
     * @param string $level 日志级别
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @return bool
     */
    public function log($level, $message, array $context = [])
    {
        if (!$this->enabled) {
            return false;
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextString = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextString}" . PHP_EOL;
        
        try {
            if (FileSystem::exists($this->logFile)) {
                return FileSystem::append($this->logFile, $logEntry);
            } else {
                return FileSystem::write($this->logFile, $logEntry);
            }
        } catch (\Exception $e) {
            // 如果写入日志失败，不应该抛出异常，而是静默失败
            return false;
        }
    }
    
    /**
     * 记录信息日志
     *
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @return bool
     */
    public function info($message, array $context = [])
    {
        return $this->log(self::LEVEL_INFO, $message, $context);
    }
    
    /**
     * 记录警告日志
     *
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @return bool
     */
    public function warning($message, array $context = [])
    {
        return $this->log(self::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * 记录错误日志
     *
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @return bool
     */
    public function error($message, array $context = [])
    {
        return $this->log(self::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * 记录调试日志
     *
     * @param string $message 日志消息
     * @param array $context 上下文数据
     * @return bool
     */
    public function debug($message, array $context = [])
    {
        return $this->log(self::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * 清除日志
     *
     * @return bool
     */
    public function clear()
    {
        if (FileSystem::exists($this->logFile)) {
            return FileSystem::delete($this->logFile);
        }
        
        return true;
    }
    
    /**
     * 获取日志内容
     *
     * @param int $lines 获取的行数，0表示获取全部
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
        
        $logLines = explode(PHP_EOL, $content);
        $logLines = array_filter($logLines);
        $logLines = array_slice($logLines, -$lines);
        
        return implode(PHP_EOL, $logLines);
    }
    
    /**
     * 启用日志
     *
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }
    
    /**
     * 禁用日志
     *
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
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
}
