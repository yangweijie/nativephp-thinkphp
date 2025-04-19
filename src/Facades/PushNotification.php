<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\PushNotification new() 创建新实例
 * @method static \Native\ThinkPHP\PushNotification setProvider(string $provider) 设置推送服务提供商
 * @method static string getProvider() 获取推送服务提供商
 * @method static \Native\ThinkPHP\PushNotification setConfig(array $config) 设置推送服务配置
 * @method static array getConfig() 获取推送服务配置
 * @method static bool registerDevice(string $token, array $data = []) 注册设备
 * @method static bool unregisterDevice(string $token) 注销设备
 * @method static string|bool send(string|array $tokens, string $title, string $body, array $data = [], array $options = []) 发送推送通知
 * @method static string|null getLastReference() 获取最后一次推送的引用ID
 * @method static array getStatus(string $reference) 获取推送状态
 * @method static bool cancel(string $reference) 取消推送
 * @method static array|null getDeviceInfo(string $token) 获取设备信息
 * @method static array getHistory(int $limit = 10, int $offset = 0) 获取推送历史
 * @method static array getStatistics(string $startDate = null, string $endDate = null) 获取推送统计
 *
 * @see \Native\ThinkPHP\PushNotification
 */
class PushNotification extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.push_notification';
    }
}
