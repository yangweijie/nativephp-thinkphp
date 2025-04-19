<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\ContextMenu;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Logger;

class ContextMenuPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'context-menu';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '上下文菜单插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
            'window.create' => [$this, 'onWindowCreate'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 加载配置的上下文菜单
        $this->loadContextMenu();

        // 监听菜单点击事件
        $this->app->event->listen('native.menu.click', function ($event) {
            $this->handleMenuClick($event);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('ContextMenu plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 移除上下文菜单
        ContextMenu::remove();
    }

    /**
     * 窗口创建事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowCreate(array $window): void
    {
        // 为新窗口注册上下文菜单
        $this->loadContextMenu();
    }

    /**
     * 加载配置的上下文菜单
     *
     * @return void
     */
    protected function loadContextMenu(): void
    {
        // 获取配置
        $config = config('native.context_menu', []);

        // 如果没有配置，则使用默认配置
        if (empty($config)) {
            return;
        }

        // 如果配置了菜单项，则创建上下文菜单
        if (isset($config['items']) && is_array($config['items'])) {
            $menu = Menu::create();

            foreach ($config['items'] as $item) {
                if (isset($item['type']) && $item['type'] === 'separator') {
                    $menu->separator();
                } elseif (isset($item['submenu']) && is_array($item['submenu'])) {
                    $menu->submenu($item['label'], function ($submenu) use ($item) {
                        foreach ($item['submenu'] as $subItem) {
                            if (isset($subItem['type']) && $subItem['type'] === 'separator') {
                                $submenu->separator();
                            } else {
                                $submenu->add($subItem['label'], $subItem['action'] ?? null);
                            }
                        }
                    });
                } else {
                    $menu->add($item['label'], $item['action'] ?? null);
                }
            }

            // 注册上下文菜单
            ContextMenu::register($menu);
        }
        // 如果配置了菜单构建器，则使用它创建上下文菜单
        elseif (isset($config['builder']) && is_callable($config['builder'])) {
            $menu = Menu::create();
            call_user_func($config['builder'], $menu);
            ContextMenu::register($menu);
        }
    }

    /**
     * 处理菜单点击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleMenuClick(array $event): void
    {
        // 获取配置
        $config = config('native.context_menu', []);

        // 如果配置了点击处理器，则使用它处理点击事件
        if (isset($config['click_handler']) && is_callable($config['click_handler'])) {
            call_user_func($config['click_handler'], $event);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 移除上下文菜单
        ContextMenu::remove();
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}
