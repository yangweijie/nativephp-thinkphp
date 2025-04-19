<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use think\Request;
use think\Response;

class CorsMiddleware
{
    /**
     * 处理请求
     *
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 处理请求
        $response = $next($request);

        // 添加 CORS 头
        $response->header([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With, X-NativePHP',
            'Access-Control-Allow-Credentials' => 'true',
        ]);

        return $response;
    }
}