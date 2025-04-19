# 系统托盘示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Tray 功能创建一个系统托盘应用程序。

## 功能

- 显示和隐藏系统托盘图标
- 设置托盘图标和提示文本
- 显示气泡提示
- 设置托盘菜单
- 处理托盘事件（点击、双击、右键点击）
- 平台特定功能（Windows 高亮显示、macOS 托盘标题）

## 文件结构

- `app/controller/Tray.php` - 托盘控制器
- `view/tray/index.html` - 主页面
- `view/tray/settings.html` - 托盘设置页面
- `view/tray/app-settings.html` - 应用设置页面
- `public/static/css/app.css` - 前端 CSS 样式
- `public/static/images/tray-icon.png` - 托盘图标

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

- **Tray**：用于创建和管理系统托盘图标
- **Menu**：用于创建托盘菜单
- **Notification**：用于发送通知
- **Window**：用于打开和管理窗口
- **Settings**：用于存储应用设置

本示例使用 Settings 类来存储托盘设置和应用设置，而不是使用数据库。这样可以简化示例的实现，并且更适合桌面应用程序的使用场景。

## 注意事项

- 系统托盘功能在不同平台上的表现可能有所不同。例如，macOS 上的托盘图标（称为状态栏图标）位于屏幕顶部，而 Windows 和 Linux 上的托盘图标位于屏幕底部。
- 气泡提示功能在 macOS 上不可用，将被忽略。
- 托盘标题功能仅在 macOS 上可用。
- 高亮显示功能仅在 Windows 上可用。
