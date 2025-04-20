# NativePHP-ThinkPHP 简单示例应用

这是一个使用 NativePHP-ThinkPHP 构建的简单桌面应用程序示例。

## 功能

- 主窗口显示
- 系统托盘图标
- 应用程序菜单
- 系统通知
- 全局快捷键
- 窗口分组管理

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

## 文件结构

- `app/controller/IndexController.php` - 主控制器
- `app/controller/NativeController.php` - Native 控制器
- `config/native.php` - NativePHP 配置
- `public/icon.png` - 应用图标
- `view/index/index.html` - 主视图
- `view/index/about.html` - 关于页面
