<?php

namespace NativePHP\Think;

use think\facade\Cache;

class WindowGroupStateManager
{
    protected string $cacheKey = 'native_window_groups';
    protected int $expireTime = 7 * 24 * 60 * 60; // 7天

    public function __construct(protected WindowManager $manager)
    {
    }

    /**
     * 自动保存所有分组状态
     */
    public function autoSaveAll(): void
    {
        $states = [];
        foreach ($this->manager->getGroups() as $name => $group) {
            $states[$name] = [
                'windows' => $group->saveState(),
                'layout' => $group->getCurrentLayout(),
                'timestamp' => time()
            ];
        }
        
        Cache::set($this->cacheKey, $states, $this->expireTime);
    }

    /**
     * 自动恢复所有分组状态
     */
    public function autoRestoreAll(): void
    {
        $states = Cache::get($this->cacheKey, []);
        
        foreach ($states as $name => $state) {
            if ($group = $this->manager->getGroup($name)) {
                $group->restoreState($state['windows']);
                
                if ($state['layout']) {
                    switch ($state['layout']) {
                        case 'horizontal':
                            $group->arrangeHorizontal();
                            break;
                        case 'vertical':
                            $group->arrangeVertical();
                            break;
                        case 'grid':
                            $group->arrangeGrid();
                            break;
                    }
                }
            }
        }
    }

    /**
     * 清除所有保存的状态
     */
    public function clearAll(): void
    {
        Cache::delete($this->cacheKey);
    }

    /**
     * 设置缓存过期时间
     */
    public function setExpireTime(int $seconds): self
    {
        $this->expireTime = $seconds;
        return $this;
    }

    /**
     * 设置缓存键名
     */
    public function setCacheKey(string $key): self
    {
        $this->cacheKey = $key;
        return $this;
    }
}