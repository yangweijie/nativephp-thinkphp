# 键盘快捷键示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Keyboard 功能创建一个键盘快捷键管理应用程序。

## 功能

- 注册和管理应用程序快捷键
- 注册和管理全局快捷键
- 模拟键盘按键和文本输入
- 监听键盘事件
- 记录快捷键触发历史
- 查看键盘布局信息

## 文件结构

- `app/controller/Keyboard.php` - 键盘控制器
- `view/keyboard/index.html` - 主页面
- `view/keyboard/history.html` - 历史记录页面
- `view/keyboard/listener.html` - 键盘监听器页面
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

- **Keyboard**：用于注册和管理快捷键、模拟键盘输入和监听键盘事件
- **Settings**：用于存储快捷键信息和历史记录
- **Notification**：用于发送本地通知

本示例使用 Settings 类来存储快捷键信息和历史记录，而不是使用数据库。这样可以简化示例的实现，并且更适合桌面应用程序的使用场景。

## 注意事项

- 全局快捷键功能允许在应用程序不在前台时也能触发快捷键，但可能会与操作系统或其他应用程序的快捷键冲突。
- 模拟键盘输入功能可能受到操作系统安全限制，某些应用程序可能会阻止模拟输入。
- 键盘监听器功能可以用来监听键盘事件，但不应该用于记录用户的敏感信息（如密码）。
