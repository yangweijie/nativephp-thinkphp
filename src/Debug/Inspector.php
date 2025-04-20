<?php

namespace NativePHP\Think\Debug;

use think\facade\Log;
use Symfony\Component\Process\Process;

class Inspector
{
    protected $host;
    protected $port;
    protected $breakOnStart;
    protected $process;
    protected $isRunning = false;

    public function __construct(string $host, int $port, bool $breakOnStart = false)
    {
        $this->host = $host;
        $this->port = $port;
        $this->breakOnStart = $breakOnStart;
    }

    public function start(): void
    {
        if ($this->isRunning) {
            return;
        }

        $inspectorArgs = ['--inspect'];
        if ($this->breakOnStart) {
            $inspectorArgs[] = '--inspect-brk';
        }
        $inspectorArgs[] = "={$this->host}:{$this->port}";

        $this->process = new Process(array_merge(
            [PHP_BINARY],
            $inspectorArgs,
            [$_SERVER['SCRIPT_FILENAME']]
        ));

        $this->process->start(function ($type, $buffer) {
            Log::channel('electron')->debug('Inspector output', [
                'type' => $type,
                'output' => $buffer
            ]);
        });

        $this->isRunning = true;
        Log::channel('electron')->info('Inspector started', [
            'host' => $this->host,
            'port' => $this->port
        ]);
    }

    public function stop(): void
    {
        if (!$this->isRunning) {
            return;
        }

        if ($this->process && $this->process->isRunning()) {
            $this->process->stop();
        }

        $this->isRunning = false;
        Log::channel('electron')->info('Inspector stopped');
    }

    public function isRunning(): bool
    {
        return $this->isRunning && $this->process && $this->process->isRunning();
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getUrl(): string
    {
        return "chrome-devtools://devtools/bundled/js_app.html?ws={$this->host}:{$this->port}";
    }
}