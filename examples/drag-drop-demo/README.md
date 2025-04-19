# 文件拖放示例

这个示例展示了如何使用 NativePHP for ThinkPHP 处理文件拖放功能，包括从操作系统拖放文件到应用程序。

## 功能

- 浏览器原生拖放上传
- 操作系统文件拖放上传
- 文件列表管理
- 文件预览和删除

## 文件结构

- `app/controller/DragDrop.php` - 拖放控制器
- `view/drag_drop/index.html` - 主页面
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

- **Window**：用于监听文件拖放事件
- **Notification**：用于发送通知

本示例实现了两种文件上传方式：

1. **浏览器原生拖放上传**：使用浏览器的 HTML5 拖放 API 和 FormData API 实现文件上传。
2. **操作系统文件拖放上传**：使用 NativePHP 的 Window.onDrop 事件监听操作系统文件拖放，并将文件路径发送到服务器进行处理。

## 注意事项

- 操作系统文件拖放功能仅在桌面应用程序中可用，在浏览器中访问时将回退到浏览器原生拖放上传。
- 上传文件大小和类型限制可以在控制器中配置。
- 上传的文件保存在 `public/uploads` 目录中，确保该目录具有写入权限。
