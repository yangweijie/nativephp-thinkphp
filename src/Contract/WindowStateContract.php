<?php

namespace NativePHP\Think\Contract;

interface WindowStateContract
{
    /**
     * 保存窗口状态
     */
    public function save(string $label): array;

    /**
     * 获取保存的窗口状态
     */
    public function get(string $label): ?array;

    /**
     * 恢复窗口状态
     */
    public function restore(string $label, array $state): self;

    /**
     * 清除窗口状态
     */
    public function clear(string $label): self;

    /**
     * 检查窗口是否有保存的状态
     */
    public function has(string $label): bool;

    /**
     * 获取所有保存的窗口状态
     */
    public function all(): array;

    /**
     * 清除所有窗口状态
     */
    public function clearAll(): self;
}