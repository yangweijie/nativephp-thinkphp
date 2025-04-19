<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Broadcasting
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
     * @param \think\App|object $app
     */
    public function __construct($app)
    {
        // 在测试环境中接受任何对象
        if (defined('PHPUNIT_RUNNING') && !($app instanceof ThinkApp)) {
            $app = app();
        }

        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 广播事件
     *
     * @param string $channel 频道名称
     * @param string $event 事件名称
     * @param array $data 事件数据
     * @return bool
     */
    public function broadcast($channel, $event, array $data = [])
    {
        $response = $this->client->post('broadcasting/broadcast', [
            'channel' => $channel,
            'event' => $event,
            'data' => $data,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 监听事件
     *
     * @param string $channel 频道名称
     * @param string $event 事件名称
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function listen($channel, $event, callable $callback)
    {
        $id = md5($channel . '.' . $event . '.' . microtime(true));

        $this->listeners[$id] = [
            'channel' => $channel,
            'event' => $event,
            'callback' => $callback,
        ];

        $response = $this->client->post('broadcasting/listen', [
            'channel' => $channel,
            'event' => $event,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            // 注册事件监听器
            $this->app->event->listen('native.broadcasting.' . $id, function ($eventData) use ($callback) {
                call_user_func($callback, $eventData);
            });
        }

        return $id;
    }

    /**
     * 取消监听事件
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function unlisten($id)
    {
        if (!isset($this->listeners[$id])) {
            return false;
        }

        $listener = $this->listeners[$id];

        $response = $this->client->post('broadcasting/unlisten', [
            'channel' => $listener['channel'],
            'event' => $listener['event'],
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->listeners[$id]);
            return true;
        }

        return false;
    }

    /**
     * 获取所有频道
     *
     * @return array
     */
    public function getChannels()
    {
        $response = $this->client->get('broadcasting/channels');
        return $response->json('channels') ?? [];
    }

    /**
     * 获取频道中的事件
     *
     * @param string $channel 频道名称
     * @return array
     */
    public function getEvents($channel)
    {
        $response = $this->client->get('broadcasting/events', [
            'channel' => $channel,
        ]);
        return $response->json('events') ?? [];
    }

    /**
     * 创建频道
     *
     * @param string $channel 频道名称
     * @return bool
     */
    public function createChannel($channel)
    {
        $response = $this->client->post('broadcasting/create-channel', [
            'channel' => $channel,
        ]);
        return (bool) $response->json('success');
    }

    /**
     * 删除频道
     *
     * @param string $channel 频道名称
     * @return bool
     */
    public function deleteChannel($channel)
    {
        $response = $this->client->post('broadcasting/delete-channel', [
            'channel' => $channel,
        ]);
        return (bool) $response->json('success');
    }

    /**
     * 清空频道
     *
     * @param string $channel 频道名称
     * @return bool
     */
    public function clearChannel($channel)
    {
        $response = $this->client->post('broadcasting/clear-channel', [
            'channel' => $channel,
        ]);
        return (bool) $response->json('success');
    }

    /**
     * 检查频道是否存在
     *
     * @param string $channel 频道名称
     * @return bool
     */
    public function channelExists($channel)
    {
        $response = $this->client->get('broadcasting/channel-exists', [
            'channel' => $channel,
        ]);
        return (bool) $response->json('exists');
    }

    /**
     * 获取频道中的监听器数量
     *
     * @param string $channel 频道名称
     * @return int
     */
    public function getListenerCount($channel)
    {
        $response = $this->client->get('broadcasting/listener-count', [
            'channel' => $channel,
        ]);
        return (int) $response->json('count');
    }

    /**
     * 获取所有监听器
     *
     * @return array
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
