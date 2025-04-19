<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Notification;
use think\facade\Event;

class WatchService
{
    /**
     * 监视配置文件路径
     *
     * @var string
     */
    protected $configFile;
    
    /**
     * 监视配置
     *
     * @var array
     */
    protected $config = [];
    
    /**
     * 是否启用监视
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
        $this->configFile = $appDataPath . DIRECTORY_SEPARATOR . 'watch.json';
        
        // 确保目录存在
        if (!is_dir($appDataPath)) {
            FileSystem::makeDirectory($appDataPath, 0755, true);
        }
        
        // 加载配置
        $this->loadConfig();
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
     * 加载配置
     *
     * @return void
     */
    protected function loadConfig()
    {
        if (FileSystem::exists($this->configFile)) {
            $content = FileSystem::read($this->configFile);
            $data = json_decode($content, true);
            
            if (is_array($data)) {
                $this->config = $data;
            }
        } else {
            // 创建默认配置
            $this->config = [
                'enabled' => true,
                'notify' => true,
                'autoRefresh' => true,
                'watchPaths' => [],
            ];
            
            $this->saveConfig();
        }
        
        $this->enabled = $this->config['enabled'] ?? true;
    }
    
    /**
     * 保存配置
     *
     * @return bool
     */
    protected function saveConfig()
    {
        $content = json_encode($this->config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return FileSystem::write($this->configFile, $content);
    }
    
    /**
     * 启用监视
     *
     * @return bool
     */
    public function enable()
    {
        $this->enabled = true;
        $this->config['enabled'] = true;
        return $this->saveConfig();
    }
    
    /**
     * 禁用监视
     *
     * @return bool
     */
    public function disable()
    {
        $this->enabled = false;
        $this->config['enabled'] = false;
        return $this->saveConfig();
    }
    
    /**
     * 启用通知
     *
     * @return bool
     */
    public function enableNotify()
    {
        $this->config['notify'] = true;
        return $this->saveConfig();
    }
    
    /**
     * 禁用通知
     *
     * @return bool
     */
    public function disableNotify()
    {
        $this->config['notify'] = false;
        return $this->saveConfig();
    }
    
    /**
     * 启用自动刷新
     *
     * @return bool
     */
    public function enableAutoRefresh()
    {
        $this->config['autoRefresh'] = true;
        return $this->saveConfig();
    }
    
    /**
     * 禁用自动刷新
     *
     * @return bool
     */
    public function disableAutoRefresh()
    {
        $this->config['autoRefresh'] = false;
        return $this->saveConfig();
    }
    
    /**
     * 添加监视路径
     *
     * @param string $path 路径
     * @param bool $recursive 是否递归监视子目录
     * @return bool
     */
    public function addWatchPath($path, $recursive = false)
    {
        if (!FileSystem::exists($path)) {
            return false;
        }
        
        // 检查是否已经监视
        foreach ($this->config['watchPaths'] as $watchPath) {
            if ($watchPath['path'] === $path) {
                return true; // 已经监视
            }
        }
        
        $this->config['watchPaths'][] = [
            'path' => $path,
            'recursive' => $recursive,
            'added' => time(),
        ];
        
        return $this->saveConfig();
    }
    
    /**
     * 移除监视路径
     *
     * @param string $path 路径
     * @return bool
     */
    public function removeWatchPath($path)
    {
        foreach ($this->config['watchPaths'] as $key => $watchPath) {
            if ($watchPath['path'] === $path) {
                unset($this->config['watchPaths'][$key]);
                $this->config['watchPaths'] = array_values($this->config['watchPaths']); // 重新索引
                return $this->saveConfig();
            }
        }
        
        return false;
    }
    
    /**
     * 获取所有监视路径
     *
     * @return array
     */
    public function getWatchPaths()
    {
        return $this->config['watchPaths'];
    }
    
    /**
     * 获取配置
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * 检查路径是否被监视
     *
     * @param string $path 路径
     * @return bool
     */
    public function isWatched($path)
    {
        foreach ($this->config['watchPaths'] as $watchPath) {
            if ($watchPath['path'] === $path) {
                return true;
            }
            
            // 检查是否是监视目录的子目录
            if ($watchPath['recursive'] && strpos($path, $watchPath['path'] . DIRECTORY_SEPARATOR) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 处理文件变化事件
     *
     * @param string $path 路径
     * @param string $event 事件类型 (created, modified, deleted)
     * @return void
     */
    public function handleFileChange($path, $event)
    {
        if (!$this->enabled) {
            return;
        }
        
        // 检查是否需要通知
        if ($this->config['notify']) {
            $filename = basename($path);
            $eventText = '';
            
            switch ($event) {
                case 'created':
                    $eventText = '创建了';
                    break;
                case 'modified':
                    $eventText = '修改了';
                    break;
                case 'deleted':
                    $eventText = '删除了';
                    break;
            }
            
            Notification::send('文件变化', "文件 {$filename} 已被{$eventText}");
        }
        
        // 触发事件
        Event::trigger('file.changed', [
            'path' => $path,
            'event' => $event,
            'autoRefresh' => $this->config['autoRefresh'],
        ]);
    }
    
    /**
     * 启动文件监视
     *
     * @return void
     */
    public function startWatching()
    {
        if (!$this->enabled || empty($this->config['watchPaths'])) {
            return;
        }
        
        // 在实际应用中，这里会启动一个后台进程来监视文件变化
        // 由于NativePHP的限制，这里只是一个示例
        // 实际应用中可以使用Node.js的chokidar或其他文件监视库
        
        // 这里我们模拟一个定时检查的过程
        $this->checkFileChanges();
    }
    
    /**
     * 检查文件变化
     *
     * @return void
     */
    protected function checkFileChanges()
    {
        if (!$this->enabled || empty($this->config['watchPaths'])) {
            return;
        }
        
        // 遍历所有监视路径
        foreach ($this->config['watchPaths'] as $watchPath) {
            $path = $watchPath['path'];
            $recursive = $watchPath['recursive'];
            
            if (!FileSystem::exists($path)) {
                continue;
            }
            
            // 获取文件列表
            $files = $this->getFiles($path, $recursive);
            
            // 检查文件变化
            // 这里需要与上次检查的结果比较，但为了简化示例，我们省略了这一步
            
            // 在实际应用中，可以将文件列表和修改时间保存到缓存中
            // 然后与当前的文件列表比较，检测变化
        }
    }
    
    /**
     * 获取目录中的所有文件
     *
     * @param string $path 路径
     * @param bool $recursive 是否递归
     * @return array
     */
    protected function getFiles($path, $recursive = false)
    {
        $result = [];
        
        if (!is_dir($path)) {
            return $result;
        }
        
        $items = scandir($path);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            $isDir = is_dir($fullPath);
            
            $result[] = [
                'path' => $fullPath,
                'isDir' => $isDir,
                'lastModified' => FileSystem::lastModified($fullPath),
            ];
            
            if ($recursive && $isDir) {
                $subFiles = $this->getFiles($fullPath, true);
                $result = array_merge($result, $subFiles);
            }
        }
        
        return $result;
    }
}
