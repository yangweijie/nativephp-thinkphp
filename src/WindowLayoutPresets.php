<?php

namespace NativePHP\Think;

class WindowLayoutPresets
{
    protected array $presets = [];

    public function __construct(protected WindowManager $manager)
    {
        $this->registerDefaultPresets();
    }

    protected function registerDefaultPresets(): void
    {
        // 水平布局预设
        $this->define('horizontal', function ($manager, $windows) {
            $screenWidth = $manager->getScreenWidth();
            $count = count($windows);
            $width = $screenWidth / $count;

            foreach ($windows as $index => $window) {
                $manager->getWindow($window)
                    ->width($width)
                    ->x($width * $index)
                    ->show();
            }
        });

        // 垂直布局预设
        $this->define('vertical', function ($manager, $windows) {
            $screenHeight = $manager->getScreenHeight();
            $count = count($windows);
            $height = $screenHeight / $count;

            foreach ($windows as $index => $window) {
                $manager->getWindow($window)
                    ->height($height)
                    ->y($height * $index)
                    ->show();
            }
        });

        // 网格布局预设
        $this->define('grid', function ($manager, $windows) {
            $count = count($windows);
            $cols = ceil(sqrt($count));
            $rows = ceil($count / $cols);

            $screenWidth = $manager->getScreenWidth();
            $screenHeight = $manager->getScreenHeight();

            $width = $screenWidth / $cols;
            $height = $screenHeight / $rows;

            foreach ($windows as $index => $window) {
                $row = floor($index / $cols);
                $col = $index % $cols;

                $manager->getWindow($window)
                    ->width($width)
                    ->height($height)
                    ->x($width * $col)
                    ->y($height * $row)
                    ->show();
            }
        });

        // 瀑布流布局预设
        $this->define('cascade', function ($manager, $windows) {
            $offset = 30;
            foreach ($windows as $index => $window) {
                $manager->getWindow($window)
                    ->x($offset * $index)
                    ->y($offset * $index)
                    ->show();
            }
        });

        // 分屏布局预设
        $this->define('split', function ($manager, $windows) {
            if (count($windows) !== 2) {
                return;
            }

            $screenWidth = $manager->getScreenWidth();
            $screenHeight = $manager->getScreenHeight();

            // 左侧窗口
            $manager->getWindow($windows[0])
                ->width($screenWidth / 2)
                ->height($screenHeight)
                ->x(0)
                ->y(0)
                ->show();

            // 右侧窗口
            $manager->getWindow($windows[1])
                ->width($screenWidth / 2)
                ->height($screenHeight)
                ->x($screenWidth / 2)
                ->y(0)
                ->show();
        });
    }

    public function define(string $name, callable $callback): self
    {
        $this->presets[$name] = $callback;
        return $this;
    }

    public function apply(string $name, array $windows): self
    {
        if (!isset($this->presets[$name])) {
            throw new \InvalidArgumentException("Layout preset '{$name}' not found.");
        }

        call_user_func($this->presets[$name], $this->manager, $windows);
        return $this;
    }

    public function getPresets(): array
    {
        return array_keys($this->presets);
    }

    public function hasPreset(string $name): bool
    {
        return isset($this->presets[$name]);
    }

    public function removePreset(string $name): self
    {
        unset($this->presets[$name]);
        return $this;
    }

    public function clearPresets(): self
    {
        $this->presets = [];
        $this->registerDefaultPresets();
        return $this;
    }
}