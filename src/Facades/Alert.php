<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Alert type(string $type) 设置警告类型
 * @method static \Native\ThinkPHP\Alert title(string $title) 设置警告标题
 * @method static \Native\ThinkPHP\Alert detail(string $detail) 设置警告详情
 * @method static \Native\ThinkPHP\Alert buttons(array $buttons) 设置按钮列表
 * @method static \Native\ThinkPHP\Alert defaultId(int $defaultId) 设置默认按钮ID
 * @method static \Native\ThinkPHP\Alert cancelId(int $cancelId) 设置取消按钮ID
 * @method static int show(string $message) 显示警告消息
 * @method static bool error(string $title, string $message) 显示错误警告
 * 
 * @see \Native\ThinkPHP\Alert
 */
class Alert extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.alert';
    }
}
