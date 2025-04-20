<?php

namespace NativePHP\Think\Contract;

interface WindowTransitionContract
{
    /**
     * 设置动画持续时间
     */
    public function duration(int $milliseconds): self;

    /**
     * 设置缓动函数
     */
    public function easing(string $easing): self;

    /**
     * 启用/禁用动画
     */
    public function setEnabled(bool $enabled): self;

    /**
     * 使用预设动画配置
     */
    public function usePreset(string $name): self;

    /**
     * 应用位置过渡
     */
    public function moveTo(int $x, int $y): self;

    /**
     * 应用大小过渡
     */
    public function resizeTo(int $width, int $height): self;

    /**
     * 应用完整布局过渡
     */
    public function layout(array $layout): self;
}