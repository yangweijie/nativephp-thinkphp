# 常见问题解答

本文档收集了使用 NativePHP for ThinkPHP 时的常见问题和解答。

## 基本问题

### 什么是 NativePHP for ThinkPHP？

NativePHP for ThinkPHP 是一个用于构建桌面应用程序的 ThinkPHP 扩展包。它允许你使用熟悉的 ThinkPHP 框架和 PHP 语言构建跨平台的桌面应用程序。

### NativePHP for ThinkPHP 支持哪些平台？

NativePHP for ThinkPHP 支持 Windows、macOS 和 Linux 平台。

### NativePHP for ThinkPHP 是如何工作的？

NativePHP for ThinkPHP 使用 Electron 作为底层运行时，将你的 ThinkPHP 应用包装在一个桌面应用程序中。它提供了一组 API，用于与桌面环境交互，如窗口管理、菜单管理、通知管理等。

### NativePHP for ThinkPHP 与 NativePHP for Laravel 有什么关系？

NativePHP for ThinkPHP 是 NativePHP for Laravel 的移植版本，它将 NativePHP 的功能移植到 ThinkPHP 框架中。两者提供类似的功能，但针对不同的 PHP 框架。

## 安装和配置

### 如何安装 NativePHP for ThinkPHP？

使用 Composer 安装 NativePHP for ThinkPHP：

```bash
composer require nativephp/thinkphp
```

然后初始化 NativePHP 应用：

```bash
php think native:init
```

### 安装时出现 "Could not find package nativephp/thinkphp" 错误怎么办？

确保你的 Composer 配置正确，并且包已发布到 Packagist。如果问题仍然存在，可以尝试更新 Composer：

```bash
composer self-update
```

### 如何配置 NativePHP for ThinkPHP？

NativePHP for ThinkPHP 的配置文件位于 `config/native.php`。你可以根据需要修改这个文件。

### 如何更改应用名称和版本？

在 `config/native.php` 文件中修改 `name` 和 `version` 配置项：

```php
return [
    'name' => '你的应用名称',
    'version' => '1.0.0',
    // 其他配置...
];
```

## 开发问题

### 如何启动 NativePHP 应用？

使用以下命令启动 NativePHP 应用：

```bash
php think native:serve
```

### 如何构建 NativePHP 应用？

使用以下命令构建 NativePHP 应用：

```bash
php think native:build
```

### 如何为特定平台构建应用？

使用 `--platform` 选项指定要构建的平台：

```bash
php think native:build --platform=windows
```

支持的平台有 `windows`、`macos` 和 `linux`。

### 如何为特定架构构建应用？

使用 `--arch` 选项指定要构建的架构：

```bash
php think native:build --arch=x64
```

支持的架构有 `x64` 和 `arm64`。

### 如何调试 NativePHP 应用？

在开发模式下，你可以使用 Electron 的开发者工具进行调试。按下 `F12` 或 `Ctrl+Shift+I`（Windows/Linux）或 `Cmd+Opt+I`（macOS）打开开发者工具。

你也可以在 `config/native.php` 文件中启用开发者工具：

```php
return [
    // 其他配置...
    'developer' => [
        'show_devtools' => true,
    ],
];
```

### 如何访问本地文件系统？

使用 `FileSystem` Facade 访问本地文件系统：

```php
use Native\ThinkPHP\Facades\FileSystem;

// 读取文件
$content = FileSystem::read('/path/to/file.txt');

// 写入文件
FileSystem::write('/path/to/file.txt', '文件内容');
```

### 如何发送系统通知？

使用 `Notification` Facade 发送系统通知：

```php
use Native\ThinkPHP\Facades\Notification;

Notification::send('通知标题', '通知内容');
```

### 如何创建应用菜单？

使用 `Menu` Facade 创建应用菜单：

```php
use Native\ThinkPHP\Facades\Menu;

Menu::create()
    ->add('文件', [
        ['label' => '新建', 'click' => 'createNew'],
        ['label' => '退出', 'click' => 'quit'],
    ])
    ->submenu('编辑', function ($submenu) {
        $submenu->add('复制', ['click' => 'copy']);
        $submenu->add('粘贴', ['click' => 'paste']);
    })
    ->setApplicationMenu();
```

### 如何注册全局快捷键？

使用 `GlobalShortcut` Facade 注册全局快捷键：

```php
use Native\ThinkPHP\Facades\GlobalShortcut;

GlobalShortcut::register('CommandOrControl+N', function () {
    // 快捷键被触发时执行的代码
});
```

### 如何创建系统托盘图标？

使用 `Tray` Facade 创建系统托盘图标：

```php
use Native\ThinkPHP\Facades\Tray;

Tray::setIcon('/path/to/icon.png')
    ->setTooltip('应用名称')
    ->setMenu(function ($menu) {
        $menu->add('显示', ['click' => 'show']);
        $menu->add('退出', ['click' => 'quit']);
    })
    ->show();
```

