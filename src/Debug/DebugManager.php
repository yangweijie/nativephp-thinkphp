<?php

namespace NativePHP\Think\Debug;

use think\App;
use think\facade\Log;
use NativePHP\Think\Debug\Performance\PerformanceCollector;

class DebugManager implements DebugInterface
{
    protected $app;
    protected $enabled = false;
    protected $collector;
    protected $activeProfilers = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->collector = $app->make('native.performance');
        $this->enabled = $app->isDebug();
    }

    public function startProfiler(?string $name = null): void
    {
        if (!$this->enabled) return;

        $name = $name ?: uniqid('profile_');
        $this->collector->startTimer($name);
        $this->activeProfilers[$name] = true;
    }

    public function stopProfiler(?string $name = null): array
    {
        if (!$this->enabled) return [];

        if ($name === null) {
            $name = array_key_last($this->activeProfilers);
        }

        if (!isset($this->activeProfilers[$name])) {
            return [];
        }

        unset($this->activeProfilers[$name]);
        return $this->collector->stopTimer($name);
    }

    public function addProfilePoint(string $name, array $data = []): void
    {
        if (!$this->enabled) return;

        $this->collector->addMeasurementPoint($name, $data);
    }

    public function getMetrics(): array
    {
        return $this->collector->getAllMetrics();
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) return;

        Log::channel('electron')->{$level}($message, $context);

        // 发送日志到 Electron
        $this->app->make('native.ipc')->send('debug:log', [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'timestamp' => microtime(true)
        ]);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);

        // 如果是开发环境，立即发送错误到 Electron
        if ($this->enabled) {
            $this->sendToElectron();
        }
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
        $this->collector->clear();
        $this->activeProfilers = [];
    }

    public function sendToElectron(): void
    {
        if (!$this->enabled) return;

        $this->collector->sendToElectron();
    }

    // 添加调试工具管理
    public function getDevTools(): DevTools
    {
        return $this->app->make('native.debug.devtools');
    }

    public function getInspector(): Inspector
    {
        return $this->app->make('native.debug.inspector');
    }

    // 获取调试相关配置
    public function getConfig(string $key = null, $default = null)
    {
        $config = $this->app->config->get('native.debug', []);
        
        if ($key === null) {
            return $config;
        }

        return $config[$key] ?? $default;
    }

    // 获取性能分析器
    public function getPerformanceCollector(): PerformanceCollector
    {
        return $this->collector;
    }
}