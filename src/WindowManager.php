<?php

namespace NativePHP\Think;

use InvalidArgumentException;
use NativePHP\Think\Contract\WindowContract;

class WindowManager
{
    protected $native;
    protected $windows = [];

    /**
     * @var array<string, WindowGroup>
     */
    protected array $groups = [];

    public function __construct(Native $native)
    {
        $this->native = $native;
    }

    public function create(string $label = 'main', array $options = [])
    {
        $window = new Window($this->native);
        
        // 合并默认配置
        $defaultOptions = $this->native->getConfig('window', []);
        $options = array_merge($defaultOptions, $options);
        
        foreach ($options as $key => $value) {
            if (method_exists($window, $key)) {
                $window->$key($value);
            }
        }
        
        $this->windows[$label] = $window;
        return $window;
    }

    public function get(string $label)
    {
        return $this->windows[$label] ?? null;
    }

    public function all()
    {
        return $this->windows;
    }

    public function close(string $label)
    {
        if (isset($this->windows[$label])) {
            unset($this->windows[$label]);
            return true;
        }
        return false;
    }

    public function closeAll()
    {
        $this->windows = [];
        return true;
    }

    public function count()
    {
        return count($this->windows);
    }

    public function createFromPreset(string $preset, ?string $label = null)
    {
        return $this->native->windowPresets()->apply($preset, $label ?? $preset);
    }

    public function createDialog(?string $label = null)
    {
        return $this->createFromPreset('dialog', $label);
    }

    public function createSettings(?string $label = null)
    {
        return $this->createFromPreset('settings', $label);
    }

    public function createSmall(?string $label = null)
    {
        return $this->createFromPreset('small', $label);
    }

    /**
     * 批量创建多个窗口
     */
    public function createMultiple(array $windows)
    {
        $instances = [];
        foreach ($windows as $label => $options) {
            $instances[$label] = $this->create($label, $options);
        }
        return $instances;
    }

    /**
     * 批量关闭多个窗口
     */
    public function closeMultiple(array $labels)
    {
        foreach ($labels as $label) {
            $this->close($label);
        }
        return $this;
    }

    /**
     * 聚焦到指定窗口
     */
    public function focus(string $label)
    {
        if ($window = $this->get($label)) {
            $window->configure(['focus' => true]);
        }
        return $this;
    }

    /**
     * 按指定顺序排列窗口
     */
    public function arrange(array $labels, string $direction = 'horizontal')
    {
        $screenWidth = 1920; // 默认屏幕宽度
        $screenHeight = 1080; // 默认屏幕高度
        
        $count = count($labels);
        if ($count === 0) return $this;

        if ($direction === 'horizontal') {
            $width = floor($screenWidth / $count);
            $x = 0;
            
            foreach ($labels as $label) {
                if ($window = $this->get($label)) {
                    $window->configure([
                        'x' => $x,
                        'y' => 0,
                        'width' => $width,
                        'height' => $screenHeight
                    ]);
                    $x += $width;
                }
            }
        } else {
            $height = floor($screenHeight / $count);
            $y = 0;
            
            foreach ($labels as $label) {
                if ($window = $this->get($label)) {
                    $window->configure([
                        'x' => 0,
                        'y' => $y,
                        'width' => $screenWidth,
                        'height' => $height
                    ]);
                    $y += $height;
                }
            }
        }
        
        return $this;
    }

    /**
     * 以网格形式排列窗口
     */
    public function grid(array $labels, int $columns = 2)
    {
        $screenWidth = 1920; // 默认屏幕宽度
        $screenHeight = 1080; // 默认屏幕高度
        
        $count = count($labels);
        if ($count === 0) return $this;

        $rows = ceil($count / $columns);
        $width = floor($screenWidth / $columns);
        $height = floor($screenHeight / $rows);
        
        $x = 0;
        $y = 0;
        $col = 0;
        
        foreach ($labels as $label) {
            if ($window = $this->get($label)) {
                $window->configure([
                    'x' => $x,
                    'y' => $y,
                    'width' => $width,
                    'height' => $height
                ]);
                
                $col++;
                if ($col >= $columns) {
                    $col = 0;
                    $x = 0;
                    $y += $height;
                } else {
                    $x += $width;
                }
            }
        }
        
        return $this;
    }

    /**
     * 交换两个窗口的位置
     */
    public function swap(string $label1, string $label2)
    {
        $window1 = $this->get($label1);
        $window2 = $this->get($label2);
        
        if ($window1 && $window2) {
            $options1 = $window1->getOptions();
            $options2 = $window2->getOptions();
            
            $pos1 = [
                'x' => $options1['x'] ?? 0,
                'y' => $options1['y'] ?? 0,
                'width' => $options1['width'] ?? 800,
                'height' => $options1['height'] ?? 600
            ];
            
            $pos2 = [
                'x' => $options2['x'] ?? 0,
                'y' => $options2['y'] ?? 0,
                'width' => $options2['width'] ?? 800,
                'height' => $options2['height'] ?? 600
            ];
            
            $window1->configure($pos2);
            $window2->configure($pos1);
        }
        
        return $this;
    }

    /**
     * 以主从布局排列窗口
     */
    public function masterDetail(string $masterLabel, string $detailLabel, float $ratio = 0.3)
    {
        $screenWidth = 1920; // 默认屏幕宽度
        $screenHeight = 1080; // 默认屏幕高度
        
        $master = $this->get($masterLabel);
        $detail = $this->get($detailLabel);
        
        if ($master && $detail) {
            $detailWidth = floor($screenWidth * $ratio);
            $masterWidth = $screenWidth - $detailWidth;
            
            $master->configure([
                'x' => 0,
                'y' => 0,
                'width' => $masterWidth,
                'height' => $screenHeight
            ]);
            
            $detail->configure([
                'x' => $masterWidth,
                'y' => 0,
                'width' => $detailWidth,
                'height' => $screenHeight
            ]);
        }
        
        return $this;
    }

    /**
     * 创建窗口组
     */
    public function createGroup(string $name, array $windows = []): WindowGroup
    {
        if (isset($this->groups[$name])) {
            throw new InvalidArgumentException("Window group '{$name}' already exists.");
        }

        $group = new WindowGroup($this, $name);
        $this->groups[$name] = $group;

        foreach ($windows as $label => $options) {
            $window = $this->create($label, $options);
            $group->add($window);
        }

        return $group;
    }

    /**
     * 获取窗口组
     */
    public function getGroup(string $name): ?WindowGroup
    {
        return $this->groups[$name] ?? null;
    }

    /**
     * 删除窗口组
     */
    public function removeGroup(string $name): bool
    {
        if (isset($this->groups[$name])) {
            unset($this->groups[$name]);
            return true;
        }
        return false;
    }

    /**
     * 保存布局预设
     */
    public function saveLayout(string $name, array $layout): self
    {
        $this->layouts[$name] = $layout;
        return $this;
    }

    /**
     * 应用布局预设
     */
    public function applyLayout(string $name): self
    {
        if (!isset($this->layouts[$name])) {
            throw new InvalidArgumentException("Layout '{$name}' not found.");
        }

        $layout = $this->layouts[$name];
        if (isset($layout['arrange'])) {
            $this->arrange($layout['arrange']['windows'], $layout['arrange']['direction'] ?? 'horizontal');
        } elseif (isset($layout['grid'])) {
            $this->grid($layout['grid']['windows'], $layout['grid']['columns'] ?? 2);
        }

        return $this;
    }
}