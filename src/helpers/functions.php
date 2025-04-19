<?php

use think\Response;

if (!function_exists('json')) {
    /**
     * 返回 JSON 响应
     *
     * @param mixed $data 响应数据
     * @param int $code HTTP 状态码
     * @param array $header 响应头
     * @param array $options JSON 选项
     * @return \think\Response
     */
    function json($data, $code = 200, $header = [], $options = [])
    {
        /** @phpstan-ignore-next-line */
        return Response::create($data, 'json', $code)->header($header);
    }
}

if (!function_exists('url')) {
    /**
     * 生成 URL
     *
     * @param string $url URL 地址
     * @param array $vars 变量
     * @param bool $suffix 是否添加后缀
     * @param bool $domain 是否添加域名
     * @return string
     */
    function url($url = '', $vars = [], $suffix = true, $domain = false)
    {
        if (function_exists('\\think\\facade\\Url::build')) {
            /** @phpstan-ignore-next-line */
            return \think\facade\Url::build($url, $vars, $suffix, $domain);
        }

        // 简单实现
        if (empty($url)) {
            return '/';
        }

        // 如果 URL 已经是完整的 URL，则直接返回
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            return $url;
        }

        // 添加前导斜杠
        if (strpos($url, '/') !== 0) {
            $url = '/' . $url;
        }

        // 添加变量
        if (!empty($vars)) {
            $url .= '?' . http_build_query($vars);
        }

        // 添加后缀
        if ($suffix && strpos($url, '.') === false) {
            $url .= '.html';
        }

        // 添加域名
        if ($domain) {
            $url = 'http://localhost' . $url;
        }

        return $url;
    }
}

if (!function_exists('response')) {
    /**
     * 创建响应对象
     *
     * @param mixed $data 响应数据
     * @param string $type 响应类型
     * @param int $code HTTP 状态码
     * @return \think\Response
     */
    function response($data = '', $type = 'html', $code = 200)
    {
        /** @phpstan-ignore-next-line */
        return Response::create($data, $type, $code);
    }
}

if (!function_exists('event')) {
    /**
     * 触发事件
     *
     * @param string $event 事件名称
     * @param mixed $params 事件参数
     * @return mixed
     */
    function event($event, $params = null)
    {
        if (function_exists('\\think\\facade\\Event::trigger')) {
            return \think\facade\Event::trigger($event, $params);
        }

        // 简单实现
        return $params;
    }
}

if (!function_exists('request')) {
    /**
     * 获取当前请求对象
     *
     * @return \think\Request
     */
    function request()
    {
        return \think\facade\Request::instance();
    }
}

if (!function_exists('config')) {
    /**
     * 获取或设置配置参数
     *
     * @param string|array|null $name 配置参数名或配置数组
     * @param mixed $default 默认值
     * @return mixed
     */
    function config($name = '', $default = null)
    {
        if (is_array($name)) {
            if (function_exists('\\think\\facade\\Config::set')) {
                foreach ($name as $key => $value) {
                    \think\facade\Config::set($key, $value);
                }
                return true;
            }
            return false;
        }

        if (function_exists('\\think\\facade\\Config::get')) {
            return \think\facade\Config::get($name, $default);
        }

        // 简单实现
        return $default;
    }
}
