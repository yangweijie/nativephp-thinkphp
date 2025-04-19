# 桌面快捷方式功能

NativePHP for ThinkPHP 提供了桌面快捷方式功能，允许你的桌面应用程序创建和管理桌面快捷方式，以及设置开机自启动。本文档将介绍如何使用这些功能。

## 基本概念

桌面快捷方式功能允许你的应用程序创建和管理桌面快捷方式、开始菜单快捷方式和自定义快捷方式，以及设置开机自启动。这些功能可以提高应用程序的可访问性和用户体验。

## 使用 Shortcut Facade

NativePHP for ThinkPHP 提供了 `Shortcut` Facade，用于创建和管理桌面快捷方式。

### 创建桌面快捷方式

```php
use Native\ThinkPHP\Facades\Shortcut;

// 创建桌面快捷方式
$result = Shortcut::createDesktopShortcut([
    'arguments' => '--arg1=value1 --arg2=value2',
    'description' => '这是一个桌面快捷方式',
    'icon' => '/path/to/icon.ico',
    'iconIndex' => 0,
    'appUserModelId' => 'com.example.app',
]);

if ($result) {
    // 快捷方式创建成功
} else {
    // 快捷方式创建失败
}
```

### 创建开始菜单快捷方式

```php
// 创建开始菜单快捷方式
$result = Shortcut::createStartMenuShortcut([
    'arguments' => '--arg1=value1 --arg2=value2',
    'description' => '这是一个开始菜单快捷方式',
    'icon' => '/path/to/icon.ico',
    'iconIndex' => 0,
    'appUserModelId' => 'com.example.app',
]);

if ($result) {
    // 快捷方式创建成功
} else {
    // 快捷方式创建失败
}
```

### 创建自定义快捷方式

```php
// 创建自定义快捷方式
$result = Shortcut::createShortcut('/path/to/shortcut.lnk', [
    'arguments' => '--arg1=value1 --arg2=value2',
    'description' => '这是一个自定义快捷方式',
    'icon' => '/path/to/icon.ico',
    'iconIndex' => 0,
    'appUserModelId' => 'com.example.app',
]);

if ($result) {
    // 快捷方式创建成功
} else {
    // 快捷方式创建失败
}
```

### 检查快捷方式是否存在

```php
// 检查桌面快捷方式是否存在
$exists = Shortcut::existsOnDesktop();

// 检查开始菜单快捷方式是否存在
$exists = Shortcut::existsInStartMenu();

// 检查自定义快捷方式是否存在
$exists = Shortcut::exists('/path/to/shortcut.lnk');
```

### 删除快捷方式

```php
// 删除桌面快捷方式
$result = Shortcut::removeFromDesktop();

// 删除开始菜单快捷方式
$result = Shortcut::removeFromStartMenu();

// 删除自定义快捷方式
$result = Shortcut::remove('/path/to/shortcut.lnk');
```

### 设置开机自启动

```php
// 设置开机自启动
$result = Shortcut::setLoginItemSettings(true, [
    'openAtLogin' => true,
    'openAsHidden' => false,
    'path' => '/path/to/app',
    'args' => ['--arg1=value1', '--arg2=value2'],
]);

// 取消开机自启动
$result = Shortcut::setLoginItemSettings(false);
```

### 获取开机自启动设置

```php
// 获取开机自启动设置
$settings = Shortcut::getLoginItemSettings();

// 获取指定应用的开机自启动设置
$settings = Shortcut::getLoginItemSettings([
    'path' => '/path/to/app',
]);
```

### 获取路径信息

```php
// 获取桌面路径
$desktopPath = Shortcut::getDesktopPath();

// 获取开始菜单路径
$startMenuPath = Shortcut::getStartMenuPath();

// 获取应用程序路径
$applicationPath = Shortcut::getApplicationPath();

// 获取应用程序名称
$applicationName = Shortcut::getApplicationName();
```

## 快捷方式选项

