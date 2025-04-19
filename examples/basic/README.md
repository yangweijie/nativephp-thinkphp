# 基础示例

这个示例展示了 NativePHP for ThinkPHP 的基本功能，包括窗口、菜单和通知。

## 功能

- 创建和管理窗口
- 创建应用菜单
- 发送系统通知
- 使用剪贴板
- 注册全局快捷键
- 使用系统托盘

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Window.php` - 窗口控制器
- `app/controller/Menu.php` - 菜单控制器
- `app/controller/Notification.php` - 通知控制器
- `view/index/index.html` - 主页面
- `view/window/index.html` - 窗口页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Clipboard;
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
        
        return view('index/index', [
            'appName' => App::name(),
            'appVersion' => App::version(),
        ]);
    }
    
    protected function createMenu()
    {
        Menu::create()
            ->add('文件', [
                ['label' => '新建窗口', 'click' => 'openNewWindow'],
                ['label' => '退出', 'click' => 'quit'],
            ])
            ->submenu('编辑', function ($submenu) {
                $submenu->add('复制', ['click' => 'copy']);
                $submenu->add('粘贴', ['click' => 'paste']);
            })
            ->submenu('帮助', function ($submenu) {
                $submenu->add('关于', ['click' => 'about']);
            })
            ->setApplicationMenu();
    }
    
    protected function createTray()
    {
        Tray::setIcon(public_path() . '/static/img/icon.png')
            ->setTooltip(App::name())
            ->setMenu(function ($menu) {
                $menu->add('显示', ['click' => 'show']);
                $menu->add('退出', ['click' => 'quit']);
            })
            ->show();
    }
    
    protected function registerShortcuts()
    {
        GlobalShortcut::register('CommandOrControl+N', function () {
            Window::open('/window/index', [
                'title' => '新窗口',
                'width' => 600,
                'height' => 400,
            ]);
        });
        
        GlobalShortcut::register('CommandOrControl+Q', function () {
            App::quit();
        });
    }
    
    public function openNewWindow()
    {
        Window::open('/window/index', [
            'title' => '新窗口',
            'width' => 600,
            'height' => 400,
        ]);
        
        return json(['success' => true]);
    }
    
    public function sendNotification()
    {
        Notification::send('通知标题', '这是一条通知内容');
        
        return json(['success' => true]);
    }
    
    public function copy()
    {
        Clipboard::setText('复制的文本');
        
        return json(['success' => true]);
    }
    
    public function paste()
    {
        $text = Clipboard::text();
        
        return json(['success' => true, 'text' => $text]);
    }
    
    public function about()
    {
        Window::open('/window/about', [
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
```

### 视图

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$appName}</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <h1>欢迎使用 {$appName}</h1>
        <p>版本: {$appVersion}</p>
        
        <div class="buttons">
            <button onclick="openNewWindow()">打开新窗口</button>
            <button onclick="sendNotification()">发送通知</button>
            <button onclick="copyText()">复制文本</button>
            <button onclick="pasteText()">粘贴文本</button>
        </div>
        
        <div id="result"></div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```
