# 自动更新示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 AutoUpdater 功能实现应用程序的自动更新。

## 功能

- 设置更新服务器 URL
- 检查更新
- 下载更新
- 安装更新
- 监听更新事件
- 显示更新进度
- 重启应用并安装更新

## 文件结构

- `app/controller/Updater.php` - 更新控制器
- `view/updater/index.html` - 主页面
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

- **AutoUpdater**：用于检查、下载和安装更新
- **Notification**：用于发送通知

本示例实现了完整的自动更新流程：

1. 设置更新服务器 URL
2. 检查更新
3. 下载更新
4. 安装更新
5. 重启应用并安装更新

同时，本示例还实现了更新事件的监听和处理，包括：

- 更新检查事件
- 更新可用事件
- 更新不可用事件
- 更新下载进度事件
- 更新下载完成事件
- 更新错误事件

## 更新服务器

更新服务器应该返回符合 Electron 自动更新格式的 JSON 数据，例如：

```json
{
  "version": "1.0.1",
  "releaseDate": "2023-01-01",
  "url": "https://example.com/updates/app-1.0.1.zip",
  "releaseNotes": "这是一个新版本，修复了一些 bug。"
}
```

## 注意事项

- 自动更新功能仅在桌面应用程序中可用，在浏览器中访问时将模拟更新流程。
- 更新服务器 URL 应该返回符合 Electron 自动更新格式的 JSON 数据。
- 在实际应用中，应该使用 WebSocket 或其他实时通信技术来实现更新事件的实时推送。
