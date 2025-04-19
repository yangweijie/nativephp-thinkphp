<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static array get(string $url, array $options = []) 发送 GET 请求
 * @method static array post(string $url, array $data = [], array $options = []) 发送 POST 请求
 * @method static array put(string $url, array $data = [], array $options = []) 发送 PUT 请求
 * @method static array delete(string $url, array $options = []) 发送 DELETE 请求
 * @method static array patch(string $url, array $data = [], array $options = []) 发送 PATCH 请求
 * @method static array head(string $url, array $options = []) 发送 HEAD 请求
 * @method static array options(string $url, array $options = []) 发送 OPTIONS 请求
 * @method static bool download(string $url, string $savePath, array $options = []) 下载文件
 * @method static \Native\ThinkPHP\WebSocket\Client websocket(string $url, array $headers = []) 创建 WebSocket 连接
 *
 * @see \Native\ThinkPHP\Http
 */
class Http extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.http';
    }
}
