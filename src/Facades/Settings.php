<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Settings setPath(string $path) 设置设置文件路径
 * @method static string|null getPath() 获取设置文件路径
 * @method static mixed get(string $key, mixed $default = null) 获取设置值
 * @method static bool set(string $key, mixed $value) 设置设置值
 * @method static bool has(string $key) 检查设置是否存在
 * @method static bool delete(string $key) 删除设置
 * @method static array all() 获取所有设置
 * @method static bool clear() 清空所有设置
 * @method static bool export(string|null $path = null) 导出设置
 * @method static bool import(string $path) 导入设置
 * @method static void watch(string $key, callable $callback) 监听设置变化
 * 
 * @see \Native\ThinkPHP\Settings
 */
class Settings extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.settings';
    }
}
