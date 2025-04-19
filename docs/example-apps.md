# 示例应用

NativePHP for ThinkPHP 提供了多个示例应用，用于展示如何使用各种功能构建桌面应用程序。本文档将介绍这些示例应用的功能和实现方式。

## 基础示例

基础示例展示了 NativePHP for ThinkPHP 的基本功能，包括窗口、菜单和通知。

### 功能

- 创建和管理窗口
- 创建应用菜单
- 发送系统通知
- 使用剪贴板
- 注册全局快捷键
- 使用系统托盘

### 实现方式

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

## 文件管理器示例

文件管理器示例展示了如何使用 NativePHP for ThinkPHP 的文件系统功能创建一个文件管理器应用。

### 功能

- 浏览文件和目录
- 创建、重命名和删除文件/目录
- 复制和移动文件/目录
- 查看文件内容
- 编辑文本文件
- 打开文件（使用系统默认应用）
- 显示文件属性

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\service\FileService;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Notification;

class File extends BaseController
{
    protected $fileService;
    
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    
    public function index()
    {
        $path = input('path', System::getHomePath());
        
        $files = $this->fileService->getFiles($path);
        
        return view('file/index', [
            'path' => $path,
            'files' => $files,
            'parentPath' => dirname($path),
        ]);
    }
    
    public function view()
    {
        $path = input('path');
        
        if (empty($path) || !FileSystem::exists($path)) {
            return redirect('/file/index');
        }
        
        $content = FileSystem::read($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        return view('file/view', [
            'path' => $path,
            'content' => $content,
            'extension' => $extension,
            'filename' => basename($path),
        ]);
    }
    
    public function edit()
    {
        $path = input('path');
        
        if (empty($path) || !FileSystem::exists($path)) {
            return redirect('/file/index');
        }
        
        $content = FileSystem::read($path);
        
        return view('file/edit', [
            'path' => $path,
            'content' => $content,
            'filename' => basename($path),
        ]);
    }
    
    public function save()
    {
        $path = input('path');
        $content = input('content');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        $result = FileSystem::write($path, $content);
        
        if ($result) {
            Notification::send('保存成功', '文件已成功保存');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '保存失败']);
        }
    }
    
    // 更多方法...
}
```

## 笔记应用示例

笔记应用示例展示了如何使用 NativePHP for ThinkPHP 创建一个支持 Markdown 的笔记应用。

### 功能

- 创建、编辑和删除笔记
- Markdown 编辑和预览
- 笔记分类和标签
- 笔记搜索
- 导入和导出笔记
- 自动保存
- 快捷键支持

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\Note;
use app\model\Category;
use app\model\Tag;
use app\service\NoteService;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\FileSystem;

class Note extends BaseController
{
    protected $noteService;
    
    public function __construct(NoteService $noteService)
    {
        $this->noteService = $noteService;
        
        // 注册全局快捷键
        $this->registerShortcuts();
    }
    
    protected function registerShortcuts()
    {
        // 新建笔记
        GlobalShortcut::register('CommandOrControl+N', function () {
            $this->create();
        });
        
        // 保存笔记
        GlobalShortcut::register('CommandOrControl+S', function () {
            $this->save();
        });
        
        // 搜索笔记
        GlobalShortcut::register('CommandOrControl+F', function () {
            $this->search();
        });
    }
    
    public function index()
    {
        $categoryId = input('category_id', 0);
        $tag = input('tag', '');
        $search = input('search', '');
        
        $notes = $this->noteService->getNotes($categoryId, $tag, $search);
        $categories = Category::select();
        $tags = Tag::select();
        
        return view('note/index', [
            'notes' => $notes,
            'categories' => $categories,
            'tags' => $tags,
            'currentCategory' => $categoryId,
            'currentTag' => $tag,
            'search' => $search,
        ]);
    }
    
    // 更多方法...
}
```

## 地图应用示例

地图应用示例展示了如何使用 NativePHP for ThinkPHP 的地理位置服务功能创建一个地图应用。

### 功能

- 显示当前位置
- 搜索地点
- 路线规划
- 保存收藏地点
- 测量距离
- 地图标记
- 离线地图

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\Location;
use app\service\MapService;
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class Map extends BaseController
{
    protected $mapService;
    
    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }
    
    public function index()
    {
        $locations = Location::where('type', 'favorite')->select();
        
        return view('map/index', [
            'locations' => $locations,
            'apiKey' => Settings::get('map.api_key', ''),
        ]);
    }
    
    public function getCurrentPosition()
    {
        $position = Geolocation::getCurrentPosition([
            'enableHighAccuracy' => true,
            'timeout' => 10000,
            'maximumAge' => 0,
        ]);
        
        if (!$position) {
            return json(['success' => false, 'message' => '无法获取当前位置']);
        }
        
        // 获取地址信息
        $address = Geolocation::getAddressFromCoordinates(
            $position['coords']['latitude'],
            $position['coords']['longitude']
        );
        
        return json([
            'success' => true,
            'position' => $position,
            'address' => $address,
        ]);
    }
    
    // 更多方法...
}
```

## 语音助手示例

语音助手示例展示了如何使用 NativePHP for ThinkPHP 的语音识别和合成功能创建一个语音助手应用。

### 功能

- 语音识别：将语音转换为文本
- 语音合成：将文本转换为语音
- 语音命令：通过语音控制应用
- 语音回答：通过语音回答问题
- 语音提醒：通过语音提醒用户

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\service\AssistantService;
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Notification;

class Voice extends BaseController
{
    protected $assistantService;
    
    public function __construct(AssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }
    
    public function index()
    {
        return view('voice/index');
    }
    
    public function startRecognition()
    {
        $result = Speech::startRecognition([
            'lang' => 'zh-CN',
            'continuous' => true,
            'interimResults' => true,
        ]);
        
        return json(['success' => $result]);
    }
    
    public function stopRecognition()
    {
        $result = Speech::stopRecognition();
        
        return json(['success' => $result]);
    }
    
    public function getRecognitionResult()
    {
        $result = Speech::getRecognitionResult();
        
        return json($result);
    }
    
    public function speak()
    {
        $text = input('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        $result = Speech::speak($text, [
            'lang' => 'zh-CN',
            'volume' => 1.0,
            'rate' => 1.0,
            'pitch' => 1.0,
        ]);
        
        return json(['success' => $result]);
    }
    
    // 更多方法...
}
```

