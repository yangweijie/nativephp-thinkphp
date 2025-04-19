<?php

namespace NativePHP\Think\Contract;

interface TrayContract
{
    /**
     * 设置托盘图标
     */
    public function icon(string $path): self;

    /**
     * 设置托盘标签
     */
    public function label(string $label): self;

    /**
     * 设置托盘提示文本
     */
    public function tooltip(string $text): self;

    /**
     * 设置托盘菜单
     */
    public function menu(callable $callback): self;

    /**
     * 创建系统托盘
     */
    public function create(): self;

    /**
     * 销毁系统托盘
     */
    public function destroy(): self;

    /**
     * 配置托盘选项
     */
    public function configure(array $options): self;

    /**
     * 获取托盘配置
     */
    public function getConfig(): array;
}