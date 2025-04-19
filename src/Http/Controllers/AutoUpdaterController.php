<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\AutoUpdater;

class AutoUpdaterController
{
    /**
     * 设置更新服务器 URL
     *
     * @param Request $request
     * @return Response
     */
    public function setFeedURL(Request $request)
    {
        $url = $request->param('url');
        
        // 设置更新服务器 URL
        AutoUpdater::setFeedURL($url);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 设置是否自动下载更新
     *
     * @param Request $request
     * @return Response
     */
    public function setAutoDownload(Request $request)
    {
        $autoDownload = $request->param('autoDownload', true);
        
        // 设置是否自动下载更新
        AutoUpdater::setAutoDownload($autoDownload);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 设置是否自动安装更新
     *
     * @param Request $request
     * @return Response
     */
    public function setAutoInstall(Request $request)
    {
        $autoInstall = $request->param('autoInstall', false);
        
        // 设置是否自动安装更新
        AutoUpdater::setAutoInstall($autoInstall);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 设置是否允许预发布版本
     *
     * @param Request $request
     * @return Response
     */
    public function setAllowPrerelease(Request $request)
    {
        $allowPrerelease = $request->param('allowPrerelease', false);
        
        // 设置是否允许预发布版本
        AutoUpdater::setAllowPrerelease($allowPrerelease);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 检查更新
     *
     * @return Response
     */
    public function checkForUpdates()
    {
        // 检查更新
        $checking = AutoUpdater::checkForUpdates();
        
        return json([
            'checking' => $checking,
        ]);
    }
    
    /**
     * 下载更新
     *
     * @return Response
     */
    public function downloadUpdate()
    {
        // 下载更新
        $downloading = AutoUpdater::downloadUpdate();
        
        return json([
            'downloading' => $downloading,
        ]);
    }
    
    /**
     * 安装更新
     *
     * @return Response
     */
    public function installUpdate()
    {
        // 安装更新
        $installing = AutoUpdater::installUpdate();
        
        return json([
            'installing' => $installing,
        ]);
    }
    
    /**
     * 获取当前版本
     *
     * @return Response
     */
    public function getCurrentVersion()
    {
        // 获取当前版本
        $version = AutoUpdater::getCurrentVersion();
        
        return json([
            'version' => $version,
        ]);
    }
    
    /**
     * 获取最新版本
     *
     * @return Response
     */
    public function getLatestVersion()
    {
        // 获取最新版本
        $version = AutoUpdater::getLatestVersion();
        
        return json([
            'version' => $version,
        ]);
    }
    
    /**
     * 获取更新信息
     *
     * @return Response
     */
    public function getUpdateInfo()
    {
        // 获取更新信息
        $info = AutoUpdater::getUpdateInfo();
        
        return json([
            'info' => $info,
        ]);
    }
    
    /**
     * 监听更新检查事件
     *
     * @return Response
     */
    public function onCheckingForUpdate()
    {
        // 监听更新检查事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听更新可用事件
     *
     * @return Response
     */
    public function onUpdateAvailable()
    {
        // 监听更新可用事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听更新不可用事件
     *
     * @return Response
     */
    public function onUpdateNotAvailable()
    {
        // 监听更新不可用事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听更新下载进度事件
     *
     * @return Response
     */
    public function onDownloadProgress()
    {
        // 监听更新下载进度事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听更新下载完成事件
     *
     * @return Response
     */
    public function onUpdateDownloaded()
    {
        // 监听更新下载完成事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听更新错误事件
     *
     * @return Response
     */
    public function onError()
    {
        // 监听更新错误事件
        $success = true;
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 取消更新下载
     *
     * @return Response
     */
    public function cancelDownload()
    {
        // 取消更新下载
        $success = AutoUpdater::cancelDownload();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 重启应用并安装更新
     *
     * @return Response
     */
    public function quitAndInstall()
    {
        // 重启应用并安装更新
        $success = AutoUpdater::quitAndInstall();
        
        return json([
            'success' => $success,
        ]);
    }
}
