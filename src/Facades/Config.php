<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Utils\Config setConfigFile(string $configFile) 设置配置文件路径
 * @method static string getConfigFile() 获取配置文件路径
 * @method static mixed get(string $key, mixed $default = null) 获取配置值
 * @method static bool set(string $key, mixed $value) 设置配置值
 * @method static bool has(string $key) 检查配置是否存在
 * @method static bool delete(string $key) 删除配置
 * @method static array all() 获取所有配置
 * @method static bool clear() 清空所有配置
 * @method static bool export(string|null $path = null) 导出配置
 * @method static bool import(string $path) 导入配置
 * @method static bool merge(array $config) 合并配置
 * @method static bool replace(array $config) 替换配置
 * 
 * @see \Native\ThinkPHP\Utils\Config
 */
class Config extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.config';
    }
}
