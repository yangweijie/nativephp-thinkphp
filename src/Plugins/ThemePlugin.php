<?php

namespace Native\ThinkPHP\Plugins;

use Native\ThinkPHP\Facades\Settings;
use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Theme;
use Native\ThinkPHP\Facades\Window;

class ThemePlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'theme';

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
    protected $description = '主题插件';

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
        // 设置默认主题
        $this->setDefaultTheme();

        // 应用当前主题
        $this->applyCurrentTheme();

        // 监听系统主题变化
        if (Theme::getCurrent() === 'system') {
            Theme::watchSystemTheme();
        }
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 检测主题
        $theme = Theme::getCurrent();

        // 记录主题设置
        \Native\ThinkPHP\Facades\Logger::info('Theme set', [
            'theme' => $theme,
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
        // 设置窗口主题
        $theme = Theme::getCurrent();
        Window::setTheme($window['id'], $theme);
    }

    /**
     * 设置默认主题
     *
     * @return void
     */
    protected function setDefaultTheme(): void
    {
        // 获取配置
        $config = config('native.theme');

        // 设置默认主题
        $defaultTheme = $config['default'] ?? 'light';
        Theme::setDefault($defaultTheme);
    }

    /**
     * 应用当前主题
     *
     * @return void
     */
    protected function applyCurrentTheme(): void
    {
        // 获取当前主题
        $theme = Theme::getCurrent();

        // 如果是系统主题，则检测系统主题
        if ($theme === 'system') {
            $systemTheme = Theme::detectSystemTheme();
            Theme::apply($systemTheme);
        } else {
            // 应用主题
            Theme::apply($theme);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 清除主题设置
        Settings::set('app.theme', null);
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
