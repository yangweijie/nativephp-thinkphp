# 高级功能增强版示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的高级功能，包括：

1. 国际化（I18n）功能
2. 主题（Theme）功能
3. 设备（Device）功能
4. 地理位置（Geolocation）功能
5. 推送通知（PushNotification）功能

## 功能

- 国际化：多语言支持、语言切换、本地化
- 主题：明暗主题切换、自定义主题、主题编辑器
- 设备：蓝牙设备管理、USB 设备管理、设备连接和通信
- 地理位置：位置获取、地图显示、距离计算、地理编码
- 推送通知：设备注册、发送通知、通知历史记录

## 文件结构

- `app/controller/AdvancedFeaturesController.php` - 主控制器
- `view/advanced-features/index.html` - 主页面
- `view/advanced-features/i18n.html` - 国际化页面
- `view/advanced-features/theme.html` - 主题页面
- `view/advanced-features/device.html` - 设备页面
- `view/advanced-features/geolocation.html` - 地理位置页面
- `view/advanced-features/push.html` - 推送通知页面

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

- **I18n**：用于多语言支持和本地化
- **Theme**：用于主题管理和切换
- **Device**：用于设备管理和通信
- **Geolocation**：用于地理位置服务
- **PushNotification**：用于推送通知服务
