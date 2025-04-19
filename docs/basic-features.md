# 基础功能

NativePHP for ThinkPHP 提供了丰富的基础功能，用于构建桌面应用程序。本文档将介绍这些基础功能的使用方法。

## 应用程序管理

应用程序管理功能由 `App` 类提供，你可以通过 `App` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\App;
```

### 获取应用信息

```php
// 获取应用名称
$name = App::name();

// 获取应用 ID
$id = App::id();

// 获取应用版本
$version = App::version();

// 获取应用根路径
$rootPath = App::getRootPath();
```

### 应用生命周期管理

```php
// 退出应用
App::quit();

// 重启应用
App::restart();

// 关闭应用（隐藏所有窗口）
App::hide();

// 显示应用（显示所有窗口）
App::show();
```

## 窗口管理

窗口管理功能由 `Window` 类提供，你可以通过 `Window` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Window;
```

### 打开窗口

```php
// 打开新窗口
Window::open('/path/to/page', [
    'title' => '窗口标题',
    'width' => 800,
    'height' => 600,
    'minWidth' => 400,
    'minHeight' => 300,
    'maxWidth' => 1200,
    'maxHeight' => 900,
    'resizable' => true,
    'movable' => true,
    'minimizable' => true,
    'maximizable' => true,
    'closable' => true,
    'focusable' => true,
    'alwaysOnTop' => false,
    'fullscreen' => false,
    'kiosk' => false,
    'center' => true,
    'x' => null,
    'y' => null,
    'backgroundColor' => '#ffffff',
    'transparent' => false,
    'frame' => true,
    'show' => true,
    'paintWhenInitiallyHidden' => true,
    'webPreferences' => [
        'devTools' => true,
        'nodeIntegration' => false,
        'contextIsolation' => true,
        'sandbox' => true,
    ],
]);
```

### 窗口操作

```php
// 关闭窗口
Window::close();

// 最小化窗口
Window::minimize();

// 最大化窗口
Window::maximize();

// 恢复窗口
Window::restore();

// 设置窗口大小
Window::setSize(800, 600);

// 设置窗口位置
Window::setPosition(100, 100);

// 设置窗口标题
Window::setTitle('新标题');

// 设置窗口是否可调整大小
Window::setResizable(true);

// 设置窗口是否可移动
Window::setMovable(true);

// 设置窗口是否可最小化
Window::setMinimizable(true);

// 设置窗口是否可最大化
Window::setMaximizable(true);

// 设置窗口是否可关闭
Window::setClosable(true);

// 设置窗口是否总是置顶
Window::setAlwaysOnTop(true);

// 设置窗口是否全屏
Window::setFullScreen(true);

// 设置窗口是否为 kiosk 模式
Window::setKiosk(true);

// 设置窗口背景颜色
Window::setBackgroundColor('#ffffff');

// 设置窗口是否透明
Window::setTransparent(true);

// 设置窗口是否显示
Window::setVisible(true);

// 设置窗口是否可聚焦
Window::setFocusable(true);

// 聚焦窗口
Window::focus();

// 刷新窗口
Window::reload();

// 强制刷新窗口
Window::forceReload();

// 打开开发者工具
Window::openDevTools();

// 关闭开发者工具
Window::closeDevTools();

// 切换开发者工具
Window::toggleDevTools();
```

### 窗口事件

```php
// 监听窗口关闭事件
Window::on('close', function () {
    // 窗口关闭时执行的代码
});

// 监听窗口最小化事件
Window::on('minimize', function () {
    // 窗口最小化时执行的代码
});

// 监听窗口最大化事件
Window::on('maximize', function () {
    // 窗口最大化时执行的代码
});

// 监听窗口恢复事件
Window::on('restore', function () {
    // 窗口恢复时执行的代码
});

// 监听窗口聚焦事件
Window::on('focus', function () {
    // 窗口聚焦时执行的代码
});

// 监听窗口失去焦点事件
Window::on('blur', function () {
    // 窗口失去焦点时执行的代码
});

// 监听窗口移动事件
Window::on('move', function ($x, $y) {
    // 窗口移动时执行的代码
});

// 监听窗口调整大小事件
Window::on('resize', function ($width, $height) {
    // 窗口调整大小时执行的代码
});
```

## 菜单管理

