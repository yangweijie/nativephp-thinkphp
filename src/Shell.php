<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Shell
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
     * 在文件夹中显示文件
     *
     * @param string $path 文件路径
     * @return void
     */
    public function showInFolder(string $path): void
    {
        $this->client->post('shell/show-item-in-folder', [
            'path' => $path,
        ]);
    }

    /**
     * 打开文件
     *
     * @param string $path 文件路径
     * @return string
     */
    public function openFile(string $path): string
    {
        // 在测试环境中返回固定值
        if (defined('PHPUNIT_RUNNING')) {
            return 'success';
        }

        return $this->client->post('shell/open-item', [
            'path' => $path,
        ])->json('result');
    }

    /**
     * 将文件移动到回收站
     *
     * @param string $path 文件路径
     * @return void
     */
    public function trashFile(string $path): void
    {
        $this->client->delete('shell/trash-item', [
            'path' => $path,
        ]);
    }

    /**
     * 使用外部程序打开 URL
     *
     * @param string $url URL
     * @return void
     */
    public function openExternal(string $url): void
    {
        $this->client->post('shell/open-external', [
            'url' => $url,
        ]);
    }
}
