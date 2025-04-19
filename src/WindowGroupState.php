<?php

namespace NativePHP\Think;

class WindowGroupState
{
    /**
     * @var array
     */
    protected array $states = [];

    /**
     * @var string|null
     */
    protected ?string $configPath = null;

    /**
     * 设置配置文件路径
     */
    public function setConfigPath(string $path): self
    {
        $this->configPath = $path;
        return $this;
    }

    /**
     * 保存窗口组状态
     */
    public function save(string $groupName, array $state): self
    {
        $this->states[$groupName] = $state;
        $this->saveToFile();
        return $this;
    }

    /**
     * 获取窗口组状态
     */
    public function get(string $groupName): ?array
    {
        return $this->states[$groupName] ?? null;
    }

    /**
     * 删除窗口组状态
     */
    public function remove(string $groupName): self
    {
        unset($this->states[$groupName]);
        $this->saveToFile();
        return $this;
    }

    /**
     * 清除所有状态
     */
    public function clear(): self
    {
        $this->states = [];
        $this->saveToFile();
        return $this;
    }

    /**
     * 保存状态到文件
     */
    protected function saveToFile(): void
    {
        if ($this->configPath) {
            file_put_contents($this->configPath, json_encode($this->states, JSON_PRETTY_PRINT));
        }
    }

    /**
     * 从文件加载状态
     */
    public function loadFromFile(): self
    {
        if ($this->configPath && file_exists($this->configPath)) {
            $content = file_get_contents($this->configPath);
            $this->states = json_decode($content, true) ?? [];
        }
        return $this;
    }

    /**
     * 获取所有状态
     */
    public function all(): array
    {
        return $this->states;
    }
}