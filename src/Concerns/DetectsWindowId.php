<?php

namespace Native\ThinkPHP\Concerns;

trait DetectsWindowId
{
    /**
     * 检测当前窗口 ID
     *
     * @return string|null
     */
    protected function detectId()
    {
        // 从请求头中获取窗口 ID
        $windowId = request()->header('X-NativePHP-Window-Id');
        
        if ($windowId) {
            return $windowId;
        }
        
        // 如果没有窗口 ID，则返回 'main'
        return 'main';
    }
}
