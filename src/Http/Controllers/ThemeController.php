<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Theme;
use Native\ThinkPHP\Facades\ThemeSwitcher;

class ThemeController
{
    /**
     * 切换主题
     *
     * @param Request $request
     * @return Response
     */
    public function switch(Request $request)
    {
        $theme = $request->param('theme');
        
        if (!$theme) {
            return json([
                'success' => false,
                'message' => 'Theme is required',
            ]);
        }
        
        $success = ThemeSwitcher::switchTheme($theme);
        
        return json([
            'success' => $success,
            'theme' => $success ? $theme : Theme::getCurrent(),
            'message' => $success ? 'Theme switched successfully' : 'Failed to switch theme',
        ]);
    }
    
    /**
     * 获取当前主题
     *
     * @return Response
     */
    public function current()
    {
        $theme = Theme::getCurrent();
        $themeName = Theme::getName($theme);
        
        return json([
            'theme' => $theme,
            'name' => $themeName,
        ]);
    }
    
    /**
     * 获取可用主题列表
     *
     * @return Response
     */
    public function available()
    {
        $themes = Theme::getAvailable();
        $themeNames = Theme::getNames();
        
        $result = [];
        foreach ($themes as $theme) {
            $result[] = [
                'theme' => $theme,
                'name' => $themeNames[$theme] ?? $theme,
            ];
        }
        
        return json([
            'themes' => $result,
        ]);
    }
    
    /**
     * 添加主题
     *
     * @param Request $request
     * @return Response
     */
    public function add(Request $request)
    {
        $theme = $request->param('theme');
        $options = $request->param('options', []);
        
        if (!$theme) {
            return json([
                'success' => false,
                'message' => 'Theme is required',
            ]);
        }
        
        $success = Theme::add($theme, $options);
        
        return json([
            'success' => $success,
            'message' => $success ? 'Theme added successfully' : 'Failed to add theme',
        ]);
    }
    
    /**
     * 移除主题
     *
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        $theme = $request->param('theme');
        
        if (!$theme) {
            return json([
                'success' => false,
                'message' => 'Theme is required',
            ]);
        }
        
        $success = Theme::remove($theme);
        
        return json([
            'success' => $success,
            'message' => $success ? 'Theme removed successfully' : 'Failed to remove theme',
        ]);
    }
    
    /**
     * 获取主题选项
     *
     * @param Request $request
     * @return Response
     */
    public function options(Request $request)
    {
        $theme = $request->param('theme');
        
        $options = Theme::getOptions($theme);
        
        return json([
            'theme' => $theme ?: Theme::getCurrent(),
            'options' => $options,
        ]);
    }
    
    /**
     * 保存主题选项
     *
     * @param Request $request
     * @return Response
     */
    public function saveOptions(Request $request)
    {
        $theme = $request->param('theme');
        $options = $request->param('options', []);
        
        if (!$theme) {
            return json([
                'success' => false,
                'message' => 'Theme is required',
            ]);
        }
        
        $success = Theme::saveOptions($theme, $options);
        
        return json([
            'success' => $success,
            'message' => $success ? 'Theme options saved successfully' : 'Failed to save theme options',
        ]);
    }
    
    /**
     * 应用主题
     *
     * @param Request $request
     * @return Response
     */
    public function apply(Request $request)
    {
        $theme = $request->param('theme');
        
        $success = Theme::apply($theme);
        
        return json([
            'success' => $success,
            'theme' => $theme ?: Theme::getCurrent(),
            'message' => $success ? 'Theme applied successfully' : 'Failed to apply theme',
        ]);
    }
    
    /**
     * 检测系统主题
     *
     * @return Response
     */
    public function detectSystem()
    {
        $theme = Theme::detectSystemTheme();
        
        return json([
            'theme' => $theme,
        ]);
    }
    
    /**
     * 系统主题变化
     *
     * @param Request $request
     * @return Response
     */
    public function systemChanged(Request $request)
    {
        $theme = $request->param('theme');
        
        // 如果当前主题是系统主题，则应用新主题
        if (Theme::getCurrent() === 'system') {
            Theme::apply($theme);
        }
        
        return json([
            'success' => true,
            'theme' => $theme,
        ]);
    }
    
    /**
     * 渲染主题切换器
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
                $html = ThemeSwitcher::renderBootstrap($options);
                break;
            case 'buttons':
                $html = ThemeSwitcher::renderButtons($options);
                break;
            case 'dropdown':
                $html = ThemeSwitcher::renderDropdown($options);
                break;
            case 'icons':
                $html = ThemeSwitcher::renderIcons($options);
                break;
            case 'toggle':
                $html = ThemeSwitcher::renderToggle($options);
                break;
            default:
                $html = ThemeSwitcher::render($options);
                break;
        }
        
        return response($html);
    }
}
