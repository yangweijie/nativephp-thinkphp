<?php

namespace Native\ThinkPHP\Middleware;

use Closure;
use Native\ThinkPHP\Facades\Settings;
use think\Request;
use think\Response;
use Native\ThinkPHP\Utils\Cache;

class CacheMiddleware
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
        // 检查是否启用缓存
        $config = app('native.app')->config('cache');
        if (!$config['enabled']) {
            return $next($request);
        }

        // 检查是否是 GET 请求
        if ($request->method() !== 'GET') {
            return $next($request);
        }

        // 生成缓存键
        $cacheKey = 'native:' . md5($request->url(true));

        // 创建缓存实例
        $cacheInstance = new Cache(app());

        // 检查缓存
        $cache = $cacheInstance->get($cacheKey);
        if ($cache) {
            // 更新缓存统计
            $this->updateCacheStats(true);

            // 返回缓存响应
            return response($cache['content'], $cache['type'], 200);
        }

        // 更新缓存统计
        $this->updateCacheStats(false);

        // 处理请求
        $response = $next($request);

        // 检查是否可缓存
        if ($response->getCode() === 200) {
            // 获取响应内容
            $content = $response->getContent();
            $type = $response->getType();
            $header = $response->getHeader();

            // 缓存响应
            $cacheInstance->set($cacheKey, [
                'content' => $content,
                'type' => $type,
                'header' => $header,
            ], $config['ttl']);
        }

        return $response;
    }

    /**
     * 更新缓存统计
     *
     * @param bool $hit 是否命中缓存
     * @return void
     */
    protected function updateCacheStats(bool $hit)
    {
        // 获取缓存统计
        $stats = Settings::get('cache_stats', [
            'hits' => 0,
            'misses' => 0,
        ]);

        // 更新统计
        if ($hit) {
            $stats['hits']++;
        } else {
            $stats['misses']++;
        }

        // 保存统计
        Settings::set('cache_stats', $stats);
    }
}