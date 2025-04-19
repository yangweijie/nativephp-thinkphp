<?php

namespace Native\ThinkPHP\Events;

class MenuEvent
{
    /**
     * 菜单 ID
     *
     * @var string
     */
    public $menuId;

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
     * @param string $menuId
     * @param string $type
     * @param array $data
     */
    public function __construct(string $menuId, string $type, array $data = [])
    {
        $this->menuId = $menuId;
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
            'menu_id' => $this->menuId,
            'type' => $this->type,
            'data' => $this->data,
        ];
    }
}
