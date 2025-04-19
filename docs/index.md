# NativePHP for ThinkPHP

NativePHP for ThinkPHP 是一个用于构建桌面应用程序的 ThinkPHP 扩展包。它允许你使用熟悉的 ThinkPHP 框架和 PHP 语言构建跨平台的桌面应用程序。

## 特点

- **跨平台**：支持 Windows、macOS 和 Linux
- **熟悉的开发体验**：使用 ThinkPHP 框架和 PHP 语言
- **丰富的 API**：提供丰富的 API 用于与桌面环境交互
- **易于使用**：简单的 API 和清晰的文档
- **可扩展**：可以轻松扩展和定制

## 功能

NativePHP for ThinkPHP 提供了丰富的功能，包括：

- **应用程序管理**：获取应用信息、退出应用、重启应用等
- **窗口管理**：打开新窗口、关闭窗口、设置窗口大小和位置等
- **菜单管理**：创建应用菜单、创建上下文菜单、添加菜单项和子菜单
- **通知管理**：发送系统通知、带图标的通知、带操作的通知
- **剪贴板管理**：读取剪贴板文本、写入文本到剪贴板、读取/写入剪贴板图片
- **全局快捷键**：注册全局快捷键、注销快捷键
- **系统托盘**：创建托盘图标、设置托盘菜单、设置托盘提示文本
- **对话框**：打开文件对话框、保存文件对话框、选择文件夹对话框、消息对话框
- **文件系统**：读取/写入文件、复制/移动文件、创建/删除目录
- **系统信息**：获取操作系统信息、获取硬件信息、打开外部 URL
- **屏幕捕获**：捕获屏幕截图、捕获窗口截图、屏幕录制
- **自动更新**：检查更新、下载更新、安装更新
- **网络请求**：发送 HTTP 请求、下载文件
- **数据库管理**：创建/删除表、插入/更新/删除数据、查询数据、备份/恢复数据库
- **设置管理**：获取/设置设置值、导入/导出设置
- **进程管理**：运行命令、运行 PHP 脚本、运行 ThinkPHP 命令、获取进程输出
- **打印功能**：打印 HTML、打印文件、打印到 PDF、显示打印预览
- **语音识别和合成**：语音识别、语音合成、文本转音频、音频转文本
- **设备管理**：蓝牙设备管理、USB 设备管理
- **地理位置服务**：获取当前位置、监视位置变化、计算距离、地址解析
- **推送通知服务**：发送推送通知、接收推送通知
- **日志工具**：记录日志、设置日志级别、轮换日志文件
- **缓存工具**：设置/获取缓存、缓存过期、清理缓存
- **事件工具**：事件监听、事件触发、事件管理
- **配置工具**：读取/写入配置、导入/导出配置

## 安装

使用 Composer 安装 NativePHP for ThinkPHP：

```bash
composer require nativephp/thinkphp
```

## 快速开始

### 初始化应用

```bash
php think native:init
```

### 启动应用

```bash
php think native:serve
```

### 构建应用

```bash
php think native:build
```

## 示例

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class Index extends BaseController
{
    public function index()
    {
        // 获取应用信息
        $name = App::name();
        $version = App::version();
        
        // 发送通知
        Notification::send('欢迎使用', '欢迎使用 ' . $name . ' ' . $version);
        
        return view('index/index', [
            'name' => $name,
            'version' => $version,
        ]);
    }
    
    public function openNewWindow()
    {
        // 打开新窗口
        Window::open('/window/index', [
            'title' => '新窗口',
            'width' => 800,
            'height' => 600,
        ]);
        
        return json(['success' => true]);
    }
    
    public function quit()
    {
        // 退出应用
        App::quit();
        
        return json(['success' => true]);
    }
}
```

## 文档

查看完整文档，请访问 [NativePHP for ThinkPHP 文档](https://nativephp.thinkphp.cn)。

## 示例应用

查看示例应用，请访问 [NativePHP for ThinkPHP 示例应用](https://github.com/nativephp/thinkphp/tree/master/examples)。

## 贡献

欢迎贡献代码和提出问题。请查看 [贡献指南](https://github.com/nativephp/thinkphp/blob/master/CONTRIBUTING.md)。

## 许可证

NativePHP for ThinkPHP 是开源软件，使用 [MIT 许可证](https://github.com/nativephp/thinkphp/blob/master/LICENSE)。
