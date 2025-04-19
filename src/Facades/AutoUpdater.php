<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\AutoUpdater setFeedURL(string $url) 设置更新服务器 URL
 * @method static \Native\ThinkPHP\AutoUpdater setAutoDownload(bool $autoDownload = true) 设置是否自动下载更新
 * @method static \Native\ThinkPHP\AutoUpdater setAutoInstall(bool $autoInstall = true) 设置是否自动安装更新
 * @method static \Native\ThinkPHP\AutoUpdater setAllowPrerelease(bool $allowPrerelease = true) 设置是否允许预发布版本
 * @method static bool checkForUpdates() 检查更新
 * @method static bool downloadUpdate() 下载更新
 * @method static bool installUpdate() 安装更新
 * @method static string getCurrentVersion() 获取当前版本
 * @method static string|null getLatestVersion() 获取最新版本
 * @method static array|null getUpdateInfo() 获取更新信息
 * @method static \Native\ThinkPHP\AutoUpdater onCheckingForUpdate(callable $callback) 监听更新检查事件
 * @method static \Native\ThinkPHP\AutoUpdater onUpdateAvailable(callable $callback) 监听更新可用事件
 * @method static \Native\ThinkPHP\AutoUpdater onUpdateNotAvailable(callable $callback) 监听更新不可用事件
 * @method static \Native\ThinkPHP\AutoUpdater onDownloadProgress(callable $callback) 监听更新下载进度事件
 * @method static \Native\ThinkPHP\AutoUpdater onUpdateDownloaded(callable $callback) 监听更新下载完成事件
 * @method static \Native\ThinkPHP\AutoUpdater onError(callable $callback) 监听更新错误事件
 * @method static bool cancelDownload() 取消更新下载
 * @method static bool quitAndInstall() 重启应用并安装更新
 */
class AutoUpdater extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.auto-updater';
    }
}
