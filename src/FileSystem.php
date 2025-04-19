<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class FileSystem
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 读取文件内容
     *
     * @param string $path 文件路径
     * @param string $encoding 编码
     * @return string|false
     */
    public function read($path, $encoding = 'utf8')
    {
        $response = $this->client->post('fileSystem/read', [
            'path' => $path,
            'encoding' => $encoding,
        ]);

        $data = json_decode($response->getContent(), true);
        return ($data['success'] ?? false) ? ($data['content'] ?? '') : false;
    }

    /**
     * 写入内容到文件
     *
     * @param string $path 文件路径
     * @param string $content 文件内容
     * @return bool
     */
    public function write($path, $content)
    {
        $response = $this->client->post('fileSystem/write', [
            'path' => $path,
            'content' => $content,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 追加内容到文件
     *
     * @param string $path 文件路径
     * @param string $content 追加内容
     * @return bool
     */
    public function append($path, $content)
    {
        $response = $this->client->post('fileSystem/append', [
            'path' => $path,
            'content' => $content,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 删除文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function delete($path)
    {
        $response = $this->client->post('fileSystem/delete', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 复制文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return bool
     */
    public function copy($source, $destination)
    {
        $response = $this->client->post('fileSystem/copy', [
            'source' => $source,
            'destination' => $destination,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 移动文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return bool
     */
    public function move($source, $destination)
    {
        $response = $this->client->post('fileSystem/move', [
            'source' => $source,
            'destination' => $destination,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 检查文件是否存在
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function exists($path)
    {
        $response = $this->client->post('fileSystem/exists', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['exists'] ?? false);
    }

    /**
     * 获取文件大小
     *
     * @param string $path 文件路径
     * @return int|false
     */
    public function size($path)
    {
        $response = $this->client->post('fileSystem/size', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return ($data['success'] ?? false) ? (int) ($data['size'] ?? 0) : false;
    }

    /**
     * 获取文件修改时间
     *
     * @param string $path 文件路径
     * @return int|false
     */
    public function lastModified($path)
    {
        $response = $this->client->post('fileSystem/last-modified', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return ($data['success'] ?? false) ? (int) ($data['time'] ?? 0) : false;
    }

    /**
     * 创建目录
     *
     * @param string $path 目录路径
     * @param int $mode 权限模式
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public function makeDirectory($path, $mode = 0755, $recursive = false)
    {
        $response = $this->client->post('fileSystem/make-directory', [
            'path' => $path,
            'mode' => $mode,
            'recursive' => $recursive,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 删除目录
     *
     * @param string $path 目录路径
     * @param bool $recursive 是否递归删除
     * @return bool
     */
    public function deleteDirectory($path, $recursive = false)
    {
        $response = $this->client->post('fileSystem/delete-directory', [
            'path' => $path,
            'recursive' => $recursive,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }
}
