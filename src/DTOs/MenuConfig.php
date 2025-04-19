<?php

namespace Native\ThinkPHP\DTOs;

class MenuConfig
{
    /**
     * 菜单标签
     *
     * @var string
     */
    public $label;

    /**
     * 菜单类型
     *
     * @var string
     */
    public $type = 'normal';

    /**
     * 菜单图标
     *
     * @var string|null
     */
    public $icon = null;

    /**
     * 菜单加速键
     *
     * @var string|null
     */
    public $accelerator = null;

    /**
     * 是否已选中
     *
     * @var bool
     */
    public $checked = false;

    /**
     * 是否已启用
     *
     * @var bool
     */
    public $enabled = true;

    /**
     * 是否可见
     *
     * @var bool
     */
    public $visible = true;

    /**
     * 子菜单
     *
     * @var array
     */
    public $submenu = [];

    /**
     * 点击回调
     *
     * @var callable|null
     */
    public $click = null;

    /**
     * 菜单 ID
     *
     * @var string|null
     */
    public $id = null;

    /**
     * 菜单角色
     *
     * @var string|null
     */
    public $role = null;

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
        $data = get_object_vars($this);

        // 处理子菜单
        if (!empty($data['submenu'])) {
            $submenu = [];
            foreach ($data['submenu'] as $item) {
                if ($item instanceof self) {
                    $submenu[] = $item->toArray();
                } else {
                    $submenu[] = $item;
                }
            }
            $data['submenu'] = $submenu;
        }

        return $data;
    }
}
