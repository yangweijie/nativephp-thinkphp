<?php

namespace Native\ThinkPHP\Contracts;

/**
 * 缓存适配器接口
 */
interface CacheAdapter
{
    /**
     * 获取缓存
     *
     * @param string $key 缓存键
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * 设置缓存
     *
     * @param string $key 缓存键
     * @param mixed $value 缓存值
     * @param int|null $ttl 过期时间（秒）
     * @return bool
     */
    public function set($key, $value, $ttl = null);

    /**
     * 删除缓存
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function delete($key);

    /**
     * 清除所有缓存
     *
     * @return bool
     */
    public function clear();

    /**
     * 检查缓存是否存在
     *
     * @param string $key 缓存键
     * @return bool
     */
    public function has($key);

    /**
     * 获取多个缓存
     *
     * @param array $keys 缓存键数组
     * @param mixed $default 默认值
     * @return array
     */
    public function getMultiple(array $keys, $default = null);

    /**
     * 设置多个缓存
     *
     * @param array $values 缓存键值对
     * @param int|null $ttl 过期时间（秒）
     * @return bool
     */
    public function setMultiple(array $values, $ttl = null);

    /**
     * 删除多个缓存
     *
     * @param array $keys 缓存键数组
     * @return bool
     */
    public function deleteMultiple(array $keys);
}
