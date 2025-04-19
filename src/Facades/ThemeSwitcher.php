<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string getCurrentTheme() 获取当前主题
 * @method static array getAvailableThemes() 获取可用主题列表
 * @method static string getThemeName(string $theme) 获取主题名称
 * @method static array getThemeNames() 获取所有主题名称
 * @method static bool switchTheme(string $theme) 切换主题
 * @method static string render(array $options = []) 渲染主题切换器
 * @method static string renderBootstrap(array $options = []) 渲染主题切换器（Bootstrap 风格）
 * @method static string renderButtons(array $options = []) 渲染主题切换器（按钮组风格）
 * @method static string renderDropdown(array $options = []) 渲染主题切换器（下拉菜单风格）
 * @method static string renderIcons(array $options = []) 渲染主题切换器（图标风格）
 * @method static string renderToggle(array $options = []) 渲染主题切换器（开关风格）
 * 
 * @see \Native\ThinkPHP\Components\ThemeSwitcher
 */
class ThemeSwitcher extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.theme_switcher';
    }
}
