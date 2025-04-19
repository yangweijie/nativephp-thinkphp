# Dock 功能

NativePHP for ThinkPHP 提供了 Dock 功能，允许你的桌面应用程序与 macOS Dock 进行交互。本文档将介绍如何使用这些功能。

## 基本概念

macOS Dock 是 macOS 操作系统中的一个重要组件，它显示在屏幕底部或侧面，用于快速访问应用程序、文件和文件夹。NativePHP for ThinkPHP 的 Dock 功能允许你的应用程序与 Dock 进行交互，例如设置徽章、弹跳图标、显示进度条等。

> **注意**：Dock 功能仅在 macOS 平台上可用。在其他平台上，这些功能将被忽略。

## 使用 Dock Facade

NativePHP for ThinkPHP 提供了 `Dock` Facade，用于与 macOS Dock 进行交互。

### 设置 Dock 图标

```php
use Native\ThinkPHP\Facades\Dock;

// 设置 Dock 图标
$result = Dock::setIcon('/path/to/icon.png');

if ($result) {
    // 图标设置成功
} else {
    // 图标设置失败或不在 macOS 平台上
}
```

### 设置 Dock 徽章文本

```php
// 设置 Dock 徽章文本
$result = Dock::setBadge('New');

if ($result) {
    // 徽章文本设置成功
} else {
    // 徽章文本设置失败或不在 macOS 平台上
}
```

### 设置 Dock 徽章计数

```php
// 设置 Dock 徽章计数
$result = Dock::setBadgeCount(5);

if ($result) {
    // 徽章计数设置成功
} else {
    // 徽章计数设置失败或不在 macOS 平台上
}
```

### 获取 Dock 徽章计数

```php
// 获取 Dock 徽章计数
$count = Dock::getBadgeCount();
```

### 清除 Dock 徽章

```php
// 清除 Dock 徽章
$result = Dock::clearBadge();

if ($result) {
    // 徽章清除成功
} else {
    // 徽章清除失败或不在 macOS 平台上
}
```

### 设置 Dock 菜单

```php
// 设置 Dock 菜单
$result = Dock::setMenu([
    [
        'label' => '新建文档',
        'click' => 'newDocument',
    ],
    [
        'type' => 'separator',
    ],
    [
        'label' => '最近文档',
        'submenu' => [
            [
                'label' => '文档1.txt',
                'click' => 'openDocument1',
            ],
            [
                'label' => '文档2.txt',
                'click' => 'openDocument2',
            ],
        ],
    ],
]);

if ($result) {
    // 菜单设置成功
} else {
    // 菜单设置失败或不在 macOS 平台上
}
```

### 显示 Dock 图标

```php
// 显示 Dock 图标
$result = Dock::show();

if ($result) {
    // 图标显示成功
} else {
    // 图标显示失败或不在 macOS 平台上
}
```

### 隐藏 Dock 图标

```php
// 隐藏 Dock 图标
$result = Dock::hide();

if ($result) {
    // 图标隐藏成功
} else {
    // 图标隐藏失败或不在 macOS 平台上
}
```

### 检查 Dock 图标是否可见

```php
// 检查 Dock 图标是否可见
$isVisible = Dock::isVisible();
```

### 弹跳 Dock 图标

```php
// 弹跳 Dock 图标（信息性弹跳，会弹跳一次）
$result = Dock::bounce('informational');

// 弹跳 Dock 图标（关键性弹跳，会一直弹跳直到应用程序激活）
$result = Dock::bounce('critical');

if ($result) {
    // 图标弹跳成功
} else {
    // 图标弹跳失败或不在 macOS 平台上
}
```

### 取消弹跳 Dock 图标

```php
// 取消弹跳 Dock 图标
$result = Dock::cancelBounce($id);

if ($result) {
    // 图标弹跳取消成功
} else {
    // 图标弹跳取消失败或不在 macOS 平台上
}
```

### 设置下载进度条

```php
// 设置下载进度条（0.0 到 1.0 之间的值）
$result = Dock::setDownloadProgress(0.5);

if ($result) {
    // 进度条设置成功
} else {
    // 进度条设置失败或不在 macOS 平台上
}
```

### 清除下载进度条

```php
// 清除下载进度条
$result = Dock::clearDownloadProgress();

if ($result) {
    // 进度条清除成功
} else {
    // 进度条清除失败或不在 macOS 平台上
}
```

### 设置 Dock 图标的工具提示

```php
// 设置 Dock 图标的工具提示
$result = Dock::setToolTip('NativePHP 应用');

if ($result) {
    // 工具提示设置成功
} else {
    // 工具提示设置失败或不在 macOS 平台上
}
```

### 注册 Dock 菜单点击事件

