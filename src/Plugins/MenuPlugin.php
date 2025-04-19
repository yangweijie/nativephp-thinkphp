<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Logger;

class MenuPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'menu';

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
    protected $description = '应用程序菜单插件';

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
        // 记录插件启动
        Logger::info('Menu plugin initialized');

        // 监听菜单事件
        $this->app->event->listen('native.menu.click', function ($event) {
            $this->handleMenuClick($event);
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
        Logger::info('Menu plugin started');

        // 创建应用程序菜单
        $this->createApplicationMenu();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Menu plugin quit');
    }

    /**
     * 窗口创建事件处理
     *
     * @param array $window
     * @return void
     */
    public function onWindowCreate(array $window): void
    {
        // 记录窗口创建
        Logger::info('Window created', [
            'id' => $window['id'] ?? null,
            'title' => $window['title'] ?? null,
        ]);
    }

    /**
     * 创建应用程序菜单
     *
     * @return void
     */
    protected function createApplicationMenu(): void
    {
        // 获取配置
        $config = config('native.menu', []);

        // 如果配置了应用程序菜单，则创建
        if (isset($config['application_menu']) && is_array($config['application_menu'])) {
            $this->createMenuFromConfig($config['application_menu']);
        } elseif (isset($config['application_menu_builder']) && is_callable($config['application_menu_builder'])) {
            // 如果配置了应用程序菜单构建器，则使用构建器创建
            $menu = Menu::create();
            call_user_func($config['application_menu_builder'], $menu);
            $menu->setApplicationMenu();
        } elseif (isset($config['auto_create_default_menu']) && $config['auto_create_default_menu']) {
            // 如果配置了自动创建默认菜单，则创建默认菜单
            $this->createDefaultApplicationMenu();
        }
    }

    /**
     * 从配置创建菜单
     *
     * @param array $menuConfig
     * @return void
     */
    protected function createMenuFromConfig(array $menuConfig): void
    {
        $menu = Menu::create();

        foreach ($menuConfig as $item) {
            if (isset($item['type']) && $item['type'] === 'separator') {
                $menu->separator();
            } elseif (isset($item['submenu']) && is_array($item['submenu'])) {
                $menu->submenu($item['label'], function ($submenu) use ($item) {
                    foreach ($item['submenu'] as $subItem) {
                        $this->addMenuItem($submenu, $subItem);
                    }
                });
            } else {
                $this->addMenuItem($menu, $item);
            }
        }

        $menu->setApplicationMenu();
    }

    /**
     * 添加菜单项
     *
     * @param \Native\ThinkPHP\Menu $menu
     * @param array $item
     * @return void
     */
    protected function addMenuItem($menu, array $item): void
    {
        if (isset($item['type']) && $item['type'] === 'separator') {
            $menu->separator();
        } elseif (isset($item['type']) && $item['type'] === 'checkbox') {
            $menu->checkbox(
                $item['label'],
                $item['checked'] ?? false,
                $item['action'] ?? null,
                $this->getMenuItemOptions($item)
            );
        } elseif (isset($item['type']) && $item['type'] === 'radio') {
            $menu->radio(
                $item['label'],
                $item['checked'] ?? false,
                $item['action'] ?? null,
                $this->getMenuItemOptions($item)
            );
        } elseif (isset($item['accelerator'])) {
            $menu->accelerator(
                $item['label'],
                $item['accelerator'],
                $item['action'] ?? null,
                $this->getMenuItemOptions($item)
            );
        } elseif (isset($item['icon'])) {
            $menu->icon(
                $item['label'],
                $item['icon'],
                $item['action'] ?? null,
                $this->getMenuItemOptions($item)
            );
        } elseif (isset($item['enabled']) && $item['enabled'] === false) {
            $menu->disabled(
                $item['label'],
                $this->getMenuItemOptions($item)
            );
        } elseif (isset($item['role'])) {
            $menu->role(
                $item['role'],
                $this->getMenuItemOptions($item)
            );
        } else {
            $menu->add(
                $item['label'],
                $item['action'] ?? null,
                $this->getMenuItemOptions($item)
            );
        }
    }

    /**
     * 获取菜单项选项
     *
     * @param array $item
     * @return array
     */
    protected function getMenuItemOptions(array $item): array
    {
        $options = [];

        if (isset($item['id'])) {
            $options['id'] = $item['id'];
        }

        if (isset($item['enabled'])) {
            $options['enabled'] = $item['enabled'];
        }

        if (isset($item['visible'])) {
            $options['visible'] = $item['visible'];
        }

        if (isset($item['icon'])) {
            $options['icon'] = $item['icon'];
        }

        return $options;
    }

    /**
     * 创建默认应用程序菜单
     *
     * @return void
     */
    protected function createDefaultApplicationMenu(): void
    {
        $appName = config('native.name', 'NativePHP');
        $isWindows = PHP_OS_FAMILY === 'Windows';
        $isMacOS = PHP_OS_FAMILY === 'Darwin';

        $menu = Menu::create();

        // 应用菜单（macOS 特有）
        if ($isMacOS) {
            $menu->submenu($appName, function ($submenu) use ($appName) {
                $submenu->role('about', ['label' => '关于 ' . $appName]);
                $submenu->separator();
                $submenu->role('services', ['label' => '服务']);
                $submenu->separator();
                $submenu->role('hide', ['label' => '隐藏 ' . $appName]);
                $submenu->role('hideothers', ['label' => '隐藏其他']);
                $submenu->role('unhide', ['label' => '显示全部']);
                $submenu->separator();
                $submenu->role('quit', ['label' => '退出 ' . $appName]);
            });
        }

        // 文件菜单
        $menu->submenu('文件', function ($submenu) use ($isWindows) {
            $submenu->add('新建', function () {
                $this->app->event->trigger('menu.file.new');
            });
            $submenu->add('打开...', function () {
                $this->app->event->trigger('menu.file.open');
            });
            $submenu->separator();
            $submenu->add('保存', function () {
                $this->app->event->trigger('menu.file.save');
            });
            $submenu->add('另存为...', function () {
                $this->app->event->trigger('menu.file.saveas');
            });
            $submenu->separator();
            if ($isWindows) {
                $submenu->add('退出', function () {
                    $this->app->event->trigger('app.quit');
                });
            }
        });

        // 编辑菜单
        $menu->submenu('编辑', function ($submenu) {
            $submenu->add('撤销', function () {
                $this->app->event->trigger('menu.edit.undo');
            });
            $submenu->add('重做', function () {
                $this->app->event->trigger('menu.edit.redo');
            });
            $submenu->separator();
            $submenu->add('剪切', function () {
                $this->app->event->trigger('menu.edit.cut');
            });
            $submenu->add('复制', function () {
                $this->app->event->trigger('menu.edit.copy');
            });
            $submenu->add('粘贴', function () {
                $this->app->event->trigger('menu.edit.paste');
            });
            $submenu->separator();
            $submenu->add('全选', function () {
                $this->app->event->trigger('menu.edit.selectall');
            });
        });

        // 视图菜单
        $menu->submenu('视图', function ($submenu) {
            $submenu->add('刷新', function () {
                $this->app->event->trigger('menu.view.reload');
            });
            $submenu->separator();
            $submenu->add('全屏', function () {
                $this->app->event->trigger('menu.view.fullscreen');
            });
            $submenu->separator();
            $submenu->add('开发者工具', function () {
                $this->app->event->trigger('menu.view.devtools');
            });
        });

        // 窗口菜单
        $menu->submenu('窗口', function ($submenu) use ($isMacOS) {
            $submenu->add('最小化', function () {
                $this->app->event->trigger('menu.window.minimize');
            });
            $submenu->add('最大化', function () {
                $this->app->event->trigger('menu.window.maximize');
            });
            if ($isMacOS) {
                $submenu->separator();
                $submenu->role('front', ['label' => '前置全部窗口']);
            }
        });

        // 帮助菜单
        $menu->submenu('帮助', function ($submenu) use ($appName) {
            $submenu->add('关于 ' . $appName, function () {
                $this->app->event->trigger('menu.help.about');
            });
        });

        $menu->setApplicationMenu();
    }

    /**
     * 处理菜单点击事件
     *
     * @param array $event
     * @return void
     */
    protected function handleMenuClick(array $event): void
    {
        // 获取配置
        $config = config('native.menu', []);

        // 如果配置了记录菜单操作，则记录
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Menu click', [
                'id' => $event['id'] ?? null,
                'label' => $event['label'] ?? null,
            ]);
        }

        // 如果配置了菜单点击处理器，则执行
        if (isset($config['click_handler']) && is_callable($config['click_handler'])) {
            call_user_func($config['click_handler'], $event);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 清除应用程序菜单
        Menu::clearApplicationMenu();

        // 记录插件卸载
        Logger::info('Menu plugin unloaded');
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
