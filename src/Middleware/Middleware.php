<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use think\Request;
use think\Response;

abstract class Middleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    abstract public function handle(Request $request, Closure $next): Response;
}