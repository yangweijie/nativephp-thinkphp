<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\Contract\IpcContract;

class MockIpc implements IpcContract
{
    protected array $handlers = [];
    protected array $messages = [];
    protected array $transitionMessages = [];

    public function handle(string $event, callable $handler): self
    {
        $this->handlers[$event] = $handler;
        return $this;
    }

    public function send(string $event, array $payload = []): self
    {
        $this->messages[] = [
            'event' => $event,
            'data' => $payload
        ];

        // 对过渡动画消息进行特殊处理
        if ($event === 'window.transition' && isset($payload['window']['label'])) {
            $this->transitionMessages[$payload['window']['label']] = $payload;
        }

        return $this;
    }

    public function on(string $event, callable $callback): self
    {
        return $this;
    }

    public function dispatch(string $event, array $payload = []): mixed
    {
        if (isset($this->handlers[$event])) {
            return ($this->handlers[$event])($payload);
        }
        return null;
    }

    public function getLastMessage(): ?array
    {
        return end($this->messages) ?: null;
    }

    public function getAllMessages(): array
    {
        return $this->messages;
    }

    public function getLastTransitionMessage(string $windowLabel): ?array
    {
        return $this->transitionMessages[$windowLabel] ?? null;
    }

    public function getAllTransitionMessages(): array
    {
        return $this->transitionMessages;
    }

    public function clearMessages(): self
    {
        $this->messages = [];
        $this->transitionMessages = [];
        return $this;
    }
}
