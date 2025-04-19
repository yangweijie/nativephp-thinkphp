<?php

namespace NativePHP\Think\Contract;

interface MenuContract
{
    /**
     * 添加菜单项
     */
    public function add(string $label, array $options = []): self;

    /**
     * 添加子菜单
     */
    public function addSubmenu(string $label, callable $callback): self;

    /**
     * 添加分隔线
     */
    public function addSeparator(): self;

    /**
     * 配置菜单项
     */
    public function configure(array $options): self;

    /**
     * 获取菜单配置
     */
    public function getConfig(): array;

    /**
     * 清空菜单
     */
    public function clear(): self;

    /**
     * 禁用菜单项
     */
    public function disable(string $label): self;

    /**
     * 启用菜单项
     */
    public function enable(string $label): self;

    /**
     * 切换菜单项状态
     */
    public function toggle(string $label): self;
}