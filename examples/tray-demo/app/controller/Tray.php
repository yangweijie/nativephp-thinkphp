<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Tray as TrayFacade;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Settings;
use think\facade\View;

class Tray extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取托盘设置
        $settings = $this->getTraySettings();
        
        return View::fetch('tray/index', [
            'settings' => $settings,
        ]);
    }
    
    /**
     * 显示托盘图标
     *
     * @return \think\Response
     */
    public function show()
    {
        // 获取托盘设置
        $settings = $this->getTraySettings();
        
        // 创建托盘菜单
        $menu = Menu::create();
        
        // 添加菜单项
        $menu->add('打开主窗口', function () {
            Window::open('/', [
                'title' => '系统托盘示例',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        $menu->add('显示通知', function () {
            Notification::send('系统托盘示例', '这是一条来自系统托盘的通知');
        });
        
        $menu->separator();
        
        $menu->submenu('设置', function ($submenu) {
            $submenu->add('托盘设置', function () {
                Window::open('/tray/settings', [
                    'title' => '托盘设置',
                    'width' => 600,
                    'height' => 400,
                ]);
            });
            
            $submenu->add('应用设置', function () {
                Window::open('/tray/app-settings', [
                    'title' => '应用设置',
                    'width' => 600,
                    'height' => 400,
                ]);
            });
        });
        
        $menu->separator();
        
        $menu->add('退出', function () {
            $this->exitApp();
        });
        
        // 设置托盘图标
        TrayFacade::setIcon($settings['icon'] ?? public_path() . 'static/images/tray-icon.png');
        
        // 设置托盘提示文本
        TrayFacade::setTooltip($settings['tooltip'] ?? '系统托盘示例');
        
        // 设置托盘菜单
        TrayFacade::setMenu(function ($trayMenu) use ($menu) {
            foreach ($menu->getItems() as $item) {
                if (isset($item['type']) && $item['type'] === 'separator') {
                    $trayMenu->separator();
                } elseif (isset($item['submenu'])) {
                    $trayMenu->submenu($item['label'], function ($submenu) use ($item) {
                        foreach ($item['submenu'] as $subItem) {
                            if (isset($subItem['type']) && $subItem['type'] === 'separator') {
                                $submenu->separator();
                            } else {
                                $submenu->add($subItem['label'], $subItem['action']);
                            }
                        }
                    });
                } else {
                    $trayMenu->add($item['label'], $item['action']);
                }
            }
        });
        
        // 注册点击事件
        if ($settings['click_action'] ?? 'none' !== 'none') {
            TrayFacade::onClick(function () use ($settings) {
                $this->handleTrayAction($settings['click_action'] ?? 'none');
            });
        }
        
        // 注册双击事件
        if ($settings['double_click_action'] ?? 'open_window' !== 'none') {
            TrayFacade::onDoubleClick(function () use ($settings) {
                $this->handleTrayAction($settings['double_click_action'] ?? 'open_window');
            });
        }
        
        // 注册右键点击事件
        if ($settings['right_click_action'] ?? 'show_menu' !== 'none') {
            TrayFacade::onRightClick(function () use ($settings) {
                $this->handleTrayAction($settings['right_click_action'] ?? 'show_menu');
            });
        }
        
        // 显示托盘图标
        $result = TrayFacade::show();
        
        if ($result) {
            return json(['success' => true, 'message' => '托盘图标已显示']);
        } else {
            return json(['success' => false, 'message' => '托盘图标显示失败']);
        }
    }
    
    /**
     * 隐藏托盘图标
     *
     * @return \think\Response
     */
    public function hide()
    {
        $result = TrayFacade::hide();
        
        if ($result) {
            return json(['success' => true, 'message' => '托盘图标已隐藏']);
        } else {
            return json(['success' => false, 'message' => '托盘图标隐藏失败']);
        }
    }
    
    /**
     * 显示气泡提示
     *
     * @return \think\Response
     */
    public function showBalloon()
    {
        $title = input('title', '系统托盘示例');
        $content = input('content', '这是一条来自系统托盘的气泡提示');
        $icon = input('icon', 'info');
        
        TrayFacade::showBalloon($title, $content, [
            'icon' => $icon,
            'timeout' => 5000,
        ]);
        
        return json(['success' => true, 'message' => '气泡提示已显示']);
    }
    
    /**
     * 设置托盘图标
     *
     * @return \think\Response
     */
    public function setIcon()
    {
        $icon = input('icon');
        
        if (empty($icon)) {
            return json(['success' => false, 'message' => '图标路径不能为空']);
        }
        
        // 保存设置
        $settings = $this->getTraySettings();
        $settings['icon'] = $icon;
        $this->saveTraySettings($settings);
        
        // 设置托盘图标
        TrayFacade::setIcon($icon);
        
        return json(['success' => true, 'message' => '托盘图标已设置']);
    }
    
    /**
     * 设置托盘提示文本
     *
     * @return \think\Response
     */
    public function setTooltip()
    {
        $tooltip = input('tooltip');
        
        if (empty($tooltip)) {
            return json(['success' => false, 'message' => '提示文本不能为空']);
        }
        
        // 保存设置
        $settings = $this->getTraySettings();
        $settings['tooltip'] = $tooltip;
        $this->saveTraySettings($settings);
        
        // 设置托盘提示文本
        TrayFacade::setTooltip($tooltip);
        
        return json(['success' => true, 'message' => '托盘提示文本已设置']);
    }
    
    /**
     * 设置托盘事件动作
     *
     * @return \think\Response
     */
    public function setAction()
    {
        $event = input('event');
        $action = input('action');
        
        if (empty($event) || empty($action)) {
            return json(['success' => false, 'message' => '事件类型和动作不能为空']);
        }
        
        // 保存设置
        $settings = $this->getTraySettings();
        $settings[$event . '_action'] = $action;
        $this->saveTraySettings($settings);
        
        return json(['success' => true, 'message' => '托盘事件动作已设置']);
    }
    
    /**
     * 显示托盘设置页面
     *
     * @return \think\Response
     */
    public function settings()
    {
        // 获取托盘设置
        $settings = $this->getTraySettings();
        
        return View::fetch('tray/settings', [
            'settings' => $settings,
        ]);
    }
    
    /**
     * 显示应用设置页面
     *
     * @return \think\Response
     */
    public function appSettings()
    {
        // 获取应用设置
        $settings = Settings::get('app', []);
        
        return View::fetch('tray/app-settings', [
            'settings' => $settings,
        ]);
    }
    
    /**
     * 保存应用设置
     *
     * @return \think\Response
     */
    public function saveAppSettings()
    {
        $settings = input('settings/a', []);
        
        // 保存应用设置
        Settings::set('app', $settings);
        
        return json(['success' => true, 'message' => '应用设置已保存']);
    }
    
    /**
     * 退出应用
     *
     * @return void
     */
    protected function exitApp()
    {
        // 销毁托盘图标
        TrayFacade::destroy();
        
        // 关闭所有窗口
        Window::closeAll();
        
        // 退出应用
        exit(0);
    }
    
    /**
     * 处理托盘动作
     *
     * @param string $action 动作类型
     * @return void
     */
    protected function handleTrayAction($action)
    {
        switch ($action) {
            case 'open_window':
                Window::open('/', [
                    'title' => '系统托盘示例',
                    'width' => 800,
                    'height' => 600,
                ]);
                break;
                
            case 'show_notification':
                Notification::send('系统托盘示例', '这是一条来自系统托盘的通知');
                break;
                
            case 'show_menu':
                // 右键点击默认会显示菜单，不需要额外处理
                break;
                
            case 'show_settings':
                Window::open('/tray/settings', [
                    'title' => '托盘设置',
                    'width' => 600,
                    'height' => 400,
                ]);
                break;
                
            case 'exit_app':
                $this->exitApp();
                break;
        }
    }
    
    /**
     * 获取托盘设置
     *
     * @return array
     */
    protected function getTraySettings()
    {
        return Settings::get('tray', [
            'icon' => public_path() . 'static/images/tray-icon.png',
            'tooltip' => '系统托盘示例',
            'click_action' => 'none',
            'double_click_action' => 'open_window',
            'right_click_action' => 'show_menu',
        ]);
    }
    
    /**
     * 保存托盘设置
     *
     * @param array $settings
     * @return void
     */
    protected function saveTraySettings($settings)
    {
        Settings::set('tray', $settings);
    }
}
