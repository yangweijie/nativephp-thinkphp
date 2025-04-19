<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string getCurrentLocale() 获取当前语言
 * @method static array getAvailableLocales() 获取可用语言列表
 * @method static string getLocaleName(string $locale) 获取语言名称
 * @method static array getLocaleNames() 获取所有语言名称
 * @method static bool switchLocale(string $locale) 切换语言
 * @method static bool addLocale(string $locale, string $name = null) 添加语言
 * @method static bool removeLocale(string $locale) 移除语言
 * @method static string render(array $options = []) 渲染语言切换器
 * @method static string renderBootstrap(array $options = []) 渲染语言切换器（Bootstrap 风格）
 * @method static string renderButtons(array $options = []) 渲染语言切换器（按钮组风格）
 * @method static string renderDropdown(array $options = []) 渲染语言切换器（下拉菜单风格）
 * 
 * @see \Native\ThinkPHP\Components\LanguageSwitcher
 */
class LanguageSwitcher extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.language_switcher';
    }
}
