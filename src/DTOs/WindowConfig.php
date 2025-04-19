<?php

namespace Native\ThinkPHP\DTOs;

class WindowConfig
{
    /**
     * 窗口标题
     *
     * @var string
     */
    public $title;

    /**
     * 窗口宽度
     *
     * @var int
     */
    public $width;

    /**
     * 窗口高度
     *
     * @var int
     */
    public $height;

    /**
     * 最小宽度
     *
     * @var int|null
     */
    public $minWidth;

    /**
     * 最小高度
     *
     * @var int|null
     */
    public $minHeight;

    /**
     * 最大宽度
     *
     * @var int|null
     */
    public $maxWidth;

    /**
     * 最大高度
     *
     * @var int|null
     */
    public $maxHeight;

    /**
     * 是否可调整大小
     *
     * @var bool
     */
    public $resizable = true;

    /**
     * 是否全屏
     *
     * @var bool
     */
    public $fullscreen = false;

    /**
     * 是否最小化
     *
     * @var bool
     */
    public $minimized = false;

    /**
     * 是否最大化
     *
     * @var bool
     */
    public $maximized = false;

    /**
     * 是否可关闭
     *
     * @var bool
     */
    public $closable = true;

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
     * 是否总是置顶
     *
     * @var bool
     */
    public $alwaysOnTop = false;

    /**
     * 是否显示窗口
     *
     * @var bool
     */
    public $show = true;

    /**
     * 窗口中心位置
     *
     * @var bool
     */
    public $center = true;

    /**
     * 窗口 X 坐标
     *
     * @var int|null
     */
    public $x = null;

    /**
     * 窗口 Y 坐标
     *
     * @var int|null
     */
    public $y = null;

    /**
     * 窗口图标
     *
     * @var string|null
     */
    public $icon = null;

    /**
     * 是否显示框架
     *
     * @var bool
     */
    public $frame = true;

    /**
     * 是否透明
     *
     * @var bool
     */
    public $transparent = false;

    /**
     * 是否有阴影
     *
     * @var bool
     */
    public $hasShadow = true;

    /**
     * 是否标准窗口
     *
     * @var bool
     */
    public $standard = true;

    /**
     * 是否模态窗口
     *
     * @var bool
     */
    public $modal = false;

    /**
     * 父窗口 ID
     *
     * @var string|null
     */
    public $parent = null;

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
