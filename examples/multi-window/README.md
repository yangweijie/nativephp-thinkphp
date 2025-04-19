# 多窗口示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Window 功能创建和管理多个窗口，以及实现窗口间通信。

## 功能

- 创建和管理多个窗口
- 设置窗口属性（标题、大小、位置等）
- 控制窗口状态（最小化、最大化、恢复等）
- 窗口间通信
- 监听窗口事件（关闭、聚焦、失去焦点、移动、调整大小等）

## 文件结构

- `app/controller/Window.php` - 窗口控制器
- `view/window/index.html` - 主窗口页面
- `view/window/child.html` - 子窗口页面
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

- **Window**：用于创建和管理窗口
- **Notification**：用于发送通知
- **Settings**：用于存储窗口信息和消息

本示例使用 Settings 类来存储窗口信息和消息，而不是使用数据库。这样可以简化示例的实现，并且更适合桌面应用程序的使用场景。

## 窗口间通信

本示例实现了一个简单的窗口间通信机制，允许不同窗口之间发送和接收消息。通信流程如下：

1. 发送窗口通过 API 将消息发送到服务器
2. 服务器将消息存储在 Settings 中
3. 接收窗口定期检查是否有新消息
4. 如果有新消息，接收窗口将显示消息并发送通知

这种实现方式简单但有效，适合大多数桌面应用程序的使用场景。对于更复杂的通信需求，可以考虑使用 WebSocket 或其他实时通信技术。

## 窗口事件

本示例展示了如何监听窗口事件，包括：

- **onClose**：窗口关闭事件
- **onFocus**：窗口获得焦点事件
- **onBlur**：窗口失去焦点事件
- **onMove**：窗口移动事件
- **onResize**：窗口调整大小事件

这些事件可以用于实现各种功能，例如保存窗口位置和大小、在窗口关闭前提示保存数据等。
