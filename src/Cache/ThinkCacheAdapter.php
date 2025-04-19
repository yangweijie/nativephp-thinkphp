<?php

namespace Native\ThinkPHP\Cache;

use Native\ThinkPHP\Contracts\CacheAdapter;
use think\Cache;

/**
 * ThinkPHP 缓存适配器
 */
class ThinkCacheAdapter implements CacheAdapter
{
    /**
     * ThinkPHP 缓存实例
     *
     * @var \think\Cache
     */
    protected $cache;

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $prefix = 'native:';

    /**
     * 默认过期时间（秒）
     *
     * @var int
     */
    protected $defaultTtl = 60;

    /**
     * 构造函数
     *
     * @param \think\Cache $cache ThinkPHP 缓存实例
     * @param string $prefix 缓存前缀
     * @param int $defaultTtl 默认过期时间（秒）
     */
    public function __construct(Cache $cache, $prefix = 'native:', $defaultTtl = 60)
    {
        $this->cache = $cache;
        $this->prefix = $prefix;
        $this->defaultTtl = $defaultTtl;
    }

    /**
     * 获取缓存
     *
     * @param string $key 缓存键
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($key, $default = null)
    {
        $value = $this->cache->get($this->prefix . $key);
        return $value !== null ? $value : $default;
    }

    /**
     * 设置缓存
     *
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int|null $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }

        return $this->cache->set($this->prefix . $key, $value, $ttl);
    }

    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function delete($key)
    {
        return $this->cache->delete($this->prefix . $key);
    }

    /**
     * 清除所有缓存
     *
     * @return bool
     */
    public function clear()
    {
        // ThinkPHP 缓存不支持按前缀清除，只能清除所有缓存
        // 这里我们只清除带有指定前缀的缓存
        $keys = $this->cache->getTagItems($this->prefix);
        if (!empty($keys)) {
            $result = true;
            foreach ($keys as $key) {
                $result = $result && $this->cache->delete($key);
            }
            return $result;
        }
        return true;
    }

    /**
     * 检查缓存是否存在
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function has($key)
    {
        return $this->cache->has($this->prefix . $key);
    }

    /**
     * 获取多个缓存
     *
     * @param array $keys 缓存键数组
     * @param mixed $default 默认值
     * @return array
     */
    public function getMultiple(array $keys, $default = null)
    {
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $this->get($key, $default);
        }

        return $result;
    }

    /**
     * 设置多个缓存
     *
     * @param array $values 缓存键值对
     * @param int|null $ttl 过期时间（秒）
     * @return bool
     */
    public function setMultiple(array $values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /**
     * 删除多个缓存
     *
     * @param array $keys 缓存键数组
     * @return bool
     */
    public function deleteMultiple(array $keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }
}
