<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Updater setServerUrl(string $url) 设置更新服务器 URL
 * @method static array|null check() 检查更新
 * @method static bool download(string|null $version = null) 下载更新
 * @method static bool install() 安装更新
 * @method static bool checkAndDownload() 检查并下载更新
 * @method static bool checkAndInstall() 检查、下载并安装更新
 * @method static string|null getServerUrl() 获取更新服务器 URL
 * @method static string getCurrentVersion() 获取当前版本
 * @method static string getLatestVersion() 获取最新版本
 * @method static string getStatus() 获取更新状态
 * @method static float getProgress() 获取更新进度
 * @method static string getReleaseNotes() 获取更新日志
 * @method static bool setFeedURL(string $url) 设置更新服务器地址
 * @method static string getFeedURL() 获取更新服务器地址
 * @method static bool cancel() 取消更新
 * @method static bool isUpdateAvailable() 检查是否有更新可用
 * @method static bool isDownloading() 检查是否正在下载
 * @method static bool isDownloaded() 检查是否已下载完成
 * @method static bool isInstalling() 检查是否正在安装
 *
 * @see \Native\ThinkPHP\Updater
 */
class Updater extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.updater';
    }
}
