<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string get(string $key, array $replace = [], string $locale = null) 获取翻译
 * @method static void setLocale(string $locale) 设置当前语言
 * @method static void setDefaultLocale(string $locale) 设置默认语言
 * @method static string getLocale() 获取当前语言
 * @method static string getDefaultLocale() 获取默认语言
 * @method static array getAvailableLocales() 获取可用语言列表
 * @method static bool hasLocale(string $locale) 检查语言是否可用
 * @method static string getLocaleFromSystem() 从系统获取语言
 * @method static string getLocaleFromBrowser() 从浏览器获取语言
 * @method static string detectLocale() 自动检测语言
 * @method static void loadTranslations(string $path) 加载语言包
 *
 * @see \Native\ThinkPHP\I18n\Translator
 */
class Translator extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.translator';
    }
}
