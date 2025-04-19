<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string open(string $url, array $options = []) 打开一个新窗口
 * @method static bool close(string|null $id = null) 关闭窗口
 * @method static bool closeAll() 关闭所有窗口
 * @method static bool minimize(string|null $id = null) 最小化窗口
 * @method static bool maximize(string|null $id = null) 最大化窗口
 * @method static bool restore(string|null $id = null) 恢复窗口大小
 * @method static bool focus(string|null $id = null) 聚焦窗口
 * @method static bool show(string|null $id = null) 显示窗口
 * @method static bool hide(string|null $id = null) 隐藏窗口
 * @method static bool setVisible(bool $visible, string|null $id = null) 设置窗口是否可见
 * @method static bool setFocusable(bool $focusable, string|null $id = null) 设置窗口是否可聚焦
 * @method static bool setClosable(bool $closable, string|null $id = null) 设置窗口是否可关闭
 * @method static bool setMinimizable(bool $minimizable, string|null $id = null) 设置窗口是否可最小化
 * @method static bool setMaximizable(bool $maximizable, string|null $id = null) 设置窗口是否可最大化
 * @method static bool setTitle(string $title, string|null $id = null) 设置窗口标题
 * @method static bool setSize(int $width, int $height, string|null $id = null) 设置窗口大小
 * @method static bool setPosition(int $x, int $y, bool $animated = false, string|null $id = null) 设置窗口位置
 * @method static bool setResizable(bool $resizable, string|null $id = null) 设置窗口是否可调整大小
 * @method static bool alwaysOnTop(bool $alwaysOnTop = true, string|null $id = null) 设置窗口是否总是置顶
 * @method static bool setFullscreen(bool $fullscreen, string|null $id = null) 设置窗口是否全屏
 * @method static bool reload(string|null $id = null) 重新加载窗口
 * @method static array current() 获取当前窗口
 * @method static array all() 获取所有窗口
 * @method static string onClose(callable $callback, string|null $id = null) 监听窗口关闭事件
 * @method static string onFocus(callable $callback, string|null $id = null) 监听窗口聚焦事件
 * @method static string onBlur(callable $callback, string|null $id = null) 监听窗口失去聚焦事件
 * @method static string onMove(callable $callback, string|null $id = null) 监听窗口移动事件
 * @method static string onResize(callable $callback, string|null $id = null) 监听窗口调整大小事件
 * @method static bool off(string $listenerId, string $event) 移除窗口事件监听器
 * @method static bool setTheme(string $theme, string|null $id = null) 设置窗口主题
 *
 * @see \Native\ThinkPHP\Window
 */
class Window extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.window';
    }
}
