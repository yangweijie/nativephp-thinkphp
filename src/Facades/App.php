<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string name() 获取应用名称
 * @method static string id() 获取应用ID
 * @method static string version() 获取应用版本
 * @method static string getRootPath() 获取应用根路径
 * @method static string getAppPath() 获取应用路径
 * @method static string getPublicPath() 获取应用公共路径
 * @method static string getRuntimePath() 获取应用运行时路径
 * @method static void quit() 退出应用
 * @method static void restart() 重启应用
 * @method static void focus() 聚焦应用
 * @method static void hide() 隐藏应用
 * @method static bool isHidden() 检查应用是否隐藏
 * @method static int badgeCount(int|null $count = null) 设置或获取应用徽章计数
 * @method static void addRecentDocument(string $path) 添加最近文档
 * @method static array recentDocuments() 获取最近文档列表
 * @method static void clearRecentDocuments() 清除最近文档列表
 * @method static bool isRunningBundled() 检查应用是否以打包方式运行
 * @method static bool openAtLogin(bool|null $open = null) 设置或获取应用是否在登录时启动
 * @method static void minimize() 最小化应用
 * @method static void maximize() 最大化应用
 * @method static void restore() 恢复应用窗口大小
 *
 * @see \Native\ThinkPHP\App
 */
class App extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.app';
    }
}
