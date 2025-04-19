<?php

namespace NativePHP\Think\Contract;

interface IpcContract
{
    /**
     * 发送消息到指定通道
     */
    public function send(string $channel, array $payload = []): void;

    /**
     * 监听指定通道的消息
     */
    public function on(string $channel, callable $callback): self;

    /**
     * 处理来自前端的请求
     */
    public function handle(string $channel, callable $handler): self;

    /**
     * 调用前端方法
     */
    public function invoke(string $method, array $args = []): mixed;

    /**
     * 移除通道监听器
     */
    public function off(string $channel, ?callable $callback = null): self;

    /**
     * 获取指定通道的所有处理器
     */
    public function getHandlers(string $channel): array;
}