## 设备管理器示例

设备管理器示例展示了如何使用 NativePHP for ThinkPHP 的设备管理功能创建一个设备管理器应用。

### 功能

- 扫描和管理蓝牙设备
- 扫描和管理 USB 设备
- 连接和配对设备
- 发送和接收数据
- 设备信息显示
- 设备历史记录

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\Device;
use app\service\DeviceService;
use Native\ThinkPHP\Facades\Device as DeviceFacade;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Logger;

class Bluetooth extends BaseController
{
    protected $deviceService;
    
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }
    
    public function index()
    {
        $devices = Device::where('type', 'bluetooth')->select();
        
        return view('bluetooth/index', [
            'devices' => $devices,
        ]);
    }
    
    public function scan()
    {
        // 扫描蓝牙设备
        $result = DeviceFacade::scanBluetoothDevices([
            'timeout' => 10000, // 10 秒
        ]);
        
        if ($result) {
            // 获取扫描结果
            $devices = DeviceFacade::getBluetoothDevices();
            
            // 保存设备到数据库
            foreach ($devices as $device) {
                $this->deviceService->saveBluetoothDevice($device);
            }
            
            Notification::send('扫描完成', '发现 ' . count($devices) . ' 个蓝牙设备');
            Logger::info('蓝牙设备扫描完成', ['count' => count($devices)]);
            
            return json(['success' => true, 'devices' => $devices]);
        } else {
            Logger::error('蓝牙设备扫描失败');
            return json(['success' => false, 'message' => '扫描失败']);
        }
    }
    
    // 更多方法...
}
```

## 推送通知客户端示例

推送通知客户端示例展示了如何使用 NativePHP for ThinkPHP 的推送通知服务功能创建一个推送通知客户端应用。

### 功能

- 注册设备接收推送通知
- 接收和显示推送通知
- 推送通知历史记录
- 推送通知设置
- 支持多种推送服务提供商（Firebase、APNS、极光推送）

### 实现方式

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\PushDevice;
use app\model\PushNotification as PushNotificationModel;
use app\service\PushService;
use Native\ThinkPHP\Facades\PushNotification;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Logger;

class Push extends BaseController
{
    protected $pushService;
    
    public function __construct(PushService $pushService)
    {
        $this->pushService = $pushService;
    }
    
    public function index()
    {
        $device = $this->pushService->getCurrentDevice();
        $notifications = PushNotificationModel::order('created_at', 'desc')->limit(10)->select();
        
        return view('push/index', [
            'device' => $device,
            'notifications' => $notifications,
            'provider' => Settings::get('push.provider', 'firebase'),
        ]);
    }
    
    public function registerDevice()
    {
        $token = input('token');
        $provider = input('provider', 'firebase');
        
        if (empty($token)) {
            return json(['success' => false, 'message' => '设备令牌不能为空']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($provider);
        
        // 注册设备
        $result = PushNotification::registerDevice($token);
        
        if ($result) {
            // 保存设备信息到数据库
            $device = PushDevice::where('token', $token)->find();
            
            if (!$device) {
                $device = new PushDevice;
                $device->token = $token;
                $device->provider = $provider;
                $device->platform = $this->getPlatform();
                $device->created_at = date('Y-m-d H:i:s');
            }
            
            $device->updated_at = date('Y-m-d H:i:s');
            $device->save();
            
            // 保存设置
            Settings::set('push.device_token', $token);
            Settings::set('push.provider', $provider);
            
            Notification::send('注册成功', '设备已成功注册，可以接收推送通知');
            Logger::info('设备注册成功', ['token' => $token, 'provider' => $provider]);
            
            return json(['success' => true, 'device' => $device]);
        } else {
            Logger::error('设备注册失败', ['token' => $token, 'provider' => $provider]);
            return json(['success' => false, 'message' => '设备注册失败']);
        }
    }
    
    // 更多方法...
}
```

## 运行示例应用

要运行示例应用，请按照以下步骤操作：

1. 克隆 NativePHP for ThinkPHP 仓库：

```bash
git clone https://github.com/nativephp/thinkphp.git
cd thinkphp/examples/[example-name]
```

2. 安装依赖：

```bash
composer install
```

3. 启动应用：

```bash
php think native:serve
```

4. 构建应用：

```bash
php think native:build
```

## 创建自己的应用

你可以使用这些示例应用作为起点，创建自己的 NativePHP for ThinkPHP 应用。只需要按照以下步骤操作：

1. 创建一个新的 ThinkPHP 项目：

```bash
composer create-project topthink/think your-app
cd your-app
```

2. 安装 NativePHP for ThinkPHP：

```bash
composer require nativephp/thinkphp
```

3. 初始化 NativePHP 应用：

```bash
php think native:init
```

4. 修改配置文件 `config/native.php`，根据需要自定义应用。

5. 创建控制器和视图，实现应用功能。

6. 启动应用：

```bash
php think native:serve
```

7. 构建应用：

```bash
php think native:build
```
