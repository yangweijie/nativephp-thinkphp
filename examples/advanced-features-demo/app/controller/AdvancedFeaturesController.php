<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Clipboard;
use Native\ThinkPHP\Facades\Screen;
use Native\ThinkPHP\Facades\Updater;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Dialog;
use think\facade\View;
use think\facade\Config;

class AdvancedFeaturesController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 注册全局快捷键
        $this->registerGlobalShortcuts();
        
        // 创建系统托盘
        $this->createTray();
        
        return View::fetch('advanced-features/index');
    }
    
    /**
     * 注册全局快捷键
     *
     * @return void
     */
    protected function registerGlobalShortcuts()
    {
        // 注册全局快捷键：Ctrl+Shift+T（显示系统托盘页面）
        GlobalShortcut::register('CommandOrControl+Shift+T', function () {
            Window::open('/advanced-features/tray', [
                'title' => '系统托盘',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        // 注册全局快捷键：Ctrl+Shift+S（显示全局快捷键页面）
        GlobalShortcut::register('CommandOrControl+Shift+S', function () {
            Window::open('/advanced-features/shortcuts', [
                'title' => '全局快捷键',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        // 注册全局快捷键：Ctrl+Shift+C（显示剪贴板页面）
        GlobalShortcut::register('CommandOrControl+Shift+C', function () {
            Window::open('/advanced-features/clipboard', [
                'title' => '剪贴板',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        // 注册全局快捷键：Ctrl+Shift+P（显示屏幕捕获页面）
        GlobalShortcut::register('CommandOrControl+Shift+P', function () {
            Window::open('/advanced-features/screen', [
                'title' => '屏幕捕获',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        // 注册全局快捷键：Ctrl+Shift+U（显示自动更新页面）
        GlobalShortcut::register('CommandOrControl+Shift+U', function () {
            Window::open('/advanced-features/updater', [
                'title' => '自动更新',
                'width' => 800,
                'height' => 600,
            ]);
        });
        
        // 注册全局快捷键：Ctrl+Shift+Q（退出应用）
        GlobalShortcut::register('CommandOrControl+Shift+Q', function () {
            App::quit();
        });
    }
    
    /**
     * 创建系统托盘
     *
     * @return void
     */
    protected function createTray()
    {
        // 设置托盘图标
        Tray::setIcon(public_path() . 'static/images/tray-icon.png');
        
        // 设置托盘提示文本
        Tray::setTooltip('高级功能示例');
        
        // 设置托盘菜单
        Tray::setMenu(function ($menu) {
            $menu->add('显示主窗口', function () {
                Window::show();
            });
            
            $menu->separator();
            
            $menu->submenu('功能', function ($submenu) {
                $submenu->add('系统托盘', function () {
                    Window::open('/advanced-features/tray', [
                        'title' => '系统托盘',
                        'width' => 800,
                        'height' => 600,
                    ]);
                });
                
                $submenu->add('全局快捷键', function () {
                    Window::open('/advanced-features/shortcuts', [
                        'title' => '全局快捷键',
                        'width' => 800,
                        'height' => 600,
                    ]);
                });
                
                $submenu->add('剪贴板', function () {
                    Window::open('/advanced-features/clipboard', [
                        'title' => '剪贴板',
                        'width' => 800,
                        'height' => 600,
                    ]);
                });
                
                $submenu->add('屏幕捕获', function () {
                    Window::open('/advanced-features/screen', [
                        'title' => '屏幕捕获',
                        'width' => 800,
                        'height' => 600,
                    ]);
                });
                
                $submenu->add('自动更新', function () {
                    Window::open('/advanced-features/updater', [
                        'title' => '自动更新',
                        'width' => 800,
                        'height' => 600,
                    ]);
                });
            });
            
            $menu->separator();
            
            $menu->add('退出', function () {
                App::quit();
            });
        });
        
        // 显示托盘图标
        Tray::show();
    }
    
    /**
     * 显示系统托盘页面
     *
     * @return \think\Response
     */
    public function tray()
    {
        return View::fetch('advanced-features/tray');
    }
    
    /**
     * 显示气泡通知
     *
     * @return \think\Response
     */
    public function showBalloon()
    {
        $title = request()->param('title', '系统托盘');
        $content = request()->param('content', '这是一条来自系统托盘的气泡通知');
        
        Tray::showBalloon($title, $content);
        
        return json(['success' => true]);
    }
    
    /**
     * 设置托盘图标
     *
     * @return \think\Response
     */
    public function setTrayIcon()
    {
        $icon = request()->param('icon');
        
        if (empty($icon)) {
            return json(['success' => false, 'message' => '图标路径不能为空']);
        }
        
        Tray::setIcon($icon);
        
        return json(['success' => true]);
    }
    
    /**
     * 设置托盘提示文本
     *
     * @return \think\Response
     */
    public function setTrayTooltip()
    {
        $tooltip = request()->param('tooltip');
        
        if (empty($tooltip)) {
            return json(['success' => false, 'message' => '提示文本不能为空']);
        }
        
        Tray::setTooltip($tooltip);
        
        return json(['success' => true]);
    }
    
    /**
     * 显示全局快捷键页面
     *
     * @return \think\Response
     */
    public function shortcuts()
    {
        return View::fetch('advanced-features/shortcuts');
    }
    
    /**
     * 注册全局快捷键
     *
     * @return \think\Response
     */
    public function registerShortcut()
    {
        $accelerator = request()->param('accelerator');
        $action = request()->param('action');
        
        if (empty($accelerator)) {
            return json(['success' => false, 'message' => '快捷键不能为空']);
        }
        
        if (empty($action)) {
            return json(['success' => false, 'message' => '操作不能为空']);
        }
        
        // 检查快捷键是否已注册
        if (GlobalShortcut::isRegistered($accelerator)) {
            return json(['success' => false, 'message' => '快捷键已注册']);
        }
        
        // 注册全局快捷键
        $registered = GlobalShortcut::register($accelerator, function () use ($action) {
            switch ($action) {
                case 'notification':
                    Notification::send('全局快捷键', '快捷键已触发');
                    break;
                case 'clipboard':
                    Clipboard::setText('这是通过全局快捷键复制的文本');
                    Notification::send('全局快捷键', '文本已复制到剪贴板');
                    break;
                case 'screenshot':
                    $path = Screen::captureScreenshot();
                    Notification::send('全局快捷键', '屏幕截图已保存到：' . $path);
                    break;
                case 'quit':
                    App::quit();
                    break;
            }
        });
        
        if ($registered) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '注册快捷键失败']);
        }
    }
    
    /**
     * 注销全局快捷键
     *
     * @return \think\Response
     */
    public function unregisterShortcut()
    {
        $accelerator = request()->param('accelerator');
        
        if (empty($accelerator)) {
            return json(['success' => false, 'message' => '快捷键不能为空']);
        }
        
        // 检查快捷键是否已注册
        if (!GlobalShortcut::isRegistered($accelerator)) {
            return json(['success' => false, 'message' => '快捷键未注册']);
        }
        
        // 注销全局快捷键
        $unregistered = GlobalShortcut::unregister($accelerator);
        
        if ($unregistered) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '注销快捷键失败']);
        }
    }
    
    /**
     * 注销所有全局快捷键
     *
     * @return \think\Response
     */
    public function unregisterAllShortcuts()
    {
        GlobalShortcut::unregisterAll();
        
        return json(['success' => true]);
    }
    
    /**
     * 显示剪贴板页面
     *
     * @return \think\Response
     */
    public function clipboard()
    {
        return View::fetch('advanced-features/clipboard');
    }
    
    /**
     * 读取剪贴板文本
     *
     * @return \think\Response
     */
    public function readClipboardText()
    {
        $text = Clipboard::text();
        
        return json(['success' => true, 'text' => $text]);
    }
    
    /**
     * 写入文本到剪贴板
     *
     * @return \think\Response
     */
    public function writeClipboardText()
    {
        $text = request()->param('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        Clipboard::setText($text);
        
        return json(['success' => true]);
    }
    
    /**
     * 读取剪贴板 HTML
     *
     * @return \think\Response
     */
    public function readClipboardHtml()
    {
        $html = Clipboard::html();
        
        return json(['success' => true, 'html' => $html]);
    }
    
    /**
     * 写入 HTML 到剪贴板
     *
     * @return \think\Response
     */
    public function writeClipboardHtml()
    {
        $html = request()->param('html');
        
        if (empty($html)) {
            return json(['success' => false, 'message' => 'HTML 不能为空']);
        }
        
        Clipboard::setHtml($html);
        
        return json(['success' => true]);
    }
    
    /**
     * 读取剪贴板图片
     *
     * @return \think\Response
     */
    public function readClipboardImage()
    {
        $image = Clipboard::image();
        
        return json(['success' => true, 'image' => $image]);
    }
    
    /**
     * 写入图片到剪贴板
     *
     * @return \think\Response
     */
    public function writeClipboardImage()
    {
        $path = request()->param('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '图片路径不能为空']);
        }
        
        Clipboard::setImage($path);
        
        return json(['success' => true]);
    }
    
    /**
     * 清空剪贴板
     *
     * @return \think\Response
     */
    public function clearClipboard()
    {
        Clipboard::clear();
        
        return json(['success' => true]);
    }
    
    /**
     * 显示屏幕捕获页面
     *
     * @return \think\Response
     */
    public function screen()
    {
        return View::fetch('advanced-features/screen');
    }
    
    /**
     * 捕获屏幕截图
     *
     * @return \think\Response
     */
    public function captureScreenshot()
    {
        $path = Screen::captureScreenshot();
        
        if ($path) {
            return json(['success' => true, 'path' => $path]);
        } else {
            return json(['success' => false, 'message' => '捕获屏幕截图失败']);
        }
    }
    
    /**
     * 捕获窗口截图
     *
     * @return \think\Response
     */
    public function captureWindow()
    {
        $windowId = request()->param('windowId');
        
        $path = Screen::captureWindow($windowId);
        
        if ($path) {
            return json(['success' => true, 'path' => $path]);
        } else {
            return json(['success' => false, 'message' => '捕获窗口截图失败']);
        }
    }
    
    /**
     * 开始屏幕录制
     *
     * @return \think\Response
     */
    public function startRecording()
    {
        $options = [
            'audio' => request()->param('audio', false),
        ];
        
        $started = Screen::startRecording($options);
        
        if ($started) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '开始屏幕录制失败']);
        }
    }
    
    /**
     * 停止屏幕录制
     *
     * @return \think\Response
     */
    public function stopRecording()
    {
        $path = Screen::stopRecording();
        
        if ($path) {
            return json(['success' => true, 'path' => $path]);
        } else {
            return json(['success' => false, 'message' => '停止屏幕录制失败']);
        }
    }
    
    /**
     * 获取屏幕信息
     *
     * @return \think\Response
     */
    public function getScreenInfo()
    {
        $displays = Screen::getAllDisplays();
        $primaryDisplay = Screen::getPrimaryDisplay();
        $currentDisplay = Screen::getCurrentDisplay();
        $cursorPosition = Screen::getCursorPosition();
        
        return json([
            'success' => true,
            'displays' => $displays,
            'primaryDisplay' => $primaryDisplay,
            'currentDisplay' => $currentDisplay,
            'cursorPosition' => $cursorPosition,
        ]);
    }
    
    /**
     * 显示自动更新页面
     *
     * @return \think\Response
     */
    public function updater()
    {
        // 获取当前版本
        $currentVersion = Updater::getCurrentVersion();
        
        // 获取更新服务器 URL
        $serverUrl = Updater::getServerUrl();
        
        return View::fetch('advanced-features/updater', [
            'currentVersion' => $currentVersion,
            'serverUrl' => $serverUrl,
        ]);
    }
    
    /**
     * 设置更新服务器 URL
     *
     * @return \think\Response
     */
    public function setUpdateServerUrl()
    {
        $url = request()->param('url');
        
        if (empty($url)) {
            return json(['success' => false, 'message' => '服务器 URL 不能为空']);
        }
        
        Updater::setServerUrl($url);
        
        return json(['success' => true]);
    }
    
    /**
     * 检查更新
     *
     * @return \think\Response
     */
    public function checkForUpdates()
    {
        $updateInfo = Updater::check();
        
        if ($updateInfo) {
            return json(['success' => true, 'updateInfo' => $updateInfo]);
        } else {
            return json(['success' => false, 'message' => '检查更新失败']);
        }
    }
    
    /**
     * 下载更新
     *
     * @return \think\Response
     */
    public function downloadUpdate()
    {
        $version = request()->param('version');
        
        $downloaded = Updater::download($version);
        
        if ($downloaded) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '下载更新失败']);
        }
    }
    
    /**
     * 安装更新
     *
     * @return \think\Response
     */
    public function installUpdate()
    {
        $installed = Updater::install();
        
        if ($installed) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '安装更新失败']);
        }
    }
    
    /**
     * 获取更新状态
     *
     * @return \think\Response
     */
    public function getUpdateStatus()
    {
        $status = Updater::getStatus();
        $progress = Updater::getProgress();
        
        return json([
            'success' => true,
            'status' => $status,
            'progress' => $progress,
        ]);
    }
    
    /**
     * 取消更新
     *
     * @return \think\Response
     */
    public function cancelUpdate()
    {
        $cancelled = Updater::cancel();
        
        if ($cancelled) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '取消更新失败']);
        }
    }
}
