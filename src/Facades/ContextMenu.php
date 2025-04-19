<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static void register(\Native\ThinkPHP\Menu $menu) 注册上下文菜单
 * @method static void remove() 移除上下文菜单
 * 
 * @see \Native\ThinkPHP\ContextMenu
 */
class ContextMenu extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.context_menu';
    }
}
