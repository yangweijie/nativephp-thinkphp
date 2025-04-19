<?php

namespace Native\ThinkPHP\Components;

use think\App;
use Native\ThinkPHP\Facades\Translator;
use Native\ThinkPHP\Facades\Settings;

class LanguageSwitcher
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 可用语言列表
     *
     * @var array
     */
    protected $availableLocales;

    /**
     * 语言名称映射
     *
     * @var array
     */
    protected $localeNames = [
        'zh-cn' => '简体中文',
        'en-us' => 'English (US)',
        'ja-jp' => '日本語',
        'ko-kr' => '한국어',
        'fr-fr' => 'Français',
        'de-de' => 'Deutsch',
        'es-es' => 'Español',
        'it-it' => 'Italiano',
        'pt-br' => 'Português (Brasil)',
        'ru-ru' => 'Русский',
    ];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->availableLocales = Translator::getAvailableLocales();
    }

    /**
     * 获取当前语言
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return Translator::getLocale();
    }

    /**
     * 获取可用语言列表
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return $this->availableLocales;
    }

    /**
     * 获取语言名称
     *
     * @param string $locale
     * @return string
     */
    public function getLocaleName($locale)
    {
        return $this->localeNames[$locale] ?? $locale;
    }

    /**
     * 获取所有语言名称
     *
     * @return array
     */
    public function getLocaleNames()
    {
        $names = [];
        
        foreach ($this->availableLocales as $locale) {
            $names[$locale] = $this->getLocaleName($locale);
        }
        
        return $names;
    }

    /**
     * 切换语言
     *
     * @param string $locale
     * @return bool
     */
    public function switchLocale($locale)
    {
        if (!in_array($locale, $this->availableLocales)) {
            return false;
        }
        
        Translator::setLocale($locale);
        Settings::set('app.locale', $locale);
        
        return true;
    }

    /**
     * 添加语言
     *
     * @param string $locale
     * @param string $name
     * @return bool
     */
    public function addLocale($locale, $name = null)
    {
        if (in_array($locale, $this->availableLocales)) {
            return false;
        }
        
        $this->availableLocales[] = $locale;
        
        if ($name) {
            $this->localeNames[$locale] = $name;
        }
        
        return true;
    }

    /**
     * 移除语言
     *
     * @param string $locale
     * @return bool
     */
    public function removeLocale($locale)
    {
        if (!in_array($locale, $this->availableLocales) || $locale === Translator::getDefaultLocale()) {
            return false;
        }
        
        $key = array_search($locale, $this->availableLocales);
        unset($this->availableLocales[$key]);
        $this->availableLocales = array_values($this->availableLocales);
        
        if (isset($this->localeNames[$locale])) {
            unset($this->localeNames[$locale]);
        }
        
        return true;
    }

    /**
     * 渲染语言切换器
     *
     * @param array $options
     * @return string
     */
    public function render($options = [])
    {
        $currentLocale = $this->getCurrentLocale();
        $localeNames = $this->getLocaleNames();
        
        $defaultOptions = [
            'class' => 'language-switcher',
            'name' => 'locale',
            'id' => 'locale-switcher',
            'onchange' => 'this.form.submit()',
            'form' => 'language-form',
            'label' => Translator::get('app.language'),
            'show_label' => true,
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('language/switch') . '">';
        }
        
        if ($options['show_label']) {
            $html .= '<label for="' . $options['id'] . '">' . $options['label'] . '</label>';
        }
        
        $html .= '<select name="' . $options['name'] . '" id="' . $options['id'] . '" class="' . $options['class'] . '" onchange="' . $options['onchange'] . '">';
        
        foreach ($localeNames as $locale => $name) {
            $selected = $locale === $currentLocale ? ' selected' : '';
            $html .= '<option value="' . $locale . '"' . $selected . '>' . $name . '</option>';
        }
        
        $html .= '</select>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染语言切换器（Bootstrap 风格）
     *
     * @param array $options
     * @return string
     */
    public function renderBootstrap($options = [])
    {
        $currentLocale = $this->getCurrentLocale();
        $localeNames = $this->getLocaleNames();
        
        $defaultOptions = [
            'class' => 'form-select',
            'name' => 'locale',
            'id' => 'locale-switcher',
            'onchange' => 'this.form.submit()',
            'form' => 'language-form',
            'label' => Translator::get('app.language'),
            'show_label' => true,
            'label_class' => 'form-label',
            'container_class' => 'mb-3',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('language/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        
        if ($options['show_label']) {
            $html .= '<label for="' . $options['id'] . '" class="' . $options['label_class'] . '">' . $options['label'] . '</label>';
        }
        
        $html .= '<select name="' . $options['name'] . '" id="' . $options['id'] . '" class="' . $options['class'] . '" onchange="' . $options['onchange'] . '">';
        
        foreach ($localeNames as $locale => $name) {
            $selected = $locale === $currentLocale ? ' selected' : '';
            $html .= '<option value="' . $locale . '"' . $selected . '>' . $name . '</option>';
        }
        
        $html .= '</select>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染语言切换器（按钮组风格）
     *
     * @param array $options
     * @return string
     */
    public function renderButtons($options = [])
    {
        $currentLocale = $this->getCurrentLocale();
        $localeNames = $this->getLocaleNames();
        
        $defaultOptions = [
            'class' => 'btn-group',
            'button_class' => 'btn btn-outline-primary',
            'active_class' => 'active',
            'name' => 'locale',
            'form' => 'language-form',
            'container_class' => 'mb-3',
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('language/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '" role="group" aria-label="Language Switcher">';
        
        foreach ($localeNames as $locale => $name) {
            $active = $locale === $currentLocale ? ' ' . $options['active_class'] : '';
            $html .= '<button type="submit" name="' . $options['name'] . '" value="' . $locale . '" class="' . $options['button_class'] . $active . '">' . $name . '</button>';
        }
        
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }

    /**
     * 渲染语言切换器（下拉菜单风格）
     *
     * @param array $options
     * @return string
     */
    public function renderDropdown($options = [])
    {
        $currentLocale = $this->getCurrentLocale();
        $localeNames = $this->getLocaleNames();
        
        $defaultOptions = [
            'class' => 'dropdown',
            'button_class' => 'btn btn-outline-primary dropdown-toggle',
            'menu_class' => 'dropdown-menu',
            'item_class' => 'dropdown-item',
            'active_class' => 'active',
            'name' => 'locale',
            'form' => 'language-form',
            'container_class' => 'mb-3',
            'button_text' => Translator::get('app.language'),
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        $html = '';
        
        if ($options['form']) {
            $html .= '<form id="' . $options['form'] . '" method="post" action="' . url('language/switch') . '">';
        }
        
        $html .= '<div class="' . $options['container_class'] . '">';
        $html .= '<div class="' . $options['class'] . '">';
        $html .= '<button class="' . $options['button_class'] . '" type="button" id="languageDropdown" data-bs-toggle="dropdown" aria-expanded="false">';
        $html .= $options['button_text'] . ': ' . $localeNames[$currentLocale];
        $html .= '</button>';
        $html .= '<ul class="' . $options['menu_class'] . '" aria-labelledby="languageDropdown">';
        
        foreach ($localeNames as $locale => $name) {
            $active = $locale === $currentLocale ? ' ' . $options['active_class'] : '';
            $html .= '<li><button type="submit" name="' . $options['name'] . '" value="' . $locale . '" class="' . $options['item_class'] . $active . '">' . $name . '</button></li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($options['form']) {
            $html .= '</form>';
        }
        
        return $html;
    }
}
