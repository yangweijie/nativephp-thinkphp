<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\WindowContract;

class Window implements WindowContract
{
    protected array $options = [];
    
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
}