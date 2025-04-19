# 桌面快捷方式示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Shortcut 功能创建和管理桌面快捷方式，以及设置开机自启动。

## 功能

- 创建和删除桌面快捷方式
- 创建和删除开始菜单快捷方式
- 创建和删除自定义快捷方式
- 设置和管理开机自启动
- 检查快捷方式是否存在

## 文件结构

- `app/controller/Shortcut.php` - 快捷方式控制器
- `view/shortcut/index.html` - 主页面
- `public/static/js/app.js` - 前端 JavaScript 代码
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

- **Shortcut**：用于创建和管理桌面快捷方式
- **Notification**：用于发送通知

本示例实现了以下功能：

1. **桌面快捷方式管理**：创建和删除桌面快捷方式
2. **开始菜单快捷方式管理**：创建和删除开始菜单快捷方式
3. **自定义快捷方式管理**：创建、删除和检查自定义快捷方式
4. **开机自启动管理**：设置和管理开机自启动

## 注意事项

- 桌面快捷方式功能在不同操作系统上的行为可能有所不同。
- 在 macOS 上，开机自启动设置需要用户授权。
- 在 Windows 上，创建快捷方式需要管理员权限。
- 在 Linux 上，开机自启动设置可能需要额外的配置。
