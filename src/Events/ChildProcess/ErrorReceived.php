<?php

namespace Native\ThinkPHP\Events\ChildProcess;

class ErrorReceived
{
    /**
     * 进程别名
     *
     * @var string
     */
    public $alias;

    /**
     * 错误数据
     *
     * @var string
     */
    public $data;

    /**
     * 构造函数
     *
     * @param string $alias
     * @param string $data
     */
    public function __construct($alias, $data)
    {
        $this->alias = $alias;
        $this->data = $data;
    }
}
