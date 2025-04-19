<?php

namespace Native\ThinkPHP\Cache;

use Native\ThinkPHP\Contracts\CacheAdapter;
use Redis;

/**
 * Redis 缓存适配器
 */
class RedisAdapter implements CacheAdapter
{
    /**
     * Redis 实例
     *
     * @var \Redis
     */
    protected $redis;

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
     * @param \Redis $redis Redis 实例
     * @param string $prefix 缓存前缀
     * @param int $defaultTtl 默认过期时间（秒）
     */
    public function __construct(Redis $redis, $prefix = 'native:', $defaultTtl = 60)
    {
        $this->redis = $redis;
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
        $value = $this->redis->get($this->prefix . $key);
        if ($value === false) {
            return $default;
        }

        return unserialize($value);
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

        $serialized = serialize($value);

        if ($ttl > 0) {
            return $this->redis->setex($this->prefix . $key, $ttl, $serialized);
        }

        return $this->redis->set($this->prefix . $key, $serialized);
    }

    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function delete($key)
    {
        $result = $this->redis->del($this->prefix . $key);
        if ($result === false) {
            return false;
        }
        return (int)$result > 0;
    }

    /**
     * 清除所有缓存
     *
     * @return bool
     */
    public function clear()
    {
        $keys = $this->redis->keys($this->prefix . '*');
        if (!empty($keys)) {
            $result = $this->redis->del($keys);
            if ($result === false) {
                return false;
            }
            return (int)$result > 0;
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
        return $this->redis->exists($this->prefix . $key);
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
        $prefixedKeys = array_map(function ($key) {
            return $this->prefix . $key;
        }, $keys);

        $values = $this->redis->mGet($prefixedKeys);
        $result = [];

        foreach ($keys as $i => $key) {
            $value = $values[$i];
            $result[$key] = $value !== false ? unserialize($value) : $default;
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
        if ($ttl === null) {
            $ttl = $this->defaultTtl;
        }

        $serializedValues = [];
        foreach ($values as $key => $value) {
            $serializedValues[$this->prefix . $key] = serialize($value);
        }

        $result = $this->redis->mSet($serializedValues);

        if ($result && $ttl > 0) {
            foreach ($values as $key => $value) {
                $this->redis->expire($this->prefix . $key, $ttl);
            }
        }

        return $result;
    }

    /**
     * 删除多个缓存
     *
     * @param array $keys 缓存键数组
     * @return bool
     */
    public function deleteMultiple(array $keys)
    {
        $prefixedKeys = array_map(function ($key) {
            return $this->prefix . $key;
        }, $keys);

        $result = $this->redis->del($prefixedKeys);
        if ($result === false) {
            return false;
        }
        return (int)$result > 0;
    }
}
