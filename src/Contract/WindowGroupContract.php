<?php

namespace NativePHP\Think\Contract;

interface WindowGroupContract
{
    /**
     * 添加窗口到组
     */
    public function add(string $label): self;

    /**
     * 从组中移除窗口
     */
    public function remove(string $label): self;

    /**
     * 检查窗口是否在组中
     */
    public function has(string $label): bool;

    /**
     * 获取组中所有窗口标识
     */
    public function all(): array;

    /**
     * 获取组中窗口数量
     */
    public function count(): int;

    /**
     * 关闭组中的所有窗口
     */
    public function closeAll(): self;
}