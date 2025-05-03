<?php

namespace native\thinkphp\exception;

use think\exception\Handle;
use Throwable;

class Handler extends Handle
{
    protected $ignoreReport = [];


    /**
     * Report or log an exception.
     *
     * @access public
     * @param Throwable $exception
     * @return void
     */
    public function report(Throwable $exception): void
    {
        error_log("[NATIVE_EXCEPTION]: {$exception->getMessage()} ({$exception->getCode()}) in {$exception->getFile()}:{$exception->getLine()}");
    }
}
