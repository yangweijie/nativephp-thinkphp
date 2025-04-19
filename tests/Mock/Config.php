<?php

namespace think\facade;

/**
 * Config 门面类模拟
 */
class Config
{
    /**
     * 获取配置
     *
     * @param string $name 配置名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function get($name = null, $default = null)
    {
        return $default;
    }

    /**
     * 设置配置
     *
     * @param string|array $name 配置名称或配置数组
     * @param mixed $value 配置值
     * @return void
     */
    public static function set($name, $value = null)
    {
        // 空实现
    }

    /**
     * 检查配置是否存在
     *
     * @param string $name 配置名称
     * @return bool
     */
    public static function has($name)
    {
        return false;
    }
}

namespace think;

/**
 * Config 类模拟
 */
class Config
{
    /**
     * 获取配置
     *
     * @param string $name 配置名称
     * @param mixed $default 默认值
     * @return mixed
     */
    public function get($name = null, $default = null)
    {
        return $default;
    }

    /**
     * 设置配置
     *
     * @param string|array $name 配置名称或配置数组
     * @param mixed $value 配置值
     * @return void
     */
    public function set($name, $value = null)
    {
        // 空实现
    }

    /**
     * 检查配置是否存在
     *
     * @param string $name 配置名称
     * @return bool
     */
    public function has($name)
    {
        return false;
    }
}
