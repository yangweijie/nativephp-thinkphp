<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Keyboard;
use Native\ThinkPHP\Facades\Logger;

class KeyboardPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'keyboard';

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
    protected $description = '键盘插件';

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
        Logger::info('Keyboard plugin initialized');

        // 监听键盘事件
        $this->app->event->listen('native.keyboard.shortcut', function ($id) {
            $this->handleShortcutTriggered($id);
        });

        $this->app->event->listen('native.keyboard.global_shortcut', function ($id) {
            $this->handleGlobalShortcutTriggered($id);
        });

        $this->app->event->listen('native.keyboard.keydown', function ($event) {
            $this->handleKeyDown($event);
        });

        $this->app->event->listen('native.keyboard.keyup', function ($event) {
            $this->handleKeyUp($event);
        });

        $this->app->event->listen('native.keyboard.keypress', function ($event) {
            $this->handleKeyPress($event);
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
        Logger::info('Keyboard plugin started');

        // 注册配置的快捷键
        $this->registerConfiguredShortcuts();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 注销所有快捷键
        Keyboard::unregisterAll();
        Keyboard::unregisterAllGlobal();

        // 记录插件卸载
        Logger::info('Keyboard plugin quit');
    }

    /**
     * 注册配置的快捷键
     *
     * @return void
     */
    protected function registerConfiguredShortcuts(): void
    {
        // 获取配置
        $config = config('native.keyboard', []);

        // 注册应用快捷键
        if (isset($config['shortcuts']) && is_array($config['shortcuts'])) {
            foreach ($config['shortcuts'] as $accelerator => $callback) {
                if (is_callable($callback)) {
                    // 如果是回调函数，直接注册
                    Keyboard::register($accelerator, $callback);
                } elseif (is_string($callback)) {
                    // 如果是字符串，尝试解析为路由或控制器方法
                    $this->registerShortcutForAction($accelerator, $callback);
                } elseif (is_array($callback) && isset($callback['action'])) {
                    // 如果是数组，尝试解析为配置项
                    $this->registerShortcutFromConfig($accelerator, $callback);
                }
            }
        }

        // 注册全局快捷键
        if (isset($config['global_shortcuts']) && is_array($config['global_shortcuts'])) {
            foreach ($config['global_shortcuts'] as $accelerator => $callback) {
                if (is_callable($callback)) {
                    // 如果是回调函数，直接注册
                    Keyboard::registerGlobal($accelerator, $callback);
                } elseif (is_string($callback)) {
                    // 如果是字符串，尝试解析为路由或控制器方法
                    $this->registerGlobalShortcutForAction($accelerator, $callback);
                } elseif (is_array($callback) && isset($callback['action'])) {
                    // 如果是数组，尝试解析为配置项
                    $this->registerGlobalShortcutFromConfig($accelerator, $callback);
                }
            }
        }
    }

    /**
     * 为动作注册快捷键
     *
     * @param string $accelerator
     * @param string $action
     * @return string|null
     */
    protected function registerShortcutForAction(string $accelerator, string $action): ?string
    {
        // 创建回调函数
        $callback = function () use ($action) {
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
        };

        return Keyboard::register($accelerator, $callback);
    }

    /**
     * 从配置注册快捷键
     *
     * @param string $accelerator
     * @param array $config
     * @return string|null
     */
    protected function registerShortcutFromConfig(string $accelerator, array $config): ?string
    {
        $action = $config['action'];
        $enabled = $config['enabled'] ?? true;

        if (!$enabled) {
            return null;
        }

        if (is_callable($action)) {
            return Keyboard::register($accelerator, $action);
        } elseif (is_string($action)) {
            return $this->registerShortcutForAction($accelerator, $action);
        }

        return null;
    }

    /**
     * 为动作注册全局快捷键
     *
     * @param string $accelerator
     * @param string $action
     * @return string|null
     */
    protected function registerGlobalShortcutForAction(string $accelerator, string $action): ?string
    {
        // 创建回调函数
        $callback = function () use ($action) {
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
        };

        return Keyboard::registerGlobal($accelerator, $callback);
    }

    /**
     * 从配置注册全局快捷键
     *
     * @param string $accelerator
     * @param array $config
     * @return string|null
     */
    protected function registerGlobalShortcutFromConfig(string $accelerator, array $config): ?string
    {
        $action = $config['action'];
        $enabled = $config['enabled'] ?? true;

        if (!$enabled) {
            return null;
        }

        if (is_callable($action)) {
            return Keyboard::registerGlobal($accelerator, $action);
        } elseif (is_string($action)) {
            return $this->registerGlobalShortcutForAction($accelerator, $action);
        }

        return null;
    }

    /**
     * 处理快捷键触发事件
     *
     * @param string $id
     * @return void
     */
    protected function handleShortcutTriggered(string $id): void
    {
        // 获取快捷键信息
        $shortcuts = Keyboard::getRegisteredShortcuts();

        // 查找对应的快捷键
        if (isset($shortcuts[$id])) {
            $shortcut = $shortcuts[$id];

            // 执行回调
            if (isset($shortcut['callback']) && is_callable($shortcut['callback'])) {
                call_user_func($shortcut['callback']);
            }

            // 记录日志
            $config = config('native.keyboard', []);
            if (isset($config['log_operations']) && $config['log_operations']) {
                Logger::info('Shortcut triggered', [
                    'id' => $id,
                    'accelerator' => $shortcut['accelerator'] ?? 'unknown',
                ]);
            }
        }
    }

    /**
     * 处理全局快捷键触发事件
     *
     * @param string $id
     * @return void
     */
    protected function handleGlobalShortcutTriggered(string $id): void
    {
        // 获取快捷键信息
        $shortcuts = Keyboard::getRegisteredShortcuts();

        // 查找对应的快捷键
        if (isset($shortcuts[$id])) {
            $shortcut = $shortcuts[$id];

            // 执行回调
            if (isset($shortcut['callback']) && is_callable($shortcut['callback'])) {
                call_user_func($shortcut['callback']);
            }

            // 记录日志
            $config = config('native.keyboard', []);
            if (isset($config['log_operations']) && $config['log_operations']) {
                Logger::info('Global shortcut triggered', [
                    'id' => $id,
                    'accelerator' => $shortcut['accelerator'] ?? 'unknown',
                ]);
            }
        }
    }

    /**
     * 处理按键按下事件
     *
     * @param array $event
     * @return void
     */
    protected function handleKeyDown(array $event): void
    {
        // 获取快捷键信息
        $shortcuts = Keyboard::getRegisteredShortcuts();

        // 查找对应的监听器
        foreach ($shortcuts as $id => $shortcut) {
            if (isset($shortcut['listener']) && $shortcut['listener'] && isset($shortcut['event']) && $shortcut['event'] === 'keydown') {
                // 执行回调
                if (isset($shortcut['callback']) && is_callable($shortcut['callback'])) {
                    call_user_func($shortcut['callback'], $event);
                }
            }
        }

        // 记录日志
        $config = config('native.keyboard', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Key down', [
                'key' => $event['key'] ?? 'unknown',
                'modifiers' => $event['modifiers'] ?? [],
            ]);
        }
    }

    /**
     * 处理按键松开事件
     *
     * @param array $event
     * @return void
     */
    protected function handleKeyUp(array $event): void
    {
        // 获取快捷键信息
        $shortcuts = Keyboard::getRegisteredShortcuts();

        // 查找对应的监听器
        foreach ($shortcuts as $id => $shortcut) {
            if (isset($shortcut['listener']) && $shortcut['listener'] && isset($shortcut['event']) && $shortcut['event'] === 'keyup') {
                // 执行回调
                if (isset($shortcut['callback']) && is_callable($shortcut['callback'])) {
                    call_user_func($shortcut['callback'], $event);
                }
            }
        }

        // 记录日志
        $config = config('native.keyboard', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Key up', [
                'key' => $event['key'] ?? 'unknown',
                'modifiers' => $event['modifiers'] ?? [],
            ]);
        }
    }

    /**
     * 处理按键按下并松开事件
     *
     * @param array $event
     * @return void
     */
    protected function handleKeyPress(array $event): void
    {
        // 获取快捷键信息
        $shortcuts = Keyboard::getRegisteredShortcuts();

        // 查找对应的监听器
        foreach ($shortcuts as $id => $shortcut) {
            if (isset($shortcut['listener']) && $shortcut['listener'] && isset($shortcut['event']) && $shortcut['event'] === 'keypress') {
                // 执行回调
                if (isset($shortcut['callback']) && is_callable($shortcut['callback'])) {
                    call_user_func($shortcut['callback'], $event);
                }
            }
        }

        // 记录日志
        $config = config('native.keyboard', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Key press', [
                'key' => $event['key'] ?? 'unknown',
                'modifiers' => $event['modifiers'] ?? [],
            ]);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 注销所有快捷键
        Keyboard::unregisterAll();
        Keyboard::unregisterAllGlobal();

        // 记录插件卸载
        Logger::info('Keyboard plugin unloaded');
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
