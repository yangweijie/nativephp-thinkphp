<?php

namespace NativePHP\Think\Middleware;

use Closure;
use think\Request;
use think\Response;
use NativePHP\Think\Debug\PerformanceProfiler;

class PerformanceMonitor
{
    protected $profiler;

    public function __construct(PerformanceProfiler $profiler)
    {
        $this->profiler = $profiler;
    }

    public function handle(Request $request, Closure $next)
    {
        // 开始性能分析
        $this->profiler->start($request->url());

        // 记录初始点
        $this->profiler->addPoint($request->url(), '请求开始');

        // 获取响应
        $response = $next($request);

        // 记录响应点
        $this->profiler->addPoint($request->url(), '响应生成');

        // 如果是 JSON 响应，添加性能指标
        if ($response instanceof Response && $response->getHeader('Content-Type') === 'application/json') {
            $metrics = $this->profiler->stop($request->url());
            
            // 获取原始数据
            $data = $response->getData();
            
            // 在开发环境下添加性能指标
            if (app()->isDebug()) {
                if (is_array($data)) {
                    $data['__performance'] = [
                        'duration' => $metrics['duration'],
                        'memory' => [
                            'peak' => $metrics['memory_peak'],
                            'usage' => $metrics['memory_usage']
                        ],
                        'timeline' => $metrics['timeline']
                    ];
                }
                $response->data($data);
            }
        } else {
            $this->profiler->stop($request->url());
        }

        return $response;
    }
}