<?php

namespace Native\ThinkPHP\DTOs;

class NotificationConfig
{
    /**
     * 通知标题
     *
     * @var string
     */
    public $title;

    /**
     * 通知内容
     *
     * @var string
     */
    public $body;

    /**
     * 通知图标
     *
     * @var string|null
     */
    public $icon = null;

    /**
     * 通知声音
     *
     * @var string|null
     */
    public $sound = null;

    /**
     * 通知紧急程度
     *
     * @var string
     */
    public $urgency = 'normal';

    /**
     * 通知超时时间（毫秒）
     *
     * @var int|null
     */
    public $timeout = null;

    /**
     * 通知引用 ID
     *
     * @var string|null
     */
    public $reference = null;

    /**
     * 通知操作
     *
     * @var array
     */
    public $actions = [];

    /**
     * 通知回复占位符
     *
     * @var string|null
     */
    public $replyPlaceholder = null;

    /**
     * 通知是否静默
     *
     * @var bool
     */
    public $silent = false;

    /**
     * 通知是否可关闭
     *
     * @var bool
     */
    public $closable = true;

    /**
     * 通知点击回调
     *
     * @var callable|null
     */
    public $onClick = null;

    /**
     * 通知关闭回调
     *
     * @var callable|null
     */
    public $onClose = null;

    /**
     * 通知回复回调
     *
     * @var callable|null
     */
    public $onReply = null;

    /**
     * 通知操作回调
     *
     * @var callable|null
     */
    public $onAction = null;

    /**
     * 从数组创建配置
     *
     * @param array $config
     * @return self
     */
    public static function fromArray(array $config)
    {
        $instance = new self();

        foreach ($config as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }

    /**
     * 转换为数组
     *
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }
}
