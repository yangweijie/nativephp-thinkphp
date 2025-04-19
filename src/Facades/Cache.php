<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Utils\Cache setCacheDir(string $cacheDir) 设置缓存目录
 * @method static string getCacheDir() 获取缓存目录
 * @method static bool set(string $key, mixed $value, int $ttl = 0) 设置缓存
 * @method static mixed get(string $key, mixed $default = null) 获取缓存
 * @method static bool delete(string $key) 删除缓存
 * @method static bool has(string $key) 检查缓存是否存在
 * @method static mixed remember(string $key, callable $callback, int $ttl = 0) 获取或设置缓存
 * @method static bool clear() 清空所有缓存
 * @method static array|null getInfo(string $key) 获取缓存信息
 * @method static array getAllInfo() 获取所有缓存信息
 * @method static int getSize() 获取缓存总大小
 * @method static int gc() 清理过期缓存
 * 
 * @see \Native\ThinkPHP\Utils\Cache
 */
class Cache extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.cache';
    }
}
