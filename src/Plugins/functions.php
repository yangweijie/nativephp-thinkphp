<?php

namespace Native\ThinkPHP\Plugins;

/**
 * 获取配置值
 *
 * @param string $key 配置键名
 * @param mixed $default 默认值
 * @return mixed
 */
function config($key = null, $default = null)
{
    return \config($key, $default);
}

/**
 * 获取应用实例
 *
 * @param string|null $name 应用名称
 * @return \think\App|mixed
 */
function app($name = null)
{
    if (is_null($name)) {
        return \think\Container::getInstance()->make('app');
    }

    return \think\Container::getInstance()->make($name);
}
