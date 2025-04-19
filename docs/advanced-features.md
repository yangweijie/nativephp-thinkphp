# 高级功能

NativePHP for ThinkPHP 提供了丰富的高级功能，用于构建功能强大的桌面应用程序。本文档将介绍这些高级功能的使用方法。

## 系统信息

系统信息功能由 `System` 类提供，你可以通过 `System` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\System;
```

### 获取系统信息

```php
// 获取操作系统信息
$os = System::getOS();

// 获取操作系统版本
$osVersion = System::getOSVersion();

// 获取操作系统架构
$arch = System::getArch();

// 获取 CPU 信息
$cpu = System::getCPU();

// 获取内存信息
$memory = System::getMemory();

// 获取磁盘信息
$disk = System::getDisk();

// 获取网络信息
$network = System::getNetwork();

// 获取显示器信息
$display = System::getDisplay();

// 获取电池信息
$battery = System::getBattery();

// 获取用户信息
$user = System::getUser();

// 获取主机名
$hostname = System::getHostname();

// 获取用户主目录
$homePath = System::getHomePath();

// 获取临时目录
$tempPath = System::getTempPath();

// 获取应用数据目录
$appDataPath = System::getAppDataPath();
```

### 系统操作

```php
// 打开外部 URL
System::openExternal('https://www.example.com');

// 打开文件或目录
System::openPath('/path/to/file/or/directory');

// 显示文件或目录在文件管理器中
System::showItemInFolder('/path/to/file/or/directory');

// 移动文件到回收站
System::moveItemToTrash('/path/to/file/or/directory');

// 播放系统声音
System::beep();

// 设置系统休眠状态
System::sleep();

// 设置系统锁屏状态
System::lock();

// 设置系统注销状态
System::logout();

// 重启系统
System::restart();

// 关闭系统
System::shutdown();
```

## 屏幕捕获

屏幕捕获功能由 `Screen` 类提供，你可以通过 `Screen` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Screen;
```

### 屏幕截图

```php
// 捕获整个屏幕的截图
$screenshot = Screen::captureScreen();

// 捕获指定屏幕的截图
$screenshot = Screen::captureScreen(0);

// 捕获指定区域的截图
$screenshot = Screen::captureArea(0, 0, 800, 600);

// 捕获当前窗口的截图
$screenshot = Screen::captureWindow();

// 保存截图到文件
Screen::captureScreen()->save('/path/to/screenshot.png');

// 获取截图的 Base64 编码
$base64 = Screen::captureScreen()->toBase64();

// 获取截图的二进制数据
$binary = Screen::captureScreen()->toBinary();
```

### 屏幕录制

```php
// 开始录制屏幕
Screen::startRecording([
    'audio' => true,
    'videoSize' => [1280, 720],
    'frameRate' => 30,
    'path' => '/path/to/recording.mp4',
]);

// 停止录制屏幕
$recordingPath = Screen::stopRecording();
```

### 屏幕信息

```php
// 获取所有屏幕信息
$screens = Screen::getAllDisplays();

// 获取主屏幕信息
$primaryScreen = Screen::getPrimaryDisplay();

// 获取当前屏幕信息
$currentScreen = Screen::getCurrentDisplay();

// 获取屏幕尺寸
$size = Screen::getDisplaySize();

// 获取屏幕工作区尺寸
$workAreaSize = Screen::getDisplayWorkAreaSize();

// 获取屏幕缩放因子
$scaleFactor = Screen::getDisplayScaleFactor();
```

## 自动更新

自动更新功能由 `Updater` 类提供，你可以通过 `Updater` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Updater;
```

### 检查更新

```php
// 检查更新
$updateInfo = Updater::check();

// 检查更新（指定服务器 URL）
$updateInfo = Updater::check('https://updates.example.com');
```

### 下载和安装更新

```php
// 下载更新
Updater::download();

// 安装更新
Updater::install();

// 下载并安装更新
Updater::update();
```

### 更新事件

```php
// 监听更新检查事件
Updater::on('checking-for-update', function () {
    // 正在检查更新时执行的代码
});

// 监听更新可用事件
Updater::on('update-available', function ($info) {
    // 有更新可用时执行的代码
});

