<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\WindowContract;

class Window implements WindowContract
{
    protected array $options = [];
    protected ?WindowTransition $transition = null;
    protected array $state = [];
    
    public function __construct(protected Native $native)
    {
        $this->options = $this->native->getConfig('window.default', []);
    }

    public function title(string $title): self
    {
        $this->options['title'] = $title;
        return $this;
    }

    public function width(int $width): self
    {
        $this->options['width'] = $width;
        return $this;
    }

    public function height(int $height): self
    {
        $this->options['height'] = $height;
        return $this;
    }

    public function minWidth(int $width): self
    {
        $this->options['minWidth'] = $width;
        return $this;
    }

    public function minHeight(int $height): self
    {
        $this->options['minHeight'] = $height;
        return $this;
    }

    public function maxWidth(int $width): self
    {
        $this->options['maxWidth'] = $width;
        return $this;
    }

    public function maxHeight(int $height): self
    {
        $this->options['maxHeight'] = $height;
        return $this;
    }

    public function x(int $x): self
    {
        $this->options['x'] = $x;
        return $this;
    }

    public function y(int $y): self
    {
        $this->options['y'] = $y;
        return $this;
    }

    public function center(): self
    {
        $this->options['center'] = true;
        return $this;
    }

    public function fullscreen(bool $fullscreen = true): self
    {
        $this->options['fullscreen'] = $fullscreen;
        return $this;
    }

    public function resizable(bool $resizable = true): self
    {
        $this->options['resizable'] = $resizable;
        return $this;
    }

    public function minimizable(bool $minimizable = true): self
    {
        $this->options['minimizable'] = $minimizable;
        return $this;
    }

    public function maximizable(bool $maximizable = true): self
    {
        $this->options['maximizable'] = $maximizable;
        return $this;
    }

    public function closable(bool $closable = true): self
    {
        $this->options['closable'] = $closable;
        return $this;
    }

    public function alwaysOnTop(bool $alwaysOnTop = true): self
    {
        $this->options['alwaysOnTop'] = $alwaysOnTop;
        return $this;
    }

    public function skipTaskbar(bool $skip = true): self
    {
        $this->options['skipTaskbar'] = $skip;
        return $this;
    }

    public function decorations(bool $decorations = true): self
    {
        $this->options['decorations'] = $decorations;
        return $this;
    }

    public function focused(bool $focused = true): self
    {
        $this->options['focused'] = $focused;
        return $this;
    }

    public function blur(): self
    {
        $this->options['focused'] = false;
        return $this;
    }

    public function show(): self
    {
        $this->options['visible'] = true;
        return $this;
    }

    public function hide(): self
    {
        $this->options['visible'] = false;
        return $this;
    }

    public function configure(array $options): self
    {
        $this->options = array_merge($this->options, $options);
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function create(): self
    {
        $this->native->events()->dispatch('window.created', $this->options);
        return $this;
    }

    public function close(): self
    {
        $this->native->events()->dispatch('window.closed', $this->options);
        return $this;
    }

    /**
     * 获取过渡动画实例
     */
    public function transition(): WindowTransition
    {
        if (!$this->transition) {
            $this->transition = new WindowTransition($this);
        }
        return $this->transition;
    }

    /**
     * 应用新的窗口布局，支持动画过渡
     */
    public function setLayout(array $layout, bool $animate = true): self
    {
        if (!$animate) {
            return $this->configure($layout);
        }

        return $this->transition()->layout($layout);
    }

    /**
     * 移动窗口到指定位置，支持动画过渡
     */
    public function moveTo(int $x, int $y, bool $animate = true): self
    {
        if (!$animate) {
            return $this->x($x)->y($y);
        }

        return $this->transition()->moveTo($x, $y);
    }

    /**
     * 调整窗口大小，支持动画过渡
     */
    public function resizeTo(int $width, int $height, bool $animate = true): self
    {
        if (!$animate) {
            return $this->width($width)->height($height);
        }

        return $this->transition()->resizeTo($width, $height);
    }

    /**
     * 设置窗口状态
     */
    public function setState(string $key, $value): self
    {
        $this->state[$key] = $value;
        return $this;
    }

    /**
     * 获取窗口状态
     */
    public function getState(string $key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * 删除窗口状态
     */
    public function clearState(string $key = null): self
    {
        if ($key === null) {
            $this->state = [];
        } else {
            unset($this->state[$key]);
        }
        return $this;
    }

    /**
     * 检查是否处于过渡动画中
     */
    public function isTransitioning(): bool
    {
        return $this->getState('transitioning', false);
    }

    /**
     * 获取所有状态
     */
    public function getAllStates(): array
    {
        return $this->state;
    }
}