<?php

namespace think\facade;

/**
 * Http 门面类模拟
 */
class Http
{
    /**
     * 设置响应头
     *
     * @param array|string $name 头名称或者数组
     * @param string $value 头值
     * @return \think\Response
     */
    public static function header($name, $value = null)
    {
        return new \think\Response();
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \think\Response
     */
    public static function get($url, $data = [], $options = [])
    {
        return new \think\Response();
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \think\Response
     */
    public static function post($url, $data = [], $options = [])
    {
        return new \think\Response();
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \think\Response
     */
    public static function put($url, $data = [], $options = [])
    {
        return new \think\Response();
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return \think\Response
     */
    public static function delete($url, $data = [], $options = [])
    {
        return new \think\Response();
    }
}

namespace think;

/**
 * Response 类模拟
 */
class Response
{
    /**
     * 设置超时时间
     *
     * @param int $seconds 超时时间（秒）
     * @return $this
     */
    public function timeout($seconds)
    {
        return $this;
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url 请求URL
     * @param array $data 请求数据
     * @param array $options 请求选项
     * @return $this
     */
    public function post($url, $data = [], $options = [])
    {
        return $this;
    }

    /**
     * 获取响应内容
     *
     * @return string
     */
    public function getContent()
    {
        return '{"status": "success", "data": {}}';
    }

    /**
     * 获取响应状态码
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 200;
    }

    /**
     * 获取响应头
     *
     * @param string $name
     * @return string|array
     */
    public function getHeader($name = null)
    {
        return [];
    }

    /**
     * 设置响应数据
     *
     * @param string $key 键名
     * @param mixed $value 值
     * @return $this
     */
    public function with($key, $value = null)
    {
        return $this;
    }

    /**
     * 获取 JSON 数据
     *
     * @param string|null $key 键名
     * @param mixed $default 默认值
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        return 0;
    }
}
