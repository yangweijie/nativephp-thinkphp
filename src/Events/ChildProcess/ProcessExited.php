<?php

namespace Native\ThinkPHP\Events\ChildProcess;

class ProcessExited
{
    /**
     * 进程别名
     *
     * @var string
     */
    public $alias;

    /**
     * 退出码
     *
     * @var int
     */
    public $code;

    /**
     * 构造函数
     *
     * @param string $alias
     * @param int $code
     */
    public function __construct($alias, $code)
    {
        $this->alias = $alias;
        $this->code = $code;
    }
}
