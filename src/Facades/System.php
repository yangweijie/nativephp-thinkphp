<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string getOS() 获取操作系统类型
 * @method static string getOSVersion() 获取操作系统版本
 * @method static string getArch() 获取 CPU 架构
 * @method static string getHostname() 获取主机名
 * @method static string getHomePath() 获取用户主目录
 * @method static string getTempPath() 获取临时目录
 * @method static string getAppDataPath() 获取应用数据目录
 * @method static array getMemoryInfo() 获取系统内存信息
 * @method static array getCPUInfo() 获取系统 CPU 信息
 * @method static array getNetworkInterfaces() 获取系统网络接口信息
 * @method static array getDisplays() 获取系统显示器信息
 * @method static array getBatteryInfo() 获取系统电池信息
 * @method static string getLanguage() 获取系统语言
 * @method static bool openExternal(string $url, array $options = []) 打开外部 URL
 * @method static bool openPath(string $path) 打开文件或目录
 * @method static bool showItemInFolder(string $path) 在文件管理器中显示文件
 * @method static bool moveItemToTrash(string $path) 移动文件到回收站
 * @method static void beep(string $type = 'info') 播放系统提示音
 * @method static bool sleep() 设置系统休眠状态
 * @method static bool lock() 设置系统锁屏状态
 * @method static bool logout() 设置系统注销状态
 * @method static bool restart() 重启系统
 * @method static bool shutdown() 关闭系统
 *
 * @see \Native\ThinkPHP\System
 */
class System extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.system';
    }
}
