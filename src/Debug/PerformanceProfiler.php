<?php

namespace NativePHP\Think\Debug;

use think\facade\Log;

class PerformanceProfiler
{
    protected $startTime;
    protected $startMemory;
    protected $measurements = [];
    protected $enabled;
    
    public function __construct(bool $enabled = true)
    {
        $this->enabled = $enabled;
    }

    public function start(string $label = 'default'): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->measurements[$label] = [
            'start_time' => microtime(true),
            'start_memory' => memory_get_usage(true),
            'timeline' => []
        ];
    }

    public function addPoint(string $label, string $description): void
    {
        if (!$this->enabled) {
            return;
        }

        $this->measurements[$label]['timeline'][] = [
            'description' => $description,
            'time' => microtime(true),
            'memory' => memory_get_usage(true)
        ];
    }

    public function stop(string $label = 'default'): array
    {
        if (!$this->enabled || !isset($this->measurements[$label])) {
            return [];
        }

        $measurement = $this->measurements[$label];
        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);

        $result = [
            'duration' => ($endTime - $measurement['start_time']) * 1000, // 转换为毫秒
            'memory_peak' => memory_get_peak_usage(true),
            'memory_usage' => $endMemory - $measurement['start_memory'],
            'timeline' => []
        ];

        // 处理时间线数据
        foreach ($measurement['timeline'] as $point) {
            $result['timeline'][] = [
                'description' => $point['description'],
                'time_offset' => ($point['time'] - $measurement['start_time']) * 1000,
                'memory_usage' => $point['memory'] - $measurement['start_memory']
            ];
        }

        // 记录到日志
        Log::channel('electron')->debug('Performance profile', [
            'label' => $label,
            'metrics' => $result
        ]);

        unset($this->measurements[$label]);
        return $result;
    }

    public function profile(string $label, callable $callback)
    {
        $this->start($label);
        $result = $callback();
        $metrics = $this->stop($label);
        return [$result, $metrics];
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function clear(): void
    {
        $this->measurements = [];
    }
}