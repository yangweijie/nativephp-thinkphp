<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Logger;

class LogMiddleware
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
        // 记录请求开始时间
        $startTime = microtime(true);

        // 记录请求日志
        Logger::info('Request', [
            'method' => $request->method(),
            'url' => $request->url(true),
            'ip' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
            'is_native' => $request->param('is_native') ? 'true' : 'false',
        ]);

        // 处理请求
        $response = $next($request);

        // 计算请求耗时
        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        // 记录响应日志
        Logger::info('Response', [
            'status' => $response->getCode(),
            'duration' => round($duration, 4),
            'size' => strlen($response->getContent()),
        ]);

        return $response;
    }
}