## 构建和发布

### 构建应用时出现 "Error: Cannot find module 'electron-builder'" 错误怎么办？

确保你已安装 electron-builder：

```bash
npm install -g electron-builder
```

### 如何为 Windows 签名应用？

在 `config/native.php` 文件中配置签名信息：

```php
return [
    // 其他配置...
    'build' => [
        'windows' => [
            'sign' => [
                'certificate' => '/path/to/certificate.pfx',
                'password' => 'certificate-password',
            ],
        ],
    ],
];
```

### 如何为 macOS 签名应用？

在 `config/native.php` 文件中配置签名信息：

```php
return [
    // 其他配置...
    'build' => [
        'mac' => [
            'sign' => [
                'identity' => 'Developer ID Application: Your Name (XXXXXXXXXX)',
            ],
        ],
    ],
];
```

### 如何自动更新应用？

在 `config/native.php` 文件中配置自动更新：

```php
return [
    // 其他配置...
    'updater' => [
        'enabled' => true,
        'check_on_startup' => true,
        'check_interval' => 3600,
        'server_url' => 'https://your-update-server.com',
    ],
];
```

然后使用 `Updater` Facade 检查更新：

```php
use Native\ThinkPHP\Facades\Updater;

// 检查更新
$updateInfo = Updater::check();

// 下载并安装更新
Updater::update();
```

## 高级问题

### 如何使用语音识别和合成功能？

使用 `Speech` Facade 进行语音识别和合成：

```php
use Native\ThinkPHP\Facades\Speech;

// 开始语音识别
Speech::startRecognition([
    'lang' => 'zh-CN',
    'continuous' => true,
    'interimResults' => true,
]);

// 语音合成
Speech::speak('你好，欢迎使用 NativePHP');
```

### 如何使用设备管理功能？

使用 `Device` Facade 管理设备：

```php
use Native\ThinkPHP\Facades\Device;

// 获取蓝牙设备列表
$devices = Device::getBluetoothDevices();

// 扫描蓝牙设备
Device::scanBluetoothDevices();
```

### 如何使用地理位置服务？

使用 `Geolocation` Facade 获取地理位置信息：

```php
use Native\ThinkPHP\Facades\Geolocation;

// 获取当前位置
$position = Geolocation::getCurrentPosition();

// 计算两点之间的距离
$distance = Geolocation::calculateDistance(39.9, 116.3, 31.2, 121.4, 'km');
```

### 如何使用推送通知服务？

使用 `PushNotification` Facade 发送和接收推送通知：

```php
use Native\ThinkPHP\Facades\PushNotification;

// 设置推送服务提供商
PushNotification::setProvider('firebase');

// 注册设备
PushNotification::registerDevice('device-token');

// 发送推送通知
PushNotification::send('device-token', '标题', '内容');
```

### 如何使用日志工具？

使用 `Logger` Facade 记录日志：

```php
use Native\ThinkPHP\Facades\Logger;

// 记录信息
Logger::info('用户登录', ['user_id' => 1]);

// 记录错误
Logger::error('系统错误', ['error_code' => 500]);
```

### 如何使用缓存工具？

使用 `Cache` Facade 管理缓存：

```php
use Native\ThinkPHP\Facades\Cache;

// 设置缓存
Cache::set('key', 'value', 3600);

// 获取缓存
$value = Cache::get('key');
```

### 如何使用事件工具？

使用 `Event` Facade 管理事件：

```php
use Native\ThinkPHP\Facades\Event;

// 添加事件监听器
Event::on('app.start', function () {
    // 应用启动时执行的代码
});

// 触发事件
Event::emit('app.start');
```

### 如何使用配置工具？

使用 `Config` Facade 管理配置：

```php
use Native\ThinkPHP\Facades\Config;

// 获取配置值
$value = Config::get('app.theme');

// 设置配置值
Config::set('app.theme', 'dark');
```

## 其他问题

### 如何获取帮助？

如果你遇到问题，可以通过以下方式获取帮助：

- [GitHub Issues](https://github.com/nativephp/thinkphp/issues)
- [ThinkPHP 社区](https://www.thinkphp.cn/topic/index/id/nativephp.html)
- [NativePHP for ThinkPHP 文档](https://nativephp.thinkphp.cn)

### 如何报告 Bug？

如果你发现了 Bug，请通过 [GitHub Issues](https://github.com/nativephp/thinkphp/issues) 报告。在报告 Bug 时，请提供详细的信息，包括重现步骤、预期行为和实际行为、相关日志和错误信息等。

### 如何贡献代码？

如果你想为项目贡献代码，请参阅 [贡献指南](contributing.md)。
