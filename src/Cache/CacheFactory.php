<?php

namespace Native\ThinkPHP\Cache;

use Native\ThinkPHP\Contracts\CacheAdapter;
use think\App;
use think\Cache;
use Redis;
use InvalidArgumentException;

/**
 * 缓存工厂类
 */
class CacheFactory
{
    /**
     * 创建缓存适配器
     *
     * @param string $driver 驱动类型：memory, think, redis
     * @param array $config 配置
     * @param \think\App $app ThinkPHP 应用实例
     * @return \Native\ThinkPHP\Contracts\CacheAdapter
     */
    public static function create($driver = 'memory', array $config = [], App $app = null)
    {
        switch ($driver) {
            case 'memory':
                return static::createMemoryAdapter($config);
            case 'think':
                $adapter = static::createThinkAdapter($config, $app);
                if ($adapter !== null) {
                    return $adapter;
                }
                // 如果创建失败，返回内存缓存适配器
                return static::createMemoryAdapter($config);
            case 'redis':
                $adapter = static::createRedisAdapter($config);
                if ($adapter !== null) {
                    return $adapter;
                }
                // 如果创建失败，返回内存缓存适配器
                return static::createMemoryAdapter($config);
            default:
                // 如果驱动不支持，返回内存缓存适配器
                return static::createMemoryAdapter($config);
        }
    }

    /**
     * 创建内存缓存适配器
     *
     * @param array $config 配置
     * @return \Native\ThinkPHP\Cache\MemoryAdapter
     */
    protected static function createMemoryAdapter(array $config = [])
    {
        $defaultTtl = isset($config['ttl']) ? $config['ttl'] : 60;
        return new MemoryAdapter($defaultTtl);
    }

    /**
     * 创建 ThinkPHP 缓存适配器
     *
     * @param array $config 配置
     * @param \think\App $app ThinkPHP 应用实例
     * @return \Native\ThinkPHP\Cache\ThinkCacheAdapter|null
     */
    protected static function createThinkAdapter(array $config = [], App $app = null)
    {
        try {
            if ($app === null) {
                $app = app();
            }

            $cache = $app->make(Cache::class);
            $prefix = isset($config['prefix']) ? $config['prefix'] : 'native:';
            $defaultTtl = isset($config['ttl']) ? $config['ttl'] : 60;

            return new ThinkCacheAdapter($cache, $prefix, $defaultTtl);
        } catch (\Exception $e) {
            // 如果发生异常，返回 null
            return null;
        }
    }

    /**
     * 创建 Redis 缓存适配器
     *
     * @param array $config 配置
     * @return \Native\ThinkPHP\Cache\RedisAdapter|null
     */
    protected static function createRedisAdapter(array $config = [])
    {
        // 如果 Redis 扩展不可用，返回 null
        if (!extension_loaded('redis')) {
            return null;
        }

        try {
            $redis = new Redis();

            $host = isset($config['host']) ? $config['host'] : '127.0.0.1';
            $port = isset($config['port']) ? $config['port'] : 6379;
            $timeout = isset($config['timeout']) ? $config['timeout'] : 0;
            $password = isset($config['password']) ? $config['password'] : null;
            $database = isset($config['database']) ? $config['database'] : 0;
            $prefix = isset($config['prefix']) ? $config['prefix'] : 'native:';
            $defaultTtl = isset($config['ttl']) ? $config['ttl'] : 60;

            $redis->connect($host, $port, $timeout);

            if ($password !== null) {
                $redis->auth($password);
            }

            if ($database !== 0) {
                $redis->select($database);
            }

            return new RedisAdapter($redis, $prefix, $defaultTtl);
        } catch (\Exception $e) {
            // 如果发生异常，返回 null
            return null;
        }
    }
}