菜单管理功能由 `Menu` 类提供，你可以通过 `Menu` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Menu;
```

### 创建应用菜单

```php
// 创建应用菜单
Menu::create()
    ->add('文件', [
        ['label' => '新建', 'accelerator' => 'CommandOrControl+N', 'click' => 'createNew'],
        ['label' => '打开', 'accelerator' => 'CommandOrControl+O', 'click' => 'openFile'],
        ['type' => 'separator'],
        ['label' => '保存', 'accelerator' => 'CommandOrControl+S', 'click' => 'saveFile'],
        ['label' => '另存为', 'accelerator' => 'CommandOrControl+Shift+S', 'click' => 'saveFileAs'],
        ['type' => 'separator'],
        ['label' => '退出', 'accelerator' => 'CommandOrControl+Q', 'click' => 'quit'],
    ])
    ->submenu('编辑', function ($submenu) {
        $submenu->add('撤销', ['accelerator' => 'CommandOrControl+Z', 'click' => 'undo']);
        $submenu->add('重做', ['accelerator' => 'CommandOrControl+Y', 'click' => 'redo']);
        $submenu->separator();
        $submenu->add('剪切', ['accelerator' => 'CommandOrControl+X', 'click' => 'cut']);
        $submenu->add('复制', ['accelerator' => 'CommandOrControl+C', 'click' => 'copy']);
        $submenu->add('粘贴', ['accelerator' => 'CommandOrControl+V', 'click' => 'paste']);
        $submenu->add('全选', ['accelerator' => 'CommandOrControl+A', 'click' => 'selectAll']);
    })
    ->submenu('视图', function ($submenu) {
        $submenu->add('放大', ['accelerator' => 'CommandOrControl+Plus', 'click' => 'zoomIn']);
        $submenu->add('缩小', ['accelerator' => 'CommandOrControl+-', 'click' => 'zoomOut']);
        $submenu->add('重置缩放', ['accelerator' => 'CommandOrControl+0', 'click' => 'resetZoom']);
        $submenu->separator();
        $submenu->add('全屏', ['accelerator' => 'F11', 'click' => 'toggleFullScreen']);
    })
    ->submenu('帮助', function ($submenu) {
        $submenu->add('关于', ['click' => 'about']);
    })
    ->setApplicationMenu();
```

### 创建上下文菜单

```php
// 创建上下文菜单
$contextMenu = Menu::create()
    ->add('剪切', ['accelerator' => 'CommandOrControl+X', 'click' => 'cut'])
    ->add('复制', ['accelerator' => 'CommandOrControl+C', 'click' => 'copy'])
    ->add('粘贴', ['accelerator' => 'CommandOrControl+V', 'click' => 'paste'])
    ->separator()
    ->add('全选', ['accelerator' => 'CommandOrControl+A', 'click' => 'selectAll'])
    ->build();

// 显示上下文菜单
Menu::popup($contextMenu);
```

## 通知管理

通知管理功能由 `Notification` 类提供，你可以通过 `Notification` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Notification;
```

### 发送通知

```php
// 发送简单通知
Notification::send('通知标题', '通知内容');

// 发送带图标的通知
Notification::send('通知标题', '通知内容', '/path/to/icon.png');

// 发送带操作的通知
Notification::send('通知标题', '通知内容', null, [
    ['label' => '确定', 'click' => 'onConfirm'],
    ['label' => '取消', 'click' => 'onCancel'],
]);
```

### 通知事件

```php
// 监听通知点击事件
Notification::on('click', function () {
    // 通知被点击时执行的代码
});

// 监听通知关闭事件
Notification::on('close', function () {
    // 通知被关闭时执行的代码
});

// 监听通知操作事件
Notification::on('action', function ($action) {
    // 通知操作被点击时执行的代码
});
```

## 剪贴板管理

剪贴板管理功能由 `Clipboard` 类提供，你可以通过 `Clipboard` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Clipboard;
```

### 文本操作

```php
// 读取剪贴板文本
$text = Clipboard::text();

// 写入文本到剪贴板
Clipboard::setText('剪贴板文本');

// 清空剪贴板
Clipboard::clear();
```

### 图片操作

```php
// 读取剪贴板图片
$image = Clipboard::image();

// 写入图片到剪贴板
Clipboard::setImage('/path/to/image.png');
```

### HTML 操作

```php
// 读取剪贴板 HTML
$html = Clipboard::html();

// 写入 HTML 到剪贴板
Clipboard::setHtml('<b>剪贴板 HTML</b>');
```

## 全局快捷键

全局快捷键功能由 `GlobalShortcut` 类提供，你可以通过 `GlobalShortcut` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\GlobalShortcut;
```

### 注册快捷键

```php
// 注册全局快捷键
GlobalShortcut::register('CommandOrControl+Shift+A', function () {
    // 快捷键被触发时执行的代码
});
```

### 管理快捷键

```php
// 检查快捷键是否已注册
$isRegistered = GlobalShortcut::isRegistered('CommandOrControl+Shift+A');

// 注销快捷键
GlobalShortcut::unregister('CommandOrControl+Shift+A');

// 注销所有快捷键
GlobalShortcut::unregisterAll();
```

## 系统托盘

系统托盘功能由 `Tray` 类提供，你可以通过 `Tray` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Tray;
```

### 创建托盘图标

```php
// 创建托盘图标
Tray::setIcon('/path/to/icon.png')
    ->setTooltip('应用名称')
    ->setMenu(function ($menu) {
        $menu->add('显示', ['click' => 'show']);
        $menu->add('隐藏', ['click' => 'hide']);
        $menu->separator();
        $menu->add('退出', ['click' => 'quit']);
    })
    ->show();
```

### 托盘操作

```php
// 设置托盘图标
Tray::setIcon('/path/to/icon.png');

// 设置托盘提示文本
Tray::setTooltip('应用名称');

