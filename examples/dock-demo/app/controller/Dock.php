<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Dock as DockFacade;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Window;
use think\facade\View;

class Dock extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 检查是否在 macOS 平台上
        $isMacOS = PHP_OS === 'Darwin';
        
        // 获取当前 Dock 设置
        $settings = $this->getDockSettings();
        
        // 获取 Dock 徽章计数
        $badgeCount = $isMacOS ? DockFacade::getBadgeCount() : 0;
        
        // 获取 Dock 图标是否可见
        $isVisible = $isMacOS ? DockFacade::isVisible() : true;
        
        return View::fetch('dock/index', [
            'isMacOS' => $isMacOS,
            'settings' => $settings,
            'badgeCount' => $badgeCount,
            'isVisible' => $isVisible,
        ]);
    }
    
    /**
     * 设置 Dock 徽章文本
     *
     * @return \think\Response
     */
    public function setBadge()
    {
        $badge = input('badge');
        
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::setBadge($badge);
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['badge'] = $badge;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 徽章文本设置成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 徽章文本设置失败']);
        }
    }
    
    /**
     * 设置 Dock 徽章计数
     *
     * @return \think\Response
     */
    public function setBadgeCount()
    {
        $count = input('count/d', 0);
        
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::setBadgeCount($count);
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['badge_count'] = $count;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 徽章计数设置成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 徽章计数设置失败']);
        }
    }
    
    /**
     * 清除 Dock 徽章
     *
     * @return \think\Response
     */
    public function clearBadge()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::clearBadge();
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['badge'] = '';
        $settings['badge_count'] = 0;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 徽章清除成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 徽章清除失败']);
        }
    }
    
    /**
     * 弹跳 Dock 图标
     *
     * @return \think\Response
     */
    public function bounce()
    {
        $type = input('type', 'informational');
        
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $id = DockFacade::bounce($type);
        
        if ($id) {
            // 保存弹跳ID
            $settings = $this->getDockSettings();
            $settings['bounce_id'] = $id;
            $this->saveDockSettings($settings);
            
            return json(['success' => true, 'message' => 'Dock 图标弹跳成功', 'id' => $id]);
        } else {
            return json(['success' => false, 'message' => 'Dock 图标弹跳失败']);
        }
    }
    
    /**
     * 取消弹跳 Dock 图标
     *
     * @return \think\Response
     */
    public function cancelBounce()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $settings = $this->getDockSettings();
        $id = $settings['bounce_id'] ?? 0;
        
        if (!$id) {
            return json(['success' => false, 'message' => '没有正在弹跳的 Dock 图标']);
        }
        
        $result = DockFacade::cancelBounce($id);
        
        if ($result) {
            // 清除弹跳ID
            $settings['bounce_id'] = 0;
            $this->saveDockSettings($settings);
            
            return json(['success' => true, 'message' => 'Dock 图标弹跳取消成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 图标弹跳取消失败']);
        }
    }
    
    /**
     * 设置下载进度条
     *
     * @return \think\Response
     */
    public function setDownloadProgress()
    {
        $progress = input('progress/f', 0);
        
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        // 确保进度在 0.0 到 1.0 之间
        $progress = max(0, min(1, $progress));
        
        $result = DockFacade::setDownloadProgress($progress);
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['download_progress'] = $progress;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => '下载进度条设置成功']);
        } else {
            return json(['success' => false, 'message' => '下载进度条设置失败']);
        }
    }
    
    /**
     * 清除下载进度条
     *
     * @return \think\Response
     */
    public function clearDownloadProgress()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::clearDownloadProgress();
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['download_progress'] = 0;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => '下载进度条清除成功']);
        } else {
            return json(['success' => false, 'message' => '下载进度条清除失败']);
        }
    }
    
    /**
     * 设置 Dock 图标的工具提示
     *
     * @return \think\Response
     */
    public function setToolTip()
    {
        $tooltip = input('tooltip');
        
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::setToolTip($tooltip);
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['tooltip'] = $tooltip;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 图标工具提示设置成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 图标工具提示设置失败']);
        }
    }
    
    /**
     * 设置 Dock 菜单
     *
     * @return \think\Response
     */
    public function setMenu()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $menu = [
            [
                'label' => '新建文档',
                'click' => 'newDocument',
            ],
            [
                'type' => 'separator',
            ],
            [
                'label' => '最近文档',
                'submenu' => [
                    [
                        'label' => '文档1.txt',
                        'click' => 'openDocument1',
                    ],
                    [
                        'label' => '文档2.txt',
                        'click' => 'openDocument2',
                    ],
                ],
            ],
            [
                'type' => 'separator',
            ],
            [
                'label' => '设置',
                'click' => 'openSettings',
            ],
            [
                'label' => '关于',
                'click' => 'openAbout',
            ],
        ];
        
        $result = DockFacade::setMenu($menu);
        
        // 注册菜单点击事件
        $id = DockFacade::onMenuClick(function ($menuItem) {
            if ($menuItem['click'] === 'newDocument') {
                Notification::send('新建文档', '您点击了新建文档菜单项');
            } elseif ($menuItem['click'] === 'openDocument1') {
                Notification::send('打开文档', '您点击了文档1.txt菜单项');
            } elseif ($menuItem['click'] === 'openDocument2') {
                Notification::send('打开文档', '您点击了文档2.txt菜单项');
            } elseif ($menuItem['click'] === 'openSettings') {
                Window::open('/dock/settings', [
                    'title' => '设置',
                    'width' => 600,
                    'height' => 400,
                ]);
            } elseif ($menuItem['click'] === 'openAbout') {
                Window::open('/dock/about', [
                    'title' => '关于',
                    'width' => 400,
                    'height' => 300,
                ]);
            }
        });
        
        // 保存设置
        $settings = $this->getDockSettings();
        $settings['menu_listener_id'] = $id;
        $this->saveDockSettings($settings);
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 菜单设置成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 菜单设置失败']);
        }
    }
    
    /**
     * 显示 Dock 图标
     *
     * @return \think\Response
     */
    public function show()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::show();
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 图标显示成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 图标显示失败']);
        }
    }
    
    /**
     * 隐藏 Dock 图标
     *
     * @return \think\Response
     */
    public function hide()
    {
        if (PHP_OS !== 'Darwin') {
            return json(['success' => false, 'message' => '此功能仅在 macOS 平台上可用']);
        }
        
        $result = DockFacade::hide();
        
        if ($result) {
            return json(['success' => true, 'message' => 'Dock 图标隐藏成功']);
        } else {
            return json(['success' => false, 'message' => 'Dock 图标隐藏失败']);
        }
    }
    
    /**
     * 显示设置页面
     *
     * @return \think\Response
     */
    public function settings()
    {
        // 获取当前 Dock 设置
        $settings = $this->getDockSettings();
        
        return View::fetch('dock/settings', [
            'settings' => $settings,
        ]);
    }
    
    /**
     * 显示关于页面
     *
     * @return \think\Response
     */
    public function about()
    {
        return View::fetch('dock/about');
    }
    
    /**
     * 获取 Dock 设置
     *
     * @return array
     */
    protected function getDockSettings()
    {
        return Settings::get('dock', [
            'badge' => '',
            'badge_count' => 0,
            'bounce_id' => 0,
            'download_progress' => 0,
            'tooltip' => 'NativePHP Dock 示例',
            'menu_listener_id' => '',
        ]);
    }
    
    /**
     * 保存 Dock 设置
     *
     * @param array $settings
     * @return void
     */
    protected function saveDockSettings($settings)
    {
        Settings::set('dock', $settings);
    }
}
