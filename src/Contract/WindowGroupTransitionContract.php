<?php

namespace NativePHP\Think\Contract;

interface WindowGroupTransitionContract
{
    /**
     * 获取分组的过渡动画实例
     */
    public function transition(): WindowTransitionContract;

    /**
     * 应用布局预设(带过渡动画)
     */
    public function applyLayoutWithTransition(string $preset, array $options = [], bool $animate = true): self;

    /**
     * 同步分组布局(带过渡动画)
     */
    public function syncLayoutWithTransition(string $targetGroup, bool $animate = true): self;

    /**
     * 水平排列(带过渡动画)
     */
    public function arrangeHorizontalWithTransition(bool $animate = true): self;

    /**
     * 垂直排列(带过渡动画)
     */
    public function arrangeVerticalWithTransition(bool $animate = true): self;

    /**
     * 网格布局(带过渡动画)
     */
    public function arrangeGridWithTransition(int $columns = 2, bool $animate = true): self;

    /**
     * 瀑布流布局(带过渡动画)
     */
    public function arrangeCascadeWithTransition(bool $animate = true): self;
}