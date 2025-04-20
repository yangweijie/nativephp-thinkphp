<?php

namespace NativePHP\Think\Debug\Performance;

use think\facade\Log;

class PerformanceCollector
{
    protected $metrics = [];
    protected $timers = [];
    protected $measurementPoints = [];
    protected $memoryPoints = [];
    protected $sqlQueries = [];
    protected $resourceUsage = [];

    public function startTimer(string $name): void
    {
        $this->timers[$name] = [
            'start' => microtime(true),
            'start_memory' => memory_get_usage(true)
        ];
    }

    public function stopTimer(string $name): array
    {
        if (!isset($this->timers[$name])) {
            return [];
        }

        $end = microtime(true);
        $endMemory = memory_get_usage(true);

        $metric = [
            'name' => $name,
            'duration' => ($end - $this->timers[$name]['start']) * 1000, // 转换为毫秒
            'memory_usage' => $endMemory - $this->timers[$name]['start_memory'],
            'memory_peak' => memory_get_peak_usage(true)
        ];

        $this->metrics[$name] = $metric;
        unset($this->timers[$name]);

        return $metric;
    }

    public function addMeasurementPoint(string $name, array $data = []): void
    {
        $this->measurementPoints[] = [
            'name' => $name,
            'timestamp' => microtime(true),
            'memory' => memory_get_usage(true),
            'data' => $data
        ];
    }

    public function trackMemory(string $name): void
    {
        $this->memoryPoints[$name] = [
            'usage' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true)
        ];
    }

    public function trackSqlQuery(string $sql, float $duration, ?string $connection = null): void
    {
        $this->sqlQueries[] = [
            'sql' => $sql,
            'duration' => $duration,
            'connection' => $connection,
            'timestamp' => microtime(true)
        ];
    }

    public function collectResourceUsage(): void
    {
        $this->resourceUsage = [
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true)
            ],
            'time' => [
                'total' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']
            ]
        ];

        if (function_exists('getrusage')) {
            $usage = getrusage();
            $this->resourceUsage['cpu'] = [
                'user' => $usage['ru_utime.tv_sec'] + $usage['ru_utime.tv_usec'] / 1000000,
                'system' => $usage['ru_stime.tv_sec'] + $usage['ru_stime.tv_usec'] / 1000000
            ];
        }
    }

    public function getAllMetrics(): array
    {
        $this->collectResourceUsage();

        return [
            'metrics' => $this->metrics,
            'measurement_points' => $this->measurementPoints,
            'memory_points' => $this->memoryPoints,
            'sql_queries' => $this->sqlQueries,
            'resource_usage' => $this->resourceUsage
        ];
    }

    public function clear(): void
    {
        $this->metrics = [];
        $this->timers = [];
        $this->measurementPoints = [];
        $this->memoryPoints = [];
        $this->sqlQueries = [];
        $this->resourceUsage = [];
    }

    public function sendToElectron(): void
    {
        $metrics = $this->getAllMetrics();
        
        // 通过 IPC 发送性能指标
        app('native.ipc')->send('performance:metrics', $metrics);
        
        // 记录到日志
        Log::channel('electron')->debug('Performance metrics collected', $metrics);
    }
}