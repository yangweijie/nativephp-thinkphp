# NativePHP ThinkPHP

将 NativePHP 移植到 ThinkPHP 框架的包，让您可以使用 ThinkPHP 构建桌面应用程序。

## 安装

```bash
composer require james.xue/nativephp-thinkphp
```

## 配置

发布配置文件：

```bash
php think vendor:publish --provider="NativePHP\Think\NativeAppServiceProvider"
```

## 基础功能

### 窗口管理

#### 创建窗口

```php
$window = Native::window('main')
    ->title('我的应用')
    ->width(1200)
    ->height(800)
    ->create();
```

#### 窗口预设

```php
// 使用预设创建窗口
$window = Native::windowManager()->createFromPreset('dialog', 'my-dialog');

// 内置预设包括：
// - dialog：对话框窗口
// - settings：设置窗口
// - small：小型窗口
```

#### 窗口分组

```php
// 创建窗口分组
$group = Native::windowManager()
    ->createGroup('editor-group')
    ->add('editor')
    ->add('preview')
    ->arrangeHorizontal();

// 应用不同的布局方式
$group->arrangeVertical();  // 垂直排列
$group->arrangeGrid(2);     // 2列网格布局

// 保存分组状态
Native::windowGroupStateManager()->autoSaveAll();

// 恢复分组状态
Native::windowGroupStateManager()->autoRestoreAll();
```

#### 高级窗口管理

```php
$manager = Native::windowManager();

// 批量创建窗口
$windows = $manager->createMultiple([
    'main' => ['width' => 1200, 'height' => 800],
    'sidebar' => ['width' => 300, 'height' => 800]
]);

// 水平排列窗口
$manager->arrange(['main', 'sidebar'], 'horizontal');

// 垂直排列窗口
$manager->arrange(['top', 'bottom'], 'vertical');

// 网格布局
$manager->grid(['win1', 'win2', 'win3', 'win4'], 2); // 2列网格

// 主从布局
$manager->masterDetail('main', 'detail', 0.3); // detail窗口占30%宽度

// 交换窗口位置
$manager->swap('window1', 'window2');

// 聚焦窗口
$manager->focus('main');

// 批量关闭窗口
$manager->closeMultiple(['window1', 'window2']);
```

### 系统托盘

```php
Native::tray()
    ->label('我的应用')
    ->icon(public_path('icon.png'))
    ->create();
```

### 快捷键

```php
Native::hotkey()
    ->register('CommandOrControl+X', function () {
        // 处理快捷键
    });
```

### 进程间通信

```php
// 主进程发送消息
Native::ipc()->send('channel-name', ['data' => 'value']);

// 渲染进程接收消息
Native::ipc()->on('channel-name', function ($data) {
    // 处理消息
});
```

## 高级功能

### 窗口状态管理

```php
// 保存窗口状态
$state = Native::windowState()->save('main');

// 恢复窗口状态
Native::windowState()->restore('main', $state);

// 自动恢复上次的窗口状态
Native::windowState()->autoRestore('main');
```

### 窗口布局预设

```php
// 使用内置布局
Native::windowLayoutPresets()->apply('split-view', ['left', 'right']);

// 自定义布局
Native::windowLayoutPresets()->define('my-layout', function ($manager, $windows) {
    // 自定义窗口排列逻辑
});
```

### 窗口分组状态管理

```php
$manager = Native::windowGroupStateManager();

// 设置缓存配置
$manager->setCacheKey('my_window_groups')
    ->setExpireTime(7 * 24 * 60 * 60); // 7天过期

// 自动保存所有分组状态
$manager->autoSaveAll();

// 自动恢复所有分组状态
$manager->autoRestoreAll();

// 清除所有保存的状态
$manager->clearAll();
```

## 配置说明

### 窗口分组配置

在 `config/native.php` 中配置：

```php
'window_groups' => [
    'default' => [
        'state_file' => runtime_path('window_states.json'),
        'auto_restore' => true,
    ],
    'editor' => [
        'windows' => [
            'main' => ['width' => 1200, 'height' => 800],
            'preview' => ['width' => 600, 'height' => 800],
        ],
        'layout' => 'horizontal',
    ],
],
```

## 许可证

MIT