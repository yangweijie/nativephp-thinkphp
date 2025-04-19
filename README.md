# NativePHP for ThinkPHP

[![Tests](https://github.com/nativephp/thinkphp/actions/workflows/tests.yml/badge.svg)](https://github.com/nativephp/thinkphp/actions/workflows/tests.yml)
[![Code Quality](https://github.com/nativephp/thinkphp/actions/workflows/code-quality.yml/badge.svg)](https://github.com/nativephp/thinkphp/actions/workflows/code-quality.yml)
[![Latest Stable Version](https://poser.pugx.org/nativephp/thinkphp/v/stable)](https://packagist.org/packages/nativephp/thinkphp)
[![Total Downloads](https://poser.pugx.org/nativephp/thinkphp/downloads)](https://packagist.org/packages/nativephp/thinkphp)
[![License](https://poser.pugx.org/nativephp/thinkphp/license)](https://packagist.org/packages/nativephp/thinkphp)

ThinkPHP 版本的 NativePHP 框架，用于构建桌面应用程序。

## 简介

NativePHP 是一个用于构建桌面应用程序的 PHP 框架。本项目是 NativePHP 的 ThinkPHP 版本，允许 ThinkPHP 开发者使用熟悉的工具和技术构建桌面应用程序。

## 移植说明

本项目是将 [nativephp/laravel](https://github.com/nativephp/laravel) 包移植到 ThinkPHP 框架的版本。移植工作包括：

1. 适配 ThinkPHP 的服务容器和依赖注入系统
2. 适配 ThinkPHP 的命令行系统
3. 适配 ThinkPHP 的配置系统
4. 适配 ThinkPHP 的事件系统
5. 适配 ThinkPHP 的 Facade 机制

移植过程中保留了原有 NativePHP 的所有功能，包括窗口管理、菜单、通知、全局快捷键、文件系统、数据库、设置、HTTP 请求等。

## 使用说明

### 安装

使用 Composer 安装：

```bash
composer require nativephp/thinkphp
```

### 初始化

在项目中运行以下命令初始化 NativePHP：

```bash
php think native:init
```

这将创建必要的配置文件和目录结构。

### 启动应用

运行以下命令启动应用：

```bash
php think native:serve
```

### 构建应用

运行以下命令构建桌面应用：

```bash
php think native:build
```

### 使用功能

#### 窗口管理

```php
use Native\ThinkPHP\Facades\Window;

// 创建新窗口
Window::create()
    ->width(800)
    ->height(600)
    ->title('我的应用')
    ->url('/welcome')
    ->show();

// 关闭窗口
Window::close();
```

#### 菜单

```php
use Native\ThinkPHP\Facades\Menu;

// 创建应用菜单
Menu::create()
    ->submenu('文件', function ($menu) {
        $menu->add('新建', function () {
            // 处理新建操作
        })
        ->separator()
        ->add('退出', function () {
            // 退出应用
        });
    })
    ->setApplicationMenu();
```

#### 通知

```php
use Native\ThinkPHP\Facades\Notification;

// 发送通知
Notification::title('标题')
    ->body('这是一条通知')
    ->show();
```

#### 文件系统

```php
use Native\ThinkPHP\Facades\FileSystem;

// 读取文件
$content = FileSystem::read('/path/to/file.txt');

// 写入文件
FileSystem::write('/path/to/file.txt', '文件内容');
```

#### 对话框

```php
use Native\ThinkPHP\Facades\Dialog;

// 显示消息框
Dialog::message('这是一条消息');

// 显示确认框
$result = Dialog::confirm('确定要执行这个操作吗？');

// 显示打开文件对话框
$file = Dialog::openFile();
```

#### HTTP 请求

```php
use Native\ThinkPHP\Facades\Http;

// 发送 GET 请求
$response = Http::get('https://api.example.com/users');

// 发送 POST 请求
$response = Http::post('https://api.example.com/users', [
    'name' => 'John',
    'email' => 'john@example.com',
]);
```

## 安装

使用 Composer 安装：

```bash
composer require nativephp/thinkphp
```

## 配置

安装完成后，运行以下命令初始化 NativePHP 应用：

```bash
php think native:init
```

这将创建必要的配置文件和示例控制器。

## 使用

### 启动应用

运行以下命令启动 NativePHP 应用：

```bash
php think native:serve
```

这将启动 ThinkPHP 服务器和 Electron 应用程序。

### 构建应用

运行以下命令构建 NativePHP 桌面应用：

```bash
php think native:build
```

这将创建一个可分发的桌面应用程序，输出目录为 `runtime/native/build/dist/`。

### 使用 Facades

NativePHP 提供了多个 Facades 以便于使用：

```php
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Clipboard;
use Native\ThinkPHP\Facades\GlobalShortcut;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Screen;
use Native\ThinkPHP\Facades\Updater;
use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Process;
use Native\ThinkPHP\Facades\Printer;
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Device;
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\PushNotification;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Facades\Cache;
use Native\ThinkPHP\Facades\Event;
use Native\ThinkPHP\Facades\Config;

// 获取应用信息
$appName = App::name();
$appVersion = App::version();

// 发送通知
Notification::send('标题', '内容');

// 打开新窗口
Window::open('/path/to/page', [
    'title' => '新窗口',
    'width' => 800,
    'height' => 600,
]);

// 创建菜单
Menu::create()
    ->add('文件')
    ->submenu('编辑', function ($submenu) {
        $submenu->add('复制');
        $submenu->add('粘贴');
    })
    ->setApplicationMenu();

// 使用剪贴板
Clipboard::setText('复制的文本');
$text = Clipboard::text();

// 注册全局快捷键
GlobalShortcut::register('CommandOrControl+Shift+G', function () {
    // 快捷键处理逻辑
});

// 使用系统托盘
Tray::setIcon('path/to/icon.png')
    ->setTooltip('应用名称')
    ->setMenu(function ($menu) {
        $menu->add('显示', function () {
            Window::show();
        });
        $menu->add('退出', function () {
            App::quit();
        });
    })
    ->show();

// 使用对话框
$filePath = Dialog::openFile([
    'title' => '选择文件',
    'filters' => [
        ['name' => '文本文件', 'extensions' => ['txt']],
        ['name' => '所有文件', 'extensions' => ['*']]
    ]
]);

Dialog::info('信息', ['detail' => '这是一条信息']);

// 使用文件系统
$content = FileSystem::read('path/to/file.txt');
FileSystem::write('path/to/file.txt', '文件内容');

// 获取系统信息
$os = System::getOS();
$osVersion = System::getOSVersion();
$homePath = System::getHomePath();
System::openExternal('https://example.com');

// 屏幕捕获
$screenshotPath = Screen::captureScreenshot();
Screen::startRecording();
$recordingPath = Screen::stopRecording();

// 自动更新
Updater::setServerUrl('https://updates.example.com');
$updateInfo = Updater::check();
if ($updateInfo && $updateInfo['canUpdate']) {
    Updater::download();
    Updater::install();
}

// HTTP 请求
$response = Http::get('https://api.example.com/users');
$response = Http::post('https://api.example.com/users', [
    'name' => '用户名',
    'email' => 'user@example.com',
]);
Http::download('https://example.com/file.zip', 'path/to/save/file.zip');

// 使用数据库
Database::setDatabasePath('path/to/database.sqlite');
Database::createTable('users', [
    'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
    'name' => 'TEXT NOT NULL',
    'email' => 'TEXT NOT NULL',
]);
$userId = Database::insert('users', [
    'name' => '用户名',
    'email' => 'user@example.com',
]);
$users = Database::select('users', '*', 'id > ?', [0]);

// 使用设置
Settings::set('theme', 'dark');
Settings::set('language', 'zh-CN');
$theme = Settings::get('theme', 'light');
if (Settings::has('language')) {
    $language = Settings::get('language');
}
Settings::export('path/to/settings.json');

// 运行进程
$processId = Process::run('ls -la');
$output = Process::getOutput($processId);
if (Process::isRunning($processId)) {
    Process::kill($processId);
}
$thinkProcessId = Process::runThink('cache:clear');

// 打印功能
Printer::printHtml('<h1>测试打印</h1><p>这是一个测试文档</p>');
$printers = Printer::getPrinters();
$defaultPrinter = Printer::getDefaultPrinter();
Printer::printToPdf('<h1>测试 PDF</h1>', 'path/to/output.pdf');

// 语音识别和合成
Speech::startRecognition();
$result = Speech::getRecognitionResult();
Speech::stopRecognition();
Speech::speak('你好，欢迎使用 NativePHP');
$voices = Speech::getVoices();
Speech::textToAudio('你好，欢迎使用 NativePHP', 'path/to/audio.mp3');

// 设备管理
Device::scanBluetoothDevices();
$bluetoothDevices = Device::getBluetoothDevices();
Device::connectBluetoothDevice('00:11:22:33:44:55');
Device::sendDataToBluetoothDevice('00:11:22:33:44:55', '测试数据');
$usbDevices = Device::getUsbDevices();
Device::openUsbDevice('device-id');

// 地理位置服务
$position = Geolocation::getCurrentPosition();
Geolocation::watchPosition();
Geolocation::clearWatch();
$distance = Geolocation::calculateDistance(39.9, 116.3, 31.2, 121.4);
$address = Geolocation::getAddressFromCoordinates(39.9, 116.3);
$coordinates = Geolocation::getCoordinatesFromAddress('北京市海淀区中关村');

// 推送通知服务
PushNotification::setProvider('firebase');
PushNotification::setConfig([
    'firebase' => [
        'server_key' => 'your-server-key',
    ],
]);
PushNotification::registerDevice('device-token');
PushNotification::send('device-token', '标题', '内容', [
    'url' => 'https://example.com',
]);

// 日志工具
Logger::info('应用启动');
Logger::error('发生错误', ['error' => '连接失败']);
Logger::setLogFile('path/to/custom.log');
Logger::setLevel('warning'); // 只记录 warning 及以上级别的日志
$logContent = Logger::get(100); // 获取最近 100 行日志
Logger::rotate(); // 轮换日志文件

// 缓存工具
Cache::set('key', 'value', 3600); // 缓存一小时
$value = Cache::get('key', 'default');
Cache::delete('key');
$data = Cache::remember('api_data', function () {
    // 获取数据的复杂逻辑
    return Http::get('https://api.example.com/data');
}, 3600);
Cache::clear(); // 清空所有缓存

// 事件工具
Event::on('app.start', function () {
    Logger::info('应用已启动');
});
Event::once('window.close', function () {
    Logger::info('窗口已关闭');
});
Event::emit('app.start'); // 触发事件
Event::off('app.start'); // 移除事件监听器

// 配置工具
Config::set('app.theme', 'dark');
Config::set('app.language', 'zh-CN');
$theme = Config::get('app.theme', 'light');
Config::export('path/to/config.json'); // 导出配置
Config::import('path/to/config.json'); // 导入配置
```

## 配置文件

配置文件位于 `config/native.php`，包含以下配置项：

- `name`：应用名称
- `app_id`：应用 ID
- `version`：应用版本
- `dev_server`：开发服务器配置
- `window`：窗口配置
- `menus`：菜单配置
- `hotkeys`：热键配置
- `updater`：自动更新配置
- `developer`：开发者工具配置
- `screen`：屏幕捕获配置
- `http`：HTTP 请求配置
- `database`：数据库配置
- `settings`：设置配置
- `process`：进程配置
- `printer`：打印配置
- `speech`：语音识别和合成配置
- `device`：设备管理配置
- `geolocation`：地理位置服务配置
- `push`：推送通知服务配置
- `logger`：日志工具配置
- `cache`：缓存工具配置
- `event`：事件工具配置
- `config`：配置工具配置

## 开发

### 运行测试

```bash
composer test
```

### 运行代码质量检查

```bash
composer lint
```

### 修复代码格式

```bash
composer fix
```

## 文档

详细文档请访问 [NativePHP for ThinkPHP 文档](https://nativephp.github.io/thinkphp/).

## 示例应用

在 [examples](examples) 目录下提供了多个示例应用，包括：

1. [基础示例](examples/basic) - 展示基本的窗口、菜单和通知功能
2. [文件管理器](examples/file-manager) - 一个简单的文件管理器应用
3. [笔记应用](examples/note-app) - 一个支持 Markdown 的笔记应用
4. [语音助手](examples/voice-assistant) - 一个使用语音识别和合成的语音助手应用
5. [设备管理器](examples/device-manager) - 一个用于管理蓝牙和 USB 设备的应用
6. [地图应用](examples/map-app) - 一个使用地理位置服务的地图应用
7. [推送通知客户端](examples/push-client) - 一个接收推送通知的客户端应用
8. [任务管理器](examples/task-manager) - 一个简单的任务管理器应用
9. [桌面聊天客户端](examples/chat-client) - 一个功能完整的桌面聊天客户端应用
10. [代码编辑器](examples/code-editor) - 一个轻量级的代码编辑器应用

## 贡献

欢迎提交 Pull Request 或创建 Issue。

## 许可证

MIT
