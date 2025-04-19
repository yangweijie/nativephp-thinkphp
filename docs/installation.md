# 安装和配置

本文档将指导你如何安装和配置 NativePHP for ThinkPHP。

## 系统要求

- PHP >= 7.4
- ThinkPHP >= 6.0
- Composer
- Node.js >= 14.0
- npm >= 6.0

## 安装

### 使用 Composer 安装

```bash
composer require nativephp/thinkphp
```

### 初始化应用

安装完成后，你需要初始化 NativePHP 应用：

```bash
php think native:init
```

这个命令会创建必要的配置文件和目录结构。

## 配置

### 配置文件

NativePHP for ThinkPHP 的配置文件位于 `config/native.php`。你可以根据需要修改这个文件。

```php
return [
    /*
    |--------------------------------------------------------------------------
    | 应用名称
    |--------------------------------------------------------------------------
    |
    | 这个值是应用程序的名称。
    |
    */
    'name' => 'NativePHP App',

    /*
    |--------------------------------------------------------------------------
    | 应用 ID
    |--------------------------------------------------------------------------
    |
    | 这个值是应用程序的唯一标识符。
    |
    */
    'app_id' => 'com.nativephp.app',

    /*
    |--------------------------------------------------------------------------
    | 应用版本
    |--------------------------------------------------------------------------
    |
    | 这个值是应用程序的版本。
    |
    */
    'version' => '1.0.0',

    // 更多配置...
];
```

### 环境变量

你也可以使用环境变量来配置 NativePHP for ThinkPHP。将以下变量添加到 `.env` 文件中：

```
NATIVEPHP_APP_NAME=NativePHP App
NATIVEPHP_APP_ID=com.nativephp.app
NATIVEPHP_APP_VERSION=1.0.0
```

## 启动应用

安装和配置完成后，你可以启动 NativePHP 应用：

```bash
php think native:serve
```

这个命令会启动一个开发服务器，并打开一个桌面窗口，显示你的应用。

## 构建应用

当你的应用准备好发布时，你可以构建它：

```bash
php think native:build
```

这个命令会为 Windows、macOS 和 Linux 构建应用程序。构建的应用程序位于 `build` 目录中。

### 构建选项

你可以使用以下选项来自定义构建过程：

- `--platform=<platform>`：指定要构建的平台（windows、macos、linux）
- `--arch=<arch>`：指定要构建的架构（x64、arm64）
- `--no-prune`：不删除开发依赖
- `--no-package`：不打包应用程序
- `--no-sign`：不签名应用程序

例如，要仅为 Windows 构建应用程序：

```bash
php think native:build --platform=windows
```

## 故障排除

### 常见问题

#### 1. 安装时出现 "Could not find package nativephp/thinkphp"

确保你的 Composer 配置正确，并且包已发布到 Packagist。

#### 2. 启动应用时出现 "Error: Cannot find module 'electron'"

确保你已安装 Node.js 和 npm，并且已安装 Electron：

```bash
npm install -g electron
```

#### 3. 构建应用时出现 "Error: Cannot find module 'electron-builder'"

确保你已安装 electron-builder：

```bash
npm install -g electron-builder
```

### 获取帮助

如果你遇到问题，可以通过以下方式获取帮助：

- [GitHub Issues](https://github.com/nativephp/thinkphp/issues)
- [ThinkPHP 社区](https://www.thinkphp.cn/topic/index/id/nativephp.html)
- [NativePHP for ThinkPHP 文档](https://nativephp.thinkphp.cn)
