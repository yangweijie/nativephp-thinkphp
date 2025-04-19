<?php

namespace NativePHP\Think;

use NativePHP\Think\Contract\WindowGroupContract;

class WindowGroup implements WindowGroupContract, WindowGroupLayoutContract
{
    /**
     * @var array<string, Window>
     */
    protected array $windows = [];

    /**
     * @var WindowGroupState
     */
    protected WindowGroupState $state;
    
    /**
     * @var string
     */
    protected string $name;

    /**
     * @var array
     */
    protected array $eventListeners = [];

    /**
     * @var int
     */
    protected int $activeIndex = 0;

    /**
     * @var array
     */
    protected array $layoutOptions = [];

    /**
     * @var string|null
     */
    protected ?string $currentLayout = null;
    
    public function __construct(
        protected WindowManager $windowManager,
        string $name,
        ?string $configPath = null
    ) {
        $this->name = $name;
        $this->state = new WindowGroupState();
        if ($configPath) {
            $this->state->setConfigPath($configPath)->loadFromFile();
            $savedState = $this->state->get($name);
            if ($savedState) {
                $this->restoreState($savedState);
            }
        }
    }
    
    /**
     * 添加窗口到组
     */
    public function add(string $label): self
    {
        if ($window = $this->windowManager->get($label)) {
            $this->windows[$label] = $window;
            $window->configure(['group' => $this->name]);
        }
        return $this;
    }
    
    /**
     * 从组中移除窗口
     */
    public function remove(string $label): self
    {
        unset($this->windows[$label]);
        return $this;
    }
    
    /**
     * 检查窗口是否在组中
     */
    public function has(string $label): bool 
    {
        return isset($this->windows[$label]);
    }
    
    /**
     * 获取组中所有窗口标识
     */
    public function all(): array
    {
        return array_keys($this->windows);
    }

    /**
     * 获取组中窗口数量
     */
    public function count(): int
    {
        return count($this->windows);
    }
    
    /**
     * 关闭组中的所有窗口
     */
    public function closeAll(): self
    {
        foreach ($this->windows as $label => $window) {
            $this->windowManager->close($label);
        }
        $this->windows = [];
        return $this;
    }

    /**
     * 获取组名称
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 水平排列组内窗口
     */
    public function arrangeHorizontal(): self
    {
        $this->windowManager->arrange($this->all(), 'horizontal');
        return $this;
    }

    /**
     * 垂直排列组内窗口
     */
    public function arrangeVertical(): self
    {
        $this->windowManager->arrange($this->all(), 'vertical');
        return $this;
    }

    /**
     * 网格布局组内窗口
     */
    public function arrangeGrid(int $columns = 2): self
    {
        $this->windowManager->grid($this->all(), $columns);
        return $this;
    }

    /**
     * 聚焦组内第一个窗口
     */
    public function focus(): self
    {
        if ($window = $this->getActiveWindow()) {
            $window->focus();
        }
        return $this;
    }

    /**
     * 切换到下一个窗口
     */
    public function nextWindow(): self
    {
        $this->activeIndex = ($this->activeIndex + 1) % count($this->windows);
        return $this->focus();
    }

    /**
     * 切换到上一个窗口
     */
    public function previousWindow(): self
    {
        $this->activeIndex = ($this->activeIndex - 1 + count($this->windows)) % count($this->windows);
        return $this->focus();
    }

    /**
     * 获取当前活动窗口
     */
    protected function getActiveWindow(): ?Window
    {
        $windows = array_values($this->windows);
        return $windows[$this->activeIndex] ?? null;
    }

    /**
     * 保存组内所有窗口状态
     */
    public function saveState(): array
    {
        $states = [];
        foreach ($this->windows as $label => $window) {
            $states[$label] = $this->windowManager->getState()->save($label);
        }
        return $states;
    }

    /**
     * 恢复组内所有窗口状态
     */
    public function restoreState(array $states): self
    {
        foreach ($states as $label => $state) {
            if ($this->has($label)) {
                $this->windowManager->getState()->restore($label, $state);
            }
        }
        return $this;
    }

