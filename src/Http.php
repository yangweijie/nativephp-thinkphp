<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\WebSocket\Client as WebSocketClient;

class Http
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 默认选项
     *
     * @var array
     */
    protected $defaultOptions = [
        'timeout' => 30,
        'verify' => true,
        'headers' => [],
    ];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    public function get($url, array $options = [])
    {
        return $this->request('GET', $url, $options);
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return array
     */
    public function post($url, array $data = [], array $options = [])
    {
        $options['data'] = $data;

        return $this->request('POST', $url, $options);
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return array
     */
    public function put($url, array $data = [], array $options = [])
    {
        $options['data'] = $data;

        return $this->request('PUT', $url, $options);
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    public function delete($url, array $options = [])
    {
        return $this->request('DELETE', $url, $options);
    }

    /**
     * 发送 PATCH 请求
     *
     * @param string $url
     * @param array $data
     * @param array $options
     * @return array
     */
    public function patch($url, array $data = [], array $options = [])
    {
        $options['data'] = $data;

        return $this->request('PATCH', $url, $options);
    }

    /**
     * 发送 HEAD 请求
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    public function head($url, array $options = [])
    {
        return $this->request('HEAD', $url, $options);
    }

    /**
     * 发送 OPTIONS 请求
     *
     * @param string $url
     * @param array $options
     * @return array
     */
    public function options($url, array $options = [])
    {
        return $this->request('OPTIONS', $url, $options);
    }

    /**
     * 下载文件
     *
     * @param string $url
     * @param string $savePath
     * @param array $options
     * @return bool
     */
    public function download($url, $savePath, array $options = [])
    {
        // 确保目录存在
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 发送请求
        $response = $this->request('GET', $url, array_merge($options, [
            'save_to' => $savePath,
        ]));

        return $response['success'] && file_exists($savePath);
    }

    /**
     * 创建 WebSocket 连接
     *
     * @param string $url WebSocket URL，格式如：ws://example.com:8080/path
     * @param array $headers 请求头
     * @return \Native\ThinkPHP\WebSocket\Client
     */
    public function websocket($url, array $headers = [])
    {
        // 创建 WebSocket 客户端
        $client = new WebSocketClient($this->app);

        // 添加默认请求头
        $headers = array_merge($this->defaultOptions['headers'], $headers);

        // 返回客户端实例，让调用者可以进一步配置和连接
        return $client;
    }

    /**
     * 发送请求
     *
     * @param string $method
     * @param string $url
     * @param array $options
     * @return array
     */
    protected function request($method, $url, array $options = [])
    {
        // 合并选项
        $options = array_merge($this->defaultOptions, $options);

        // 创建 cURL 资源
        $ch = curl_init();

        // 设置 URL
        curl_setopt($ch, CURLOPT_URL, $url);

        // 设置请求方法
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // 设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $options['timeout']);

        // 设置是否验证 SSL 证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $options['verify']);

        // 设置是否返回响应内容
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // 设置请求头
        if (!empty($options['headers'])) {
            $headers = [];
            foreach ($options['headers'] as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        // 设置请求数据
        if (!empty($options['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        // 设置保存路径
        if (!empty($options['save_to'])) {
            $fp = fopen($options['save_to'], 'w');
            curl_setopt($ch, CURLOPT_FILE, $fp);
        }

        // 执行请求
        $response = curl_exec($ch);

        // 获取响应状态码
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // 获取错误信息
        $error = curl_error($ch);

        // 关闭 cURL 资源
        curl_close($ch);

        // 关闭文件句柄
        if (!empty($options['save_to']) && isset($fp)) {
            fclose($fp);
        }

        // 返回响应
        return [
            'success' => empty($error),
            'status_code' => $statusCode,
            'data' => $response,
            'error' => $error,
        ];
    }

    /**
     * 创建带有认证令牌的请求
     *
     * @param string $token
     * @return $this
     */
    public function withToken($token)
    {
        $this->defaultOptions['headers']['Authorization'] = 'Bearer ' . $token;
        return $this;
    }

    /**
     * 创建带有基本认证的请求
     *
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function withBasicAuth($username, $password)
    {
        $this->defaultOptions['headers']['Authorization'] = 'Basic ' . base64_encode($username . ':' . $password);
        return $this;
    }

    /**
     * 设置请求头
     *
     * @param array|string $header
     * @param string|null $value
     * @return $this
     */
    public function withHeader($header, $value = null)
    {
        if (is_array($header)) {
            foreach ($header as $key => $value) {
                $this->defaultOptions['headers'][$key] = $value;
            }
        } else {
            $this->defaultOptions['headers'][$header] = $value;
        }

        return $this;
    }

    /**
     * 设置请求超时
     *
     * @param int $seconds
     * @return $this
     */
    public function timeout($seconds)
    {
        $this->defaultOptions['timeout'] = $seconds;
        return $this;
    }

    /**
     * 设置是否验证 SSL 证书
     *
     * @param bool $verify
     * @return $this
     */
    public function verify($verify = true)
    {
        $this->defaultOptions['verify'] = $verify;
        return $this;
    }
}
