<?php

namespace Native\ThinkPHP\DTOs;

class DialogConfig
{
    /**
     * 对话框标题
     *
     * @var string|null
     */
    public $title = null;

    /**
     * 对话框消息
     *
     * @var string|null
     */
    public $message = null;

    /**
     * 对话框类型
     *
     * @var string
     */
    public $type = 'info';

    /**
     * 对话框按钮
     *
     * @var array
     */
    public $buttons = [];

    /**
     * 默认按钮 ID
     *
     * @var int|null
     */
    public $defaultId = null;

    /**
     * 取消按钮 ID
     *
     * @var int|null
     */
    public $cancelId = null;

    /**
     * 是否显示复选框
     *
     * @var bool
     */
    public $checkboxChecked = false;

    /**
     * 复选框标签
     *
     * @var string|null
     */
    public $checkboxLabel = null;

    /**
     * 是否模态
     *
     * @var bool
     */
    public $modal = false;

    /**
     * 是否可调整大小
     *
     * @var bool
     */
    public $resizable = true;

    /**
     * 是否总是置顶
     *
     * @var bool
     */
    public $alwaysOnTop = false;

    /**
     * 是否显示在任务栏
     *
     * @var bool
     */
    public $skipTaskbar = false;

    /**
     * 是否可最小化
     *
     * @var bool
     */
    public $minimizable = true;

    /**
     * 是否可最大化
     *
     * @var bool
     */
    public $maximizable = true;

    /**
     * 是否可关闭
     *
     * @var bool
     */
    public $closable = true;

    /**
     * 是否显示帮助按钮
     *
     * @var bool
     */
    public $showHelpButton = false;

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
