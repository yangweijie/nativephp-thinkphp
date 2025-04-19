<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\CustomResponse;

/**
 * NativePHP 客户端
 */
class Client
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * HTTP 客户端实例
     *
     * @var mixed
     */
    protected $httpClient;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->initHttpClient();
    }

    /**
     * 初始化 HTTP 客户端
     *
     * @return void
     */
    protected function initHttpClient()
    {
        // 在测试环境中使用空实现
        if (defined('PHPUNIT_RUNNING')) {
            $this->httpClient = new Client\HttpAdapter(null);
            return;
        }

        // 使用 ThinkPHP 的 HTTP 客户端
        if (class_exists('\\think\\facade\\Http')) {
            $httpClient = \think\facade\Http::header([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'X-NativePHP-Secret' => $this->app->config->get('native.secret', ''),
            ])->timeout(60 * 60);
            
            $this->httpClient = new Client\HttpAdapter($httpClient);
            return;
        }

        // 使用默认实现
        $this->httpClient = new Client\HttpAdapter(null);
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url
     * @param array $query
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function get(string $url, array $query = [])
    {
        return $this->httpClient->get($url, $query);
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function post(string $url, array $data = [])
    {
        return $this->httpClient->post($url, $data);
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $url
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function put(string $url, array $data = [])
    {
        return $this->httpClient->put($url, $data);
    }

    /**
     * 发送 PATCH 请求
     *
     * @param string $url
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function patch(string $url, array $data = [])
    {
        return $this->httpClient->patch($url, $data);
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function delete(string $url, array $data = [])
    {
        return $this->httpClient->delete($url, $data);
    }

    /**
     * 下载文件
     *
     * @param string $url
     * @param string $savePath
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function download(string $url, string $savePath, array $data = [])
    {
        // 创建目录
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 打开文件
        $fp = fopen($savePath, 'w');
        if (!$fp) {
            $response = new CustomResponse();
            $response->data(['error' => 'Could not open file for writing']);
            return $response;
        }

        // 发送请求
        $httpResponse = $this->httpClient->get($url, $data);

        // 写入文件
        fwrite($fp, $httpResponse->getContent());
        fclose($fp);

        // 返回响应
        $response = new CustomResponse();
        $response->data(['success' => file_exists($savePath)]);
        return $response;
    }

    /**
     * 上传文件
     *
     * @param string $url
     * @param string $filePath
     * @param array $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function upload(string $url, string $filePath, array $data = [])
    {
        // 检查文件是否存在
        if (!file_exists($filePath)) {
            $response = new CustomResponse();
            $response->data(['error' => 'File not found']);
            return $response;
        }

        // 读取文件内容
        $fileContent = file_get_contents($filePath);
        
        // 添加文件内容到数据
        $data['file'] = $fileContent;
        $data['filename'] = basename($filePath);
        
        // 发送请求
        return $this->httpClient->post($url, $data);
    }
}
