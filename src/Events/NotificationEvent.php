<?php

namespace Native\ThinkPHP\Events;

class NotificationEvent
{
    /**
     * 通知 ID
     *
     * @var string
     */
    public $notificationId;

    /**
     * 事件类型
     *
     * @var string
     */
    public $type;

    /**
     * 事件数据
     *
     * @var array
     */
    public $data;

    /**
     * 构造函数
     *
     * @param string $notificationId
     * @param string $type
     * @param array $data
     */
    public function __construct(string $notificationId, string $type, array $data = [])
    {
        $this->notificationId = $notificationId;
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * 转换为数组
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'notification_id' => $this->notificationId,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
