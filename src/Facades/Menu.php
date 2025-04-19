<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static \Native\ThinkPHP\Menu create() 创建一个新菜单
 * @method static \Native\ThinkPHP\Menu add(string $label, callable|array|null $action = null, array $options = []) 添加菜单项
 * @method static \Native\ThinkPHP\Menu checkbox(string $label, bool $checked = false, callable|null $action = null, array $options = []) 添加复选框菜单项
 * @method static \Native\ThinkPHP\Menu radio(string $label, bool $checked = false, callable|null $action = null, array $options = []) 添加单选框菜单项
 * @method static \Native\ThinkPHP\Menu separator() 添加分隔线
 * @method static \Native\ThinkPHP\Menu submenu(string $label, callable $callback) 添加子菜单
 * @method static \Native\ThinkPHP\Menu accelerator(string $label, string $accelerator, callable|null $action = null, array $options = []) 添加快捷键菜单项
 * @method static \Native\ThinkPHP\Menu icon(string $label, string $icon, callable|null $action = null, array $options = []) 添加图标菜单项
 * @method static \Native\ThinkPHP\Menu disabled(string $label, array $options = []) 添加禁用的菜单项
 * @method static \Native\ThinkPHP\Menu role(string $role, array $options = []) 添加角色菜单项（macOS特有）
 * @method static bool setApplicationMenu() 设置应用菜单
 * @method static bool setContextMenu() 设置上下文菜单
 * @method static bool popup(int|null $x = null, int|null $y = null) 弹出上下文菜单
 * @method static bool clearApplicationMenu() 清除应用菜单
 * @method static bool clearContextMenu() 清除上下文菜单
 * @method static array getItems() 获取菜单项
 *
 * @see \Native\ThinkPHP\Menu
 */
class Menu extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.menu';
    }
}
