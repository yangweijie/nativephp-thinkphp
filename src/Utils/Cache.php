<?php

namespace Native\ThinkPHP\Utils;

use think\App as ThinkApp;
use Native\ThinkPHP\Facades\FileSystem;

class Cache
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 缓存目录
     *
     * @var string
     */
    protected $cacheDir;

    /**
     * 缓存文件扩展名
     *
     * @var string
     */
    protected $extension = '.cache';

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param string|null $cacheDir
     */
    public function __construct(ThinkApp $app, $cacheDir = null)
    {
        $this->app = $app;
        $this->cacheDir = $cacheDir ?: $this->getDefaultCacheDir();
        
        // 确保缓存目录存在
        if (!is_dir($this->cacheDir)) {
            FileSystem::makeDirectory($this->cacheDir, 0755, true);
        }
    }

    /**
     * 获取默认缓存目录
     *
     * @return string
     */
    protected function getDefaultCacheDir()
    {
        return $this->app->getRuntimePath() . 'cache/native/';
    }

    /**
     * 设置缓存目录
     *
     * @param string $cacheDir
     * @return $this
     */
    public function setCacheDir($cacheDir)
    {
        $this->cacheDir = $cacheDir;
        
        // 确保缓存目录存在
        if (!is_dir($this->cacheDir)) {
            FileSystem::makeDirectory($this->cacheDir, 0755, true);
        }
        
        return $this;
    }

    /**
     * 获取缓存目录
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this->cacheDir;
    }

    /**
     * 获取缓存文件路径
     *
     * @param string $key
     * @return string
     */
    protected function getCacheFile($key)
    {
        return $this->cacheDir . md5($key) . $this->extension;
    }

    /**
     * 设置缓存
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = 0)
    {
        $cacheFile = $this->getCacheFile($key);
        
        $data = [
            'key' => $key,
            'value' => $value,
            'ttl' => $ttl,
            'created_at' => time(),
            'expires_at' => $ttl > 0 ? time() + $ttl : 0,
        ];
        
        return FileSystem::write($cacheFile, serialize($data));
    }

    /**
     * 获取缓存
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if (!FileSystem::exists($cacheFile)) {
            return $default;
        }
        
        $data = unserialize(FileSystem::read($cacheFile));
        
        // 检查缓存是否过期
        if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $data['value'];
    }

    /**
     * 删除缓存
     *
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if (FileSystem::exists($cacheFile)) {
            return FileSystem::delete($cacheFile);
        }
        
        return false;
    }

    /**
     * 检查缓存是否存在
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if (!FileSystem::exists($cacheFile)) {
            return false;
        }
        
        $data = unserialize(FileSystem::read($cacheFile));
        
        // 检查缓存是否过期
        if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
            $this->delete($key);
            return false;
        }
        
        return true;
    }

    /**
     * 获取或设置缓存
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function remember($key, callable $callback, $ttl = 0)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $value = $callback();
        
        $this->set($key, $value, $ttl);
        
        return $value;
    }

    /**
     * 清空所有缓存
     *
     * @return bool
     */
    public function clear()
    {
        $files = glob($this->cacheDir . '*' . $this->extension);
        
        foreach ($files as $file) {
            FileSystem::delete($file);
        }
        
        return true;
    }

    /**
     * 获取缓存信息
     *
     * @param string $key
     * @return array|null
     */
    public function getInfo($key)
    {
        $cacheFile = $this->getCacheFile($key);
        
        if (!FileSystem::exists($cacheFile)) {
            return null;
        }
        
        $data = unserialize(FileSystem::read($cacheFile));
        
        // 检查缓存是否过期
        if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
            $this->delete($key);
            return null;
        }
        
        return [
            'key' => $data['key'],
            'created_at' => $data['created_at'],
            'expires_at' => $data['expires_at'],
            'ttl' => $data['ttl'],
            'size' => FileSystem::size($cacheFile),
        ];
    }

    /**
     * 获取所有缓存信息
     *
     * @return array
     */
    public function getAllInfo()
    {
        $files = glob($this->cacheDir . '*' . $this->extension);
        $info = [];
        
        foreach ($files as $file) {
            $data = unserialize(FileSystem::read($file));
            
            // 检查缓存是否过期
            if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
                FileSystem::delete($file);
                continue;
            }
            
            $info[] = [
                'key' => $data['key'],
                'created_at' => $data['created_at'],
                'expires_at' => $data['expires_at'],
                'ttl' => $data['ttl'],
                'size' => FileSystem::size($file),
            ];
        }
        
        return $info;
    }

    /**
     * 获取缓存总大小
     *
     * @return int
     */
    public function getSize()
    {
        $files = glob($this->cacheDir . '*' . $this->extension);
        $size = 0;
        
        foreach ($files as $file) {
            $size += FileSystem::size($file);
        }
        
        return $size;
    }

    /**
     * 清理过期缓存
     *
     * @return int
     */
    public function gc()
    {
        $files = glob($this->cacheDir . '*' . $this->extension);
        $count = 0;
        
        foreach ($files as $file) {
            $data = unserialize(FileSystem::read($file));
            
            // 检查缓存是否过期
            if ($data['expires_at'] > 0 && $data['expires_at'] < time()) {
                FileSystem::delete($file);
                $count++;
            }
        }
        
        return $count;
    }
}
