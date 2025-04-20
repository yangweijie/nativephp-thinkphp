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
        // 创建主窗口分组
        $group = Native::windowManager()->createGroup('main-group');

        // 创建主窗口(带动画)
        Native::window('main')
            ->title('NativePHP-ThinkPHP 示例应用')
            ->width(800)
            ->height(600)
            ->transition()
                ->duration(500)
                ->easing('easeOutQuint')
            ->setLayout([
                'x' => 100,
                'y' => 100,
                'width' => 800,
                'height' => 600,
            ]);

        // 创建编辑器窗口(带动画)
        Native::window('editor')
            ->title('编辑器')
            ->width(600)
            ->height(600)
            ->transition()
                ->duration(500)
                ->easing('easeOutQuint')
            ->setLayout([
                'x' => 920,
                'y' => 100,
                'width' => 600,
                'height' => 600,
            ]);

        // 添加窗口到分组
        $group->add('main')->add('editor');
        
        // 创建系统托盘
        Native::tray()
            ->icon(public_path('icon.png'))
            ->tooltip('NativePHP-ThinkPHP 示例应用')
            ->addItem('显示主窗口', function () {
                Native::window('main')->show();
            })
            ->addItem('显示编辑器', function () {
                Native::window('editor')->show();
            })
            ->addSeparator()
            ->addSubmenu('窗口布局', function ($menu) {
                $menu->add('水平排列 (快速)', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->arrangeHorizontal(true);
                });
                
                $menu->add('垂直排列 (缓慢)', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->transition()
                            ->duration(800)
                            ->easing('easeInOutQuart')
                        ->arrangeVertical(true);
                });
                
                $menu->add('网格布局 (弹性)', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->transition()
                            ->duration(600)
                            ->easing('easeOutElastic')
                        ->arrangeGrid(2, true);
                });
                
                $menu->add('瀑布流布局 (弹跳)', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->transition()
                            ->duration(500)
                            ->easing('easeOutBounce')
                        ->arrangeCascade(true);
                });
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
            ->addSubmenu('布局', function ($menu) {
                $menu->add('默认布局', function () {
                    $this->resetLayout();
                })->accelerator('CmdOrCtrl+R');
                
                $menu->add('水平排列', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->arrangeHorizontal(true);
                })->accelerator('CmdOrCtrl+H');
                
                $menu->add('垂直排列', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->arrangeVertical(true);
                })->accelerator('CmdOrCtrl+V');
                
                $menu->add('网格布局', function () {
                    Native::windowManager()
                        ->getGroup('main-group')
                        ->arrangeGrid(2, true);
                })->accelerator('CmdOrCtrl+G');
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
     * 重置布局到默认状态
     */
    protected function resetLayout()
    {
        Native::window('main')
            ->transition()
                ->duration(500)
                ->easing('easeOutQuint')
            ->setLayout([
                'x' => 100,
                'y' => 100,
                'width' => 800,
                'height' => 600,
            ]);

        Native::window('editor')
            ->transition()
                ->duration(500)
                ->easing('easeOutQuint')
            ->setLayout([
                'x' => 920,
                'y' => 100,
                'width' => 600,
                'height' => 600,
            ]);
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