// 设置托盘菜单
Tray::setMenu(function ($menu) {
    $menu->add('显示', ['click' => 'show']);
    $menu->add('隐藏', ['click' => 'hide']);
    $menu->separator();
    $menu->add('退出', ['click' => 'quit']);
});

// 显示托盘图标
Tray::show();

// 隐藏托盘图标
Tray::hide();

// 销毁托盘图标
Tray::destroy();
```

### 托盘事件

```php
// 监听托盘图标点击事件
Tray::on('click', function () {
    // 托盘图标被点击时执行的代码
});

// 监听托盘图标右键点击事件
Tray::on('right-click', function () {
    // 托盘图标被右键点击时执行的代码
});

// 监听托盘图标双击事件
Tray::on('double-click', function () {
    // 托盘图标被双击时执行的代码
});
```

## 对话框

对话框功能由 `Dialog` 类提供，你可以通过 `Dialog` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Dialog;
```

### 文件对话框

```php
// 打开文件对话框
$filePath = Dialog::openFile([
    'title' => '打开文件',
    'defaultPath' => '/path/to/default',
    'filters' => [
        ['name' => '文本文件', 'extensions' => ['txt', 'md']],
        ['name' => '图片文件', 'extensions' => ['jpg', 'png', 'gif']],
        ['name' => '所有文件', 'extensions' => ['*']],
    ],
    'multiSelections' => false,
]);

// 打开多个文件对话框
$filePaths = Dialog::openFiles([
    'title' => '打开多个文件',
    'defaultPath' => '/path/to/default',
    'filters' => [
        ['name' => '文本文件', 'extensions' => ['txt', 'md']],
        ['name' => '图片文件', 'extensions' => ['jpg', 'png', 'gif']],
        ['name' => '所有文件', 'extensions' => ['*']],
    ],
]);

// 保存文件对话框
$filePath = Dialog::saveFile([
    'title' => '保存文件',
    'defaultPath' => '/path/to/default/file.txt',
    'filters' => [
        ['name' => '文本文件', 'extensions' => ['txt', 'md']],
        ['name' => '图片文件', 'extensions' => ['jpg', 'png', 'gif']],
        ['name' => '所有文件', 'extensions' => ['*']],
    ],
]);

// 选择文件夹对话框
$folderPath = Dialog::selectFolder([
    'title' => '选择文件夹',
    'defaultPath' => '/path/to/default',
]);
```

### 消息对话框

```php
// 显示消息对话框
$response = Dialog::message('消息内容', [
    'title' => '消息标题',
    'type' => 'info', // info, warning, error, question
    'buttons' => ['确定', '取消'],
    'defaultId' => 0,
    'cancelId' => 1,
]);

// 显示错误对话框
$response = Dialog::error('错误内容', [
    'title' => '错误标题',
    'buttons' => ['确定'],
]);

// 显示警告对话框
$response = Dialog::warning('警告内容', [
    'title' => '警告标题',
    'buttons' => ['确定', '取消'],
]);

// 显示信息对话框
$response = Dialog::info('信息内容', [
    'title' => '信息标题',
    'buttons' => ['确定'],
]);

// 显示确认对话框
$response = Dialog::confirm('确认内容', [
    'title' => '确认标题',
    'buttons' => ['是', '否'],
]);
```

## 文件系统

文件系统功能由 `FileSystem` 类提供，你可以通过 `FileSystem` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\FileSystem;
```

### 文件操作

```php
// 读取文件
$content = FileSystem::read('/path/to/file.txt');

// 写入文件
FileSystem::write('/path/to/file.txt', '文件内容');

// 追加内容到文件
FileSystem::append('/path/to/file.txt', '追加内容');

// 复制文件
FileSystem::copy('/path/to/source.txt', '/path/to/destination.txt');

// 移动文件
FileSystem::move('/path/to/source.txt', '/path/to/destination.txt');

// 删除文件
FileSystem::delete('/path/to/file.txt');

// 检查文件是否存在
$exists = FileSystem::exists('/path/to/file.txt');

// 获取文件大小
$size = FileSystem::size('/path/to/file.txt');

// 获取文件最后修改时间
$time = FileSystem::lastModified('/path/to/file.txt');

// 获取文件类型
$type = FileSystem::type('/path/to/file.txt');

// 获取文件 MIME 类型
$mime = FileSystem::mimeType('/path/to/file.txt');
```

### 目录操作

```php
// 创建目录
FileSystem::makeDirectory('/path/to/directory');

// 递归创建目录
FileSystem::makeDirectory('/path/to/nested/directory', 0755, true);

// 复制目录
FileSystem::copyDirectory('/path/to/source', '/path/to/destination');

// 移动目录
FileSystem::moveDirectory('/path/to/source', '/path/to/destination');

// 删除目录
FileSystem::deleteDirectory('/path/to/directory');

// 清空目录
FileSystem::cleanDirectory('/path/to/directory');

// 获取目录中的所有文件
$files = FileSystem::files('/path/to/directory');

// 获取目录中的所有目录
$directories = FileSystem::directories('/path/to/directory');

// 获取目录中的所有文件和目录
$all = FileSystem::allFiles('/path/to/directory');
```
