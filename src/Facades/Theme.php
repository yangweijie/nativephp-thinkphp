<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string getCurrent() 获取当前主题
 * @method static bool setCurrent(string $theme) 设置当前主题
 * @method static string getDefault() 获取默认主题
 * @method static bool setDefault(string $theme) 设置默认主题
 * @method static array getAvailable() 获取可用主题列表
 * @method static bool add(string $theme, array $options = []) 添加主题
 * @method static bool remove(string $theme) 移除主题
 * @method static array getOptions(string $theme = null) 获取主题选项
 * @method static bool saveOptions(string $theme, array $options) 保存主题选项
 * @method static bool apply(string $theme = null) 应用主题
 * @method static string detectSystemTheme() 检测系统主题
 * @method static void watchSystemTheme() 监听系统主题变化
 * @method static string getName(string $theme = null) 获取主题名称
 * @method static array getNames() 获取所有主题名称
 * 
 * @see \Native\ThinkPHP\Theme
 */
class Theme extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.theme';
    }
}
