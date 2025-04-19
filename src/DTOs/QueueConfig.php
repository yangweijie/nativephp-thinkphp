<?php

namespace Native\ThinkPHP\DTOs;

class QueueConfig
{
    /**
     * 队列连接
     *
     * @var string
     */
    public $connection = 'default';

    /**
     * 队列名称
     *
     * @var string
     */
    public $queue = 'default';

    /**
     * 尝试次数
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 超时时间（秒）
     *
     * @var int
     */
    public $timeout = 60;

    /**
     * 休眠时间（秒）
     *
     * @var int
     */
    public $sleep = 3;

    /**
     * 是否强制
     *
     * @var bool
     */
    public $force = false;

    /**
     * 是否持久化
     *
     * @var bool
     */
    public $persistent = true;

    /**
     * 进程 ID
     *
     * @var int|null
     */
    public $pid = null;

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
