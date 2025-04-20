<?php

namespace NativePHP\Think\Debug\Performance;

use think\Event;
use think\db\Connection;

class PerformanceListener
{
    protected $collector;

    public function __construct(PerformanceCollector $collector)
    {
        $this->collector = $collector;
    }

    public function handle(Event $event): void
    {
        $this->collector->startTimer('request');
        $this->collector->addMeasurementPoint('request_start');

        // 监听请求结束
        $event->listen('response_send', function ($response) {
            $this->collector->stopTimer('request');
            $this->collector->addMeasurementPoint('request_end');
            $this->collector->sendToElectron();
        });

        // 监听数据库查询
        $event->listen('db:query', function ($sql, $bindings, Connection $connection) {
            $startTime = microtime(true);
            return function () use ($sql, $connection, $startTime) {
                $duration = (microtime(true) - $startTime) * 1000;
                $this->collector->trackSqlQuery($sql, $duration, $connection->getConfig('database'));
            };
        });

        // 监听缓存操作
        $event->listen('cache:write', function ($key, $value, $expire) {
            $this->collector->addMeasurementPoint('cache_write', [
                'key' => $key,
                'expire' => $expire
            ]);
        });

        $event->listen('cache:read', function ($key, $value) {
            $this->collector->addMeasurementPoint('cache_read', [
                'key' => $key,
                'hit' => $value !== false
            ]);
        });

        // 监听异常
        $event->listen('exception:handle', function ($e) {
            $this->collector->addMeasurementPoint('exception', [
                'type' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
        });

        // 定期采集性能指标
        $this->startPeriodicCollection();
    }

    protected function startPeriodicCollection(): void
    {
        // 每 5 秒采集一次系统资源使用情况
        app('native')->ipc()->setInterval(5000, function () {
            $this->collector->trackMemory('periodic');
            $this->collector->collectResourceUsage();
            $this->collector->sendToElectron();
        });
    }
}