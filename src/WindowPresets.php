<?php

namespace NativePHP\Think;

class WindowPresets
{
    protected array $presets = [];

    public function __construct(protected Native $native)
    {
        $this->registerDefaultPresets();
    }

    protected function registerDefaultPresets(): void
    {
        // 默认窗口预设
        $this->define('default', [
            'width' => 800,
            'height' => 600,
            'center' => true,
            'resizable' => true,
            'decorations' => true,
        ]);

        // 主窗口预设
        $this->define('main', [
            'width' => 1200,
            'height' => 800,
            'center' => true,
            'resizable' => true,
            'maximizable' => true,
            'minimizable' => true,
            'closable' => true,
            'decorations' => true,
        ]);

        // 对话框预设
        $this->define('dialog', [
            'width' => 500,
            'height' => 400,
            'center' => true,
            'resizable' => false,
            'maximizable' => false,
            'minimizable' => false,
            'closable' => true,
            'decorations' => true,
            'alwaysOnTop' => true,
            'modal' => true,
        ]);

        // 无边框预设
        $this->define('frameless', [
            'decorations' => false,
            'resizable' => false,
            'draggable' => true,
            'transparent' => true,
        ]);

        // 侧边栏预设
        $this->define('sidebar', [
            'width' => 300,
            'height' => '100%',
            'x' => 0,
            'y' => 0,
            'resizable' => false,
            'maximizable' => false,
            'minimizable' => false,
            'skipTaskbar' => true,
        ]);

        // 工具栏预设
        $this->define('toolbar', [
            'width' => '100%',
            'height' => 40,
            'x' => 0,
            'y' => 0,
            'resizable' => false,
            'maximizable' => false,
            'minimizable' => false,
            'skipTaskbar' => true,
            'alwaysOnTop' => true,
            'decorations' => false,
        ]);

        // 通知预设
        $this->define('notification', [
            'width' => 300,
            'height' => 100,
            'resizable' => false,
            'maximizable' => false,
            'minimizable' => false,
            'closable' => true,
            'skipTaskbar' => true,
            'alwaysOnTop' => true,
            'decorations' => false,
            'autoHide' => 5000, // 5 seconds
        ]);

        // 浮动面板预设
        $this->define('panel', [
            'width' => 400,
            'height' => 300,
            'resizable' => true,
            'maximizable' => false,
            'minimizable' => true,
            'closable' => true,
            'alwaysOnTop' => true,
            'skipTaskbar' => true,
        ]);
    }

    public function define(string $name, array $options): self
    {
        $this->presets[$name] = $options;
        return $this;
    }

    public function get(string $name): ?array
    {
        return $this->presets[$name] ?? null;
    }

    public function apply(string $name, Window $window): self
    {
        if (!isset($this->presets[$name])) {
            throw new \InvalidArgumentException("Window preset '{$name}' not found.");
        }

        $window->configure($this->presets[$name]);
        return $this;
    }

    public function exists(string $name): bool
    {
        return isset($this->presets[$name]);
    }

    public function remove(string $name): self
    {
        unset($this->presets[$name]);
        return $this;
    }

    public function getPresets(): array
    {
        return array_keys($this->presets);
    }

    public function getPresetOptions(string $name): ?array
    {
        return $this->presets[$name] ?? null;
    }

    public function clearPresets(): self
    {
        $this->presets = [];
        $this->registerDefaultPresets();
        return $this;
    }
}