<?php
declare(strict_types=1);

namespace app\controller;

use NativePHP\Think\Facades\Native;
use think\Request;

class NativeController
{
    /**
     * 应用初始化
     */
    public function initialize()
    {
        // 创建主窗口
        Native::window()
            ->title('NativePHP-ThinkPHP 示例应用')
            ->width(800)
            ->height(600)
            ->center()
            ->show();
        
        // 创建系统托盘
        Native::tray()
            ->icon(public_path('icon.png'))
            ->tooltip('NativePHP-ThinkPHP 示例应用')
            ->addItem('显示应用', function () {
                Native::window()->show();
            })
            ->addSeparator()
            ->addItem('退出', function () {
                Native::exit();
            })
            ->show();
        
        // 创建应用菜单
        Native::menu()
            ->addSubmenu('文件', function ($menu) {
                $menu->add('新建', function () {
                    $this->showNotification('新建文件', '您点击了新建菜单项');
                })->accelerator('CmdOrCtrl+N');
                
                $menu->add('打开', function () {
                    $this->showNotification('打开文件', '您点击了打开菜单项');
                })->accelerator('CmdOrCtrl+O');
                
                $menu->addSeparator();
                
                $menu->add('退出', function () {
                    Native::exit();
                })->accelerator('CmdOrCtrl+Q');
            })
            ->addSubmenu('编辑', function ($menu) {
                $menu->add('撤销')->accelerator('CmdOrCtrl+Z');
                $menu->add('重做')->accelerator('Shift+CmdOrCtrl+Z');
                $menu->addSeparator();
                $menu->add('剪切')->accelerator('CmdOrCtrl+X');
                $menu->add('复制')->accelerator('CmdOrCtrl+C');
                $menu->add('粘贴')->accelerator('CmdOrCtrl+V');
            })
            ->addSubmenu('帮助', function ($menu) {
                $menu->add('关于', function () {
                    $this->showAbout();
                });
            });
        
        // 注册全局快捷键
        Native::hotkey()->register('CommandOrControl+Shift+F', function () {
            $this->showNotification('快捷键', '您按下了 Ctrl+Shift+F 快捷键');
        });
        
        // 注册 IPC 处理器
        Native::ipc()->handle('show-notification', function ($data) {
            $this->showNotification($data['title'] ?? '通知', $data['message'] ?? '');
        });
    }
    
    /**
     * 显示通知
     */
    protected function showNotification($title, $message)
    {
        Native::notification()
            ->title($title)
            ->body($message)
            ->show();
    }
    
    /**
     * 显示关于对话框
     */
    protected function showAbout()
    {
        Native::window()
            ->title('关于')
            ->width(400)
            ->height(300)
            ->center()
            ->url(url('index/about'))
            ->show();
    }
}
