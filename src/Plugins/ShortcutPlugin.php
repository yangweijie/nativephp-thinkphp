<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Shortcut;
use Native\ThinkPHP\Facades\Logger;

class ShortcutPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'shortcut';

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
    protected $description = '桌面快捷方式插件';

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
        Logger::info('Shortcut plugin initialized');

        // 监听快捷方式事件
        $this->app->event->listen('native.shortcut.create', function ($event) {
            $this->handleShortcutCreate($event);
        });

        $this->app->event->listen('native.shortcut.remove', function ($event) {
            $this->handleShortcutRemove($event);
        });

        $this->app->event->listen('native.shortcut.login_item', function ($event) {
            $this->handleLoginItemSettings($event);
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
        Logger::info('Shortcut plugin started');

        // 检查是否需要自动创建快捷方式
        $this->checkAutoCreateShortcuts();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Shortcut plugin quit');
    }

    /**
     * 检查是否需要自动创建快捷方式
     *
     * @return void
     */
    protected function checkAutoCreateShortcuts(): void
    {
        // 获取配置
        $config = config('native.shortcut', []);

        // 检查是否需要自动创建桌面快捷方式
        if (isset($config['auto_create_desktop']) && $config['auto_create_desktop']) {
            $this->createDesktopShortcutIfNotExists($config);
        }

        // 检查是否需要自动创建开始菜单快捷方式
        if (isset($config['auto_create_start_menu']) && $config['auto_create_start_menu']) {
            $this->createStartMenuShortcutIfNotExists($config);
        }

        // 检查是否需要设置开机自启动
        if (isset($config['auto_start']) && $config['auto_start']) {
            $this->setLoginItemSettingsIfNeeded($config);
        }
    }

    /**
     * 如果不存在则创建桌面快捷方式
     *
     * @param array $config
     * @return void
     */
    protected function createDesktopShortcutIfNotExists(array $config): void
    {
        // 检查桌面快捷方式是否存在
        if (!Shortcut::existsOnDesktop()) {
            // 创建桌面快捷方式
            $options = $config['desktop_options'] ?? [];
            $success = Shortcut::createDesktopShortcut($options);

            // 记录日志
            if ($success) {
                Logger::info('Desktop shortcut created');
            } else {
                Logger::error('Failed to create desktop shortcut');
            }
        }
    }

    /**
     * 如果不存在则创建开始菜单快捷方式
     *
     * @param array $config
     * @return void
     */
    protected function createStartMenuShortcutIfNotExists(array $config): void
    {
        // 检查开始菜单快捷方式是否存在
        if (!Shortcut::existsInStartMenu()) {
            // 创建开始菜单快捷方式
            $options = $config['start_menu_options'] ?? [];
            $success = Shortcut::createStartMenuShortcut($options);

            // 记录日志
            if ($success) {
                Logger::info('Start menu shortcut created');
            } else {
                Logger::error('Failed to create start menu shortcut');
            }
        }
    }

    /**
     * 如果需要则设置开机自启动
     *
     * @param array $config
     * @return void
     */
    protected function setLoginItemSettingsIfNeeded(array $config): void
    {
        // 获取当前开机自启动设置
        $settings = Shortcut::getLoginItemSettings();

        // 如果当前设置与配置不一致，则设置开机自启动
        if (!isset($settings['openAtLogin']) || $settings['openAtLogin'] !== true) {
            // 设置开机自启动
            $options = $config['auto_start_options'] ?? [];
            $success = Shortcut::setLoginItemSettings(true, $options);

            // 记录日志
            if ($success) {
                Logger::info('Login item settings updated');
            } else {
                Logger::error('Failed to update login item settings');
            }
        }
    }

    /**
     * 处理快捷方式创建事件
     *
     * @param array $event
     * @return void
     */
    protected function handleShortcutCreate(array $event): void
    {
        // 记录快捷方式创建
        $config = config('native.shortcut', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Shortcut created', [
                'type' => $event['type'] ?? 'custom',
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理快捷方式删除事件
     *
     * @param array $event
     * @return void
     */
    protected function handleShortcutRemove(array $event): void
    {
        // 记录快捷方式删除
        $config = config('native.shortcut', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Shortcut removed', [
                'type' => $event['type'] ?? 'custom',
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理开机自启动设置事件
     *
     * @param array $event
     * @return void
     */
    protected function handleLoginItemSettings(array $event): void
    {
        // 记录开机自启动设置
        $config = config('native.shortcut', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Login item settings updated', [
                'enabled' => $event['enabled'] ?? false,
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
        // 记录插件卸载
        Logger::info('Shortcut plugin unloaded');
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
