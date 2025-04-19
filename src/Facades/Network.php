<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string getStatus() 获取网络状态
 * @method static bool isOnline() 检查是否在线
 * @method static bool isOffline() 检查是否离线
 * @method static array getInterfaces() 获取网络接口信息
 * @method static string|null getIPAddress(string $family = 'IPv4') 获取当前 IP 地址
 * @method static string|null getMACAddress() 获取当前 MAC 地址
 * @method static string getConnectionType() 获取网络连接类型
 * @method static int getDownloadSpeed() 获取网络下载速度（字节/秒）
 * @method static int getUploadSpeed() 获取网络上传速度（字节/秒）
 * @method static int getPing(string $host = '8.8.8.8') 获取网络延迟（毫秒）
 * @method static string onStatusChange(callable $callback) 监听网络状态变化事件
 * @method static string onOnline(callable $callback) 监听网络连接事件
 * @method static string onOffline(callable $callback) 监听网络断开事件
 * @method static string onConnectionTypeChange(callable $callback) 监听网络连接类型变化事件
 * @method static bool off(string $id) 移除事件监听器
 * @method static bool offAll() 移除所有事件监听器
 * @method static bool testConnection(string $host, int $port = 80, int $timeout = 5000) 测试网络连接
 * @method static array getStats() 获取网络统计信息
 * @method static array getDNSServers() 获取 DNS 服务器
 * @method static array resolveDomain(string $domain) 解析域名
 * @method static string|null getPublicIPAddress() 获取公网 IP 地址
 * @method static array getBandwidth() 获取网络带宽（字节/秒）
 * @method static array getUsage() 获取网络使用情况
 *
 * @see \Native\ThinkPHP\Network
 */
class Network extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.network';
    }
}
