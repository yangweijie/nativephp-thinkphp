<?php

namespace Native\ThinkPHP\Plugins;

use Native\ThinkPHP\Facades\Settings;
use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\I18n;

class I18nPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'i18n';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '国际化插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'window.create' => [$this, 'onWindowCreate'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 设置默认语言
        $this->setDefaultLocale();

        // 加载语言包
        $this->loadTranslations();
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 检测语言
        $locale = I18n::detectLocale();

        // 设置当前语言
        I18n::setLocale($locale);

        // 记录语言设置
        \Native\ThinkPHP\Facades\Logger::info('Locale set', [
            'locale' => $locale,
        ]);
    }

    /**
     * 窗口创建事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowCreate(array $window): void
    {
        // 设置窗口语言
        $locale = I18n::getLocale();
        /** @phpstan-ignore-next-line */
        \Native\ThinkPHP\Facades\Window::setLocale($window['id'], $locale);
    }

    /**
     * 设置默认语言
     *
     * @return void
     */
    protected function setDefaultLocale(): void
    {
        // 获取配置
        $config = config('lang');

        // 设置默认语言
        $defaultLocale = $config['default_lang'] ?? 'zh-cn';
        I18n::setDefaultLocale($defaultLocale);
    }

    /**
     * 加载语言包
     *
     * @return void
     */
    protected function loadTranslations(): void
    {
        // 获取语言目录
        $langPath = app()->getRootPath() . 'lang';

        // 加载语言包
        I18n::loadTranslations($langPath);
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 清除语言设置
        Settings::set('locale', null);
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}