创建快捷方式时，可以指定以下选项：

- `arguments`：快捷方式的命令行参数
- `description`：快捷方式的描述
- `icon`：快捷方式的图标路径
- `iconIndex`：图标索引（如果图标文件包含多个图标）
- `appUserModelId`：应用程序用户模型 ID（Windows 特有）

## 开机自启动选项

设置开机自启动时，可以指定以下选项：

- `openAtLogin`：是否在登录时启动应用程序
- `openAsHidden`：是否以隐藏方式启动应用程序
- `path`：应用程序路径
- `args`：应用程序启动参数

## 平台差异

桌面快捷方式功能在不同平台上的行为可能有所不同：

### Windows

- 支持创建 `.lnk` 格式的快捷方式
- 支持设置快捷方式图标、描述和命令行参数
- 支持设置开机自启动

### macOS

- 支持创建 `.app` 格式的快捷方式
- 支持设置快捷方式图标
- 支持设置开机自启动

### Linux

- 支持创建 `.desktop` 格式的快捷方式
- 支持设置快捷方式图标、描述和命令行参数
- 支持设置开机自启动

## 完整示例

下面是一个完整的桌面快捷方式示例：

```php
use Native\ThinkPHP\Facades\Shortcut;
use Native\ThinkPHP\Facades\Notification;

// 检查桌面快捷方式是否存在
if (!Shortcut::existsOnDesktop()) {
    // 创建桌面快捷方式
    $result = Shortcut::createDesktopShortcut([
        'description' => '这是一个桌面快捷方式',
    ]);
    
    if ($result) {
        Notification::send('快捷方式', '桌面快捷方式创建成功');
    } else {
        Notification::send('快捷方式', '桌面快捷方式创建失败');
    }
}

// 检查开始菜单快捷方式是否存在
if (!Shortcut::existsInStartMenu()) {
    // 创建开始菜单快捷方式
    $result = Shortcut::createStartMenuShortcut([
        'description' => '这是一个开始菜单快捷方式',
    ]);
    
    if ($result) {
        Notification::send('快捷方式', '开始菜单快捷方式创建成功');
    } else {
        Notification::send('快捷方式', '开始菜单快捷方式创建失败');
    }
}

// 设置开机自启动
$result = Shortcut::setLoginItemSettings(true, [
    'openAtLogin' => true,
    'openAsHidden' => false,
]);

if ($result) {
    Notification::send('快捷方式', '开机自启动设置成功');
} else {
    Notification::send('快捷方式', '开机自启动设置失败');
}

// 获取开机自启动设置
$settings = Shortcut::getLoginItemSettings();

// 获取桌面路径
$desktopPath = Shortcut::getDesktopPath();

// 获取开始菜单路径
$startMenuPath = Shortcut::getStartMenuPath();

// 获取应用程序路径
$applicationPath = Shortcut::getApplicationPath();

// 获取应用程序名称
$applicationName = Shortcut::getApplicationName();
```

## 最佳实践

1. **检查快捷方式是否存在**：在创建快捷方式之前，始终检查快捷方式是否已经存在，以避免创建重复的快捷方式。

2. **提供有用的描述**：为快捷方式提供有用的描述，以便用户了解快捷方式的用途。

3. **提供自定义图标**：为快捷方式提供自定义图标，以便用户更容易识别快捷方式。

4. **尊重用户选择**：在设置开机自启动之前，询问用户是否希望应用程序在开机时自动启动。

5. **提供取消选项**：提供取消开机自启动的选项，以便用户可以随时更改设置。

## 故障排除

### 快捷方式创建失败

- 确保应用程序有足够的权限创建快捷方式
- 确保指定的图标文件存在并且格式正确
- 确保指定的路径存在并且可写

### 开机自启动设置失败

- 确保应用程序有足够的权限设置开机自启动
- 确保指定的应用程序路径存在并且可执行
- 确保指定的参数格式正确
