<?php

namespace NativePHP\Think\Middleware;

use Closure;
use think\Request;
use NativePHP\Think\Facades\Updater;

class CheckForUpdates
{
    /**
     * 处理请求
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // 检查是否有更新
            if ($update = Updater::checkForUpdates()) {
                // 通过Bridge发送更新通知
                app('native')->bridge()->emit('updater:update-available', $update);
            }
        } catch (\Exception $e) {
            // 记录错误但不中断请求
            trace($e->getMessage(), 'error');
        }

        return $next($request);
    }
}