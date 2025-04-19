<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use think\Request;
use think\Response;

class NativeMiddleware
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
        if ($request->header('X-NativePHP') === 'true') {
            // 设置请求属性
            // 使用请求对象的魔术方法来设置参数
            // 由于无法直接设置属性，我们在后续代码中直接检查请求头
        }

        // 处理请求
        $response = $next($request);

        // 如果是 NativePHP 请求，添加响应头
        if ($request->param('is_native')) {
            $response->header('X-NativePHP', 'true');
        }

        return $response;
    }
}