<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool setIcon(string $path) 设置 Dock 图标
 * @method static bool setBadge(string $text) 设置 Dock 徽章文本
 * @method static bool setBadgeCount(int $count) 设置 Dock 徽章计数
 * @method static int getBadgeCount() 获取 Dock 徽章计数
 * @method static bool clearBadge() 清除 Dock 徽章
 * @method static bool setMenu(array $items) 设置 Dock 菜单
 * @method static bool show() 显示 Dock 图标
 * @method static bool hide() 隐藏 Dock 图标
 * @method static bool isVisible() 检查 Dock 图标是否可见
 * @method static bool bounce(string $type = 'informational') 弹跳 Dock 图标
 * @method static bool cancelBounce(int $id) 取消弹跳 Dock 图标
 * @method static bool setDownloadProgress(float $progress) 下载进度条
 * @method static bool clearDownloadProgress() 清除下载进度条
 * @method static bool setToolTip(string $tooltip) 设置 Dock 图标的工具提示
 * @method static string onMenuClick(callable $callback) 注册 Dock 菜单点击事件
 * @method static bool offMenuClick(string $id) 移除 Dock 菜单点击事件监听器
 * @method static string onClick(callable $callback) 注册 Dock 图标点击事件
 * @method static bool offClick(string $id) 移除 Dock 图标点击事件监听器
 * @method static bool setFlash(bool $flash = true) 设置 Dock 图标闪烁
 * @method static bool createMenu(array $template) 创建自定义 Dock 菜单
 * @method static array getIconSize() 获取 Dock 图标大小
 *
 * @see \Native\ThinkPHP\Dock
 */
class Dock extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.dock';
    }
}