    /**
     * 应用布局预设
     */
    public function applyLayout(string $preset, array $options = []): self
    {
        $this->layoutOptions = array_merge($this->layoutOptions, $options);
        $this->currentLayout = $preset;
        $this->windowManager->getLayoutPresets()->apply($preset, $this->all(), $this->layoutOptions);
        $this->trigger('layout.changed', ['preset' => $preset, 'options' => $options]);
        $this->state->save($this->name, $this->saveState());
        return $this;
    }

    /**
     * 同步窗口组布局到其他组
     */
    public function syncLayout(string $targetGroup): self
    {
        if ($target = $this->windowManager->getGroup($targetGroup)) {
            $target->applyLayout($this->getCurrentLayout(), $this->getLayoutOptions());
        }
        return $this;
    }

    /**
     * 导出布局配置
     */
    public function exportLayout(): array
    {
        return [
            'preset' => $this->getCurrentLayout(),
            'options' => $this->getLayoutOptions(),
            'windows' => $this->all()
        ];
    }

    /**
     * 导入布局配置
     */
    public function importLayout(array $config): self
    {
        if (isset($config['preset'])) {
            $this->applyLayout($config['preset'], $config['options'] ?? []);
        }
        return $this;
    }

    /**
     * 保存布局配置到文件
     */
    public function saveLayoutToFile(string $path): self
    {
        file_put_contents($path, json_encode($this->exportLayout(), JSON_PRETTY_PRINT));
        return $this;
    }

    /**
     * 从文件加载布局配置
     */
    public function loadLayoutFromFile(string $path): self
    {
        if (file_exists($path)) {
            $config = json_decode(file_get_contents($path), true);
            $this->importLayout($config);
        }
        return $this;
    }

    /**
     * 注册布局变更事件监听器
     */
    public function onLayoutChange(callable $callback): self
    {
        return $this->on('layout.changed', $callback);
    }

    /**
     * 获取当前布局预设名称
     */
    public function getCurrentLayout(): ?string
    {
        return $this->getState('current_layout');
    }

    /**
     * 获取布局选项
     */
    public function getLayoutOptions(): array
    {
        return $this->layoutOptions;
    }

    /**
     * 应用瀑布流布局
     */
    public function cascade(): self
    {
        return $this->applyLayout('cascade');
    }

    /**
     * 应用分屏布局
     */
    public function split(): self
    {
        return $this->applyLayout('split');
    }

    /**
     * 最小化所有窗口
     */
    public function minimizeAll(): self
    {
        foreach ($this->windows as $window) {
            $window->minimize();
        }
        $this->trigger('windows.minimized');
        return $this;
    }

    /**
     * 恢复所有窗口
     */
    public function restoreAll(): self
    {
        foreach ($this->windows as $window) {
            $window->restore();
        }
        $this->trigger('windows.restored');
        return $this;
    }

    /**
     * 设置窗口组状态
     */
    public function setState(string $key, $value): self
    {
        $this->state[$key] = $value;
        $this->trigger('state.changed', ['key' => $key, 'value' => $value]);
        return $this;
    }

    /**
     * 获取窗口组状态
     */
    public function getState(string $key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * 清除窗口组状态
     */
    public function clearState(string $key = null): self
    {
        if ($key === null) {
            $this->state = [];
            $this->trigger('state.cleared');
        } else {
            unset($this->state[$key]);
            $this->trigger('state.removed', ['key' => $key]);
        }
        return $this;
    }

    /**
     * 添加事件监听器
     */
    public function on(string $event, callable $callback): self
    {
        if (!isset($this->eventListeners[$event])) {
            $this->eventListeners[$event] = [];
        }
        $this->eventListeners[$event][] = $callback;
        return $this;
    }

    /**
     * 触发事件
     */
    protected function trigger(string $event, array $data = []): void
    {
        if (isset($this->eventListeners[$event])) {
            foreach ($this->eventListeners[$event] as $callback) {
                $callback($data, $this);
            }
        }
    }
}