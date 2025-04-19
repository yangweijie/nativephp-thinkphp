<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class GlobalShortcut
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
     * 已注册的快捷键
     *
     * @var array
     */
    protected $shortcuts = [];

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
     * 注册全局快捷键
     *
     * @param string $accelerator 快捷键组合，如 'CommandOrControl+Shift+G'
     * @param callable $callback 回调函数
     * @return bool
     */
    public function register($accelerator, $callback)
    {
        $response = $this->client->post('globalShortcut/register', [
            'accelerator' => $accelerator,
        ]);

        if ($response->json('success')) {
            $this->shortcuts[$accelerator] = $callback;
            return true;
        }

        return false;
    }

    /**
     * 检查快捷键是否已注册
     *
     * @param string $accelerator 快捷键组合
     * @return bool
     */
    public function isRegistered($accelerator)
    {
        $response = $this->client->post('globalShortcut/is-registered', [
            'accelerator' => $accelerator,
        ]);

        return (bool) $response->json('registered');
    }

    /**
     * 注销指定的快捷键
     *
     * @param string $accelerator 快捷键组合
     * @return bool
     */
    public function unregister($accelerator)
    {
        $response = $this->client->post('globalShortcut/unregister', [
            'accelerator' => $accelerator,
        ]);

        if ($response->json('success')) {
            unset($this->shortcuts[$accelerator]);
            return true;
        }

        return false;
    }

    /**
     * 注销所有快捷键
     *
     * @return void
     */
    public function unregisterAll()
    {
        $this->client->post('globalShortcut/unregister-all');
        $this->shortcuts = [];
    }

    /**
     * 获取所有已注册的快捷键
     *
     * @return array
     */
    public function getShortcuts()
    {
        return array_keys($this->shortcuts);
    }
}
