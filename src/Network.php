<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Network
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
     * 事件监听器
     *
     * @var array
     */
    protected $listeners = [];

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
     * 获取网络状态
     *
     * @return string 'online', 'offline', 'unknown'
     */
    public function getStatus()
    {
        $response = $this->client->get('network/status');
        return $response->json('status') ?? 'unknown';
    }

    /**
     * 检查是否在线
     *
     * @return bool
     */
    public function isOnline()
    {
        return $this->getStatus() === 'online';
    }

    /**
     * 检查是否离线
     *
     * @return bool
     */
    public function isOffline()
    {
        return $this->getStatus() === 'offline';
    }

    /**
     * 获取网络接口信息
     *
     * @return array
     */
    public function getInterfaces()
    {
        $response = $this->client->get('network/interfaces');
        return $response->json('interfaces') ?? [];
    }

    /**
     * 获取当前 IP 地址
     *
     * @param string $family IP 地址族，'IPv4' 或 'IPv6'
     * @return string|null
     */
    public function getIPAddress($family = 'IPv4')
    {
        $interfaces = $this->getInterfaces();
        
        foreach ($interfaces as $interface => $addresses) {
            foreach ($addresses as $address) {
                if ($address['family'] === $family && !$address['internal']) {
                    return $address['address'];
                }
            }
        }
        
        return null;
    }

    /**
     * 获取当前 MAC 地址
     *
     * @return string|null
     */
    public function getMACAddress()
    {
        $interfaces = $this->getInterfaces();
        
        foreach ($interfaces as $interface => $addresses) {
            foreach ($addresses as $address) {
                if (!empty($address['mac']) && $address['mac'] !== '00:00:00:00:00:00') {
                    return $address['mac'];
                }
            }
        }
        
        return null;
    }

    /**
     * 获取网络连接类型
     *
     * @return string 'wifi', 'ethernet', 'cellular', 'unknown'
     */
    public function getConnectionType()
    {
        $response = $this->client->get('network/connection-type');
        return $response->json('type') ?? 'unknown';
    }

    /**
     * 获取网络下载速度（字节/秒）
     *
     * @return int
     */
    public function getDownloadSpeed()
    {
        $response = $this->client->get('network/download-speed');
        return (int) $response->json('speed');
    }

    /**
     * 获取网络上传速度（字节/秒）
     *
     * @return int
     */
    public function getUploadSpeed()
    {
        $response = $this->client->get('network/upload-speed');
        return (int) $response->json('speed');
    }

    /**
     * 获取网络延迟（毫秒）
     *
     * @param string $host 主机名或 IP 地址
     * @return int
     */
    public function getPing($host = '8.8.8.8')
    {
        $response = $this->client->post('network/ping', [
            'host' => $host,
        ]);
        return (int) $response->json('ping');
    }

    /**
     * 监听网络状态变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onStatusChange($callback)
    {
        $id = md5('status-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'status-change',
            'callback' => $callback,
        ];

        $this->client->post('network/on', [
            'event' => 'status-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听网络连接事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onOnline($callback)
    {
        $id = md5('online' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'online',
            'callback' => $callback,
        ];

        $this->client->post('network/on', [
            'event' => 'online',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听网络断开事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onOffline($callback)
    {
        $id = md5('offline' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'offline',
            'callback' => $callback,
        ];

        $this->client->post('network/on', [
            'event' => 'offline',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 监听网络连接类型变化事件
     *
     * @param callable $callback
     * @return string 监听器ID
     */
    public function onConnectionTypeChange($callback)
    {
        $id = md5('connection-type-change' . microtime(true));
        $this->listeners[$id] = [
            'event' => 'connection-type-change',
            'callback' => $callback,
        ];

        $this->client->post('network/on', [
            'event' => 'connection-type-change',
            'id' => $id,
        ]);

        return $id;
    }

    /**
     * 移除事件监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function off($id)
    {
        if (!isset($this->listeners[$id])) {
            return false;
        }

        $event = $this->listeners[$id]['event'];

        $response = $this->client->post('network/off', [
            'event' => $event,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->listeners[$id]);
            return true;
        }

        return false;
    }

    /**
     * 移除所有事件监听器
     *
     * @return bool
     */
    public function offAll()
    {
        $response = $this->client->post('network/off-all');
        $this->listeners = [];

        return (bool) $response->json('success');
    }

    /**
     * 测试网络连接
     *
     * @param string $host 主机名或 IP 地址
     * @param int $port 端口号
     * @param int $timeout 超时时间（毫秒）
     * @return bool
     */
    public function testConnection($host, $port = 80, $timeout = 5000)
    {
        $response = $this->client->post('network/test-connection', [
            'host' => $host,
            'port' => $port,
            'timeout' => $timeout,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取网络统计信息
     *
     * @return array
     */
    public function getStats()
    {
        $response = $this->client->get('network/stats');
        return $response->json('stats') ?? [];
    }

    /**
     * 获取 DNS 服务器
     *
     * @return array
     */
    public function getDNSServers()
    {
        $response = $this->client->get('network/dns-servers');
        return $response->json('servers') ?? [];
    }

    /**
     * 解析域名
     *
     * @param string $domain 域名
     * @return array
     */
    public function resolveDomain($domain)
    {
        $response = $this->client->post('network/resolve-domain', [
            'domain' => $domain,
        ]);

        return $response->json('addresses') ?? [];
    }

    /**
     * 获取公网 IP 地址
     *
     * @return string|null
     */
    public function getPublicIPAddress()
    {
        $response = $this->client->get('network/public-ip');
        return $response->json('ip');
    }

    /**
     * 获取网络带宽（字节/秒）
     *
     * @return array
     */
    public function getBandwidth()
    {
        $response = $this->client->get('network/bandwidth');
        return $response->json('bandwidth') ?? [
            'download' => 0,
            'upload' => 0,
        ];
    }

    /**
     * 获取网络使用情况
     *
     * @return array
     */
    public function getUsage()
    {
        $response = $this->client->get('network/usage');
        return $response->json('usage') ?? [
            'received' => 0,
            'sent' => 0,
        ];
    }
}
