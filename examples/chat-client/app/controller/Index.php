<?php

namespace app\controller;

use app\BaseController;
use app\service\AuthService;
use app\service\ChatService;
use app\service\NotificationService;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\Settings;

class Index extends BaseController
{
    protected $authService;
    protected $chatService;
    protected $notificationService;
    
    public function __construct(
        AuthService $authService,
        ChatService $chatService,
        NotificationService $notificationService
    ) {
        $this->authService = $authService;
        $this->chatService = $chatService;
        $this->notificationService = $notificationService;
    }
    
    public function index()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 创建应用菜单
        $this->createMenu();
        
        // 创建系统托盘
        $this->createTray();
        
        // 注册全局快捷键
        $this->registerShortcuts();
        
        // 连接聊天服务器
        $this->chatService->connect();
        
        // 获取用户信息
        $user = $this->authService->getCurrentUser();
        
        // 获取联系人列表
        $contacts = $this->chatService->getContacts();
        
        // 获取最近会话
        $conversations = $this->chatService->getRecentConversations();
        
        return view('index/index', [
            'user' => $user,
            'contacts' => $contacts,
            'conversations' => $conversations,
            'theme' => Settings::get('app.theme', 'light'),
        ]);
    }
    
    protected function createMenu()
    {
        Menu::create()
            ->add('文件', [
                ['label' => '新建会话', 'click' => 'newConversation'],
                ['label' => '新建群组', 'click' => 'newGroup'],
                ['type' => 'separator'],
                ['label' => '设置', 'click' => 'openSettings'],
                ['type' => 'separator'],
                ['label' => '退出', 'click' => 'quit'],
            ])
            ->submenu('编辑', function ($submenu) {
                $submenu->add('复制', ['click' => 'copy']);
                $submenu->add('粘贴', ['click' => 'paste']);
                $submenu->add('全选', ['click' => 'selectAll']);
            })
            ->submenu('视图', function ($submenu) {
                $submenu->add('联系人', ['click' => 'viewContacts']);
                $submenu->add('会话', ['click' => 'viewConversations']);
                $submenu->add('文件', ['click' => 'viewFiles']);
                $submenu->add('通话记录', ['click' => 'viewCalls']);
                $submenu->add('设置', ['click' => 'openSettings']);
            })
            ->submenu('帮助', function ($submenu) {
                $submenu->add('关于', ['click' => 'about']);
                $submenu->add('检查更新', ['click' => 'checkUpdate']);
            })
            ->setApplicationMenu();
    }
    
    protected function createTray()
    {
        Tray::setIcon(public_path() . '/static/img/icon.png')
            ->setTooltip('聊天客户端')
            ->setMenu(function ($menu) {
                $menu->add('显示', ['click' => 'show']);
                $menu->add('新建会话', ['click' => 'newConversation']);
                $menu->add('设置', ['click' => 'openSettings']);
                $menu->add('退出', ['click' => 'quit']);
            })
            ->show();
    }
    
    protected function registerShortcuts()
    {
        GlobalShortcut::register('CommandOrControl+N', function () {
            Window::open('/chat/new', [
                'title' => '新建会话',
                'width' => 400,
                'height' => 500,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+G', function () {
            Window::open('/chat/new-group', [
                'title' => '新建群组',
                'width' => 500,
                'height' => 600,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+,', function () {
            Window::open('/setting', [
                'title' => '设置',
                'width' => 600,
                'height' => 500,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+Q', function () {
            App::quit();
        });
    }
    
    public function newConversation()
    {
        Window::open('/chat/new', [
            'title' => '新建会话',
            'width' => 400,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function newGroup()
    {
        Window::open('/chat/new-group', [
            'title' => '新建群组',
            'width' => 500,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function openSettings()
    {
        Window::open('/setting', [
            'title' => '设置',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewContacts()
    {
        Window::open('/contact', [
            'title' => '联系人',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewConversations()
    {
        Window::open('/chat/conversations', [
            'title' => '会话',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewFiles()
    {
        Window::open('/file', [
            'title' => '文件',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function viewCalls()
    {
        Window::open('/call/history', [
            'title' => '通话记录',
            'width' => 600,
            'height' => 500,
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
    
    public function checkUpdate()
    {
        // 检查更新
        $this->notificationService->notify('检查更新', '正在检查更新...');
        
        // 模拟检查更新
        sleep(1);
        
        $this->notificationService->notify('检查更新', '当前已是最新版本');
        
        return json(['success' => true]);
    }
    
    public function quit()
    {
        // 断开聊天服务器连接
        $this->chatService->disconnect();
        
        // 退出应用
        App::quit();
        
        return json(['success' => true]);
    }
}
