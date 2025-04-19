<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Updater
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 更新服务器 URL
     *
     * @var string|null
     */
    protected $serverUrl = null;

    /**
     * 更新状态
     *
     * @var string
     */
    protected $status = 'idle';

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
        $this->serverUrl = $this->app->config->get('native.updater.server_url');
    }

    /**
     * 设置更新服务器 URL
     *
     * @param string $url 服务器地址
     * @return $this
     */
    public function setServerUrl($url)
    {
        $this->serverUrl = $url;

        // 设置 Electron 的更新服务器 URL
        $this->client->post('updater/feed-url', [
            'url' => $url,
        ]);

        return $this;
    }

    /**
     * 检查更新
     *
     * @return array|null 更新信息
     */
    public function check()
    {
        if (!$this->serverUrl) {
            return null;
        }

        $response = $this->client->post('updater/check');
        if (!$response->json('success')) {
            return null;
        }

        $this->status = 'checking';

        // 获取当前版本
        $currentVersion = $this->getCurrentVersion();
        $latestVersion = $response->json('version');

        return [
            'version' => $latestVersion,
            'releaseDate' => $response->json('releaseDate') ?? date('Y-m-d'),
            'releaseNotes' => $response->json('releaseNotes') ?? '',
            'downloadUrl' => $response->json('downloadUrl') ?? ($this->serverUrl . '/download/' . $latestVersion),
            'canUpdate' => version_compare($latestVersion, $currentVersion, '>'),
        ];
    }

    /**
     * 下载更新
     *
     * @param string|null $version 版本号
     * @return bool 是否开始下载
     */
    public function download($version = null)
    {
        if (!$this->serverUrl) {
            return false;
        }

        $response = $this->client->post('updater/download', [
            'version' => $version,
        ]);

        if ($response->json('success')) {
            $this->status = 'downloading';
            return true;
        }

        return false;
    }

    /**
     * 安装更新
     *
     * @return bool 是否开始安装
     */
    public function install()
    {
        $response = $this->client->post('updater/install');

        if ($response->json('success')) {
            $this->status = 'installing';
            return true;
        }

        return false;
    }

    /**
     * 检查并下载更新
     *
     * @return bool
     */
    public function checkAndDownload()
    {
        $updateInfo = $this->check();

        if ($updateInfo && $updateInfo['canUpdate']) {
            return $this->download($updateInfo['version']);
        }

        return false;
    }

    /**
     * 检查、下载并安装更新
     *
     * @return bool
     */
    public function checkAndInstall()
    {
        if ($this->checkAndDownload()) {
            return $this->install();
        }

        return false;
    }

    /**
     * 获取更新服务器 URL
     *
     * @return string|null 服务器地址
     */
    public function getServerUrl()
    {
        $response = $this->client->get('updater/feed-url');
        $url = $response->json('url');

        if ($url) {
            $this->serverUrl = $url;
        }

        return $this->serverUrl;
    }

    /**
     * 获取当前版本
     *
     * @return string 当前版本号
     */
    public function getCurrentVersion()
    {
        $response = $this->client->get('updater/current-version');
        return $response->json('version') ?? $this->app->config->get('native.version', '1.0.0');
    }

    /**
     * 获取最新版本
     *
     * @return string 最新版本号
     */
    public function getLatestVersion()
    {
        $response = $this->client->get('updater/latest-version');
        return $response->json('version') ?? '1.0.0';
    }

    /**
     * 获取更新状态
     *
     * @return string 更新状态，可能的值有：'idle', 'checking', 'downloading', 'downloaded', 'installing'
     */
    public function getStatus()
    {
        $response = $this->client->get('updater/status');
        $status = $response->json('status');

        if ($status) {
            $this->status = $status;
        }

        return $this->status;
    }

    /**
     * 获取更新进度
     *
     * @return float 下载进度，范围从 0 到 1
     */
    public function getProgress()
    {
        $response = $this->client->get('updater/progress');
        return (float) ($response->json('progress') ?? 0.0);
    }

    /**
     * 获取更新日志
     *
     * @return string 更新日志
     */
    public function getReleaseNotes()
    {
        $response = $this->client->get('updater/release-notes');
        return $response->json('notes') ?? '';
    }

    /**
     * 取消更新
     *
     * @return bool 是否取消成功
     */
    public function cancel()
    {
        $response = $this->client->post('updater/cancel');
        if ($response->json('success')) {
            $this->status = 'idle';
            return true;
        }

        return false;
    }
}
