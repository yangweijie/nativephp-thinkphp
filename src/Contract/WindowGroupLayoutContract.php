<?php

namespace NativePHP\Think\Contract;

interface WindowGroupLayoutContract
{
    /**
     * 同步窗口组布局到其他组
     */
    public function syncLayout(string $targetGroup): self;

    /**
     * 导出布局配置
     */
    public function exportLayout(): array;

    /**
     * 导入布局配置
     */
    public function importLayout(array $config): self;

    /**
     * 保存布局配置到文件
     */
    public function saveLayoutToFile(string $path): self;

    /**
     * 从文件加载布局配置
     */
    public function loadLayoutFromFile(string $path): self;

    /**
     * 注册布局变更事件监听器
     */
    public function onLayoutChange(callable $callback): self;

    /**
     * 获取当前布局预设名称
     */
    public function getCurrentLayout(): ?string;

    /**
     * 获取布局选项
     */
    public function getLayoutOptions(): array;
}