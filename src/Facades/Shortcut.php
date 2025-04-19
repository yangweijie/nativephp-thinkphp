<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool createDesktopShortcut(array $options = []) 创建桌面快捷方式
 * @method static bool createStartMenuShortcut(array $options = []) 创建开始菜单快捷方式
 * @method static bool createShortcut(string $path, array $options = []) 创建应用程序快捷方式
 * @method static bool existsOnDesktop() 检查桌面快捷方式是否存在
 * @method static bool existsInStartMenu() 检查开始菜单快捷方式是否存在
 * @method static bool exists(string $path) 检查快捷方式是否存在
 * @method static bool removeFromDesktop() 删除桌面快捷方式
 * @method static bool removeFromStartMenu() 删除开始菜单快捷方式
 * @method static bool remove(string $path) 删除快捷方式
 * @method static bool setLoginItemSettings(bool $enabled = true, array $options = []) 设置开机自启动
 * @method static array getLoginItemSettings(array $options = []) 获取开机自启动设置
 * @method static string getDesktopPath() 获取桌面路径
 * @method static string getStartMenuPath() 获取开始菜单路径
 * @method static string getApplicationPath() 获取应用程序路径
 * @method static string getApplicationName() 获取应用程序名称
 */
class Shortcut extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.shortcut';
    }
}
