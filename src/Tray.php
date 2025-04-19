<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Tray
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
     * 托盘图标路径
     *
     * @var string|null
     */
    protected $iconPath = null;

    /**
     * 托盘提示文本
     *
     * @var string|null
     */
    protected $tooltip = null;

    /**
     * 托盘菜单项
     *
     * @var array
     */
    protected $menuItems = [];

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
     * 设置托盘图标
     *
     * @param string $path 图标路径
     * @return $this
     */
    public function setIcon($path)
    {
        $this->iconPath = $path;

        return $this;
    }

    /**
     * 设置托盘提示文本
     *
     * @param string $tooltip
     * @return $this
     */
    public function setTooltip($tooltip)
    {
        $this->tooltip = $tooltip;

        return $this;
    }

    /**
     * 设置托盘菜单
     *
     * @param callable $callback
     * @return $this
     */
    public function setMenu(callable $callback)
    {
        $menu = new Menu($this->app);
        $callback($menu);

        $this->menuItems = $menu->getItems();

        return $this;
    }

    /**
     * 设置托盘图标标题（仅在 macOS 上有效）
     *
     * @param string $title 标题文本
     * @return $this
     */
    public function setTitle($title)
    {
        $this->client->post('tray/set-title', [
            'title' => $title,
        ]);

        return $this;
    }

    /**
     * 设置托盘图标图像
     *
     * @param string $path 图像路径
     * @param string|null $title 标题文本（仅在 macOS 上有效）
     * @return $this
     */
    public function setImage($path, $title = null)
    {
        $this->iconPath = $path;

        $data = ['image' => $path];

        if ($title !== null) {
            $data['title'] = $title;
        }

        $this->client->post('tray/set-image', $data);

        return $this;
    }

    /**
     * 显示气泡提示
     *
     * @param string $title 标题
     * @param string $content 内容
     * @param array $options 选项
     * @return $this
     */
    public function showBalloon($title, $content, array $options = [])
    {
        $this->client->post('tray/show-balloon', [
            'title' => $title,
            'content' => $content,
            'options' => $options,
        ]);

        return $this;
    }

    /**
     * 设置托盘图标是否高亮（仅在 Windows 上有效）
     *
     * @param bool $highlighted 是否高亮
     * @return $this
     */
    public function setHighlighted($highlighted = true)
    {
        $this->client->post('tray/set-highlighted', [
            'highlighted' => $highlighted,
        ]);

        return $this;
    }

    /**
     * 设置托盘图标是否忽略双击事件
     *
     * @param bool $ignore 是否忽略
     * @return $this
     */
    public function setIgnoreDoubleClickEvents($ignore = true)
    {
        $this->client->post('tray/set-ignore-double-click-events', [
            'ignore' => $ignore,
        ]);

        return $this;
    }

    /**
     * 显示托盘图标
     *
     * @return bool
     */
    public function show()
    {
        $response = $this->client->post('tray/show', [
            'icon' => $this->iconPath,
            'tooltip' => $this->tooltip,
            'menu' => $this->menuItems,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 隐藏托盘图标
     *
     * @return bool
     */
    public function hide()
    {
        $response = $this->client->post('tray/hide');

        return (bool) $response->json('success');
    }

    /**
     * 销毁托盘图标
     *
     * @return bool
     */
    public function destroy()
    {
        $response = $this->client->post('tray/destroy');

        return (bool) $response->json('success');
    }

    /**
     * 注册点击事件
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onClick($callback)
    {
        $id = md5('tray-click' . microtime(true));

        $response = $this->client->post('tray/on-click', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.tray.click', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $id;
    }

    /**
     * 注册双击事件
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onDoubleClick($callback)
    {
        $id = md5('tray-double-click' . microtime(true));

        $response = $this->client->post('tray/on-double-click', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.tray.double-click', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $id;
    }

    /**
     * 注册右键点击事件
     *
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function onRightClick($callback)
    {
        $id = md5('tray-right-click' . microtime(true));

        $response = $this->client->post('tray/on-right-click', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.tray.right-click', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $id;
    }

    /**
     * 移除事件监听器
     *
     * @param string $id 监听器ID
     * @param string $event 事件类型，如 'click', 'double-click', 'right-click'
     * @return bool
     */
    public function off($id, $event = 'click')
    {
        $response = $this->client->post('tray/off', [
            'id' => $id,
            'event' => $event,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取托盘图标路径
     *
     * @return string|null
     */
    public function getIconPath()
    {
        return $this->iconPath;
    }

    /**
     * 获取托盘提示文本
     *
     * @return string|null
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * 获取托盘菜单项
     *
     * @return array
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }
}