// 监听更新不可用事件
Updater::on('update-not-available', function () {
    // 没有更新可用时执行的代码
});

// 监听更新错误事件
Updater::on('error', function ($error) {
    // 更新出错时执行的代码
});

// 监听更新下载进度事件
Updater::on('download-progress', function ($progress) {
    // 更新下载进度变化时执行的代码
});

// 监听更新下载完成事件
Updater::on('update-downloaded', function () {
    // 更新下载完成时执行的代码
});
```

## HTTP 请求

HTTP 请求功能由 `Http` 类提供，你可以通过 `Http` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Http;
```

### 发送请求

```php
// 发送 GET 请求
$response = Http::get('https://api.example.com/users');

// 发送带查询参数的 GET 请求
$response = Http::get('https://api.example.com/users', [
    'page' => 1,
    'limit' => 10,
]);

// 发送 POST 请求
$response = Http::post('https://api.example.com/users', [
    'name' => '张三',
    'email' => 'zhangsan@example.com',
]);

// 发送 PUT 请求
$response = Http::put('https://api.example.com/users/1', [
    'name' => '张三',
    'email' => 'zhangsan@example.com',
]);

// 发送 PATCH 请求
$response = Http::patch('https://api.example.com/users/1', [
    'name' => '张三',
]);

// 发送 DELETE 请求
$response = Http::delete('https://api.example.com/users/1');

// 发送带头信息的请求
$response = Http::withHeaders([
    'Authorization' => 'Bearer token',
    'Accept' => 'application/json',
])->get('https://api.example.com/users');

// 发送带超时的请求
$response = Http::timeout(30)->get('https://api.example.com/users');

// 发送带重试的请求
$response = Http::retry(3, 100)->get('https://api.example.com/users');

// 发送带认证的请求
$response = Http::withBasicAuth('username', 'password')->get('https://api.example.com/users');

// 发送带 Bearer 令牌的请求
$response = Http::withToken('token')->get('https://api.example.com/users');

// 发送带 JSON 的请求
$response = Http::withJson([
    'name' => '张三',
    'email' => 'zhangsan@example.com',
])->post('https://api.example.com/users');

// 发送带文件的请求
$response = Http::withFile('avatar', '/path/to/avatar.jpg')->post('https://api.example.com/users');

// 发送带表单的请求
$response = Http::withForm([
    'name' => '张三',
    'email' => 'zhangsan@example.com',
])->post('https://api.example.com/users');
```

### 处理响应

```php
// 获取响应状态码
$statusCode = $response['status_code'];

// 获取响应头信息
$headers = $response['headers'];

// 获取响应内容
$data = $response['data'];

// 检查响应是否成功
$success = $response['success'];

// 获取响应错误信息
$error = $response['error'];
```

### 下载文件

```php
// 下载文件
Http::download('https://example.com/file.zip', '/path/to/save/file.zip');

// 下载文件（带进度回调）
Http::download('https://example.com/file.zip', '/path/to/save/file.zip', function ($progress) {
    // 下载进度变化时执行的代码
    // $progress 包含 total, received, percent 等信息
});
```

## 数据库管理

数据库管理功能由 `Database` 类提供，你可以通过 `Database` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Database;
```

### 连接数据库

```php
// 设置数据库路径
Database::setDatabasePath('/path/to/database.sqlite');

// 获取数据库路径
$path = Database::getDatabasePath();

// 连接数据库
$pdo = Database::connect();

// 断开数据库连接
Database::disconnect();
```

### 执行 SQL

```php
// 执行 SQL 查询
$results = Database::query('SELECT * FROM users WHERE id = ?', [1]);

// 执行 SQL 语句
$success = Database::exec('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)');

// 获取最后插入的 ID
$id = Database::lastInsertId();

// 开始事务
Database::beginTransaction();

// 提交事务
Database::commit();

// 回滚事务
Database::rollBack();
```

### 表操作

```php
// 检查表是否存在
$exists = Database::tableExists('users');

// 创建表
Database::createTable('users', [
    'id' => 'INTEGER PRIMARY KEY AUTOINCREMENT',
    'name' => 'TEXT NOT NULL',
    'email' => 'TEXT NOT NULL',
]);

// 删除表
Database::dropTable('users');

