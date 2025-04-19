<?php

namespace Native\ThinkPHP\Client;

use think\facade\Config;
use think\facade\Http;
use Native\ThinkPHP\Client\CustomResponse;

class Client
{
    /**
     * HTTP 客户端实例
     *
     * @var mixed
     */
    protected $client;

    /**
     * 构造函数
     */
    public function __construct()
    {
        // 在测试环境中使用空实现
        if (defined('PHPUNIT_RUNNING')) {
            $httpClient = Http::header([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'X-NativePHP-Secret' => '',
                ])
                ->timeout(60 * 60);
            $this->client = new HttpAdapter($httpClient);
            return;
        }

        $httpClient = Http::header([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-NativePHP-Secret' => Config::get('native.secret', ''),
            ])
            ->timeout(60 * 60);
        $this->client = new HttpAdapter($httpClient);
    }

    /**
     * 发送 GET 请求
     *
     * @param string $endpoint
     * @param array|string|null $query
     * @return \think\Response
     */
    public function get(string $endpoint, $query = null)
    {
        $url = $this->getApiUrl($endpoint);
        return $this->client->get($url, $query);
    }

    /**
     * 发送 POST 请求
     *
     * @param string $endpoint
     * @param array $data
     * @return \think\Response
     */
    public function post(string $endpoint, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);
        return $this->client->post($url, json_encode($data));
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $endpoint
     * @param array $data
     * @return \think\Response
     */
    public function put(string $endpoint, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);
        return $this->client->put($url, json_encode($data));
    }

    /**
     * 发送 PATCH 请求
     *
     * @param string $endpoint
     * @param array $data
     * @return \think\Response
     */
    public function patch(string $endpoint, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);
        return $this->client->patch($url, json_encode($data));
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $endpoint
     * @param array $data
     * @return \think\Response
     */
    public function delete(string $endpoint, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);
        return $this->client->delete($url, json_encode($data));
    }

    /**
     * 上传文件
     *
     * @param string $endpoint
     * @param string $filePath 本地文件路径
     * @param array $data 其他数据
     * @return \think\Response
     */
    public function upload(string $endpoint, string $filePath, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);

        // 创建一个新的 HTTP 客户端，不设置 Content-Type
        $client = Http::header([
                'Accept' => 'application/json',
                'X-NativePHP-Secret' => Config::get('native.secret', ''),
            ])
            ->timeout(60 * 60);

        // 准备文件数据
        $fileData = [];
        if (file_exists($filePath)) {
            $fileData['file'] = new \CURLFile($filePath);
        } else {
            // 如果文件不存在，返回模拟响应
            $response = new CustomResponse();
            $response->data(['error' => 'File not found']);
            return $response;
        }

        // 合并数据
        $postData = array_merge($data, $fileData);

        // 发送请求
        return $client->post($url, $postData);
    }

    /**
     * 下载文件
     *
     * @param string $endpoint
     * @param string $savePath 保存路径
     * @param array $data 请求数据
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function download(string $endpoint, string $savePath, array $data = [])
    {
        $url = $this->getApiUrl($endpoint);

        // 创建一个新的 HTTP 客户端
        $client = Http::header([
                'Accept' => '*/*',
                'X-NativePHP-Secret' => Config::get('native.secret', ''),
            ])
            ->timeout(60 * 60);

        // 确保目录存在
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 打开文件句柄
        $fp = fopen($savePath, 'w');
        if (!$fp) {
            // 如果无法打开文件，返回 false
            $response = new CustomResponse();
            $response->data(['error' => 'Could not open file for writing']);
            return $response;
        }

        // 发送请求
        $httpResponse = $this->client->get($url, $data);

        // 写入文件
        fwrite($fp, $httpResponse->getContent());
        fclose($fp);

        $response = new CustomResponse();
        $response->data(['success' => file_exists($savePath)]);
        return $response;
    }

    /**
     * 创建 WebSocket 连接
     *
     * @param string $url
     * @return \Native\ThinkPHP\WebSocket\Client
     */
    public function websocket(string $url)
    {
        // 创建 WebSocket 客户端实例
        $client = new \Native\ThinkPHP\WebSocket\Client(app());

        // 返回客户端实例，让调用者可以进一步配置和连接
        return $client;
    }

    /**
     * 获取 API URL
     *
     * @param string $endpoint
     * @return string
     */
    protected function getApiUrl(string $endpoint)
    {
        $baseUrl = Config::get('native.api_url', 'http://localhost:31199/api');
        return rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
    }
}
