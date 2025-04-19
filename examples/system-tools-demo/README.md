# 系统工具示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的系统工具功能，包括：

1. 文件系统（FileSystem）
2. 系统（System）
3. 语音识别和合成（Speech）
4. 电源监控（PowerMonitor）
5. 网络状态（Network）

## 功能

- 文件系统：浏览、创建、修改和删除文件和目录
- 系统：获取系统信息、执行系统命令、管理进程
- 语音识别和合成：语音识别、语音合成、语音命令
- 电源监控：监控电源状态、电池状态、系统休眠和唤醒
- 网络状态：监控网络连接状态、获取网络信息、测试网络连接

## 文件结构

- `app/controller/SystemToolsController.php` - 主控制器
- `view/system-tools/index.html` - 主页面
- `view/system-tools/filesystem.html` - 文件系统页面
- `view/system-tools/system.html` - 系统页面
- `view/system-tools/speech.html` - 语音识别和合成页面
- `view/system-tools/power.html` - 电源监控页面
- `view/system-tools/network.html` - 网络状态页面

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

- **FileSystem**：用于浏览、创建、修改和删除文件和目录
- **System**：用于获取系统信息、执行系统命令、管理进程
- **Speech**：用于语音识别、语音合成、语音命令
- **PowerMonitor**：用于监控电源状态、电池状态、系统休眠和唤醒
- **Network**：用于监控网络连接状态、获取网络信息、测试网络连接
