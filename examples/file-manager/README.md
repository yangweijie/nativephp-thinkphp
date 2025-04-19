# 文件管理器示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的文件系统功能创建一个简单的文件管理器应用。

## 功能

- 浏览文件和目录
- 创建、重命名和删除文件/目录
- 复制和移动文件/目录
- 查看文件内容
- 编辑文本文件
- 打开文件（使用系统默认应用）
- 显示文件属性
- 在文件夹中显示文件
- 错误处理和通知

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/File.php` - 文件控制器
- `app/service/FileService.php` - 文件服务
- `view/index/index.html` - 主页面
- `view/file/index.html` - 文件列表页面
- `view/file/view.html` - 文件查看页面
- `view/file/edit.html` - 文件编辑页面
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

- **FileSystem**：用于文件和目录操作
- **Shell**：用于打开文件和在文件夹中显示文件
- **Dialog**：用于选择文件夹
- **System**：用于获取系统路径
- **Notification**：用于发送通知
- **Window**：用于窗口管理
- **Menu**：用于创建应用菜单
- **Tray**：用于创建系统托盘
- **GlobalShortcut**：用于注册全局快捷键

## 错误处理

本示例实现了完善的错误处理机制：

1. 使用 try-catch 捕获异常
2. 显示用户友好的错误消息
3. 使用通知系统提醒用户错误
4. 在出错时提供恢复选项

## 注意事项

- 文件操作可能需要适当的权限
- 大文件的读写可能会影响性能
- 某些文件类型可能无法正确预览
- 在不同操作系统上，文件路径的处理方式可能不同
