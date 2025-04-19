<?php

namespace NativePHP\Think\Contract;

interface HotkeyContract
{
    /**
     * 注册全局快捷键
     */
    public function register(string $accelerator, callable $callback): self;

    /**
     * 取消注册全局快捷键
     */
    public function unregister(string $accelerator): self;

    /**
     * 取消所有已注册的快捷键
     */
    public function unregisterAll(): self;

    /**
     * 获取所有已注册的快捷键
     */
    public function getRegistered(): array;

    /**
     * 检查快捷键是否已注册
     */
    public function isRegistered(string $accelerator): bool;
}