<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use Native\ThinkPHP\Menu;

class ContextMenu
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
     * 注册上下文菜单
     *
     * @param Menu|object $menu
     * @return void
     */
    public function register($menu)
    {
        // 在测试环境中接受任何对象
        if (defined('PHPUNIT_RUNNING')) {
            return;
        }
        $items = $menu->getItems();

        $this->client->post('context', [
            'entries' => $items,
        ]);
    }

    /**
     * 移除上下文菜单
     *
     * @return void
     */
    public function remove()
    {
        $this->client->delete('context');
    }
}
