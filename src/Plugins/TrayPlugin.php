<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Settings;

class TrayPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'tray';

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
    protected $description = '系统托盘插件';

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
            'window.all_closed' => [$this, 'onWindowAllClosed'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 加载配置的托盘设置
        $this->loadTrayConfig();

        // 监听托盘事件
        $this->app->event->listen('native.tray.click', function ($event) {
            $this->handleTrayClick($event);
        });

        $this->app->event->listen('native.tray.double-click', function ($event) {
            $this->handleTrayDoubleClick($event);
        });

        $this->app->event->listen('native.tray.right-click', function ($event) {
            $this->handleTrayRightClick($event);
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
        Logger::info('Tray plugin started');

        // 如果配置了自动显示托盘图标，则显示
        $config = config('native.tray', []);
        if (isset($config['auto_show']) && $config['auto_show']) {
            $this->showTray();
        }
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 销毁托盘图标
        Tray::destroy();
    }

    /**
     * 所有窗口关闭事件处理
     *
     * @return void
     */
    public function onWindowAllClosed(): void
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果配置了窗口全部关闭时保持应用运行，则不退出应用
        if (isset($config['keep_running_when_all_windows_closed']) && $config['keep_running_when_all_windows_closed']) {
            // 不做任何处理，保持应用运行
            Logger::info('All windows closed, but keeping app running due to tray configuration');
        } else {
            // 否则退出应用
            $this->app->event->trigger('app.quit');
        }
    }

    /**
     * 加载托盘配置
     *
     * @return void
     */
    protected function loadTrayConfig(): void
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果没有配置，则使用默认配置
        if (empty($config)) {
            return;
        }

        // 设置托盘图标
        if (isset($config['icon'])) {
            Tray::setIcon($config['icon']);
        }

        // 设置托盘提示文本
        if (isset($config['tooltip'])) {
            Tray::setTooltip($config['tooltip']);
        }

        // 设置托盘菜单
        if (isset($config['menu']) && is_callable($config['menu'])) {
            Tray::setMenu($config['menu']);
        } elseif (isset($config['menu_items']) && is_array($config['menu_items'])) {
            Tray::setMenu(function ($menu) use ($config) {
                foreach ($config['menu_items'] as $item) {
                    if (isset($item['type']) && $item['type'] === 'separator') {
                        $menu->separator();
                    } elseif (isset($item['submenu'])) {
                        $menu->submenu($item['label'], function ($submenu) use ($item) {
                            foreach ($item['submenu'] as $subItem) {
                                if (isset($subItem['type']) && $subItem['type'] === 'separator') {
                                    $submenu->separator();
                                } else {
                                    $submenu->add($subItem['label'], $subItem['action']);
                                }
                            }
                        });
                    } else {
                        $menu->add($item['label'], $item['action']);
                    }
                }
            });
        }

        // 注册点击事件
        if (isset($config['on_click']) && is_callable($config['on_click'])) {
            Tray::onClick($config['on_click']);
        }

        // 注册双击事件
        if (isset($config['on_double_click']) && is_callable($config['on_double_click'])) {
            Tray::onDoubleClick($config['on_double_click']);
        }

        // 注册右键点击事件
        if (isset($config['on_right_click']) && is_callable($config['on_right_click'])) {
            Tray::onRightClick($config['on_right_click']);
        }
    }

    /**
     * 显示托盘图标
     *
     * @return bool
     */
    protected function showTray(): bool
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果没有设置图标，则使用默认图标
        if (!Tray::getIconPath() && isset($config['icon'])) {
            Tray::setIcon($config['icon']);
        } elseif (!Tray::getIconPath()) {
            // 使用默认图标
            $defaultIcon = public_path() . 'favicon.ico';
            if (file_exists($defaultIcon)) {
                Tray::setIcon($defaultIcon);
            } else {
                Logger::warning('No tray icon specified and default icon not found');
                return false;
            }
        }

        // 如果没有设置提示文本，则使用应用名称
        if (!Tray::getTooltip() && isset($config['tooltip'])) {
            Tray::setTooltip($config['tooltip']);
        } elseif (!Tray::getTooltip()) {
            Tray::setTooltip(config('native.name', 'NativePHP'));
        }

        // 如果没有设置菜单，则使用默认菜单
        if (empty(Tray::getMenuItems()) && !isset($config['menu']) && !isset($config['menu_items'])) {
            Tray::setMenu(function ($menu) {
                $menu->add('显示', function () {
                    $windows = Window::all();
                    if (empty($windows)) {
                        // 如果没有窗口，则打开主窗口
                        Window::open('/', [
                            'title' => config('native.name', 'NativePHP'),
                            'width' => 800,
                            'height' => 600,
                        ]);
                    } else {
                        // 显示所有窗口
                        foreach ($windows as $window) {
                            Window::show($window['id']);
                        }
                    }
                });

                $menu->add('隐藏', function () {
                    $windows = Window::all();
                    foreach ($windows as $window) {
                        Window::hide($window['id']);
                    }
                });

                $menu->separator();

                $menu->add('退出', function () {
                    $this->app->event->trigger('app.quit');
                });
            });
        }

        // 显示托盘图标
        return Tray::show();
    }

    /**
     * 处理托盘点击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleTrayClick(array $event): void
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果配置了点击动作，则执行
        if (isset($config['click_action'])) {
            $this->handleTrayAction($config['click_action']);
        }
    }

    /**
     * 处理托盘双击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleTrayDoubleClick(array $event): void
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果配置了双击动作，则执行
        if (isset($config['double_click_action'])) {
            $this->handleTrayAction($config['double_click_action']);
        }
    }

    /**
     * 处理托盘右键点击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleTrayRightClick(array $event): void
    {
        // 获取配置
        $config = config('native.tray', []);

        // 如果配置了右键点击动作，则执行
        if (isset($config['right_click_action'])) {
            $this->handleTrayAction($config['right_click_action']);
        }
    }

    /**
     * 处理托盘动作
     *
     * @param string|callable $action
     * @return void
     */
    protected function handleTrayAction($action): void
    {
        if (is_callable($action)) {
            // 如果是回调函数，直接执行
            call_user_func($action);
        } elseif (is_string($action)) {
            // 如果是字符串，根据预定义的动作执行
            switch ($action) {
                case 'show_window':
                    $windows = Window::all();
                    if (empty($windows)) {
                        // 如果没有窗口，则打开主窗口
                        Window::open('/', [
                            'title' => config('native.name', 'NativePHP'),
                            'width' => 800,
                            'height' => 600,
                        ]);
                    } else {
                        // 显示所有窗口
                        foreach ($windows as $window) {
                            Window::show($window['id']);
                        }
                    }
                    break;

                case 'hide_window':
                    $windows = Window::all();
                    foreach ($windows as $window) {
                        Window::hide($window['id']);
                    }
                    break;

                case 'toggle_window':
                    $windows = Window::all();
                    $allHidden = true;

                    foreach ($windows as $window) {
                        if ($window['visible']) {
                            $allHidden = false;
                            break;
                        }
                    }

                    if ($allHidden || empty($windows)) {
                        // 如果所有窗口都隐藏或没有窗口，则显示窗口
                        if (empty($windows)) {
                            // 如果没有窗口，则打开主窗口
                            Window::open('/', [
                                'title' => config('native.name', 'NativePHP'),
                                'width' => 800,
                                'height' => 600,
                            ]);
                        } else {
                            // 显示所有窗口
                            foreach ($windows as $window) {
                                Window::show($window['id']);
                            }
                        }
                    } else {
                        // 隐藏所有窗口
                        foreach ($windows as $window) {
                            Window::hide($window['id']);
                        }
                    }
                    break;

                case 'quit':
                    $this->app->event->trigger('app.quit');
                    break;

                default:
                    // 尝试解析为路由或控制器方法
                    if (strpos($action, '@') !== false) {
                        // 控制器方法
                        list($controller, $method) = explode('@', $action);
                        $controller = $this->app->make($controller);
                        call_user_func([$controller, $method]);
                    } else {
                        // 路由
                        $this->app->make('think\Route')->url($action);
                    }
                    break;
            }
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 销毁托盘图标
        Tray::destroy();
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
