# 高级功能示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的高级功能，包括：

1. 系统托盘（Tray）
2. 全局快捷键（Global Shortcuts）
3. 剪贴板（Clipboard）
4. 屏幕捕获（Screen Capture）
5. 自动更新（Auto Update）

## 功能

- 系统托盘：创建系统托盘图标，设置托盘菜单，显示气泡通知
- 全局快捷键：注册全局快捷键，执行特定操作
- 剪贴板：读取和写入剪贴板文本、图片、HTML 等
- 屏幕捕获：捕获屏幕截图，录制屏幕
- 自动更新：检查、下载和安装应用更新

## 文件结构

- `app/controller/AdvancedFeaturesController.php` - 主控制器
- `view/advanced-features/index.html` - 主页面
- `view/advanced-features/tray.html` - 系统托盘页面
- `view/advanced-features/shortcuts.html` - 全局快捷键页面
- `view/advanced-features/clipboard.html` - 剪贴板页面
- `view/advanced-features/screen.html` - 屏幕捕获页面
- `view/advanced-features/updater.html` - 自动更新页面

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

- **Tray**：用于创建系统托盘图标和菜单
- **GlobalShortcut**：用于注册全局快捷键
- **Clipboard**：用于读取和写入剪贴板
- **Screen**：用于捕获屏幕截图和录制屏幕
- **Updater**：用于检查、下载和安装应用更新
