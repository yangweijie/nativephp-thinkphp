<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Logger;

class WindowPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'window';

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
    protected $description = '窗口管理插件';

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
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Window plugin initialized');

        // 监听窗口事件
        $this->app->event->listen('native.window.create', function ($event) {
            $this->handleWindowCreate($event);
        });

        $this->app->event->listen('native.window.close', function ($event) {
            $this->handleWindowClose($event);
        });

        $this->app->event->listen('native.window.focus', function ($event) {
            $this->handleWindowFocus($event);
        });

        $this->app->event->listen('native.window.blur', function ($event) {
            $this->handleWindowBlur($event);
        });

        $this->app->event->listen('native.window.move', function ($event) {
            $this->handleWindowMove($event);
        });

        $this->app->event->listen('native.window.resize', function ($event) {
            $this->handleWindowResize($event);
        });

        $this->app->event->listen('native.window.maximize', function ($event) {
            $this->handleWindowMaximize($event);
        });

        $this->app->event->listen('native.window.minimize', function ($event) {
            $this->handleWindowMinimize($event);
        });

        $this->app->event->listen('native.window.restore', function ($event) {
            $this->handleWindowRestore($event);
        });

        $this->app->event->listen('native.window.enter-fullscreen', function ($event) {
            $this->handleWindowEnterFullscreen($event);
        });

        $this->app->event->listen('native.window.leave-fullscreen', function ($event) {
            $this->handleWindowLeaveFullscreen($event);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Window plugin started');

        // 创建主窗口
        $this->createMainWindow();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 关闭所有窗口
        Window::closeAll();

        // 记录插件卸载
        Logger::info('Window plugin quit');
    }

    /**
     * 创建主窗口
     *
     * @return void
     */
    protected function createMainWindow(): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了自动创建主窗口，则创建
        if (isset($config['auto_create_main_window']) && $config['auto_create_main_window']) {
            // 获取主窗口配置
            $mainWindowConfig = $config['main_window'] ?? [];

            // 设置默认值
            $url = $mainWindowConfig['url'] ?? '/';
            $title = $mainWindowConfig['title'] ?? config('native.name', 'NativePHP');
            $width = $mainWindowConfig['width'] ?? 800;
            $height = $mainWindowConfig['height'] ?? 600;
            $resizable = $mainWindowConfig['resizable'] ?? true;
            $minimizable = $mainWindowConfig['minimizable'] ?? true;
            $maximizable = $mainWindowConfig['maximizable'] ?? true;
            $closable = $mainWindowConfig['closable'] ?? true;
            $alwaysOnTop = $mainWindowConfig['alwaysOnTop'] ?? false;
            $fullscreen = $mainWindowConfig['fullscreen'] ?? false;
            $x = $mainWindowConfig['x'] ?? null;
            $y = $mainWindowConfig['y'] ?? null;
            $center = $mainWindowConfig['center'] ?? true;
            $frame = $mainWindowConfig['frame'] ?? true;
            $transparent = $mainWindowConfig['transparent'] ?? false;
            $backgroundColor = $mainWindowConfig['backgroundColor'] ?? null;
            $icon = $mainWindowConfig['icon'] ?? null;

            // 创建窗口选项
            $options = [
                'title' => $title,
                'width' => $width,
                'height' => $height,
                'resizable' => $resizable,
                'minimizable' => $minimizable,
                'maximizable' => $maximizable,
                'closable' => $closable,
                'alwaysOnTop' => $alwaysOnTop,
                'fullscreen' => $fullscreen,
                'center' => $center,
                'frame' => $frame,
                'transparent' => $transparent,
            ];

            // 设置窗口位置
            if ($x !== null && $y !== null) {
                $options['x'] = $x;
                $options['y'] = $y;
            }

            // 设置窗口背景色
            if ($backgroundColor !== null) {
                $options['backgroundColor'] = $backgroundColor;
            }

            // 打开主窗口
            $windowId = Window::open($url, $options);

            // 设置窗口图标
            if ($icon !== null) {
                /** @phpstan-ignore-next-line */
                Window::setIcon($icon, $windowId);
            }

            // 记录主窗口创建
            Logger::info('Main window created', [
                'id' => $windowId,
                'title' => $title,
                'url' => $url,
            ]);
        }
    }

    /**
     * 处理窗口创建事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowCreate(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window created', [
                'id' => $event['id'] ?? null,
                'title' => $event['title'] ?? null,
                'url' => $event['url'] ?? null,
            ]);
        }

        // 如果配置了窗口创建回调，则执行
        if (isset($config['on_create']) && is_callable($config['on_create'])) {
            call_user_func($config['on_create'], $event);
        }

        // 触发窗口创建事件
        $this->app->event->trigger('window.create', $event);
    }

    /**
     * 处理窗口关闭事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowClose(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window closed', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口关闭回调，则执行
        if (isset($config['on_close']) && is_callable($config['on_close'])) {
            call_user_func($config['on_close'], $event);
        }

        // 触发窗口关闭事件
        $this->app->event->trigger('window.close', $event);

        // 检查是否所有窗口都已关闭
        $windows = Window::all();
        if (empty($windows)) {
            // 触发所有窗口关闭事件
            $this->app->event->trigger('window.all_closed');
        }
    }

    /**
     * 处理窗口聚焦事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowFocus(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window focused', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口聚焦回调，则执行
        if (isset($config['on_focus']) && is_callable($config['on_focus'])) {
            call_user_func($config['on_focus'], $event);
        }
    }

    /**
     * 处理窗口失去焦点事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowBlur(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window blurred', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口失去焦点回调，则执行
        if (isset($config['on_blur']) && is_callable($config['on_blur'])) {
            call_user_func($config['on_blur'], $event);
        }
    }

    /**
     * 处理窗口移动事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowMove(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window moved', [
                'id' => $event['id'] ?? null,
                'x' => $event['x'] ?? null,
                'y' => $event['y'] ?? null,
            ]);
        }

        // 如果配置了窗口移动回调，则执行
        if (isset($config['on_move']) && is_callable($config['on_move'])) {
            call_user_func($config['on_move'], $event);
        }
    }

    /**
     * 处理窗口调整大小事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowResize(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window resized', [
                'id' => $event['id'] ?? null,
                'width' => $event['width'] ?? null,
                'height' => $event['height'] ?? null,
            ]);
        }

        // 如果配置了窗口调整大小回调，则执行
        if (isset($config['on_resize']) && is_callable($config['on_resize'])) {
            call_user_func($config['on_resize'], $event);
        }
    }

    /**
     * 处理窗口最大化事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowMaximize(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window maximized', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口最大化回调，则执行
        if (isset($config['on_maximize']) && is_callable($config['on_maximize'])) {
            call_user_func($config['on_maximize'], $event);
        }
    }

    /**
     * 处理窗口最小化事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowMinimize(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window minimized', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口最小化回调，则执行
        if (isset($config['on_minimize']) && is_callable($config['on_minimize'])) {
            call_user_func($config['on_minimize'], $event);
        }
    }

    /**
     * 处理窗口恢复事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowRestore(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window restored', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口恢复回调，则执行
        if (isset($config['on_restore']) && is_callable($config['on_restore'])) {
            call_user_func($config['on_restore'], $event);
        }
    }

    /**
     * 处理窗口进入全屏事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowEnterFullscreen(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window entered fullscreen', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口进入全屏回调，则执行
        if (isset($config['on_enter_fullscreen']) && is_callable($config['on_enter_fullscreen'])) {
            call_user_func($config['on_enter_fullscreen'], $event);
        }
    }

    /**
     * 处理窗口退出全屏事件
     *
     * @param array $event
     * @return void
     */
    protected function handleWindowLeaveFullscreen(array $event): void
    {
        // 获取配置
        $config = config('native.window', []);

        // 如果配置了记录窗口事件，则记录
        if (isset($config['log_events']) && $config['log_events']) {
            Logger::info('Window left fullscreen', [
                'id' => $event['id'] ?? null,
            ]);
        }

        // 如果配置了窗口退出全屏回调，则执行
        if (isset($config['on_leave_fullscreen']) && is_callable($config['on_leave_fullscreen'])) {
            call_user_func($config['on_leave_fullscreen'], $event);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 关闭所有窗口
        Window::closeAll();

        // 记录插件卸载
        Logger::info('Window plugin unloaded');
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
