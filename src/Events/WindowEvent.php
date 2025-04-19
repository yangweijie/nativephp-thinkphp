<?php

namespace Native\ThinkPHP\Events;

class WindowEvent
{
    /**
     * 窗口 ID
     *
     * @var string
     */
    public $windowId;

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
     * @param string $windowId
     * @param string $type
     * @param array $data
     */
    public function __construct(string $windowId, string $type, array $data = [])
    {
        $this->windowId = $windowId;
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
            'window_id' => $this->windowId,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
