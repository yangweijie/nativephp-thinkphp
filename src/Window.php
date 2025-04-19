<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use Native\ThinkPHP\Concerns\DetectsWindowId;

class Window
{
    use DetectsWindowId;

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
     * 打开一个新窗口
     *
     * @param string $url
     * @param array $options
     * @return string 窗口ID
     */
    public function open($url, array $options = [])
    {
        $response = $this->client->post('window/open', [
            'url' => $url,
            'options' => $options,
        ]);

        $data = json_decode($response->getContent(), true);
        return $data['id'] ?? null;
    }

    /**
     * 关闭窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则关闭当前窗口
     * @return bool
     */
    public function close($id = null)
    {
        $response = $this->client->post('window/close', [
            'id' => $id ?? $this->detectId(),
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 最小化窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则最小化当前窗口
     * @return bool
     */
    public function minimize($id = null)
    {
        $response = $this->client->post('window/minimize', [
            'id' => $id ?? $this->detectId(),
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 最大化窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则最大化当前窗口
     * @return bool
     */
    public function maximize($id = null)
    {
        $response = $this->client->post('window/maximize', [
            'id' => $id ?? $this->detectId(),
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 恢复窗口大小
     *
     * @param string|null $id 窗口ID，如果为 null 则恢复当前窗口
     * @return bool
     */
    public function restore($id = null)
    {
        $response = $this->client->post('window/restore', [
            'id' => $id ?? $this->detectId(),
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置窗口标题
     *
     * @param string $title 窗口标题
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setTitle($title, $id = null)
    {
        $response = $this->client->post('window/title', [
            'id' => $id ?? $this->detectId(),
            'title' => $title,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置窗口大小
     *
     * @param int $width 窗口宽度
     * @param int $height 窗口高度
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setSize($width, $height, $id = null)
    {
        $response = $this->client->post('window/resize', [
            'id' => $id ?? $this->detectId(),
            'width' => $width,
            'height' => $height,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置窗口位置
     *
     * @param int $x X 坐标
     * @param int $y Y 坐标
     * @param bool $animated 是否使用动画
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setPosition($x, $y, $animated = false, $id = null)
    {
        $response = $this->client->post('window/position', [
            'id' => $id ?? $this->detectId(),
            'x' => $x,
            'y' => $y,
            'animate' => $animated,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置窗口是否可调整大小
     *
     * @param bool $resizable 是否可调整大小
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setResizable($resizable, $id = null)
    {
        $response = $this->client->post('window/resizable', [
            'id' => $id ?? $this->detectId(),
            'resizable' => $resizable,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置窗口是否总是置顶
     *
     * @param bool $alwaysOnTop 是否总是置顶
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function alwaysOnTop($alwaysOnTop = true, $id = null)
    {
        $response = $this->client->post('window/always-on-top', [
            'id' => $id ?? $this->detectId(),
            'alwaysOnTop' => $alwaysOnTop,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 重新加载窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则重新加载当前窗口
     * @return bool
     */
    public function reload($id = null)
    {
        $response = $this->client->post('window/reload', [
            'id' => $id ?? $this->detectId(),
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 获取当前窗口
     *
     * @return object
     */
    public function current()
    {
        $response = $this->client->get('window/current');
        return json_decode($response->getContent());
    }

    /**
     * 获取所有窗口
     *
     * @return array
     */
    public function all()
    {
        $response = $this->client->get('window/all');
        return json_decode($response->getContent(), true);
    }

    /**
     * 设置窗口是否全屏
     *
     * @param bool $fullscreen 是否全屏
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setFullscreen($fullscreen, $id = null)
    {
        $response = $this->client->post('window/fullscreen', [
            'id' => $id ?? $this->detectId(),
            'fullscreen' => $fullscreen,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 关闭所有窗口
     *
     * @return bool
     */
    public function closeAll()
    {
        $response = $this->client->post('window/close-all');

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 聚焦窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则聚焦当前窗口
     * @return bool
     */
    public function focus($id = null)
    {
        $response = $this->client->post('window/focus', [
            'id' => $id ?? $this->detectId(),
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 显示窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则显示当前窗口
     * @return bool
     */
    public function show($id = null)
    {
        $response = $this->client->post('window/show', [
            'id' => $id ?? $this->detectId(),
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 隐藏窗口
     *
     * @param string|null $id 窗口ID，如果为 null 则隐藏当前窗口
     * @return bool
     */
    public function hide($id = null)
    {
        $response = $this->client->post('window/hide', [
            'id' => $id ?? $this->detectId(),
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否可见
     *
     * @param bool $visible 是否可见
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setVisible($visible, $id = null)
    {
        $response = $this->client->post('window/visible', [
            'id' => $id ?? $this->detectId(),
            'visible' => $visible,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否可聚焦
     *
     * @param bool $focusable 是否可聚焦
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setFocusable($focusable, $id = null)
    {
        $response = $this->client->post('window/focusable', [
            'id' => $id ?? $this->detectId(),
            'focusable' => $focusable,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否可关闭
     *
     * @param bool $closable 是否可关闭
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setClosable($closable, $id = null)
    {
        $response = $this->client->post('window/closable', [
            'id' => $id ?? $this->detectId(),
            'closable' => $closable,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否可最小化
     *
     * @param bool $minimizable 是否可最小化
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setMinimizable($minimizable, $id = null)
    {
        $response = $this->client->post('window/minimizable', [
            'id' => $id ?? $this->detectId(),
            'minimizable' => $minimizable,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否可最大化
     *
     * @param bool $maximizable 是否可最大化
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setMaximizable($maximizable, $id = null)
    {
        $response = $this->client->post('window/maximizable', [
            'id' => $id ?? $this->detectId(),
            'maximizable' => $maximizable,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 监听窗口关闭事件
     *
     * @param callable $callback 回调函数
     * @param string|null $id 窗口ID，如果为 null 则监听当前窗口
     * @return string 监听器ID
     */
    public function onClose($callback, $id = null)
    {
        $id = $id ?? $this->detectId();
        $listenerId = md5('window-close-' . $id . '-' . microtime(true));

        $response = $this->client->post('window/on-close', [
            'id' => $id,
            'listener_id' => $listenerId,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.window.close', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $listenerId;
    }

    /**
     * 监听窗口聚焦事件
     *
     * @param callable $callback 回调函数
     * @param string|null $id 窗口ID，如果为 null 则监听当前窗口
     * @return string 监听器ID
     */
    public function onFocus($callback, $id = null)
    {
        $id = $id ?? $this->detectId();
        $listenerId = md5('window-focus-' . $id . '-' . microtime(true));

        $response = $this->client->post('window/on-focus', [
            'id' => $id,
            'listener_id' => $listenerId,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.window.focus', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $listenerId;
    }

    /**
     * 监听窗口失去聚焦事件
     *
     * @param callable $callback 回调函数
     * @param string|null $id 窗口ID，如果为 null 则监听当前窗口
     * @return string 监听器ID
     */
    public function onBlur($callback, $id = null)
    {
        $id = $id ?? $this->detectId();
        $listenerId = md5('window-blur-' . $id . '-' . microtime(true));

        $response = $this->client->post('window/on-blur', [
            'id' => $id,
            'listener_id' => $listenerId,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.window.blur', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $listenerId;
    }

    /**
     * 监听窗口移动事件
     *
     * @param callable $callback 回调函数
     * @param string|null $id 窗口ID，如果为 null 则监听当前窗口
     * @return string 监听器ID
     */
    public function onMove($callback, $id = null)
    {
        $id = $id ?? $this->detectId();
        $listenerId = md5('window-move-' . $id . '-' . microtime(true));

        $response = $this->client->post('window/on-move', [
            'id' => $id,
            'listener_id' => $listenerId,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.window.move', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $listenerId;
    }

    /**
     * 监听窗口调整大小事件
     *
     * @param callable $callback 回调函数
     * @param string|null $id 窗口ID，如果为 null 则监听当前窗口
     * @return string 监听器ID
     */
    public function onResize($callback, $id = null)
    {
        $id = $id ?? $this->detectId();
        $listenerId = md5('window-resize-' . $id . '-' . microtime(true));

        $response = $this->client->post('window/on-resize', [
            'id' => $id,
            'listener_id' => $listenerId,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.window.resize', function ($event) use ($callback, $id) {
                if ($event['id'] === $id) {
                    call_user_func($callback, $event);
                }
            });
        }

        return $listenerId;
    }

    /**
     * 移除窗口事件监听器
     *
     * @param string $listenerId 监听器ID
     * @param string $event 事件类型，如 'close', 'focus', 'blur', 'move', 'resize'
     * @return bool
     */
    public function off($listenerId, $event)
    {
        $response = $this->client->post('window/off', [
            'listener_id' => $listenerId,
            'event' => $event,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口图标
     *
     * @param string $icon 图标路径
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setIcon($icon, $id = null)
    {
        $response = $this->client->post('window/icon', [
            'id' => $id ?? $this->detectId(),
            'icon' => $icon,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否显示在任务栏
     *
     * @param bool $skipTaskbar 是否跳过任务栏
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function skipTaskbar($skipTaskbar = true, $id = null)
    {
        $response = $this->client->post('window/skip-taskbar', [
            'id' => $id ?? $this->detectId(),
            'skipTaskbar' => $skipTaskbar,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否有阴影
     *
     * @param bool $hasShadow 是否有阴影
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setHasShadow($hasShadow, $id = null)
    {
        $response = $this->client->post('window/has-shadow', [
            'id' => $id ?? $this->detectId(),
            'hasShadow' => $hasShadow,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否透明
     *
     * @param bool $transparent 是否透明
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setTransparent($transparent, $id = null)
    {
        $response = $this->client->post('window/transparent', [
            'id' => $id ?? $this->detectId(),
            'transparent' => $transparent,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否模态
     *
     * @param bool $modal 是否模态
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setModal($modal, $id = null)
    {
        $response = $this->client->post('window/modal', [
            'id' => $id ?? $this->detectId(),
            'modal' => $modal,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口父窗口
     *
     * @param string $parentId 父窗口ID
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setParent($parentId, $id = null)
    {
        $response = $this->client->post('window/parent', [
            'id' => $id ?? $this->detectId(),
            'parentId' => $parentId,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否显示框架
     *
     * @param bool $frame 是否显示框架
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setFrame($frame, $id = null)
    {
        $response = $this->client->post('window/frame', [
            'id' => $id ?? $this->detectId(),
            'frame' => $frame,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口是否居中
     *
     * @param bool $center 是否居中
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setCenter($center, $id = null)
    {
        $response = $this->client->post('window/center', [
            'id' => $id ?? $this->detectId(),
            'center' => $center,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 设置窗口主题
     *
     * @param string $theme 主题名称
     * @param string|null $id 窗口ID，如果为 null 则设置当前窗口
     * @return bool
     */
    public function setTheme($theme, $id = null)
    {
        $response = $this->client->post('window/theme', [
            'id' => $id ?? $this->detectId(),
            'theme' => $theme,
        ]);

        return (bool) $response->json('success');
    }
}
