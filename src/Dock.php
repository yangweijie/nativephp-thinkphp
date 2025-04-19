<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Dock
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
     * 是否为 macOS 系统
     *
     * @var bool
     */
    protected $isMacOS;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
        $this->isMacOS = PHP_OS === 'Darwin';
    }

    /**
     * 设置 Dock 图标
     *
     * @param string $path 图标路径
     * @return bool
     */
    public function setIcon($path)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-icon', [
            'path' => $path,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置 Dock 徽章文本
     *
     * @param string $text 徽章文本
     * @return bool
     */
    public function setBadge($text)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-badge', [
            'text' => $text,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置 Dock 徽章计数
     *
     * @param int $count 徽章计数
     * @return bool
     */
    public function setBadgeCount($count)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-badge-count', [
            'count' => $count,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取 Dock 徽章计数
     *
     * @return int
     */
    public function getBadgeCount()
    {
        if (!$this->isMacOS) {
            return 0;
        }

        $response = $this->client->get('dock/get-badge-count');
        return (int) $response->json('count');
    }

    /**
     * 清除 Dock 徽章
     *
     * @return bool
     */
    public function clearBadge()
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/clear-badge');
        return (bool) $response->json('success');
    }

    /**
     * 设置 Dock 菜单
     *
     * @param array $items 菜单项
     * @return bool
     */
    public function setMenu(array $items)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-menu', [
            'items' => $items,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 显示 Dock 图标
     *
     * @return bool
     */
    public function show()
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/show');
        return (bool) $response->json('success');
    }

    /**
     * 隐藏 Dock 图标
     *
     * @return bool
     */
    public function hide()
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/hide');
        return (bool) $response->json('success');
    }

    /**
     * 检查 Dock 图标是否可见
     *
     * @return bool
     */
    public function isVisible()
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->get('dock/is-visible');
        return (bool) $response->json('visible');
    }

    /**
     * 弹跳 Dock 图标
     *
     * @param string $type 弹跳类型，'informational' 或 'critical'
     * @return bool
     */
    public function bounce($type = 'informational')
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/bounce', [
            'type' => $type,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 取消弹跳 Dock 图标
     *
     * @param int $id 弹跳ID
     * @return bool
     */
    public function cancelBounce($id)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/cancel-bounce', [
            'id' => $id,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 下载进度条
     *
     * @param float $progress 进度，0.0 到 1.0 之间的值
     * @return bool
     */
    public function setDownloadProgress($progress)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-download-progress', [
            'progress' => $progress,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 清除下载进度条
     *
     * @return bool
     */
    public function clearDownloadProgress()
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/clear-download-progress');
        return (bool) $response->json('success');
    }

    /**
     * 设置 Dock 图标的工具提示
     *
     * @param string $tooltip 工具提示文本
     * @return bool
     */
    public function setToolTip($tooltip)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-tooltip', [
            'tooltip' => $tooltip,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 注册 Dock 菜单点击事件
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onMenuClick($callback)
    {
        if (!$this->isMacOS) {
            return '';
        }

        $id = md5('dock-menu-click' . microtime(true));

        $response = $this->client->post('dock/on-menu-click', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.dock.menu.click', function ($menuItem) use ($callback, $id) {
                if ($menuItem['id'] === $id) {
                    call_user_func($callback, $menuItem);
                }
            });
        }

        return $id;
    }

    /**
     * 注册 Dock 图标点击事件
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onClick($callback)
    {
        if (!$this->isMacOS) {
            return '';
        }

        $id = md5('dock-click' . microtime(true));

        $response = $this->client->post('dock/on-click', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.dock.click', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $id;
    }

    /**
     * 移除 Dock 菜单点击事件监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function offMenuClick($id)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/off-menu-click', [
            'id' => $id,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 移除 Dock 图标点击事件监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function offClick($id)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/off-click', [
            'id' => $id,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置 Dock 图标闪烁
     *
     * @param bool $flash 是否闪烁
     * @return bool
     */
    public function setFlash($flash = true)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/set-flash', [
            'flash' => $flash,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 创建自定义 Dock 菜单
     *
     * @param array $template 菜单模板
     * @return bool
     */
    public function createMenu(array $template)
    {
        if (!$this->isMacOS) {
            return false;
        }

        $response = $this->client->post('dock/create-menu', [
            'template' => $template,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取 Dock 图标大小
     *
     * @return array
     */
    public function getIconSize()
    {
        if (!$this->isMacOS) {
            return ['width' => 0, 'height' => 0];
        }

        $response = $this->client->get('dock/get-icon-size');
        return $response->json('size') ?? ['width' => 0, 'height' => 0];
    }
}
