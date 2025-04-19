<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use think\Request;
use think\Response;

class SecurityMiddleware
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
        // 检查是否是 NativePHP 请求
        if ($request->param('is_native')) {
            // 检查安全令牌
            $token = $request->header('X-NativePHP-Token');
            $expectedToken = app('native.app')->getSecurityToken();

            if ($token !== $expectedToken) {
                return response(['error' => 'Unauthorized'], 'json', 401);
            }
        }

        // 处理请求
        $response = $next($request);

        // 添加安全头
        $response->header([
            'X-Content-Type-Options' => 'nosniff',
            'X-XSS-Protection' => '1; mode=block',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline'; img-src 'self' data:; font-src 'self' data:; connect-src 'self' ws: wss:;",
        ]);

        return $response;
    }
}