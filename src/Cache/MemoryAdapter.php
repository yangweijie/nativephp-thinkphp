<?php

namespace Native\ThinkPHP\Cache;

use Native\ThinkPHP\Contracts\CacheAdapter;

/**
 * 内存缓存适配器
 */
class MemoryAdapter implements CacheAdapter
{
    /**
     * 缓存数据
     *
     * @var array
     */
    protected static $cache = [];

    /**
     * 缓存过期时间
     *
     * @var array
     */
    protected static $expires = [];

    /**
     * 默认过期时间（秒）
     *
     * @var int
     */
    protected $defaultTtl = 60;

    /**
     * 构造函数
     *
     * @param int $defaultTtl 默认过期时间（秒）
     */
    public function __construct($defaultTtl = 60)
    {
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
        $this->clearExpired();

        if (isset(static::$cache[$key]) && (!isset(static::$expires[$key]) || static::$expires[$key] > time())) {
            return static::$cache[$key];
        }

        return $default;
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
        static::$cache[$key] = $value;

        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }

        if ($ttl > 0) {
            static::$expires[$key] = time() + $ttl;
        } else {
            unset(static::$expires[$key]);
        }

        return true;
    }

    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function delete($key)
    {
        unset(static::$cache[$key], static::$expires[$key]);
        return true;
    }

    /**
     * 清除所有缓存
     *
     * @return bool
     */
    public function clear()
    {
        static::$cache = [];
        static::$expires = [];
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
        $this->clearExpired();
        return isset(static::$cache[$key]) && (!isset(static::$expires[$key]) || static::$expires[$key] > time());
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
        $this->clearExpired();

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

    /**
     * 清除过期缓存
     *
     * @return void
     */
    protected function clearExpired()
    {
        $now = time();
        foreach (static::$expires as $key => $expire) {
            if ($expire <= $now) {
                unset(static::$cache[$key], static::$expires[$key]);
            }
        }
    }
}
