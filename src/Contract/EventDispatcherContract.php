<?php

namespace NativePHP\Think\Contract;

interface EventDispatcherContract
{
    /**
     * 注册事件监听器
     */
    public function listen(string $event, callable $listener): self;

    /**
     * 触发事件
     */
    public function dispatch(string $event, array $payload = []): void;

    /**
     * 移除事件的所有监听器
     */
    public function remove(string $event): void;

    /**
     * 移除事件监听器
     */
    public function removeListener(string $event, ?callable $listener = null): self;

    /**
     * 获取指定事件的所有监听器
     */
    public function getListeners(string $event): array;

    /**
     * 清除所有事件监听器
     */
    public function clear(): self;
}