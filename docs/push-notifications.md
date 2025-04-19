# 推送通知

NativePHP for ThinkPHP 提供了强大的推送通知功能，允许你的桌面应用程序发送和接收推送通知。本文档将介绍如何使用这些功能。

## 基本概念

推送通知是一种消息传递机制，允许服务器向客户端设备发送消息，即使应用程序未在前台运行。NativePHP for ThinkPHP 支持多种推送服务提供商，包括：

- Firebase Cloud Messaging (FCM)
- Apple Push Notification Service (APNS)
- 极光推送 (JPush)

## 配置

在使用推送通知功能之前，你需要在 `config/native.php` 文件中配置推送服务：

```php
return [
    // 其他配置...
    
    'push' => [
        'provider' => 'firebase', // 默认推送服务提供商
        
        // Firebase 配置
        'firebase' => [
            'server_key' => 'your-server-key',
            'sender_id' => 'your-sender-id',
        ],
        
        // APNS 配置
        'apns' => [
            'cert_path' => 'path/to/certificate.pem',
            'cert_password' => 'certificate-password',
            'environment' => 'production', // 或 'sandbox'
        ],
        
        // 极光推送配置
        'jpush' => [
            'app_key' => 'your-app-key',
            'master_secret' => 'your-master-secret',
        ],
    ],
];
```

## 使用 PushNotification Facade

NativePHP for ThinkPHP 提供了 `PushNotification` Facade，用于发送和管理推送通知。

### 设置推送服务提供商

```php
use Native\ThinkPHP\Facades\PushNotification;

// 设置推送服务提供商
PushNotification::setProvider('firebase');

// 设置推送服务配置
PushNotification::setConfig([
    'server_key' => 'your-server-key',
    'sender_id' => 'your-sender-id',
]);
```

### 注册设备

在发送推送通知之前，你需要注册设备：

```php
// 注册设备
$result = PushNotification::registerDevice('device-token', [
    'platform' => 'windows', // 或 'macos', 'linux'
    'name' => 'My Device',
    'app_version' => '1.0.0',
    'locale' => 'zh-CN',
    'timezone' => 'Asia/Shanghai',
    'metadata' => [
        'user_id' => 1,
        'email' => 'user@example.com',
    ],
]);

if ($result) {
    // 设备注册成功
} else {
    // 设备注册失败
}
```

### 注销设备

当不再需要接收推送通知时，你可以注销设备：

```php
// 注销设备
$result = PushNotification::unregisterDevice('device-token');

if ($result) {
    // 设备注销成功
} else {
    // 设备注销失败
}
```

### 发送推送通知

```php
// 发送推送通知
$reference = PushNotification::send(
    'device-token', // 或者设备令牌数组 ['token1', 'token2']
    '通知标题',
    '通知内容',
    [
        // 附加数据
        'type' => 'message',
        'sender_id' => 1,
        'message_id' => 123,
    ],
    [
        // 选项
        'badge' => 5,
        'sound' => 'default',
        'icon' => 'https://example.com/icon.png',
        'click_action' => 'OPEN_MESSAGE',
        'tag' => 'message',
        'color' => '#FF0000',
        'priority' => 'high',
        'content_available' => true,
        'mutable_content' => true,
        'time_to_live' => 3600,
        'collapse_key' => 'messages',
        'channel_id' => 'general',
    ]
);

if ($reference) {
    // 推送通知发送成功，返回引用ID
} else {
    // 推送通知发送失败
}
```

### 发送带有图标的推送通知

```php
// 发送带有图标的推送通知
$reference = PushNotification::sendWithIcon(
    'device-token',
    '通知标题',
    '通知内容',
    'https://example.com/icon.png',
    ['type' => 'message'],
    ['sound' => 'default']
);
```

### 发送带有声音的推送通知

```php
// 发送带有声音的推送通知
$reference = PushNotification::sendWithSound(
    'device-token',
    '通知标题',
    '通知内容',
    'notification.mp3',
    ['type' => 'message'],
    ['badge' => 5]
);
```

### 发送带有徽章的推送通知

```php
// 发送带有徽章的推送通知
$reference = PushNotification::sendWithBadge(
    'device-token',
    '通知标题',
    '通知内容',
    5,
    ['type' => 'message'],
    ['sound' => 'default']
);
```

### 获取推送状态

```php
// 获取推送状态
$status = PushNotification::getStatus('reference-id');

// 状态包含以下信息
$status = [
    'status' => 'delivered', // 'sent', 'delivered', 'failed'
    'sent_at' => '2023-10-01 12:00:00',
    'delivered_at' => '2023-10-01 12:00:05',
    'error' => null,
];
```

### 取消推送

```php
// 取消推送
$result = PushNotification::cancel('reference-id');

if ($result) {
    // 推送取消成功
} else {
    // 推送取消失败
}
```

### 获取设备信息

