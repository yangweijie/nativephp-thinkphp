# NativePHP-ThinkPHP 简单示例应用

这是一个使用 NativePHP-ThinkPHP 构建的简单桌面应用程序示例。

## 功能

- 主窗口显示
- 系统托盘图标
- 应用程序菜单
- 系统通知
- 全局快捷键

## 安装

1. 克隆仓库
2. 安装依赖：`composer install`
3. 运行应用：`php think native:serve`

## 文件结构

- `app/controller/IndexController.php` - 主控制器
- `app/controller/NativeController.php` - Native 控制器
- `config/native.php` - NativePHP 配置
- `public/icon.png` - 应用图标
- `view/index/index.html` - 主视图
