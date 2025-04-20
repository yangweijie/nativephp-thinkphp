<?php

namespace NativePHP\Think\Debug;

use Symfony\Component\Finder\Finder;
use think\facade\Log;

class FileWatcher
{
    protected $path;
    protected $ignored;
    protected $delay;
    protected $lastCheck;
    protected $files = [];
    protected $callbacks = [];
    protected $isWatching = false;

    public function __construct(string $path, array $ignored = [], int $delay = 1000)
    {
        $this->path = $path;
        $this->ignored = $ignored;
        $this->delay = $delay;
        $this->lastCheck = time();
        $this->files = $this->scanFiles();
    }

    public function start(): void
    {
        if ($this->isWatching) {
            return;
        }

        $this->isWatching = true;
        $this->watch();
    }

    public function stop(): void
    {
        $this->isWatching = false;
    }

    public function onChange(callable $callback): self
    {
        $this->callbacks[] = $callback;
        return $this;
    }

    protected function watch(): void
    {
        if (!$this->isWatching) {
            return;
        }

        // 检查文件变化
        $this->checkChanges();

        // 继续监视
        if (function_exists('pcntl_async_signals')) {
            pcntl_async_signals(true);
            pcntl_signal(SIGALRM, [$this, 'watch']);
            pcntl_alarm(1);
        } else {
            // 在不支持 pcntl 的环境下使用定时器
            register_tick_function(function () {
                if (time() - $this->lastCheck >= ($this->delay / 1000)) {
                    $this->checkChanges();
                    $this->lastCheck = time();
                }
            });
        }
    }

    protected function checkChanges(): void
    {
        $currentFiles = $this->scanFiles();
        $changes = [];

        // 检查新增和修改的文件
        foreach ($currentFiles as $file => $mtime) {
            if (!isset($this->files[$file])) {
                $changes[] = [
                    'type' => 'created',
                    'file' => $file
                ];
            } elseif ($this->files[$file] !== $mtime) {
                $changes[] = [
                    'type' => 'modified',
                    'file' => $file
                ];
            }
        }

        // 检查删除的文件
        foreach ($this->files as $file => $mtime) {
            if (!isset($currentFiles[$file])) {
                $changes[] = [
                    'type' => 'deleted',
                    'file' => $file
                ];
            }
        }

        // 更新文件列表
        $this->files = $currentFiles;

        // 如果有变化，触发回调
        if (!empty($changes)) {
            foreach ($changes as $change) {
                Log::channel('electron')->debug('File change detected', $change);
                foreach ($this->callbacks as $callback) {
                    call_user_func($callback, $change);
                }
            }
        }
    }

    protected function scanFiles(): array
    {
        $files = [];
        
        if (!is_dir($this->path)) {
            return $files;
        }

        $finder = new Finder();
        $finder->files()->in($this->path);

        // 添加忽略规则
        foreach ($this->ignored as $pattern) {
            $finder->notPath($pattern);
        }

        foreach ($finder as $file) {
            $files[$file->getRealPath()] = $file->getMTime();
        }

        return $files;
    }

    public function getWatchPath(): string
    {
        return $this->path;
    }

    public function getIgnored(): array
    {
        return $this->ignored;
    }

    public function isWatching(): bool
    {
        return $this->isWatching;
    }
}