```php
// 获取设备信息
$deviceInfo = PushNotification::getDeviceInfo('device-token');

// 设备信息包含以下内容
$deviceInfo = [
    'token' => 'device-token',
    'provider' => 'firebase',
    'platform' => 'windows',
    'name' => 'My Device',
    'app_version' => '1.0.0',
    'locale' => 'zh-CN',
    'timezone' => 'Asia/Shanghai',
    'metadata' => [
        'user_id' => 1,
        'email' => 'user@example.com',
    ],
    'registered_at' => '2023-10-01 10:00:00',
    'last_active_at' => '2023-10-01 12:00:00',
];
```

### 获取推送历史

```php
// 获取推送历史
$history = PushNotification::getHistory(10, 0); // 每页10条，第1页

// 历史记录包含以下内容
$history = [
    [
        'reference' => 'reference-id-1',
        'title' => '通知标题1',
        'body' => '通知内容1',
        'data' => ['type' => 'message'],
        'options' => ['sound' => 'default'],
        'status' => 'delivered',
        'sent_at' => '2023-10-01 12:00:00',
        'delivered_at' => '2023-10-01 12:00:05',
        'error' => null,
    ],
    [
        'reference' => 'reference-id-2',
        'title' => '通知标题2',
        'body' => '通知内容2',
        'data' => ['type' => 'alert'],
        'options' => ['badge' => 5],
        'status' => 'sent',
        'sent_at' => '2023-10-01 11:00:00',
        'delivered_at' => null,
        'error' => null,
    ],
];
```

### 获取推送统计

```php
// 获取推送统计
$statistics = PushNotification::getStatistics('2023-10-01', '2023-10-31');

// 统计信息包含以下内容
$statistics = [
    'sent' => 100,
    'delivered' => 95,
    'opened' => 80,
    'failed' => 5,
];
```

## 接收推送通知

要接收推送通知，你需要在应用程序中实现一个处理推送通知的机制。NativePHP for ThinkPHP 提供了一个事件系统，可以用来处理接收到的推送通知。

### 注册事件监听器

在 `app/event.php` 文件中注册事件监听器：

```php
return [
    'listen' => [
        'native.push_notification.received' => [
            'app\\listener\\PushNotificationReceived',
        ],
    ],
];
```

### 创建事件监听器

创建 `app/listener/PushNotificationReceived.php` 文件：

```php
<?php

namespace app\listener;

use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dock;
use Native\ThinkPHP\Facades\Tray;
use Native\ThinkPHP\Facades\Settings;

class PushNotificationReceived
{
    public function handle($notification)
    {
        // 保存通知
        $this->saveNotification($notification);
        
        // 显示本地通知
        Notification::send(
            $notification['title'],
            $notification['body'],
            [
                'icon' => $notification['options']['icon'] ?? null,
                'sound' => $notification['options']['sound'] ?? 'default',
            ]
        );
        
        // 更新徽章数量
        $unreadCount = $this->getUnreadCount();
        if (PHP_OS === 'Darwin') {
            Dock::setBadgeCount($unreadCount);
        }
        
        // 更新托盘图标
        if ($unreadCount > 0) {
            Tray::setTitle($unreadCount);
        }
    }
    
    protected function saveNotification($notification)
    {
        // 生成通知ID
        $notification['id'] = md5(uniqid('notification', true));
        $notification['status'] = 'received';
        $notification['received_at'] = date('Y-m-d H:i:s');
        
        // 获取通知历史
        $notifications = Settings::get('push.notifications', []);
        
        // 添加新通知
        array_unshift($notifications, $notification);
        
        // 限制通知数量
        if (count($notifications) > 100) {
            $notifications = array_slice($notifications, 0, 100);
        }
        
        // 保存通知历史
        Settings::set('push.notifications', $notifications);
    }
    
    protected function getUnreadCount()
    {
        $notifications = Settings::get('push.notifications', []);
        
        $count = 0;
        foreach ($notifications as $notification) {
            if ($notification['status'] === 'received') {
                $count++;
            }
        }
        
        return $count;
    }
}
```

## 最佳实践

1. **安全性**：确保你的推送服务配置（如 API 密钥和证书）安全存储，不要将它们硬编码在应用程序中。

2. **错误处理**：始终检查推送通知的发送结果，并适当处理错误。

3. **用户体验**：不要过度发送推送通知，这可能会导致用户禁用通知或卸载应用程序。

4. **本地化**：根据用户的语言和地区设置，提供本地化的推送通知内容。

5. **深度链接**：使用推送通知的附加数据来实现深度链接，使用户可以直接导航到应用程序中的特定页面。

## 故障排除

### 设备注册失败

- 检查设备令牌是否有效
- 确保推送服务提供商配置正确
- 检查网络连接

### 推送通知发送失败

- 检查设备是否已注册
- 确保推送服务提供商配置正确
- 检查网络连接
- 查看推送服务提供商的错误日志

### 推送通知未显示

- 检查设备是否已注册
- 确保应用程序有权限显示通知
- 检查通知设置是否已启用
- 确保推送服务提供商配置正确
