# WebSocket 示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 WebSocket 功能与服务器进行实时通信。

## 功能

- 连接到 WebSocket 服务器
- 发送和接收消息
- 处理连接断开和重连
- 处理心跳

## 文件结构

- `app/controller/WebSocketDemo.php` - WebSocket 控制器
- `view/websocket-demo/index.html` - 主页面
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

- **Http::websocket**：用于创建 WebSocket 连接
- **WebSocket\Client**：用于管理 WebSocket 连接
- **Window**：用于创建和管理窗口
- **Notification**：用于发送通知

本示例实现了一个简单的聊天客户端，可以连接到 WebSocket 服务器，发送和接收消息。
