# 系统对话框示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的 Dialog 功能创建各种系统对话框。

## 功能

- 消息对话框（信息、错误、警告、问题）
- 确认对话框
- 输入对话框
- 文件对话框（打开文件、保存文件、选择文件夹）
- 颜色选择对话框
- 字体选择对话框
- 证书选择对话框

## 文件结构

- `app/controller/Dialog.php` - 对话框控制器
- `view/dialog/index.html` - 主页面
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

- **Dialog**：用于显示各种系统对话框
- **Notification**：用于发送通知

本示例展示了如何使用 Dialog 类的各种方法来显示不同类型的系统对话框，包括：

- **message**：显示基本消息对话框
- **info**：显示信息消息对话框
- **error**：显示错误消息对话框
- **warning**：显示警告消息对话框
- **question**：显示问题消息对话框
- **confirm**：显示确认对话框
- **prompt**：显示输入对话框
- **openFile**：显示打开文件对话框
- **saveFile**：显示保存文件对话框
- **selectFolder**：显示选择文件夹对话框
- **color**：显示颜色选择对话框
- **font**：显示字体选择对话框
- **certificate**：显示证书选择对话框

## 注意事项

- 某些对话框类型（如字体选择、证书选择）可能在某些平台上不可用。
- 文件对话框的过滤器格式因平台而异，但本示例使用了一种通用格式，应该在大多数平台上工作。
- 对话框的外观和行为可能因操作系统而异。
