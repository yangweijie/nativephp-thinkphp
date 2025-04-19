<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Logger;

class GlobalShortcutPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'global-shortcut';

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
    protected $description = '全局快捷键插件';

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
     * 已注册的快捷键
     *
     * @var array
     */
    protected $shortcuts = [];

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
        // 加载配置的快捷键
        $this->loadConfiguredShortcuts();

        // 监听全局快捷键事件
        $this->app->event->listen('native.keyboard.global_shortcut', function ($id) {
            $this->handleShortcutTriggered($id);
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
        Logger::info('GlobalShortcut plugin started');
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 注销所有快捷键
        GlobalShortcut::unregisterAll();
    }

    /**
     * 加载配置的快捷键
     *
     * @return void
     */
    protected function loadConfiguredShortcuts(): void
    {
        // 获取配置
        $shortcuts = config('native.shortcuts', []);

        // 注册配置的快捷键
        foreach ($shortcuts as $accelerator => $action) {
            if (is_callable($action)) {
                // 如果是回调函数，直接注册
                $this->registerShortcut($accelerator, $action);
            } elseif (is_string($action)) {
                // 如果是字符串，尝试解析为路由或控制器方法
                $this->registerShortcutForAction($accelerator, $action);
            } elseif (is_array($action) && isset($action['action'])) {
                // 如果是数组，尝试解析为配置项
                $this->registerShortcutFromConfig($accelerator, $action);
            }
        }
    }

    /**
     * 注册快捷键
     *
     * @param string $accelerator
     * @param callable $callback
     * @return bool
     */
    protected function registerShortcut(string $accelerator, callable $callback): bool
    {
        $success = GlobalShortcut::register($accelerator, $callback);

        if ($success) {
            $this->shortcuts[$accelerator] = $callback;
            Logger::info('Registered global shortcut', ['accelerator' => $accelerator]);
        } else {
            Logger::error('Failed to register global shortcut', ['accelerator' => $accelerator]);
        }

        return $success;
    }

    /**
     * 为动作注册快捷键
     *
     * @param string $accelerator
     * @param string $action
     * @return bool
     */
    protected function registerShortcutForAction(string $accelerator, string $action): bool
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

        return $this->registerShortcut($accelerator, $callback);
    }

    /**
     * 从配置注册快捷键
     *
     * @param string $accelerator
     * @param array $config
     * @return bool
     */
    protected function registerShortcutFromConfig(string $accelerator, array $config): bool
    {
        $action = $config['action'];
        $enabled = $config['enabled'] ?? true;

        if (!$enabled) {
            return false;
        }

        if (is_callable($action)) {
            return $this->registerShortcut($accelerator, $action);
        } elseif (is_string($action)) {
            return $this->registerShortcutForAction($accelerator, $action);
        }

        return false;
    }

    /**
     * 处理快捷键触发事件
     *
     * @param string $id
     * @return void
     */
    protected function handleShortcutTriggered(string $id): void
    {
        // 查找对应的快捷键
        foreach ($this->shortcuts as $accelerator => $callback) {
            if (md5('global:' . $accelerator) === $id) {
                // 执行回调
                call_user_func($callback);
                
                // 记录日志
                Logger::info('Global shortcut triggered', ['accelerator' => $accelerator]);
                
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
        // 注销所有快捷键
        GlobalShortcut::unregisterAll();
        
        // 清空快捷键列表
        $this->shortcuts = [];
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
