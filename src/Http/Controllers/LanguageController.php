<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Translator;
use Native\ThinkPHP\Facades\LanguageSwitcher;

class LanguageController
{
    /**
     * 切换语言
     *
     * @param Request $request
     * @return Response
     */
    public function switch(Request $request)
    {
        $locale = $request->param('locale');
        
        if (!$locale) {
            return json([
                'success' => false,
                'message' => 'Locale is required',
            ]);
        }
        
        $success = LanguageSwitcher::switchLocale($locale);
        
        return json([
            'success' => $success,
            'locale' => $success ? $locale : Translator::getLocale(),
            'message' => $success ? 'Language switched successfully' : 'Failed to switch language',
        ]);
    }
    
    /**
     * 获取当前语言
     *
     * @return Response
     */
    public function current()
    {
        $locale = Translator::getLocale();
        $localeName = LanguageSwitcher::getLocaleName($locale);
        
        return json([
            'locale' => $locale,
            'name' => $localeName,
        ]);
    }
    
    /**
     * 获取可用语言列表
     *
     * @return Response
     */
    public function available()
    {
        $locales = LanguageSwitcher::getAvailableLocales();
        $localeNames = LanguageSwitcher::getLocaleNames();
        
        $result = [];
        foreach ($locales as $locale) {
            $result[] = [
                'locale' => $locale,
                'name' => $localeNames[$locale] ?? $locale,
            ];
        }
        
        return json([
            'locales' => $result,
        ]);
    }
    
    /**
     * 添加语言
     *
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $locale = $request->param('locale');
        $name = $request->param('name');
        
        if (!$locale) {
            return json([
                'success' => false,
                'message' => 'Locale is required',
            ]);
        }
        
        $success = LanguageSwitcher::addLocale($locale, $name);
        
        return json([
            'success' => $success,
            'message' => $success ? 'Language added successfully' : 'Failed to add language',
        ]);
    }
    
    /**
     * 移除语言
     *
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        $locale = $request->param('locale');
        
        if (!$locale) {
            return json([
                'success' => false,
                'message' => 'Locale is required',
            ]);
        }
        
        $success = LanguageSwitcher::removeLocale($locale);
        
        return json([
            'success' => $success,
            'message' => $success ? 'Language removed successfully' : 'Failed to remove language',
        ]);
    }
    
    /**
     * 渲染语言切换器
     *
     * @param Request $request
     * @return Response
     */
    public function render(Request $request)
    {
        $style = $request->param('style', 'default');
        $options = $request->param('options', []);
        
        $html = '';
        
        switch ($style) {
            case 'bootstrap':
                $html = LanguageSwitcher::renderBootstrap($options);
                break;
            case 'buttons':
                $html = LanguageSwitcher::renderButtons($options);
                break;
            case 'dropdown':
                $html = LanguageSwitcher::renderDropdown($options);
                break;
            default:
                $html = LanguageSwitcher::render($options);
                break;
        }
        
        return response($html);
    }
}
