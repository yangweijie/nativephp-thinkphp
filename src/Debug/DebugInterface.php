<?php

namespace NativePHP\Think\Debug;

interface DebugInterface
{
    /**
     * 开始性能分析
     */
    public function startProfiler(?string $name = null): void;

    /**
     * 停止性能分析
     */
    public function stopProfiler(?string $name = null): array;

    /**
     * 添加性能检查点
     */
    public function addProfilePoint(string $name, array $data = []): void;

    /**
     * 获取所有性能指标
     */
    public function getMetrics(): array;

    /**
     * 记录日志
     */
    public function log(string $level, string $message, array $context = []): void;

    /**
     * 记录错误
     */
    public function error(string $message, array $context = []): void;

    /**
     * 启用调试模式
     */
    public function enable(): void;

    /**
     * 禁用调试模式
     */
    public function disable(): void;

    /**
     * 获取调试状态
     */
    public function isEnabled(): bool;

    /**
     * 清除所有调试数据
     */
    public function clear(): void;

    /**
     * 发送调试数据到 Electron
     */
    public function sendToElectron(): void;
}