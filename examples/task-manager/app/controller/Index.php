<?php

namespace app\controller;

use app\BaseController;
use app\model\Category;
use app\model\Task;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Tray;

class Index extends BaseController
{
    public function index()
    {
        // 创建应用菜单
        $this->createMenu();
        
        // 创建系统托盘
        $this->createTray();
        
        // 注册全局快捷键
        $this->registerShortcuts();
        
        return redirect('/task/index');
    }
    
    protected function createMenu()
    {
        Menu::create()
            ->add('文件', [
                ['label' => '新建任务', 'click' => 'createTask'],
                ['label' => '导入任务', 'click' => 'importTasks'],
                ['label' => '导出任务', 'click' => 'exportTasks'],
                ['type' => 'separator'],
                ['label' => '退出', 'click' => 'quit'],
            ])
            ->submenu('编辑', function ($submenu) {
                $submenu->add('复制', ['click' => 'copy']);
                $submenu->add('粘贴', ['click' => 'paste']);
            })
            ->submenu('视图', function ($submenu) {
                $submenu->add('所有任务', ['click' => 'viewAllTasks']);
                $submenu->add('待办任务', ['click' => 'viewPendingTasks']);
                $submenu->add('已完成任务', ['click' => 'viewCompletedTasks']);
                $submenu->add('统计报表', ['click' => 'viewStatistics']);
            })
            ->submenu('帮助', function ($submenu) {
                $submenu->add('关于', ['click' => 'about']);
            })
            ->setApplicationMenu();
    }
    
    protected function createTray()
    {
        Tray::setIcon(public_path() . '/static/img/icon.png')
            ->setTooltip('任务管理器')
            ->setMenu(function ($menu) {
                $menu->add('显示', ['click' => 'show']);
                $menu->add('新建任务', ['click' => 'createTask']);
                $menu->add('退出', ['click' => 'quit']);
            })
            ->show();
    }
    
    protected function registerShortcuts()
    {
        GlobalShortcut::register('CommandOrControl+N', function () {
            Window::open('/task/create', [
                'title' => '新建任务',
                'width' => 600,
                'height' => 500,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+O', function () {
            Window::open('/task/index', [
                'title' => '任务列表',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+S', function () {
            Window::open('/task/statistics', [
                'title' => '统计报表',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+Q', function () {
            App::quit();
        });
    }
    
    public function createTask()
    {
        Window::open('/task/create', [
            'title' => '新建任务',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function importTasks()
    {
        Window::open('/task/import', [
            'title' => '导入任务',
            'width' => 600,
            'height' => 400,
        ]);
        
        return json(['success' => true]);
    }
    
    public function exportTasks()
    {
        Window::open('/task/export', [
            'title' => '导出任务',
            'width' => 600,
            'height' => 400,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewAllTasks()
    {
        Window::open('/task/index', [
            'title' => '所有任务',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewPendingTasks()
    {
        Window::open('/task/index?status=pending', [
            'title' => '待办任务',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewCompletedTasks()
    {
        Window::open('/task/index?status=completed', [
            'title' => '已完成任务',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewStatistics()
    {
        Window::open('/task/statistics', [
            'title' => '统计报表',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function about()
    {
        Window::open('/about', [
            'title' => '关于',
            'width' => 400,
            'height' => 300,
        ]);
        
        return json(['success' => true]);
    }
    
    public function quit()
    {
        App::quit();
        
        return json(['success' => true]);
    }
}
