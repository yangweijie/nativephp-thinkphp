<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class PushNotification
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
     * 推送服务提供商
     *
     * @var string
     */
    protected $provider;

    /**
     * 推送服务配置
     *
     * @var array
     */
    protected $config;

    /**
     * 最后一次推送的引用ID
     *
     * @var string|null
     */
    protected $lastReference = null;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
        $this->provider = $this->app->config->get('native.push.provider', 'firebase');
        $this->config = $this->app->config->get('native.push', []);
    }

    /**
     * 设置推送服务提供商
     *
     * @param string $provider
     * @return $this
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * 获取推送服务提供商
     *
     * @return string
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * 设置推送服务配置
     *
     * @param array $config
     * @return $this
     */
    public function setConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * 获取推送服务配置
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * 注册设备
     *
     * @param string $token 设备令牌
     * @param array $data 设备数据
     * @return bool
     */
    public function registerDevice($token, array $data = [])
    {
        // 默认设备数据
        $defaultData = [
            'platform' => 'unknown',
            'name' => 'Unknown Device',
            'model' => null,
            'os_version' => null,
            'app_version' => $this->app->config->get('native.version', '1.0.0'),
            'locale' => 'zh-CN',
            'timezone' => date_default_timezone_get(),
            'metadata' => [],
        ];

        $data = array_merge($defaultData, $data);

        // 使用 Client 注册设备
        $response = $this->client->post('push-notification/register-device', [
            'provider' => $this->provider,
            'token' => $token,
            'data' => $data,
            'config' => $this->config,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 注销设备
     *
     * @param string $token 设备令牌
     * @return bool
     */
    public function unregisterDevice($token)
    {
        // 使用 Client 注销设备
        $response = $this->client->post('push-notification/unregister-device', [
            'provider' => $this->provider,
            'token' => $token,
            'config' => $this->config,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 创建新实例
     *
     * @return \Native\ThinkPHP\PushNotification
     */
    public static function new()
    {
        return new self(app());
    }

    /**
     * 发送推送通知
     *
     * @param string|array $tokens 设备令牌
     * @param string $title 通知标题
     * @param string $body 通知内容
     * @param array $data 附加数据
     * @param array $options 选项
     * @return string|bool 推送引用ID或失败状态
     */
    public function send($tokens, $title, $body, array $data = [], array $options = [])
    {
        // 确保 tokens 是数组
        if (!is_array($tokens)) {
            $tokens = [$tokens];
        }

        // 默认选项
        $defaultOptions = [
            'badge' => null,
            'sound' => 'default',
            'icon' => null,
            'click_action' => null,
            'tag' => null,
            'color' => null,
            'priority' => 'high',
            'content_available' => false,
            'mutable_content' => false,
            'time_to_live' => null,
            'collapse_key' => null,
            'channel_id' => null,
        ];

        $options = array_merge($defaultOptions, $options);

        // 使用 Client 发送推送通知
        $response = $this->client->post('push-notification/send', [
            'provider' => $this->provider,
            'tokens' => $tokens,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'options' => $options,
            'config' => $this->config,
        ]);

        if ($response->json('success')) {
            $this->lastReference = $response->json('reference');
            return $this->lastReference;
        }

        return false;
    }

    /**
     * 获取最后一次推送的引用ID
     *
     * @return string|null
     */
    public function getLastReference()
    {
        return $this->lastReference;
    }

    /**
     * 获取推送状态
     *
     * @param string $reference 推送引用ID
     * @return array
     */
    public function getStatus($reference)
    {
        $response = $this->client->get('push-notification/status', [
            'reference' => $reference,
        ]);

        return $response->json();
    }

    /**
     * 取消推送
     *
     * @param string $reference 推送引用ID
     * @return bool
     */
    public function cancel($reference)
    {
        $response = $this->client->post('push-notification/cancel', [
            'reference' => $reference,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取设备信息
     *
     * @param string $token 设备令牌
     * @return array|null
     */
    public function getDeviceInfo($token)
    {
        $response = $this->client->get('push-notification/device', [
            'token' => $token,
        ]);

        return $response->json('success') ? $response->json('device') : null;
    }

    /**
     * 获取推送历史
     *
     * @param int $limit 每页数量
     * @param int $offset 偏移量
     * @return array
     */
    public function getHistory($limit = 10, $offset = 0)
    {
        $response = $this->client->get('push-notification/history', [
            'limit' => $limit,
            'offset' => $offset,
        ]);

        return $response->json('history') ?? [];
    }

    /**
     * 获取推送统计
     *
     * @param string $startDate 开始日期，格式为 Y-m-d
     * @param string $endDate 结束日期，格式为 Y-m-d
     * @return array
     */
    public function getStatistics($startDate = null, $endDate = null)
    {
        $response = $this->client->get('push-notification/statistics', [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return $response->json('statistics') ?? [
            'sent' => 0,
            'delivered' => 0,
            'opened' => 0,
            'failed' => 0,
        ];
    }

    /**
     * 发送带有图标的推送通知
     *
     * @param string|array $tokens 设备令牌
     * @param string $title 通知标题
     * @param string $body 通知内容
     * @param string $icon 图标URL
     * @param array $data 附加数据
     * @param array $options 选项
     * @return string|bool 推送引用ID或失败状态
     */
    public function sendWithIcon($tokens, $title, $body, $icon, array $data = [], array $options = [])
    {
        $options['icon'] = $icon;
        return $this->send($tokens, $title, $body, $data, $options);
    }

    /**
     * 发送带有声音的推送通知
     *
     * @param string|array $tokens 设备令牌
     * @param string $title 通知标题
     * @param string $body 通知内容
     * @param string $sound 声音文件名
     * @param array $data 附加数据
     * @param array $options 选项
     * @return string|bool 推送引用ID或失败状态
     */
    public function sendWithSound($tokens, $title, $body, $sound, array $data = [], array $options = [])
    {
        $options['sound'] = $sound;
        return $this->send($tokens, $title, $body, $data, $options);
    }

    /**
     * 发送带有徽章的推送通知
     *
     * @param string|array $tokens 设备令牌
     * @param string $title 通知标题
     * @param string $body 通知内容
     * @param int $badge 徽章数量
     * @param array $data 附加数据
     * @param array $options 选项
     * @return string|bool 推送引用ID或失败状态
     */
    public function sendWithBadge($tokens, $title, $body, $badge, array $data = [], array $options = [])
    {
        $options['badge'] = $badge;
        return $this->send($tokens, $title, $body, $data, $options);
    }
}
