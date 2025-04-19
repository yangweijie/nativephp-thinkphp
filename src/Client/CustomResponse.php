<?php

namespace Native\ThinkPHP\Client;

use think\Response;

/**
 * 自定义响应类
 */
class CustomResponse extends Response
{
    /**
     * 响应数据
     *
     * @var array
     */
    protected $responseData = [];

    /**
     * 设置响应数据
     *
     * @param array $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->responseData = $data;
        return $this;
    }

    /**
     * 获取响应数据
     *
     * @param string|null $key
     * @param mixed $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {
        if ($key === null) {
            return $this->responseData;
        }

        return $this->responseData[$key] ?? $default;
    }

    /**
     * 获取响应内容
     *
     * @return string
     */
    public function getContent()
    {
        return json_encode($this->responseData);
    }
}
