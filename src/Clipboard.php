<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Clipboard
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
     * 读取剪贴板文本
     *
     * @return string
     */
    public function text()
    {
        $response = $this->client->get('clipboard/text');
        return $response->json('text') ?? '';
    }

    /**
     * 写入文本到剪贴板
     *
     * @param string $text 文本内容
     * @return void
     */
    public function setText($text)
    {
        $this->client->post('clipboard/text', [
            'text' => $text,
        ]);
    }

    /**
     * 读取剪贴板图片
     *
     * @return string|null 图片的 Data URL
     */
    public function image()
    {
        $response = $this->client->get('clipboard/image');
        return $response->json('image');
    }

    /**
     * 写入图片到剪贴板
     *
     * @param string $path 图片路径
     * @return void
     */
    public function setImage($path)
    {
        $this->client->post('clipboard/image', [
            'path' => $path,
        ]);
    }

    /**
     * 清空剪贴板
     *
     * @return void
     */
    public function clear()
    {
        $this->client->post('clipboard/clear');
    }

    /**
     * 读取剪贴板 HTML
     *
     * @return string
     */
    public function html()
    {
        $response = $this->client->get('clipboard/html');
        return $response->json('html') ?? '';
    }

    /**
     * 写入 HTML 到剪贴板
     *
     * @param string $html HTML 内容
     * @return void
     */
    public function setHtml($html)
    {
        $this->client->post('clipboard/html', [
            'html' => $html,
        ]);
    }

    /**
     * 检查剪贴板是否包含指定格式的数据
     *
     * @param string $format 格式，如 'text/plain', 'text/html', 'image/png' 等
     * @return bool
     */
    public function has($format)
    {
        $response = $this->client->post('clipboard/has', [
            'format' => $format,
        ]);

        return (bool) $response->json('has');
    }

    /**
     * 获取剪贴板中可用的格式
     *
     * @return array
     */
    public function formats()
    {
        $response = $this->client->get('clipboard/formats');
        return $response->json('formats') ?? [];
    }

    /**
     * 读取剪贴板 RTF
     *
     * @return string
     */
    public function rtf()
    {
        $response = $this->client->get('clipboard/rtf');
        return $response->json('rtf') ?? '';
    }

    /**
     * 写入 RTF 到剪贴板
     *
     * @param string $rtf RTF 内容
     * @return void
     */
    public function setRtf($rtf)
    {
        $this->client->post('clipboard/rtf', [
            'rtf' => $rtf,
        ]);
    }

    /**
     * 读取剪贴板文件路径
     *
     * @return array
     */
    public function files()
    {
        $response = $this->client->get('clipboard/files');
        return $response->json('files') ?? [];
    }

    /**
     * 写入文件路径到剪贴板
     *
     * @param array $files 文件路径数组
     * @return void
     */
    public function setFiles(array $files)
    {
        $this->client->post('clipboard/files', [
            'files' => $files,
        ]);
    }

    /**
     * 读取剪贴板自定义格式数据
     *
     * @param string $format 格式
     * @return string|null
     */
    public function readFormat($format)
    {
        $response = $this->client->post('clipboard/read-format', [
            'format' => $format,
        ]);

        return $response->json('data');
    }

    /**
     * 写入自定义格式数据到剪贴板
     *
     * @param string $format 格式
     * @param string $data 数据
     * @return void
     */
    public function writeFormat($format, $data)
    {
        $this->client->post('clipboard/write-format', [
            'format' => $format,
            'data' => $data,
        ]);
    }

    /**
     * 监听剪贴板变化
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onChange($callback)
    {
        $id = md5('clipboard-change-' . microtime(true));

        $response = $this->client->post('clipboard/on-change', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.clipboard.change', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $id;
    }

    /**
     * 移除剪贴板变化监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function offChange($id)
    {
        $response = $this->client->post('clipboard/off-change', [
            'id' => $id,
        ]);

        return (bool) $response->json('success');
    }
}
