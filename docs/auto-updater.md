# 自动更新功能

NativePHP for ThinkPHP 提供了自动更新功能，允许你的桌面应用程序自动检查、下载和安装更新。本文档将介绍如何使用这些功能。

## 基本概念

自动更新功能使用 Electron 的 `autoUpdater` 模块实现，它允许应用程序自动检查更新、下载更新并安装更新。自动更新功能需要一个更新服务器，该服务器应该返回符合 Electron 自动更新格式的 JSON 数据。

## 配置更新服务器

在使用自动更新功能之前，你需要配置更新服务器。更新服务器应该返回符合 Electron 自动更新格式的 JSON 数据，例如：

```json
{
  "version": "1.0.1",
  "releaseDate": "2023-01-01",
  "url": "https://example.com/updates/app-1.0.1.zip",
  "releaseNotes": "这是一个新版本，修复了一些 bug。"
}
```

你可以在 `config/native.php` 文件中配置更新服务器：

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

## 使用 AutoUpdater Facade

NativePHP for ThinkPHP 提供了 `AutoUpdater` Facade，用于与自动更新功能进行交互。

### 设置更新服务器 URL

```php
use Native\ThinkPHP\Facades\AutoUpdater;

// 设置更新服务器 URL
AutoUpdater::setFeedURL('https://your-update-server.com');
```

### 设置自动下载更新

```php
// 设置是否自动下载更新
AutoUpdater::setAutoDownload(true);
```

### 设置自动安装更新

```php
// 设置是否自动安装更新
AutoUpdater::setAutoInstall(false);
```

### 设置是否允许预发布版本

```php
// 设置是否允许预发布版本
AutoUpdater::setAllowPrerelease(false);
```

### 检查更新

```php
// 检查更新
$checking = AutoUpdater::checkForUpdates();

if ($checking) {
    // 正在检查更新
} else {
    // 检查更新失败
}
```

### 下载更新

```php
// 下载更新
$downloading = AutoUpdater::downloadUpdate();

if ($downloading) {
    // 正在下载更新
} else {
    // 下载更新失败
}
```

### 安装更新

```php
// 安装更新
$installing = AutoUpdater::installUpdate();

if ($installing) {
    // 正在安装更新
} else {
    // 安装更新失败
}
```

### 获取当前版本

```php
// 获取当前版本
$currentVersion = AutoUpdater::getCurrentVersion();
```

### 获取最新版本

```php
// 获取最新版本
$latestVersion = AutoUpdater::getLatestVersion();
```

### 获取更新信息

```php
// 获取更新信息
$updateInfo = AutoUpdater::getUpdateInfo();
```

### 取消更新下载

```php
// 取消更新下载
$success = AutoUpdater::cancelDownload();

if ($success) {
    // 取消下载成功
} else {
    // 取消下载失败
}
```

### 重启应用并安装更新

```php
// 重启应用并安装更新
$success = AutoUpdater::quitAndInstall();

if ($success) {
    // 重启应用并安装更新成功
} else {
    // 重启应用并安装更新失败
}
```

## 监听更新事件

NativePHP for ThinkPHP 提供了一系列事件，用于监听自动更新过程中的各种事件。

### 监听更新检查事件

```php
// 监听更新检查事件
AutoUpdater::onCheckingForUpdate(function () {
    // 正在检查更新时执行的代码
});
```

### 监听更新可用事件

```php
// 监听更新可用事件
AutoUpdater::onUpdateAvailable(function ($info) {
    // 有更新可用时执行的代码
    // $info 包含更新信息，如版本号、发布日期、发布说明等
});
```

### 监听更新不可用事件

```php
// 监听更新不可用事件
AutoUpdater::onUpdateNotAvailable(function () {
    // 没有更新可用时执行的代码
});
```

### 监听更新下载进度事件

```php
// 监听更新下载进度事件
AutoUpdater::onDownloadProgress(function ($progress) {
    // 更新下载进度变化时执行的代码
    // $progress 包含下载进度信息，如已下载字节数、总字节数、下载百分比等
});
```

### 监听更新下载完成事件

```php
// 监听更新下载完成事件
AutoUpdater::onUpdateDownloaded(function ($info) {
    // 更新下载完成时执行的代码
    // $info 包含更新信息，如版本号、发布日期、发布说明等
});
```

### 监听更新错误事件

```php
// 监听更新错误事件
AutoUpdater::onError(function ($error) {
    // 更新出错时执行的代码
    // $error 包含错误信息
});
```

## 完整示例

下面是一个完整的自动更新示例：

```php
use Native\ThinkPHP\Facades\AutoUpdater;
use Native\ThinkPHP\Facades\Notification;

// 设置更新服务器 URL
AutoUpdater::setFeedURL('https://your-update-server.com');

// 设置是否自动下载更新
AutoUpdater::setAutoDownload(true);

// 设置是否自动安装更新
AutoUpdater::setAutoInstall(false);

// 设置是否允许预发布版本
AutoUpdater::setAllowPrerelease(false);

// 监听更新检查事件
AutoUpdater::onCheckingForUpdate(function () {
    Notification::send('更新', '正在检查更新...');
});

// 监听更新可用事件
AutoUpdater::onUpdateAvailable(function ($info) {
    Notification::send('更新', "发现新版本：{$info['version']}");
});

// 监听更新不可用事件
AutoUpdater::onUpdateNotAvailable(function () {
    Notification::send('更新', '当前已是最新版本');
});

// 监听更新下载进度事件
AutoUpdater::onDownloadProgress(function ($progress) {
    // 更新下载进度
    // 这里可以更新进度条或显示下载百分比
});

// 监听更新下载完成事件
AutoUpdater::onUpdateDownloaded(function ($info) {
    Notification::send('更新', "版本 {$info['version']} 已下载完成，重启应用后将安装更新");
});

// 监听更新错误事件
AutoUpdater::onError(function ($error) {
    Notification::send('更新错误', $error['message']);
});

// 检查更新
AutoUpdater::checkForUpdates();
```

## 最佳实践

1. **定期检查更新**：在应用启动时或定期检查更新，但不要过于频繁，以免给服务器带来过大负担。

2. **提供更新说明**：在更新可用时，向用户显示更新说明，让用户了解新版本的变化。

3. **用户控制**：让用户决定是否下载和安装更新，不要强制更新，除非是安全更新。

4. **错误处理**：妥善处理更新过程中可能出现的错误，并向用户提供有用的错误信息。

5. **测试更新**：在发布更新之前，确保更新包可以正确下载和安装，并且更新后的应用可以正常运行。

## 故障排除

### 更新检查失败

- 确保更新服务器 URL 正确
- 检查网络连接
- 确保更新服务器返回的 JSON 数据格式正确

### 更新下载失败

- 确保更新包的 URL 可访问
- 检查网络连接
- 确保应用有足够的磁盘空间

### 更新安装失败

- 确保应用有足够的权限安装更新
- 确保更新包格式正确
- 确保没有其他程序占用更新文件

### 更新后应用无法启动

- 确保更新包中的文件完整
- 确保更新包与当前应用版本兼容
- 检查应用日志，查找可能的错误信息
