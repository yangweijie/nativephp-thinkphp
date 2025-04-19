<?php

namespace Native\ThinkPHP;

use think\App;
use think\facade\Event;

class AutoUpdater
{
    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * NativePHP 客户端
     *
     * @var \Native\ThinkPHP\Client
     */
    protected $client;

    /**
     * 更新服务器 URL
     *
     * @var string
     */
    protected $feedUrl;

    /**
     * 是否自动下载更新
     *
     * @var bool
     */
    protected $autoDownload = true;

    /**
     * 是否自动安装更新
     *
     * @var bool
     */
    protected $autoInstall = false;

    /**
     * 是否允许预发布版本
     *
     * @var bool
     */
    protected $allowPrerelease = false;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->client = $app->make('native.client');
    }

    /**
     * 设置更新服务器 URL
     *
     * @param string $url 更新服务器 URL
     * @return $this
     */
    public function setFeedURL($url)
    {
        $this->feedUrl = $url;

        $this->client->post('auto-updater/set-feed-url', [
            'url' => $url,
        ]);

        return $this;
    }

    /**
     * 设置是否自动下载更新
     *
     * @param bool $autoDownload 是否自动下载更新
     * @return $this
     */
    public function setAutoDownload($autoDownload = true)
    {
        $this->autoDownload = $autoDownload;

        $this->client->post('auto-updater/set-auto-download', [
            'autoDownload' => $autoDownload,
        ]);

        return $this;
    }

    /**
     * 设置是否自动安装更新
     *
     * @param bool $autoInstall 是否自动安装更新
     * @return $this
     */
    public function setAutoInstall($autoInstall = true)
    {
        $this->autoInstall = $autoInstall;

        $this->client->post('auto-updater/set-auto-install', [
            'autoInstall' => $autoInstall,
        ]);

        return $this;
    }

    /**
     * 设置是否允许预发布版本
     *
     * @param bool $allowPrerelease 是否允许预发布版本
     * @return $this
     */
    public function setAllowPrerelease($allowPrerelease = true)
    {
        $this->allowPrerelease = $allowPrerelease;

        $this->client->post('auto-updater/set-allow-prerelease', [
            'allowPrerelease' => $allowPrerelease,
        ]);

        return $this;
    }

    /**
     * 检查更新
     *
     * @return bool
     */
    public function checkForUpdates()
    {
        if (empty($this->feedUrl)) {
            // 如果没有设置更新源URL，返回 false
            return false;
        }

        try {
            $response = $this->client->post('auto-updater/check-for-updates');
            return (bool) $response->json('checking');
        } catch (\Exception $e) {
            // 如果请求失败，返回 false
            return false;
        }
    }

    /**
     * 下载更新
     *
     * @return bool
     */
    public function downloadUpdate()
    {
        $response = $this->client->post('auto-updater/download-update');

        return (bool) $response->json('downloading');
    }

    /**
     * 安装更新
     *
     * @return bool
     */
    public function installUpdate()
    {
        $response = $this->client->post('auto-updater/install-update');

        return (bool) $response->json('installing');
    }

    /**
     * 获取当前版本
     *
     * @return string
     */
    public function getCurrentVersion()
    {
        $response = $this->client->get('auto-updater/current-version');

        return $response->json('version');
    }

    /**
     * 获取最新版本
     *
     * @return string|null
     */
    public function getLatestVersion()
    {
        $response = $this->client->get('auto-updater/latest-version');

        return $response->json('version');
    }

    /**
     * 获取更新信息
     *
     * @return array|null
     */
    public function getUpdateInfo()
    {
        $response = $this->client->get('auto-updater/update-info');

        return $response->json('info');
    }

    /**
     * 监听更新检查事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onCheckingForUpdate($callback)
    {
        $this->client->post('auto-updater/on-checking-for-update');

        Event::listen('native.auto-updater.checking-for-update', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 监听更新可用事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onUpdateAvailable($callback)
    {
        $this->client->post('auto-updater/on-update-available');

        Event::listen('native.auto-updater.update-available', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 监听更新不可用事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onUpdateNotAvailable($callback)
    {
        $this->client->post('auto-updater/on-update-not-available');

        Event::listen('native.auto-updater.update-not-available', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 监听更新下载进度事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onDownloadProgress($callback)
    {
        $this->client->post('auto-updater/on-download-progress');

        Event::listen('native.auto-updater.download-progress', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 监听更新下载完成事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onUpdateDownloaded($callback)
    {
        $this->client->post('auto-updater/on-update-downloaded');

        Event::listen('native.auto-updater.update-downloaded', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 监听更新错误事件
     *
     * @param callable $callback 回调函数
     * @return $this
     */
    public function onError($callback)
    {
        $this->client->post('auto-updater/on-error');

        Event::listen('native.auto-updater.error', function ($event) use ($callback) {
            call_user_func($callback, $event);
        });

        return $this;
    }

    /**
     * 取消更新下载
     *
     * @return bool
     */
    public function cancelDownload()
    {
        $response = $this->client->post('auto-updater/cancel-download');

        return (bool) $response->json('success');
    }

    /**
     * 重启应用并安装更新
     *
     * @return bool
     */
    public function quitAndInstall()
    {
        $response = $this->client->post('auto-updater/quit-and-install');

        return (bool) $response->json('success');
    }
}
