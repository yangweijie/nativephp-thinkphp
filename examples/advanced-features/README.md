# 高级功能示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的高级功能，包括：

1. 文件上传和下载
2. 使用 DTOs（数据传输对象）
3. 事件系统
4. 高级菜单和窗口管理

## 功能

- 文件上传和下载
- 使用 DTOs 配置窗口和菜单
- 使用事件系统处理窗口和菜单事件
- 高级窗口管理（透明窗口、模态窗口等）
- 高级菜单管理（动态菜单、上下文菜单等）

## 文件结构

- `app/controller/AdvancedFeaturesController.php` - 主控制器
- `view/advanced-features/index.html` - 主页面
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

- **Client::upload**：用于上传文件
- **Client::download**：用于下载文件
- **DTOs**：用于配置窗口和菜单
- **Events**：用于处理窗口和菜单事件
- **Window**：用于创建和管理窗口
- **Menu**：用于创建和管理菜单
- **Dialog**：用于显示对话框
