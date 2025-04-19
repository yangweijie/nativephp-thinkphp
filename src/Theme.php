<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Window;

class Theme
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 当前主题
     *
     * @var string
     */
    protected $current;

    /**
     * 默认主题
     *
     * @var string
     */
    protected $default = 'light';

    /**
     * 可用主题列表
     *
     * @var array
     */
    protected $available = ['light', 'dark', 'system'];

    /**
     * 主题配置
     *
     * @var array
     */
    protected $config = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->loadConfig();
        $this->current = Settings::get('app.theme', $this->default);
    }

    /**
     * 加载主题配置
     *
     * @return void
     */
    protected function loadConfig()
    {
        $this->config = $this->app->config->get('native.theme', []);

        if (isset($this->config['default'])) {
            $this->default = $this->config['default'];
        }

        if (isset($this->config['available']) && is_array($this->config['available'])) {
            $this->available = $this->config['available'];
        }
    }

    /**
     * 获取当前主题
     *
     * @return string
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * 设置当前主题
     *
     * @param string $theme
     * @return bool
     */
    public function setCurrent($theme)
    {
        if (!in_array($theme, $this->available)) {
            return false;
        }

        $this->current = $theme;
        Settings::set('app.theme', $theme);

        return true;
    }

    /**
     * 获取默认主题
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * 设置默认主题
     *
     * @param string $theme
     * @return bool
     */
    public function setDefault($theme)
    {
        if (!in_array($theme, $this->available)) {
            return false;
        }

        $this->default = $theme;
        $this->app->config->set(['theme' => ['default' => $theme]], 'native');

        return true;
    }

    /**
     * 获取可用主题列表
     *
     * @return array
     */
    public function getAvailable()
    {
        return $this->available;
    }

    /**
     * 添加主题
     *
     * @param string $theme
     * @param array $options
     * @return bool
     */
    public function add($theme, array $options = [])
    {
        if (in_array($theme, $this->available)) {
            return false;
        }

        $this->available[] = $theme;

        // 更新配置
        $available = $this->app->config->get('native.theme.available', []);
        $available[] = $theme;
        $this->app->config->set(['theme' => ['available' => $available]], 'native');

        // 保存主题选项
        if (!empty($options)) {
            $this->saveThemeOptions($theme, $options);
        }

        return true;
    }

    /**
     * 移除主题
     *
     * @param string $theme
     * @return bool
     */
    public function remove($theme)
    {
        if (!in_array($theme, $this->available) || $theme === $this->default) {
            return false;
        }

        $key = array_search($theme, $this->available);
        unset($this->available[$key]);
        $this->available = array_values($this->available);

        // 更新配置
        $available = $this->app->config->get('native.theme.available', []);
        $key = array_search($theme, $available);
        if ($key !== false) {
            unset($available[$key]);
            $available = array_values($available);
            $this->app->config->set(['theme' => ['available' => $available]], 'native');
        }

        // 如果当前主题被移除，切换到默认主题
        if ($this->current === $theme) {
            $this->setCurrent($this->default);
        }

        return true;
    }

    /**
     * 获取主题选项
     *
     * @param string $theme
     * @return array
     */
    public function getOptions($theme = null)
    {
        $theme = $theme ?: $this->current;

        return $this->app->config->get('native.theme.options.' . $theme, []);
    }

    /**
     * 保存主题选项
     *
     * @param string $theme
     * @param array $options
     * @return bool
     */
    public function saveOptions($theme, array $options)
    {
        if (!in_array($theme, $this->available)) {
            return false;
        }

        $this->app->config->set(['theme' => ['options' => [$theme => $options]]], 'native');

        return true;
    }

    /**
     * 应用主题
     *
     * @param string $theme
     * @return bool
     */
    public function apply($theme = null)
    {
        $theme = $theme ?: $this->current;

        if (!in_array($theme, $this->available)) {
            return false;
        }

        // 获取主题选项
        $options = $this->getOptions($theme);

        // 应用主题到窗口
        if (isset($options['window'])) {
            $this->applyWindowTheme($options['window']);
        }

        // 应用主题到 CSS
        if (isset($options['css'])) {
            $this->applyCssTheme($options['css'], $theme);
        }

        // 应用主题到 JavaScript
        if (isset($options['js'])) {
            $this->applyJsTheme($options['js'], $theme);
        }

        return true;
    }

    /**
     * 应用窗口主题
     *
     * @param array $options
     * @return void
     */
    protected function applyWindowTheme(array $options)
    {
        if (isset($options['backgroundColor'])) {
            /** @phpstan-ignore-next-line */
            Window::setBackgroundColor($options['backgroundColor']);
        }

        if (isset($options['vibrancy'])) {
            /** @phpstan-ignore-next-line */
            Window::setVibrancy($options['vibrancy']);
        }

        if (isset($options['titleBarStyle'])) {
            /** @phpstan-ignore-next-line */
            Window::setTitleBarStyle($options['titleBarStyle']);
        }

        if (isset($options['trafficLightPosition'])) {
            /** @phpstan-ignore-next-line */
            Window::setTrafficLightPosition($options['trafficLightPosition']);
        }
    }

    /**
     * 应用 CSS 主题
     *
     * @param array $options
     * @param string $theme
     * @return void
     */
    protected function applyCssTheme(array $options, $theme)
    {
        // 添加主题类到 body
        $js = "document.body.classList.remove('theme-light', 'theme-dark', 'theme-system');";
        $js .= "document.body.classList.add('theme-{$theme}');";

        // 添加 CSS 变量
        if (isset($options['variables']) && is_array($options['variables'])) {
            $js .= "const root = document.documentElement;";

            foreach ($options['variables'] as $name => $value) {
                $js .= "root.style.setProperty('--{$name}', '{$value}');";
            }
        }

        // 添加 CSS 文件
        if (isset($options['file'])) {
            $js .= "
                const themeLink = document.getElementById('theme-css');
                if (themeLink) {
                    themeLink.href = '{$options['file']}';
                } else {
                    const link = document.createElement('link');
                    link.id = 'theme-css';
                    link.rel = 'stylesheet';
                    link.href = '{$options['file']}';
                    document.head.appendChild(link);
                }
            ";
        }

        /** @phpstan-ignore-next-line */
        Window::executeJavaScript($js);
    }

    /**
     * 应用 JavaScript 主题
     *
     * @param array $options
     * @param string $theme
     * @return void
     */
    protected function applyJsTheme(array $options, $theme)
    {
        // 设置主题变量
        $js = "window.currentTheme = '{$theme}';";

        // 触发主题变更事件
        $js .= "
            const themeEvent = new CustomEvent('themechange', {
                detail: {
                    theme: '{$theme}',
                    options: " . json_encode($options) . "
                }
            });
            window.dispatchEvent(themeEvent);
        ";

        // 添加 JavaScript 文件
        if (isset($options['file'])) {
            $js .= "
                const themeScript = document.getElementById('theme-js');
                if (themeScript) {
                    themeScript.src = '{$options['file']}';
                } else {
                    const script = document.createElement('script');
                    script.id = 'theme-js';
                    script.src = '{$options['file']}';
                    document.body.appendChild(script);
                }
            ";
        }

        /** @phpstan-ignore-next-line */
        Window::executeJavaScript($js);
    }

    /**
     * 检测系统主题
     *
     * @return string
     */
    public function detectSystemTheme()
    {
        // 使用 JavaScript 检测系统主题
        $js = "
            const isDarkMode = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
            isDarkMode ? 'dark' : 'light';
        ";

        /** @phpstan-ignore-next-line */
        $result = Window::executeJavaScript($js);

        return $result === 'dark' ? 'dark' : 'light';
    }

    /**
     * 监听系统主题变化
     *
     * @return void
     */
    public function watchSystemTheme()
    {
        $js = "
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', event => {
                const newTheme = event.matches ? 'dark' : 'light';

                // 如果当前主题是系统主题，则应用新主题
                if (window.currentTheme === 'system') {
                    const themeEvent = new CustomEvent('themechange', {
                        detail: {
                            theme: newTheme,
                            systemTheme: true
                        }
                    });
                    window.dispatchEvent(themeEvent);
                }

                // 发送消息到后端
                fetch('/_native/theme/system-changed', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ theme: newTheme }),
                });
            });
        ";

        /** @phpstan-ignore-next-line */
        Window::executeJavaScript($js);
    }

    /**
     * 保存主题选项
     *
     * @param string $theme 主题名称
     * @param array $options 选项
     * @return bool
     */
    protected function saveThemeOptions($theme, array $options)
    {
        // 获取当前主题配置
        $config = $this->app->config->get('native.theme', []);

        // 更新主题选项
        if (!isset($config['options'])) {
            $config['options'] = [];
        }

        $config['options'][$theme] = $options;

        // 保存配置
        $this->app->config->set(['theme' => $config], 'native');

        return true;
    }

    /**
     * 获取主题名称
     *
     * @param string $theme
     * @return string
     */
    public function getName($theme = null)
    {
        $theme = $theme ?: $this->current;

        $names = [
            'light' => '浅色',
            'dark' => '深色',
            'system' => '跟随系统',
        ];

        return $names[$theme] ?? $theme;
    }

    /**
     * 获取所有主题名称
     *
     * @return array
     */
    public function getNames()
    {
        $names = [];

        foreach ($this->available as $theme) {
            $names[$theme] = $this->getName($theme);
        }

        return $names;
    }
}
