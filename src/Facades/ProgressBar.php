<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\ProgressBar create(int $maxSteps) 创建新实例
 * @method static void start() 开始进度条
 * @method static void advance(int $step = 1) 前进步骤
 * @method static void setProgress(int $step) 设置进度
 * @method static void finish() 完成进度条
 * @method static void display() 显示进度条
 * 
 * @see \Native\ThinkPHP\ProgressBar
 */
class ProgressBar extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.progress_bar';
    }
}
