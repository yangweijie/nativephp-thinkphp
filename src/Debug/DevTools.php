<?php

namespace NativePHP\Think\Debug;

use think\App;

class DevTools
{
    protected $app;
    protected $enabled = false;
    protected $port;
    protected $host;

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->enabled = $app->isDebug();
        $this->port = $app->config->get('native.debug.devtools.port', 9222);
        $this->host = $app->config->get('native.debug.devtools.host', '127.0.0.1');
    }

    public function enable(): void
    {
        if (!$this->enabled) return;

        // 通知 Electron 开启开发者工具
        $this->app->make('native.ipc')->send('devtools:open', [
            'port' => $this->port,
            'host' => $this->host,
            'options' => [
                'mode' => 'detach',
                'webSecurity' => false
            ]
        ]);
    }

    public function disable(): void
    {
        $this->app->make('native.ipc')->send('devtools:close');
    }

    public function inspect(string $selector): void
    {
        if (!$this->enabled) return;

        $this->app->make('native.ipc')->send('devtools:inspect', [
            'selector' => $selector
        ]);
    }

    public function console(string $level, string $message, array $args = []): void
    {
        if (!$this->enabled) return;

        $this->app->make('native.ipc')->send('devtools:console', [
            'level' => $level,
            'message' => $message,
            'args' => $args
        ]);
    }

    public function evaluateScript(string $script): void
    {
        if (!$this->enabled) return;

        $this->app->make('native.ipc')->send('devtools:evaluate', [
            'script' => $script
        ]);
    }

    public function reload(): void
    {
        $this->app->make('native.ipc')->send('window:reload');
    }

    public function openInEditor(string $file, int $line = 1): void
    {
        if (!$this->enabled) return;

        $this->app->make('native.ipc')->send('devtools:open-in-editor', [
            'file' => $file,
            'line' => $line
        ]);
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}