// 获取表的所有列
$columns = Database::getColumns('users');

// 获取所有表
$tables = Database::getTables();
```

### 数据操作

```php
// 插入数据
$id = Database::insert('users', [
    'name' => '张三',
    'email' => 'zhangsan@example.com',
]);

// 更新数据
$count = Database::update('users', [
    'name' => '李四',
    'email' => 'lisi@example.com',
], 'id = ?', [1]);

// 删除数据
$count = Database::delete('users', 'id = ?', [1]);

// 查询数据
$users = Database::select('users', '*', 'id > ?', [0], 'id DESC', 10, 0);

// 获取单行数据
$user = Database::selectOne('users', '*', 'id = ?', [1]);
```

### 备份和恢复

```php
// 备份数据库
Database::backup('/path/to/backup.db');

// 恢复数据库
Database::restore('/path/to/backup.db');
```

## 设置管理

设置管理功能由 `Settings` 类提供，你可以通过 `Settings` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Settings;
```

### 设置操作

```php
// 设置设置文件路径
Settings::setPath('/path/to/settings.json');

// 获取设置文件路径
$path = Settings::getPath();

// 获取设置值
$value = Settings::get('theme', 'light');

// 设置设置值
Settings::set('theme', 'dark');

// 检查设置是否存在
$exists = Settings::has('theme');

// 删除设置
Settings::delete('theme');

// 获取所有设置
$settings = Settings::all();

// 清空所有设置
Settings::clear();
```

### 导入和导出

```php
// 导出设置
Settings::export('/path/to/settings.json');

// 导入设置
Settings::import('/path/to/settings.json');
```

### 监听设置变化

```php
// 监听设置变化
Settings::watch('theme', function ($value) {
    // 设置值变化时执行的代码
});
```

## 进程管理

进程管理功能由 `Process` 类提供，你可以通过 `Process` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Process;
```

### 运行命令

```php
// 运行命令
$processId = Process::run('ls -la');

// 运行命令（带选项）
$processId = Process::run('ls -la', [
    'cwd' => '/path/to/directory',
    'env' => ['PATH' => '/usr/bin'],
    'detached' => false,
    'shell' => true,
]);

// 运行 PHP 脚本
$processId = Process::runPhp('/path/to/script.php', ['arg1', 'arg2']);

// 运行 ThinkPHP 命令
$processId = Process::runThink('cache:clear');
```

### 进程操作

```php
// 获取进程信息
$process = Process::get($processId);

// 获取所有进程
$processes = Process::all();

// 获取进程输出
$output = Process::getOutput($processId);

// 获取进程错误
$error = Process::getError($processId);

// 获取进程退出码
$exitCode = Process::getExitCode($processId);

// 检查进程是否正在运行
$isRunning = Process::isRunning($processId);

// 向进程发送输入
Process::write($processId, 'input text');

// 终止进程
Process::kill($processId);

// 等待进程结束
Process::wait($processId, 10);

// 清理已结束的进程
$count = Process::cleanup();
```

## 打印功能

打印功能由 `Printer` 类提供，你可以通过 `Printer` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Printer;
```

### 获取打印机

```php
// 获取所有打印机
$printers = Printer::getPrinters();

// 获取默认打印机
$defaultPrinter = Printer::getDefaultPrinter();
```

### 打印文档

```php
// 打印 HTML
Printer::printHtml('<h1>测试打印</h1><p>这是一个测试文档</p>');

// 打印 HTML（带选项）
Printer::printHtml('<h1>测试打印</h1><p>这是一个测试文档</p>', [
    'silent' => false,
    'printBackground' => true,
    'deviceName' => '',
    'color' => true,
    'landscape' => false,
    'scaleFactor' => 1.0,
    'pagesPerSheet' => 1,
    'collate' => true,
    'copies' => 1,
    'pageRanges' => [],
    'duplexMode' => 'simplex',
    'dpi' => 300,
]);

// 打印文件
Printer::printFile('/path/to/file.pdf');

// 打印到 PDF
Printer::printToPdf('<h1>测试 PDF</h1>', '/path/to/output.pdf');

// 显示打印预览
Printer::showPrintPreview('<h1>测试预览</h1>');
```
