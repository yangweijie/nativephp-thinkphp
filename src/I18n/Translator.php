<?php

namespace Native\ThinkPHP\I18n;

use think\App;
use think\facade\Lang as ThinkLang;
use Native\ThinkPHP\Facades\Settings;

class Translator
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 当前语言
     *
     * @var string
     */
    protected $locale;

    /**
     * 默认语言
     *
     * @var string
     */
    protected $defaultLocale = 'zh-cn';

    /**
     * 可用语言列表
     *
     * @var array
     */
    protected $availableLocales = ['zh-cn', 'en-us'];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->locale = Settings::get('app.locale', $this->defaultLocale);

        // 加载语言包
        $this->loadLanguageFiles();
    }

    /**
     * 加载语言文件
     *
     * @return void
     */
    protected function loadLanguageFiles()
    {
        // 设置 ThinkPHP 的语言
        ThinkLang::setLangSet($this->locale);

        // 加载 NativePHP 的语言文件
        $langPath = __DIR__ . '/../../resources/lang';

        if (is_dir($langPath)) {
            ThinkLang::load($langPath . '/' . $this->locale . '.php');
        }
    }

    /**
     * 获取翻译
     *
     * @param string $key
     * @param array $replace
     * @param string|null $locale
     * @return string
     */
    public function get($key, array $replace = [], $locale = null)
    {
        $locale = $locale ?: $this->locale;

        // 使用 ThinkPHP 的语言功能
        return ThinkLang::get($key, $replace, $locale);
    }

    /**
     * 设置当前语言
     *
     * @param string $locale
     * @return void
     */
    public function setLocale($locale)
    {
        if (!in_array($locale, $this->availableLocales)) {
            $locale = $this->defaultLocale;
        }

        $this->locale = $locale;
        ThinkLang::setLangSet($locale);
        Settings::set('app.locale', $locale);

        // 重新加载语言文件
        $this->loadLanguageFiles();
    }

    /**
     * 获取当前语言
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * 获取默认语言
     *
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     * 设置默认语言
     *
     * @param string $locale
     * @return void
     */
    public function setDefaultLocale($locale)
    {
        $this->defaultLocale = $locale;

        // 如果当前语言是默认语言，则更新当前语言
        if ($this->locale === $this->defaultLocale) {
            $this->setLocale($locale);
        }
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
     * 检查语言是否可用
     *
     * @param string $locale
     * @return bool
     */
    public function hasLocale($locale)
    {
        return in_array($locale, $this->availableLocales);
    }

    /**
     * 从系统获取语言
     *
     * @return string
     */
    public function getLocaleFromSystem()
    {
        $locale = $this->defaultLocale;

        // 获取系统语言
        /** @phpstan-ignore-next-line */
        $systemLocale = setlocale(LC_ALL, 0);

        // 解析系统语言
        if (preg_match('/^([a-z]{2})[-_]([A-Z]{2})/', $systemLocale, $matches)) {
            $lang = strtolower($matches[1]);
            $country = strtolower($matches[2]);
            $localeCode = $lang . '-' . $country;

            if ($this->hasLocale($localeCode)) {
                $locale = $localeCode;
            }
        }

        return $locale;
    }

    /**
     * 从浏览器获取语言
     *
     * @return string
     */
    public function getLocaleFromBrowser()
    {
        $locale = $this->defaultLocale;

        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLocales = explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);

            foreach ($browserLocales as $browserLocale) {
                $browserLocale = substr($browserLocale, 0, 2);

                foreach ($this->availableLocales as $availableLocale) {
                    if (strpos($availableLocale, $browserLocale) === 0) {
                        $locale = $availableLocale;
                        break 2;
                    }
                }
            }
        }

        return $locale;
    }

    /**
     * 自动检测语言
     *
     * @return string
     */
    public function detectLocale()
    {
        // 首先尝试从设置中获取
        $locale = Settings::get('app.locale');

        if (!$locale || !$this->hasLocale($locale)) {
            // 然后尝试从系统获取
            $locale = $this->getLocaleFromSystem();

            if (!$this->hasLocale($locale)) {
                // 最后尝试从浏览器获取
                $locale = $this->getLocaleFromBrowser();

                if (!$this->hasLocale($locale)) {
                    // 如果都失败了，使用默认语言
                    $locale = $this->defaultLocale;
                }
            }
        }

        return $locale;
    }

    /**
     * 加载语言包
     *
     * @param string $path 语言包路径
     * @return void
     */
    public function loadTranslations($path)
    {
        if (!is_dir($path)) {
            return;
        }

        // 加载当前语言的语言包
        $localePath = $path . DIRECTORY_SEPARATOR . $this->locale . '.php';

        if (file_exists($localePath)) {
            ThinkLang::load($localePath);
        }

        // 加载子目录中的语言包
        $dirs = glob($path . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $localePath = $dir . DIRECTORY_SEPARATOR . $this->locale . '.php';

            if (file_exists($localePath)) {
                ThinkLang::load($localePath);
            }
        }
    }
}
