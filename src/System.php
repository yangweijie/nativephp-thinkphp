<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class System
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
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 获取操作系统类型
     *
     * @return string
     */
    public function getOS()
    {
        $response = $this->client->get('system/os');
        $data = json_decode($response->getContent(), true);
        return $data['os'] ?? $this->getPhpOS();
    }

    /**
     * 使用 PHP 获取操作系统类型
     *
     * @return string
     */
    protected function getPhpOS()
    {
        $os = PHP_OS;

        if (strtoupper(substr($os, 0, 3)) === 'WIN') {
            return 'windows';
        } elseif (strtoupper(substr($os, 0, 3)) === 'DAR') {
            return 'macos';
        } elseif (strtoupper(substr($os, 0, 5)) === 'LINUX') {
            return 'linux';
        } else {
            return strtolower($os);
        }
    }

    /**
     * 获取操作系统版本
     *
     * @return string
     */
    public function getOSVersion()
    {
        $response = $this->client->get('system/os-version');
        $data = json_decode($response->getContent(), true);
        return $data['version'] ?? php_uname('r');
    }

    /**
     * 获取 CPU 架构
     *
     * @return string
     */
    public function getArch()
    {
        $response = $this->client->get('system/arch');
        $data = json_decode($response->getContent(), true);
        return $data['arch'] ?? php_uname('m');
    }

    /**
     * 获取主机名
     *
     * @return string
     */
    public function getHostname()
    {
        $response = $this->client->get('system/hostname');
        $data = json_decode($response->getContent(), true);
        return $data['hostname'] ?? php_uname('n');
    }

    /**
     * 获取用户主目录
     *
     * @return string
     */
    public function getHomePath()
    {
        $response = $this->client->get('system/home-path');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['path'] ?? '';
        }

        if ($this->getOS() === 'windows') {
            return getenv('USERPROFILE');
        } else {
            return getenv('HOME');
        }
    }

    /**
     * 获取临时目录
     *
     * @return string
     */
    public function getTempPath()
    {
        $response = $this->client->get('system/temp-path');
        $data = json_decode($response->getContent(), true);
        return $data['path'] ?? sys_get_temp_dir();
    }

    /**
     * 获取应用数据目录
     *
     * @return string
     */
    public function getAppDataPath()
    {
        $response = $this->client->get('system/app-data-path');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['path'] ?? '';
        }

        $os = $this->getOS();
        $appName = $this->app->config->get('native.name', 'NativePHP');

        if ($os === 'windows') {
            return getenv('APPDATA') . DIRECTORY_SEPARATOR . $appName;
        } elseif ($os === 'macos') {
            return $this->getHomePath() . DIRECTORY_SEPARATOR . 'Library' . DIRECTORY_SEPARATOR . 'Application Support' . DIRECTORY_SEPARATOR . $appName;
        } else {
            return $this->getHomePath() . DIRECTORY_SEPARATOR . '.config' . DIRECTORY_SEPARATOR . $appName;
        }
    }

    /**
     * 获取系统内存信息
     *
     * @return array
     */
    public function getMemoryInfo()
    {
        $response = $this->client->get('system/memory-info');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['info'] ?? [];
        }

        return [
            'total' => 0,
            'free' => 0,
        ];
    }

    /**
     * 获取系统 CPU 信息
     *
     * @return array
     */
    public function getCPUInfo()
    {
        $response = $this->client->get('system/cpu-info');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['info'] ?? [];
        }

        return [
            'model' => '',
            'speed' => 0,
            'cores' => 0,
        ];
    }

    /**
     * 获取系统网络接口信息
     *
     * @return array
     */
    public function getNetworkInterfaces()
    {
        $response = $this->client->get('system/network-interfaces');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['interfaces'] ?? [];
        }

        return [];
    }

    /**
     * 获取系统显示器信息
     *
     * @return array
     */
    public function getDisplays()
    {
        $response = $this->client->get('system/displays');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['displays'] ?? [];
        }

        return [];
    }

    /**
     * 获取系统电池信息
     *
     * @return array
     */
    public function getBatteryInfo()
    {
        $response = $this->client->get('system/battery-info');
        $data = json_decode($response->getContent(), true);
        if (isset($data['success']) && $data['success']) {
            return $data['info'] ?? [];
        }

        return [
            'level' => 0,
            'charging' => false,
        ];
    }

    /**
     * 获取系统语言
     *
     * @return string
     */
    public function getLanguage()
    {
        $response = $this->client->get('system/language');
        $data = json_decode($response->getContent(), true);
        return $data['language'] ?? (getenv('LANG') ?: 'en-US');
    }

    /**
     * 打开外部 URL
     *
     * @param string $url URL
     * @param array $options 选项
     * @return bool
     */
    public function openExternal($url, array $options = [])
    {
        $response = $this->client->post('system/open-external', [
            'url' => $url,
            'options' => $options,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 打开文件或目录
     *
     * @param string $path 文件或目录路径
     * @return bool
     */
    public function openPath($path)
    {
        $response = $this->client->post('system/open-path', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 在文件管理器中显示文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function showItemInFolder($path)
    {
        $response = $this->client->post('system/show-item-in-folder', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 移动文件到回收站
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function moveItemToTrash($path)
    {
        $response = $this->client->post('system/move-item-to-trash', [
            'path' => $path,
        ]);

        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 播放系统提示音
     *
     * @param string $type 提示音类型，如 'info', 'warning', 'error'
     * @return void
     */
    public function beep($type = 'info')
    {
        $this->client->post('system/beep', [
            'type' => $type,
        ]);
    }

    /**
     * 设置系统休眠状态
     *
     * @return bool
     */
    public function sleep()
    {
        $response = $this->client->post('system/sleep');
        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置系统锁屏状态
     *
     * @return bool
     */
    public function lock()
    {
        $response = $this->client->post('system/lock');
        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 设置系统注销状态
     *
     * @return bool
     */
    public function logout()
    {
        $response = $this->client->post('system/logout');
        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 重启系统
     *
     * @return bool
     */
    public function restart()
    {
        $response = $this->client->post('system/restart');
        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }

    /**
     * 关闭系统
     *
     * @return bool
     */
    public function shutdown()
    {
        $response = $this->client->post('system/shutdown');
        $data = json_decode($response->getContent(), true);
        return (bool) ($data['success'] ?? false);
    }


}
