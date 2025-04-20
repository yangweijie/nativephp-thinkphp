<?php

namespace NativePHP\Think\Debug;

use Symfony\Component\Process\Process;
use think\facade\Log;

class TestRunner
{
    protected $app;
    protected $profiler;
    protected $testPaths = [];
    protected $options = [];

    public function __construct($app, PerformanceProfiler $profiler)
    {
        $this->app = $app;
        $this->profiler = $profiler;
        $this->testPaths = [
            $this->app->getRootPath() . 'tests'
        ];
    }

    public function addTestPath(string $path): self
    {
        $this->testPaths[] = $path;
        return $this;
    }

    public function setOptions(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function runTests(array $filter = []): array
    {
        $this->profiler->start('tests');

        $results = [];
        foreach ($this->testPaths as $path) {
            if (!is_dir($path)) {
                continue;
            }

            $results = array_merge(
                $results,
                $this->runTestsInDirectory($path, $filter)
            );
        }

        $metrics = $this->profiler->stop('tests');

        return [
            'results' => $results,
            'metrics' => $metrics
        ];
    }

    protected function runTestsInDirectory(string $path, array $filter): array
    {
        $command = $this->buildCommand($path, $filter);
        
        $process = new Process($command);
        $process->setTimeout(null);
        
        $this->profiler->addPoint('tests', "Running tests in {$path}");
        
        $results = [];
        $process->run(function ($type, $buffer) use (&$results) {
            if (Process::ERR === $type) {
                Log::channel('electron')->error('Test error', ['output' => $buffer]);
            } else {
                // 解析测试输出
                $this->parseTestOutput($buffer, $results);
            }
        });

        return $results;
    }

    protected function buildCommand(string $path, array $filter): array
    {
        $command = [
            PHP_BINARY,
            'vendor/bin/pest'
        ];

        // 添加测试目录
        $command[] = $path;

        // 添加过滤器
        if (!empty($filter)) {
            $command[] = '--filter=' . implode(',', $filter);
        }

        // 添加其他选项
        foreach ($this->options as $option => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $command[] = "--{$option}";
                }
            } else {
                $command[] = "--{$option}={$value}";
            }
        }

        return $command;
    }

    protected function parseTestOutput(string $output, array &$results): void
    {
        // 解析测试结果
        if (preg_match('/Tests:\s+(\d+),\s+Assertions:\s+(\d+)/', $output, $matches)) {
            $results['summary'] = [
                'tests' => (int) $matches[1],
                'assertions' => (int) $matches[2]
            ];
        }

        // 解析失败的测试
        if (preg_match_all('/FAILED\s+(.*?)\s+⨯/', $output, $matches)) {
            $results['failures'] = $matches[1];
        }

        // 解析跳过的测试
        if (preg_match_all('/SKIPPED\s+(.*?)\s+→/', $output, $matches)) {
            $results['skipped'] = $matches[1];
        }

        // 记录原始输出
        $results['raw_output'] = $output;
    }

    public function watch(array $paths = [], callable $callback = null): void
    {
        $watcher = $this->app->make('native.debug')->getWatchers()[0] ?? null;
        if (!$watcher) {
            return;
        }

        // 监听测试文件变化
        $watcher->onChange(function ($event) use ($callback) {
            if ($event['type'] === 'modified' && pathinfo($event['file'], PATHINFO_EXTENSION) === 'php') {
                $results = $this->runTests();
                if ($callback) {
                    $callback($results);
                }
            }
        });
    }
}