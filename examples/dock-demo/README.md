# Dock 功能示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Dock 功能创建一个与 macOS Dock 交互的桌面应用程序。

## 功能

- 设置徽章文本和计数
- 弹跳 Dock 图标
- 设置下载进度条
- 设置工具提示
- 设置 Dock 菜单
- 控制 Dock 图标的可见性

## 文件结构

- `app/controller/Dock.php` - Dock 控制器
- `view/dock/index.html` - 主页面
- `view/dock/settings.html` - 设置页面
- `view/dock/about.html` - 关于页面
- `public/static/css/app.css` - 前端 CSS 样式

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 实现说明

本示例使用 NativePHP for ThinkPHP 的以下功能：

- **Dock**：用于与 macOS Dock 进行交互
- **Settings**：用于存储 Dock 设置
- **Notification**：用于发送本地通知
- **Window**：用于打开设置和关于窗口

本示例使用 Settings 类来存储 Dock 设置，而不是使用数据库。这样可以简化示例的实现，并且更适合桌面应用程序的使用场景。

## 注意事项

- Dock 功能仅在 macOS 平台上可用。在其他平台上，这些功能将被忽略。
- 本示例会检查当前平台是否为 macOS，并在非 macOS 平台上显示警告信息。
