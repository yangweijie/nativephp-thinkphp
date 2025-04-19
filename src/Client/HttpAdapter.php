<?php

namespace Native\ThinkPHP\Client;

use Native\ThinkPHP\Client\CustomResponse;

/**
 * HTTP 客户端适配器
 */
class HttpAdapter
{
    /**
     * HTTP 客户端实例
     *
     * @var mixed
     */
    protected $client;

    /**
     * 构造函数
     *
     * @param mixed $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * 发送 GET 请求
     *
     * @param string $url
     * @param array|string|null $query
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function get(string $url, $query = null)
    {
        if (method_exists($this->client, 'get')) {
            return $this->client->get($url, $query);
        }

        // 模拟响应
        return $this->createMockResponse();
    }

    /**
     * 发送 POST 请求
     *
     * @param string $url
     * @param array|string|null $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function post(string $url, $data = null)
    {
        if (method_exists($this->client, 'post')) {
            return $this->client->post($url, $data);
        }

        // 模拟响应
        return $this->createMockResponse();
    }

    /**
     * 发送 PUT 请求
     *
     * @param string $url
     * @param array|string|null $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function put(string $url, $data = null)
    {
        if (method_exists($this->client, 'put')) {
            return $this->client->put($url, $data);
        }

        // 模拟响应
        return $this->createMockResponse();
    }

    /**
     * 发送 PATCH 请求
     *
     * @param string $url
     * @param array|string|null $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function patch(string $url, $data = null)
    {
        if (method_exists($this->client, 'patch')) {
            return $this->client->patch($url, $data);
        }

        // 模拟响应
        return $this->createMockResponse();
    }

    /**
     * 发送 DELETE 请求
     *
     * @param string $url
     * @param array|string|null $data
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    public function delete(string $url, $data = null)
    {
        if (method_exists($this->client, 'delete')) {
            return $this->client->delete($url, $data);
        }

        // 模拟响应
        return $this->createMockResponse();
    }

    /**
     * 创建模拟响应
     *
     * @return \Native\ThinkPHP\Client\CustomResponse
     */
    protected function createMockResponse()
    {
        $response = new CustomResponse();
        $response->data(['result' => 'success']);
        return $response;
    }
}
