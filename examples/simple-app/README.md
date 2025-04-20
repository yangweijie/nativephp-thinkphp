# NativePHP-ThinkPHP 简单示例应用

这是一个使用 NativePHP-ThinkPHP 构建的简单桌面应用程序示例。

## 功能

- 主窗口显示
- 系统托盘图标
- 应用程序菜单
- 系统通知
- 全局快捷键
- 窗口分组管理
- 窗口过渡动画

## 安装

1. 克隆仓库
2. 安装依赖：`composer install`
3. 运行应用：`php think native:serve`

## 窗口分组功能演示

### 创建窗口分组

```php
// 创建一个包含主窗口和编辑器窗口的分组
Native::windowManager()->createGroup('main-group')
    ->add('main')
    ->add('editor')
    ->arrangeHorizontal();
```

### 分组布局管理

```php
// 水平排列
Native::windowManager()->getGroup('main-group')->arrangeHorizontal();

// 垂直排列
Native::windowManager()->getGroup('main-group')->arrangeVertical();

// 网格布局
Native::windowManager()->getGroup('main-group')->arrangeGrid(2);
```

### 分组状态管理

```php
// 保存所有窗口组状态
Native::windowGroupStateManager()->autoSaveAll();

// 恢复所有窗口组状态
Native::windowGroupStateManager()->autoRestoreAll();

// 清除所有保存的状态
Native::windowGroupStateManager()->clearAll();
```

### 自定义布局

```php
// 定义自定义布局
Native::windowLayoutPresets()->define('my-layout', function ($manager, $windows) {
    foreach ($windows as $index => $window) {
        $manager->getWindow($window)
            ->x(100 * $index)
            ->y(100 * $index)
            ->show();
    }
});

// 应用自定义布局
Native::windowManager()->getGroup('main-group')->applyLayout('my-layout');
```

## 窗口过渡动画演示

### 基本动画使用

```php
// 带动画的窗口移动
Native::window('main')
    ->transition()
        ->duration(500)
        ->easing('easeOutQuint')
    ->moveTo(100, 100);

// 带动画的窗口大小调整
Native::window('main')
    ->transition()
        ->duration(300)
        ->easing('easeInOutCubic')
    ->resizeTo(800, 600);

// 一次性应用完整布局
Native::window('main')
    ->transition()
        ->duration(400)
        ->easing('easeOutBounce')
    ->setLayout([
        'x' => 100,
        'y' => 100,
        'width' => 800,
        'height' => 600
    ]);
```

### 动画预设

```php
// 使用快速动画预设
Native::window('main')
    ->transition()
        ->usePreset('fast')
    ->moveTo(200, 200);

// 使用弹跳动画预设
Native::window('main')
    ->transition()
        ->usePreset('bounce')
    ->resizeTo(1000, 800);
```

### 分组动画

```php
// 水平排列(带动画)
Native::windowManager()
    ->getGroup('main-group')
    ->arrangeHorizontal(true);

// 垂直排列(带自定义动画)
Native::windowManager()
    ->getGroup('main-group')
    ->transition()
        ->duration(800)
        ->easing('easeInOutQuart')
    ->arrangeVertical(true);

// 网格布局(带弹性动画)
Native::windowManager()
    ->getGroup('main-group')
    ->transition()
        ->duration(600)
        ->easing('easeOutElastic')
    ->arrangeGrid(2, true);

// 瀑布流布局(带弹跳动画)
Native::windowManager()
    ->getGroup('main-group')
    ->transition()
        ->duration(500)
        ->easing('easeOutBounce')
    ->arrangeCascade(true);
```

### 动画配置

在 `config/native.php` 中配置：

```php
'transitions' => [
    'enabled' => true, // 全局启用/禁用动画
    'duration' => 300, // 默认持续时间
    'easing' => 'easeInOutCubic', // 默认缓动函数
    'presets' => [
        'fast' => [
            'duration' => 150,
            'easing' => 'easeOutQuint'
        ],
        'slow' => [
            'duration' => 600,
            'easing' => 'easeInOutQuint'
        ],
        // ... 更多预设
    ]
]
```

## 文件结构

- `app/controller/IndexController.php` - 主控制器
- `app/controller/NativeController.php` - Native 控制器
- `config/native.php` - NativePHP 配置
- `public/icon.png` - 应用图标
- `view/index/index.html` - 主视图
- `view/index/about.html` - 关于页面
