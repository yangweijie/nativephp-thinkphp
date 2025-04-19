<?php

namespace Native\ThinkPHP\Components;

use think\App;
use Native\ThinkPHP\Facades\Theme;
use Native\ThinkPHP\Facades\Translator;

class ThemeSwitcher
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 获取当前主题
     *
     * @return string
     */
    public function getCurrentTheme()
    {
        return Theme::getCurrent();
    }

    /**
     * 获取可用主题列表
     *
     * @return array
     */
    public function getAvailableThemes()
    {
        return Theme::getAvailable();
    }

    /**
     * 获取主题名称
     *
     * @param string $theme
     * @return string
     */
    public function getThemeName($theme)
    {
        return Theme::getName($theme);
    }

    /**
     * 获取所有主题名称
     *
     * @return array
     */
    public function getThemeNames()
    {
        return Theme::getNames();
    }

    /**
     * 切换主题
     *
     * @param string $theme
     * @return bool
     */
    public function switchTheme($theme)
    {
        $success = Theme::setCurrent($theme);
        
        if ($success) {
            Theme::apply($theme);
        }
        
        return $success;
    }

    /**
     * 渲染主题切换器
     *
     * @param array $options
     * @return string
     */
    public function render($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        $themeNames = $this->getThemeNames();
        
        $defaultOptions = [
            'class' => 'theme-switcher',
            'name' => 'theme',
            'id' => 'theme-switcher',
            'onchange' => 'this.form.submit()',
            'form' => 'theme-form',
            'label' => Translator::get('app.theme'),
            'show_label' => true,
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        if ($options['show_label']) {
            $html .= '<label for="' . $options['id'] . '">' . $options['label'] . '</label>';
        }
        
        $html .= '<select name="' . $options['name'] . '" id="' . $options['id'] . '" class="' . $options['class'] . '" onchange="' . $options['onchange'] . '">';
        
        foreach ($themeNames as $theme => $name) {
            $selected = $theme === $currentTheme ? ' selected' : '';
            $html .= '<option value="' . $theme . '"' . $selected . '>' . $name . '</option>';
        }
        
        $html .= '</select>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染主题切换器（Bootstrap 风格）
     *
     * @param array $options
     * @return string
     */
    public function renderBootstrap($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        $themeNames = $this->getThemeNames();
        
        $defaultOptions = [
            'class' => 'form-select',
            'name' => 'theme',
            'id' => 'theme-switcher',
            'onchange' => 'this.form.submit()',
            'form' => 'theme-form',
            'label' => Translator::get('app.theme'),
            'show_label' => true,
            'label_class' => 'form-label',
            'container_class' => 'mb-3',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        
        if ($options['show_label']) {
            $html .= '<label for="' . $options['id'] . '" class="' . $options['label_class'] . '">' . $options['label'] . '</label>';
        }
        
        $html .= '<select name="' . $options['name'] . '" id="' . $options['id'] . '" class="' . $options['class'] . '" onchange="' . $options['onchange'] . '">';
        
        foreach ($themeNames as $theme => $name) {
            $selected = $theme === $currentTheme ? ' selected' : '';
            $html .= '<option value="' . $theme . '"' . $selected . '>' . $name . '</option>';
        }
        
        $html .= '</select>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染主题切换器（按钮组风格）
     *
     * @param array $options
     * @return string
     */
    public function renderButtons($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        $themeNames = $this->getThemeNames();
        
        $defaultOptions = [
            'class' => 'btn-group',
            'button_class' => 'btn btn-outline-primary',
            'active_class' => 'active',
            'name' => 'theme',
            'form' => 'theme-form',
            'container_class' => 'mb-3',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '" role="group" aria-label="Theme Switcher">';
        
        foreach ($themeNames as $theme => $name) {
            $active = $theme === $currentTheme ? ' ' . $options['active_class'] : '';
            $html .= '<button type="submit" name="' . $options['name'] . '" value="' . $theme . '" class="' . $options['button_class'] . $active . '">' . $name . '</button>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染主题切换器（下拉菜单风格）
     *
     * @param array $options
     * @return string
     */
    public function renderDropdown($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        $themeNames = $this->getThemeNames();
        
        $defaultOptions = [
            'class' => 'dropdown',
            'button_class' => 'btn btn-outline-primary dropdown-toggle',
            'menu_class' => 'dropdown-menu',
            'item_class' => 'dropdown-item',
            'active_class' => 'active',
            'name' => 'theme',
            'form' => 'theme-form',
            'container_class' => 'mb-3',
            'button_text' => Translator::get('app.theme'),
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '">';
        $html .= '<button class="' . $options['button_class'] . '" type="button" id="themeDropdown" data-bs-toggle="dropdown" aria-expanded="false">';
        $html .= $options['button_text'] . ': ' . $themeNames[$currentTheme];
        $html .= '</button>';
        $html .= '<ul class="' . $options['menu_class'] . '" aria-labelledby="themeDropdown">';
        
        foreach ($themeNames as $theme => $name) {
            $active = $theme === $currentTheme ? ' ' . $options['active_class'] : '';
            $html .= '<li><button type="submit" name="' . $options['name'] . '" value="' . $theme . '" class="' . $options['item_class'] . $active . '">' . $name . '</button></li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染主题切换器（图标风格）
     *
     * @param array $options
     * @return string
     */
    public function renderIcons($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        
        $defaultOptions = [
            'class' => 'theme-switcher-icons',
            'button_class' => 'btn btn-outline-primary',
            'active_class' => 'active',
            'name' => 'theme',
            'form' => 'theme-form',
            'container_class' => 'mb-3',
            'icons' => [
                'light' => '<i class="fas fa-sun"></i>',
                'dark' => '<i class="fas fa-moon"></i>',
                'system' => '<i class="fas fa-desktop"></i>',
            ],
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '">';
        
        foreach ($this->getAvailableThemes() as $theme) {
            $active = $theme === $currentTheme ? ' ' . $options['active_class'] : '';
            $icon = $options['icons'][$theme] ?? '';
            
            $html .= '<button type="submit" name="' . $options['name'] . '" value="' . $theme . '" class="' . $options['button_class'] . $active . '" title="' . $this->getThemeName($theme) . '">' . $icon . '</button>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染主题切换器（开关风格）
     *
     * @param array $options
     * @return string
     */
    public function renderToggle($options = [])
    {
        $currentTheme = $this->getCurrentTheme();
        
        $defaultOptions = [
            'class' => 'form-check form-switch',
            'input_class' => 'form-check-input',
            'label_class' => 'form-check-label',
            'name' => 'theme',
            'id' => 'theme-toggle',
            'form' => 'theme-form',
            'container_class' => 'mb-3',
            'light_label' => Translator::get('app.light'),
            'dark_label' => Translator::get('app.dark'),
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('theme/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '">';
        
        $checked = $currentTheme === 'dark' ? ' checked' : '';
        $value = $currentTheme === 'dark' ? 'light' : 'dark';
        
        $html .= '<input type="checkbox" class="' . $options['input_class'] . '" id="' . $options['id'] . '" name="' . $options['name'] . '" value="' . $value . '"' . $checked . ' onchange="this.form.submit()">';
        $html .= '<label class="' . $options['label_class'] . '" for="' . $options['id'] . '">' . ($currentTheme === 'dark' ? $options['dark_label'] : $options['light_label']) . '</label>';
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }
}
