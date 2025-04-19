<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Notification
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
     * 通知引用
     *
     * @var string|null
     */
    protected $reference = null;

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
     * 创建新实例
     *
     * @return \Native\ThinkPHP\Notification
     */
    public static function new()
    {
        return new self(app());
    }

    /**
     * 设置通知引用
     *
     * @param string $reference
     * @return $this
     */
    public function reference(string $reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * 发送通知
     *
     * @param string $title 通知标题
     * @param string $body 通知内容
     * @param array $options 选项
     * @return string 通知引用
     */
    public function send($title, $body, array $options = [])
    {
        $response = $this->client->post('notification', [
            'reference' => $this->reference,
            'title' => $title,
            'body' => $body,
            'options' => $options,
        ]);

        // 处理响应
        $data = json_decode($response->getContent(), true);
        $this->reference = $data['reference'] ?? null;

        return $this->reference;
    }

    /**
     * 发送带有图标的通知
     *
     * @param string $title
     * @param string $body
     * @param string $icon
     * @param array $options
     * @return string|null
     */
    public function sendWithIcon($title, $body, $icon, array $options = [])
    {
        $options['icon'] = $icon;

        return $this->send($title, $body, $options);
    }

    /**
     * 发送带有声音的通知
     *
     * @param string $title
     * @param string $body
     * @param string $sound
     * @param array $options
     * @return string|null
     */
    public function sendWithSound($title, $body, $sound, array $options = [])
    {
        $options['sound'] = $sound;

        return $this->send($title, $body, $options);
    }

    /**
     * 发送带有操作的通知
     *
     * @param string $title
     * @param string $body
     * @param array $actions
     * @param array $options
     * @return string|null
     */
    public function sendWithActions($title, $body, array $actions, array $options = [])
    {
        $options['actions'] = $actions;

        return $this->send($title, $body, $options);
    }
}
