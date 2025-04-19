<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\AutoUpdater;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;
use think\facade\Config;

class Updater extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取当前版本
        $currentVersion = AutoUpdater::getCurrentVersion();
        
        // 获取更新配置
        $updateConfig = Config::get('update', [
            'feed_url' => '',
            'auto_download' => true,
            'auto_install' => false,
            'allow_prerelease' => false,
        ]);
        
        return View::fetch('updater/index', [
            'currentVersion' => $currentVersion,
            'updateConfig' => $updateConfig,
        ]);
    }
    
    /**
     * 设置更新配置
     *
     * @return \think\Response
     */
    public function setConfig()
    {
        $feedUrl = input('feed_url');
        $autoDownload = input('auto_download/b', false);
        $autoInstall = input('auto_install/b', false);
        $allowPrerelease = input('allow_prerelease/b', false);
        
        if (empty($feedUrl)) {
            return json(['success' => false, 'message' => '更新服务器 URL 不能为空']);
        }
        
        // 保存配置
        Config::set([
            'feed_url' => $feedUrl,
            'auto_download' => $autoDownload,
            'auto_install' => $autoInstall,
            'allow_prerelease' => $allowPrerelease,
        ], 'update');
        
        // 设置更新服务器 URL
        AutoUpdater::setFeedURL($feedUrl);
        
        // 设置是否自动下载更新
        AutoUpdater::setAutoDownload($autoDownload);
        
        // 设置是否自动安装更新
        AutoUpdater::setAutoInstall($autoInstall);
        
        // 设置是否允许预发布版本
        AutoUpdater::setAllowPrerelease($allowPrerelease);
        
        return json(['success' => true, 'message' => '更新配置已保存']);
    }
    
    /**
     * 检查更新
     *
     * @return \think\Response
     */
    public function checkForUpdates()
    {
        // 获取更新配置
        $updateConfig = Config::get('update', [
            'feed_url' => '',
            'auto_download' => true,
            'auto_install' => false,
            'allow_prerelease' => false,
        ]);
        
        if (empty($updateConfig['feed_url'])) {
            return json(['success' => false, 'message' => '请先设置更新服务器 URL']);
        }
        
        // 设置更新服务器 URL
        AutoUpdater::setFeedURL($updateConfig['feed_url']);
        
        // 设置是否自动下载更新
        AutoUpdater::setAutoDownload($updateConfig['auto_download']);
        
        // 设置是否自动安装更新
        AutoUpdater::setAutoInstall($updateConfig['auto_install']);
        
        // 设置是否允许预发布版本
        AutoUpdater::setAllowPrerelease($updateConfig['allow_prerelease']);
        
        // 注册更新事件
        $this->registerUpdateEvents();
        
        // 检查更新
        $checking = AutoUpdater::checkForUpdates();
        
        if ($checking) {
            return json(['success' => true, 'message' => '正在检查更新']);
        } else {
            return json(['success' => false, 'message' => '检查更新失败']);
        }
    }
    
    /**
     * 下载更新
     *
     * @return \think\Response
     */
    public function downloadUpdate()
    {
        // 下载更新
        $downloading = AutoUpdater::downloadUpdate();
        
        if ($downloading) {
            return json(['success' => true, 'message' => '正在下载更新']);
        } else {
            return json(['success' => false, 'message' => '下载更新失败']);
        }
    }
    
    /**
     * 安装更新
     *
     * @return \think\Response
     */
    public function installUpdate()
    {
        // 安装更新
        $installing = AutoUpdater::installUpdate();
        
        if ($installing) {
            return json(['success' => true, 'message' => '正在安装更新']);
        } else {
            return json(['success' => false, 'message' => '安装更新失败']);
        }
    }
    
    /**
     * 重启应用并安装更新
     *
     * @return \think\Response
     */
    public function quitAndInstall()
    {
        // 重启应用并安装更新
        $result = AutoUpdater::quitAndInstall();
        
        if ($result) {
            return json(['success' => true, 'message' => '正在重启应用并安装更新']);
        } else {
            return json(['success' => false, 'message' => '重启应用并安装更新失败']);
        }
    }
    
    /**
     * 获取更新信息
     *
     * @return \think\Response
     */
    public function getUpdateInfo()
    {
        // 获取当前版本
        $currentVersion = AutoUpdater::getCurrentVersion();
        
        // 获取最新版本
        $latestVersion = AutoUpdater::getLatestVersion();
        
        // 获取更新信息
        $updateInfo = AutoUpdater::getUpdateInfo();
        
        return json([
            'success' => true,
            'currentVersion' => $currentVersion,
            'latestVersion' => $latestVersion,
            'updateInfo' => $updateInfo,
        ]);
    }
    
    /**
     * 取消更新下载
     *
     * @return \think\Response
     */
    public function cancelDownload()
    {
        // 取消更新下载
        $result = AutoUpdater::cancelDownload();
        
        if ($result) {
            return json(['success' => true, 'message' => '已取消更新下载']);
        } else {
            return json(['success' => false, 'message' => '取消更新下载失败']);
        }
    }
    
    /**
     * 注册更新事件
     *
     * @return void
     */
    protected function registerUpdateEvents()
    {
        // 监听更新检查事件
        AutoUpdater::onCheckingForUpdate(function ($event) {
            $this->sendUpdateEvent('checking-for-update', '正在检查更新');
        });
        
        // 监听更新可用事件
        AutoUpdater::onUpdateAvailable(function ($event) {
            $version = $event['version'] ?? '';
            $this->sendUpdateEvent('update-available', "发现新版本: {$version}");
            
            // 显示通知
            Notification::send('更新可用', "发现新版本: {$version}");
        });
        
        // 监听更新不可用事件
        AutoUpdater::onUpdateNotAvailable(function ($event) {
            $version = $event['version'] ?? '';
            $this->sendUpdateEvent('update-not-available', "当前已是最新版本: {$version}");
            
            // 显示通知
            Notification::send('无可用更新', "当前已是最新版本: {$version}");
        });
        
        // 监听更新下载进度事件
        AutoUpdater::onDownloadProgress(function ($event) {
            $percent = $event['percent'] ?? 0;
            $this->sendUpdateEvent('download-progress', "下载进度: {$percent}%", [
                'percent' => $percent,
            ]);
        });
        
        // 监听更新下载完成事件
        AutoUpdater::onUpdateDownloaded(function ($event) {
            $version = $event['version'] ?? '';
            $this->sendUpdateEvent('update-downloaded', "更新已下载: {$version}");
            
            // 显示通知
            Notification::send('更新已下载', "更新已下载: {$version}，重启应用后将安装更新");
        });
        
        // 监听更新错误事件
        AutoUpdater::onError(function ($event) {
            $error = $event['error'] ?? '未知错误';
            $this->sendUpdateEvent('error', "更新错误: {$error}");
            
            // 显示通知
            Notification::send('更新错误', "更新错误: {$error}");
        });
    }
    
    /**
     * 发送更新事件
     *
     * @param string $type 事件类型
     * @param string $message 事件消息
     * @param array $data 事件数据
     * @return void
     */
    protected function sendUpdateEvent($type, $message, array $data = [])
    {
        // 发送事件到前端
        $event = [
            'type' => $type,
            'message' => $message,
            'time' => date('Y-m-d H:i:s'),
            'data' => $data,
        ];
        
        // 这里可以使用 WebSocket 或其他方式将事件发送到前端
        // 在实际应用中，可以使用 think-swoole 扩展实现 WebSocket 通信
    }
}
