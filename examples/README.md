# NativePHP for ThinkPHP 示例应用

这个目录包含了一些示例应用，展示如何使用 NativePHP for ThinkPHP 包构建桌面应用程序。

## 示例列表

1. [基础示例](basic/README.md) - 展示基本的窗口、菜单和通知功能
2. [文件管理器](file-manager/README.md) - 一个简单的文件管理器应用
3. [笔记应用](note-app/README.md) - 一个支持 Markdown 的笔记应用
4. [语音助手](voice-assistant/README.md) - 一个使用语音识别和合成的语音助手应用
5. [设备管理器](device-manager/README.md) - 一个用于管理蓝牙和 USB 设备的应用
6. [地图应用](map-app/README.md) - 一个使用地理位置服务的地图应用
7. [推送通知客户端](push-client/README.md) - 一个接收推送通知的客户端应用
8. [任务管理器](task-manager/README.md) - 一个简单的任务管理器应用
9. [桌面聊天客户端](chat-client/README.md) - 一个功能完整的桌面聊天客户端应用
10. [代码编辑器](code-editor/README.md) - 一个轻量级的代码编辑器应用

## 如何运行示例

1. 克隆仓库：

```bash
git clone https://github.com/nativephp/thinkphp.git
cd thinkphp/examples/[example-name]
```

2. 安装依赖：

```bash
composer install
```

3. 启动应用：

```bash
php think native:serve
```

4. 构建应用：

```bash
php think native:build
```

## 示例应用结构

每个示例应用都有以下结构：

- `README.md` - 示例应用的说明文档
- `app/` - 应用代码
- `config/` - 配置文件
- `public/` - 公共资源
- `view/` - 视图文件
- `think` - ThinkPHP 命令行工具
