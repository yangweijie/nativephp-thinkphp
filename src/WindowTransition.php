<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\WindowTransitionContract;

class WindowTransition implements WindowTransitionContract
{
    protected array $options = [
        'duration' => 300,
        'easing' => 'easeInOutCubic',
        'enabled' => true,
    ];

    public function __construct(protected Window $window)
    {
        // 从配置中加载默认值
        $config = $this->window->native->getConfig('transitions', []);
        if (isset($config['enabled'])) {
            $this->options['enabled'] = $config['enabled'];
        }
        if (isset($config['duration'])) {
            $this->options['duration'] = $config['duration'];
        }
        if (isset($config['easing'])) {
            $this->options['easing'] = $config['easing'];
        }
    }

    public function duration(int $milliseconds): self
    {
        $this->options['duration'] = $milliseconds;
        return $this;
    }

    public function easing(string $easing): self
    {
        $this->options['easing'] = $easing;
        return $this;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->options['enabled'] = $enabled;
        return $this;
    }

    public function usePreset(string $name): self
    {
        $presets = $this->window->native->getConfig('transitions.presets', []);
        if (isset($presets[$name])) {
            $this->options = array_merge($this->options, $presets[$name]);
        }
        return $this;
    }

    public function moveTo(int $x, int $y): self
    {
        if (!$this->options['enabled']) {
            $this->window->x($x)->y($y);
            return $this;
        }

        $currentX = $this->window->getOptions()['x'] ?? 0;
        $currentY = $this->window->getOptions()['y'] ?? 0;

        $this->window->native->ipc()->send('window.transition', [
            'window' => $this->window->getOptions(),
            'from' => ['x' => $currentX, 'y' => $currentY],
            'to' => ['x' => $x, 'y' => $y],
            'options' => $this->options
        ]);

        return $this;
    }

    public function resizeTo(int $width, int $height): self
    {
        if (!$this->options['enabled']) {
            $this->window->width($width)->height($height);
            return $this;
        }

        $currentWidth = $this->window->getOptions()['width'] ?? 800;
        $currentHeight = $this->window->getOptions()['height'] ?? 600;

        $this->window->native->ipc()->send('window.transition', [
            'window' => $this->window->getOptions(),
            'from' => ['width' => $currentWidth, 'height' => $currentHeight],
            'to' => ['width' => $width, 'height' => $height],
            'options' => $this->options
        ]);

        return $this;
    }

    public function layout(array $layout): self
    {
        if (!$this->options['enabled']) {
            $this->window->configure($layout);
            return $this;
        }

        $current = $this->window->getOptions();

        $this->window->native->ipc()->send('window.transition', [
            'window' => $this->window->getOptions(),
            'from' => [
                'x' => $current['x'] ?? 0,
                'y' => $current['y'] ?? 0,
                'width' => $current['width'] ?? 800,
                'height' => $current['height'] ?? 600
            ],
            'to' => [
                'x' => $layout['x'] ?? 0,
                'y' => $layout['y'] ?? 0,
                'width' => $layout['width'] ?? 800,
                'height' => $layout['height'] ?? 600
            ],
            'options' => $this->options
        ]);

        return $this;
    }
}