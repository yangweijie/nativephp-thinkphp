<?php

namespace Native\ThinkPHP\Events\ChildProcess;

class ProcessSpawned
{
    /**
     * 进程别名
     *
     * @var string
     */
    public $alias;

    /**
     * 进程 ID
     *
     * @var int
     */
    public $pid;

    /**
     * 构造函数
     *
     * @param string $alias
     * @param int $pid
     */
    public function __construct($alias, $pid)
    {
        $this->alias = $alias;
        $this->pid = $pid;
    }
}
