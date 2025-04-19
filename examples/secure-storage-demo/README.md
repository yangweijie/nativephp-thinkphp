# 安全存储示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的安全存储功能，包括：

1. 数据库同步（Database Sync）功能
2. 离线存储（Offline Storage）功能
3. 安全存储（Secure Storage）功能
4. 加密（Encryption）功能
5. 日志（Logging）功能

## 功能

- 数据库同步：本地数据库与远程数据库同步
- 离线存储：在没有网络连接时存储数据
- 安全存储：安全地存储敏感数据
- 加密：加密和解密数据
- 日志：记录应用程序活动

## 文件结构

- `app/controller/SecureStorageController.php` - 主控制器
- `view/secure-storage/index.html` - 主页面
- `view/secure-storage/database.html` - 数据库同步页面
- `view/secure-storage/offline.html` - 离线存储页面
- `view/secure-storage/secure.html` - 安全存储页面
- `view/secure-storage/encryption.html` - 加密页面
- `view/secure-storage/logging.html` - 日志页面

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

- **Database**：用于数据库同步
- **Settings**：用于离线存储
- **FileSystem**：用于文件操作
- **Logger**：用于日志记录
