<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Tray setIcon(string $path) 设置托盘图标
 * @method static \Native\ThinkPHP\Tray setTooltip(string $tooltip) 设置托盘提示文本
 * @method static \Native\ThinkPHP\Tray setMenu(callable $callback) 设置托盘菜单
 * @method static \Native\ThinkPHP\Tray setTitle(string $title) 设置托盘图标标题（仅在 macOS 上有效）
 * @method static \Native\ThinkPHP\Tray setImage(string $path, string|null $title = null) 设置托盘图标图像
 * @method static \Native\ThinkPHP\Tray showBalloon(string $title, string $content, array $options = []) 显示气泡提示
 * @method static \Native\ThinkPHP\Tray setHighlighted(bool $highlighted = true) 设置托盘图标是否高亮（仅在 Windows 上有效）
 * @method static \Native\ThinkPHP\Tray setIgnoreDoubleClickEvents(bool $ignore = true) 设置托盘图标是否忽略双击事件
 * @method static bool show() 显示托盘图标
 * @method static bool hide() 隐藏托盘图标
 * @method static bool destroy() 销毁托盘图标
 * @method static string onClick(callable $callback) 注册点击事件
 * @method static string onDoubleClick(callable $callback) 注册双击事件
 * @method static string onRightClick(callable $callback) 注册右键点击事件
 * @method static bool off(string $id, string $event = 'click') 移除事件监听器
 * @method static string|null getIconPath() 获取托盘图标路径
 * @method static string|null getTooltip() 获取托盘提示文本
 * @method static array getMenuItems() 获取托盘菜单项
 *
 * @see \Native\ThinkPHP\Tray
 */
class Tray extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.tray';
    }
}
