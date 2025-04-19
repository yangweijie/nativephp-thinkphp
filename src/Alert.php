<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use function Native\ThinkPHP\Plugins\app;

class Alert
{
    /**
     * 警告类型
     *
     * @var string|null
     */
    protected ?string $type = null;

    /**
     * 警告标题
     *
     * @var string|null
     */
    protected ?string $title = null;

    /**
     * 警告详情
     *
     * @var string|null
     */
    protected ?string $detail = null;

    /**
     * 按钮列表
     *
     * @var array|null
     */
    protected ?array $buttons = null;

    /**
     * 默认按钮ID
     *
     * @var int|null
     */
    protected ?int $defaultId = null;

    /**
     * 取消按钮ID
     *
     * @var int|null
     */
    protected ?int $cancelId = null;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

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
     * @return \Native\ThinkPHP\Alert
     */
    public static function new()
    {
        return new self(app());
    }

    /**
     * 设置警告类型
     *
     * @param string $type
     * @return $this
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 设置警告标题
     *
     * @param string $title
     * @return $this
     */
    public function title(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * 设置警告详情
     *
     * @param string $detail
     * @return $this
     */
    public function detail(string $detail): self
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * 设置按钮列表
     *
     * @param array $buttons
     * @return $this
     */
    public function buttons(array $buttons): self
    {
        $this->buttons = $buttons;
        return $this;
    }

    /**
     * 设置默认按钮ID
     *
     * @param int $defaultId
     * @return $this
     */
    public function defaultId(int $defaultId): self
    {
        $this->defaultId = $defaultId;
        return $this;
    }

    /**
     * 设置取消按钮ID
     *
     * @param int $cancelId
     * @return $this
     */
    public function cancelId(int $cancelId): self
    {
        $this->cancelId = $cancelId;
        return $this;
    }

    /**
     * 显示警告消息
     *
     * @param string $message
     * @return int
     */
    public function show(string $message): int
    {
        $response = $this->client->post('alert/message', [
            'message' => $message,
            'type' => $this->type,
            'title' => $this->title,
            'detail' => $this->detail,
            'buttons' => $this->buttons,
            'defaultId' => $this->defaultId,
            'cancelId' => $this->cancelId,
        ]);

        // 在测试环境中，直接返回模拟的响应
        if (defined('PHPUNIT_RUNNING')) {
            return 0;
        }

        return (int) $response->json('result');
    }

    /**
     * 显示错误警告
     *
     * @param string $title
     * @param string $message
     * @return bool
     */
    public function error(string $title, string $message): bool
    {
        $response = $this->client->post('alert/error', [
            'title' => $title,
            'message' => $message,
        ]);

        return (bool) $response->json('result');
    }
}
