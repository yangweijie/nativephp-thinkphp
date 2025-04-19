<?php

namespace Native\ThinkPHP\Events;

class AppEvent
{
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
     * @param string $type
     * @param array $data
     */
    public function __construct(string $type, array $data = [])
    {
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
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