```php
// 注册 Dock 菜单点击事件
$id = Dock::onMenuClick(function ($menuItem) {
    // 处理菜单点击事件
    echo "菜单项 {$menuItem['label']} 被点击了";
});

if ($id) {
    // 事件注册成功
} else {
    // 事件注册失败或不在 macOS 平台上
}
```

### 移除 Dock 菜单点击事件监听器

```php
// 移除 Dock 菜单点击事件监听器
$result = Dock::offMenuClick($id);

if ($result) {
    // 监听器移除成功
} else {
    // 监听器移除失败或不在 macOS 平台上
}
```

### 注册 Dock 图标点击事件

```php
// 注册 Dock 图标点击事件
$id = Dock::onClick(function ($event) {
    // 处理图标点击事件
    echo "Dock 图标被点击了";
});

if ($id) {
    // 事件注册成功
} else {
    // 事件注册失败或不在 macOS 平台上
}
```

### 移除 Dock 图标点击事件监听器

```php
// 移除 Dock 图标点击事件监听器
$result = Dock::offClick($id);

if ($result) {
    // 监听器移除成功
} else {
    // 监听器移除失败或不在 macOS 平台上
}
```

### 设置 Dock 图标闪烁

```php
// 设置 Dock 图标闪烁
$result = Dock::setFlash(true);

if ($result) {
    // 图标闪烁设置成功
} else {
    // 图标闪烁设置失败或不在 macOS 平台上
}

// 取消 Dock 图标闪烁
$result = Dock::setFlash(false);
```

### 创建自定义 Dock 菜单

```php
// 创建自定义 Dock 菜单
$result = Dock::createMenu([
    [
        'label' => '新建文档',
        'accelerator' => 'Command+N',
        'click' => 'newDocument',
    ],
    [
        'type' => 'separator',
    ],
    [
        'label' => '最近文档',
        'submenu' => [
            [
                'label' => '文档 1',
                'click' => 'openDocument1',
            ],
            [
                'label' => '文档 2',
                'click' => 'openDocument2',
            ],
        ],
    ],
]);

if ($result) {
    // 菜单创建成功
} else {
    // 菜单创建失败或不在 macOS 平台上
}
```

### 获取 Dock 图标大小

```php
// 获取 Dock 图标大小
$size = Dock::getIconSize();

echo "图标宽度：{$size['width']}px, 高度：{$size['height']}px";
```

## 实际应用场景

### 显示未读消息数量

```php
// 显示未读消息数量
$unreadCount = $this->getUnreadMessageCount();
if ($unreadCount > 0) {
    Dock::setBadgeCount($unreadCount);
} else {
    Dock::clearBadge();
}
```

### 显示下载进度

```php
// 显示下载进度
$progress = $this->getDownloadProgress();
if ($progress < 1.0) {
    Dock::setDownloadProgress($progress);
} else {
    Dock::clearDownloadProgress();
    Notification::send('下载完成', '文件已下载完成');
}
```

### 创建自定义 Dock 菜单

```php
// 创建自定义 Dock 菜单
$recentFiles = $this->getRecentFiles();
$menuItems = [
    [
        'label' => '新建文档',
        'click' => 'newDocument',
    ],
    [
        'type' => 'separator',
    ],
    [
        'label' => '最近文档',
        'submenu' => [],
    ],
];

foreach ($recentFiles as $file) {
    $menuItems[2]['submenu'][] = [
        'label' => $file['name'],
        'click' => "openFile:{$file['id']}",
    ];
}

Dock::setMenu($menuItems);

// 注册菜单点击事件
Dock::onMenuClick(function ($menuItem) {
    if ($menuItem['click'] === 'newDocument') {
        $this->createNewDocument();
    } elseif (strpos($menuItem['click'], 'openFile:') === 0) {
        $fileId = substr($menuItem['click'], 9);
        $this->openFile($fileId);
    }
});
```

## 最佳实践

1. **平台检查**：在使用 Dock 功能之前，始终检查当前平台是否为 macOS。

2. **徽章计数**：徽章计数应该反映应用程序中需要用户注意的项目数量，例如未读消息、待办事项等。

3. **弹跳图标**：不要过度使用弹跳图标，这可能会干扰用户。只在需要用户立即注意的情况下使用关键性弹跳。

4. **下载进度**：下载进度条应该准确反映下载进度，并在下载完成后清除。

5. **菜单项**：Dock 菜单应该简洁明了，包含最常用的功能。

## 故障排除

### Dock 功能不起作用

- 确保你的应用程序运行在 macOS 平台上
- 检查 Dock 功能是否已正确初始化
- 确保你的应用程序有权限修改 Dock

### 徽章计数不显示

- 确保徽章计数是一个有效的数字
- 检查应用程序是否有权限显示徽章
- 尝试清除徽章后重新设置

### 菜单点击事件不触发

- 确保菜单项的 `click` 属性已正确设置
- 检查事件监听器是否已正确注册
- 尝试重新注册事件监听器
