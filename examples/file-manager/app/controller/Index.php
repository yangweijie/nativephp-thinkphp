<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\System;

class Index extends BaseController
{
    /**
     * 初始化方法
     */
    public function initialize()
    {
        // 创建应用菜单
        $this->createMenu();

        // 创建系统托盘
        $this->createTray();

        // 注册全局快捷键
        $this->registerShortcuts();
    }

    /**
     * 首页
     */
    public function index()
    {
        // 重定向到文件控制器
        return redirect('/file/index');
    }

    /**
     * 创建应用菜单
     */
    protected function createMenu()
    {
        Menu::create()
            ->add('文件', [
                ['label' => '打开文件夹', 'click' => 'openFolder', 'accelerator' => 'CommandOrControl+O'],
                ['label' => '新建文件', 'click' => 'newFile', 'accelerator' => 'CommandOrControl+N'],
                ['label' => '新建文件夹', 'click' => 'newFolder'],
                ['type' => 'separator'],
                ['label' => '退出', 'click' => 'quit', 'accelerator' => 'CommandOrControl+Q'],
            ])
            ->submenu('编辑', function ($submenu) {
                $submenu->add('复制', ['click' => 'copy', 'accelerator' => 'CommandOrControl+C']);
                $submenu->add('粘贴', ['click' => 'paste', 'accelerator' => 'CommandOrControl+V']);
                $submenu->add('剪切', ['click' => 'cut', 'accelerator' => 'CommandOrControl+X']);
                $submenu->add('删除', ['click' => 'delete', 'accelerator' => 'Delete']);
            })
            ->submenu('视图', function ($submenu) {
                $submenu->add('刷新', ['click' => 'refresh', 'accelerator' => 'F5']);
                $submenu->add('详细信息', ['click' => 'details', 'type' => 'checkbox', 'checked' => true]);
                $submenu->add('预览窗格', ['click' => 'preview', 'type' => 'checkbox', 'checked' => false]);
            })
            ->submenu('工具', function ($submenu) {
                $submenu->add('日志查看器', ['click' => 'viewLogs']);
            })
            ->submenu('帮助', function ($submenu) {
                $submenu->add('关于', ['click' => 'about']);
                $submenu->add('文档', ['click' => 'documentation']);
            })
            ->setApplicationMenu();
    }

    /**
     * 创建系统托盘
     */
    protected function createTray()
    {
        Tray::create()
            ->setTitle('文件管理器')
            ->setTooltip('NativePHP 文件管理器')
            ->setContextMenu([
                ['label' => '显示', 'click' => 'show'],
                ['label' => '隐藏', 'click' => 'hide'],
                ['type' => 'separator'],
                ['label' => '退出', 'click' => 'quit'],
            ]);
    }

    /**
     * 注册全局快捷键
     */
    protected function registerShortcuts()
    {
        GlobalShortcut::register('CommandOrControl+O', function () {
            $this->openFolder();
        });

        GlobalShortcut::register('CommandOrControl+N', function () {
            $this->newFile();
        });

        GlobalShortcut::register('F5', function () {
            $this->refresh();
        });
    }

    /**
     * 打开文件夹
     */
    public function openFolder()
    {
        return redirect('/file/selectFolder');
    }

    /**
     * 新建文件
     */
    public function newFile()
    {
        return redirect('/file/create?type=file');
    }

    /**
     * 新建文件夹
     */
    public function newFolder()
    {
        return redirect('/file/create?type=directory');
    }

    /**
     * 刷新
     */
    public function refresh()
    {
        $window = Window::current();
        if ($window) {
            $window->reload();
        }

        return json(['success' => true]);
    }

    /**
     * 关于
     */
    public function about()
    {
        Window::open('/about', [
            'title' => '关于文件管理器',
            'width' => 400,
            'height' => 300,
            'resizable' => false,
        ]);

        return json(['success' => true]);
    }

    /**
     * 文档
     */
    public function documentation()
    {
        System::openExternal('https://github.com/nativephp/thinkphp');

        return json(['success' => true]);
    }

    /**
     * 查看日志
     */
    public function viewLogs()
    {
        return redirect('/log/index');
    }

    /**
     * 显示窗口
     */
    public function show()
    {
        Window::current()->show();

        return json(['success' => true]);
    }

    /**
     * 隐藏窗口
     */
    public function hide()
    {
        Window::current()->hide();

        return json(['success' => true]);
    }

    /**
     * 退出应用
     */
    public function quit()
    {
        Window::current()->close();

        return json(['success' => true]);
    }
}
