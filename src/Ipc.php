<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\IpcContract;

class Ipc implements IpcContract
{
    /**
     * @var array<string, array<callable>>
     */
    protected array $handlers = [];

    /**
     * @var array<string, array<callable>>
     */
    protected array $listeners = [];

    public function __construct(protected Native $native) {}

    public function send(string $channel, array $payload = []): void
    {
        $this->native->events()->dispatch('ipc.send', [
            'channel' => $channel,
            'payload' => $payload
        ]);
    }

    public function on(string $channel, callable $callback): self
    {
        $this->listeners[$channel][] = $callback;
        return $this;
    }

    public function handle(string $channel, callable $handler): self
    {
        $this->handlers[$channel][] = $handler;
        return $this;
    }

    public function invoke(string $method, array $args = []): mixed
    {
        // 触发调用事件
        $this->native->events()->dispatch('ipc.invoke', [
            'method' => $method,
            'args' => $args
        ]);

        // 使用 send 方法发送调用请求
        $this->send('invoke:' . $method, $args);

        // 注意：这是异步调用，不会等待结果
        return null;
    }

    public function off(string $channel, ?callable $callback = null): self
    {
        if ($callback === null) {
            unset($this->listeners[$channel]);
            return $this;
        }

        if (isset($this->listeners[$channel])) {
            $this->listeners[$channel] = array_filter(
                $this->listeners[$channel],
                fn($listener) => $listener !== $callback
            );

            if (empty($this->listeners[$channel])) {
                unset($this->listeners[$channel]);
            }
        }

        return $this;
    }

    public function getHandlers(string $channel): array
    {
        return $this->handlers[$channel] ?? [];
    }

    /**
     * 处理来自 Electron 的消息
     */
    public function handleMessage(string $channel, array $payload = []): void
    {
        // 触发事件
        $this->native->events()->dispatch('ipc.message', [
            'channel' => $channel,
            'payload' => $payload
        ]);

        // 调用通道对应的所有处理器
        foreach ($this->getHandlers($channel) as $handler) {
            call_user_func($handler, $payload);
        }

        // 触发监听器
        if (isset($this->listeners[$channel])) {
            foreach ($this->listeners[$channel] as $listener) {
                call_user_func($listener, $payload);
            }
        }
    }
}