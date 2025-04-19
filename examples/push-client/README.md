# 推送通知客户端示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的推送通知服务功能创建一个推送通知客户端应用。

## 功能

- 注册设备接收推送通知
- 接收和显示推送通知
- 推送通知历史记录
- 推送通知设置
- 支持多种推送服务提供商（Firebase、APNS、极光推送）

## 文件结构

- `app/controller/Push.php` - 推送控制器
- `view/push/index.html` - 主页面
- `view/push/settings.html` - 设置页面
- `view/push/history.html` - 历史记录页面
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

- **PushNotification**：用于注册设备、发送推送通知和管理推送状态
- **Settings**：用于存储设备信息和推送设置
- **Notification**：用于发送本地通知
- **Dock**：用于在 macOS 上显示徽章数量
- **Tray**：用于在系统托盘上显示未读通知数量
- **Window**：用于打开设置窗口

本示例使用 Settings 类来存储设备信息和推送通知历史记录，而不是使用数据库。这样可以简化示例的实现，并且更适合桌面应用程序的使用场景。

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\PushDevice;
use app\model\PushNotification as PushNotificationModel;
use app\service\PushService;
use Native\ThinkPHP\Facades\PushNotification;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Logger;

class Push extends BaseController
{
    protected $pushService;

    public function __construct(PushService $pushService)
    {
        $this->pushService = $pushService;
    }

    public function index()
    {
        $device = $this->pushService->getCurrentDevice();
        $notifications = PushNotificationModel::order('created_at', 'desc')->limit(10)->select();

        return view('push/index', [
            'device' => $device,
            'notifications' => $notifications,
            'provider' => Settings::get('push.provider', 'firebase'),
        ]);
    }

    public function settings()
    {
        $provider = Settings::get('push.provider', 'firebase');
        $config = Settings::get('push', []);

        return view('push/settings', [
            'provider' => $provider,
            'config' => $config,
        ]);
    }

    public function history()
    {
        $notifications = PushNotificationModel::order('created_at', 'desc')->paginate(20);

        return view('push/history', [
            'notifications' => $notifications,
        ]);
    }

    public function registerDevice()
    {
        $token = input('token');
        $provider = input('provider', 'firebase');

        if (empty($token)) {
            return json(['success' => false, 'message' => '设备令牌不能为空']);
        }

        // 设置推送服务提供商
        PushNotification::setProvider($provider);

        // 注册设备
        $result = PushNotification::registerDevice($token);

        if ($result) {
            // 保存设备信息到数据库
            $device = PushDevice::where('token', $token)->find();

            if (!$device) {
                $device = new PushDevice;
                $device->token = $token;
                $device->provider = $provider;
                $device->platform = $this->getPlatform();
                $device->created_at = date('Y-m-d H:i:s');
            }

            $device->updated_at = date('Y-m-d H:i:s');
            $device->save();

            // 保存设置
            Settings::set('push.device_token', $token);
            Settings::set('push.provider', $provider);

            Notification::send('注册成功', '设备已成功注册，可以接收推送通知');
            Logger::info('设备注册成功', ['token' => $token, 'provider' => $provider]);

            return json(['success' => true, 'device' => $device]);
        } else {
            Logger::error('设备注册失败', ['token' => $token, 'provider' => $provider]);
            return json(['success' => false, 'message' => '设备注册失败']);
        }
    }

    public function unregisterDevice()
    {
        $token = Settings::get('push.device_token');

        if (empty($token)) {
            return json(['success' => false, 'message' => '没有注册的设备']);
        }

        // 注销设备
        $result = PushNotification::unregisterDevice($token);

        if ($result) {
            // 从数据库中删除设备
            PushDevice::where('token', $token)->delete();

            // 清除设置
            Settings::delete('push.device_token');

            Notification::send('注销成功', '设备已成功注销，将不再接收推送通知');
            Logger::info('设备注销成功', ['token' => $token]);

            return json(['success' => true]);
        } else {
            Logger::error('设备注销失败', ['token' => $token]);
            return json(['success' => false, 'message' => '设备注销失败']);
        }
    }

    public function saveSettings()
    {
        $provider = input('provider');
        $config = input('config/a', []);

        if (empty($provider)) {
            return json(['success' => false, 'message' => '推送服务提供商不能为空']);
        }

        // 保存设置
        Settings::set('push.provider', $provider);

        foreach ($config as $key => $value) {
            Settings::set('push.' . $provider . '.' . $key, $value);
        }

        // 设置推送服务提供商
        PushNotification::setProvider($provider);
        PushNotification::setConfig($config);

        Notification::send('设置已保存', '推送通知设置已成功保存');

        return json(['success' => true]);
    }

    public function sendTestNotification()
    {
        $token = Settings::get('push.device_token');

        if (empty($token)) {
            return json(['success' => false, 'message' => '没有注册的设备']);
        }

        $title = '测试通知';
        $body = '这是一条测试推送通知，发送时间：' . date('Y-m-d H:i:s');
        $data = [
            'type' => 'test',
            'time' => time(),
        ];

        // 发送推送通知
        $result = PushNotification::send($token, $title, $body, $data);

        if ($result) {
            // 保存通知记录
            $notification = new PushNotificationModel;
            $notification->title = $title;
            $notification->body = $body;
            $notification->data = json_encode($data);
            $notification->sent_at = date('Y-m-d H:i:s');
            $notification->status = 'sent';
            $notification->save();

            Logger::info('测试通知发送成功', ['token' => $token]);

            return json(['success' => true]);
        } else {
            Logger::error('测试通知发送失败', ['token' => $token]);
            return json(['success' => false, 'message' => '测试通知发送失败']);
        }
    }

    public function getNotifications()
    {
        $limit = input('limit', 10);
        $offset = input('offset', 0);

        $notifications = PushNotificationModel::order('created_at', 'desc')
            ->limit($offset, $limit)
            ->select();

        return json(['success' => true, 'notifications' => $notifications]);
    }

    public function clearHistory()
    {
        PushNotificationModel::where('id', '>', 0)->delete();

        Notification::send('历史记录已清空', '推送通知历史记录已成功清空');

        return json(['success' => true]);
    }

    protected function getPlatform()
    {
        $os = PHP_OS;

        if (strtoupper(substr($os, 0, 3)) === 'WIN') {
            return 'windows';
        } elseif (strtoupper(substr($os, 0, 3)) === 'DAR') {
            return 'macos';
        } elseif (strtoupper(substr($os, 0, 5)) === 'LINUX') {
            return 'linux';
        } else {
            return strtolower($os);
        }
    }
}
```

### 服务

```php
<?php

namespace app\service;

use app\model\PushDevice;
use app\model\PushNotification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Logger;

class PushService
{
    /**
     * 获取当前设备
     *
     * @return \app\model\PushDevice|null
     */
    public function getCurrentDevice()
    {
        $token = Settings::get('push.device_token');

        if (empty($token)) {
            return null;
        }

        return PushDevice::where('token', $token)->find();
    }

    /**
     * 处理接收到的推送通知
     *
     * @param array $notification
     * @return bool
     */
    public function handleReceivedNotification(array $notification)
    {
        try {
            // 保存通知记录
            $model = new PushNotification;
            $model->title = $notification['title'] ?? '';
            $model->body = $notification['body'] ?? '';
            $model->data = isset($notification['data']) ? json_encode($notification['data']) : '{}';
            $model->received_at = date('Y-m-d H:i:s');
            $model->status = 'received';
            $model->save();

            Logger::info('收到推送通知', $notification);

            return true;
        } catch (\Exception $e) {
            Logger::error('处理推送通知失败', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * 获取推送通知统计
     *
     * @return array
     */
    public function getStatistics()
    {
        $total = PushNotification::count();
        $sent = PushNotification::where('status', 'sent')->count();
        $received = PushNotification::where('status', 'received')->count();
        $read = PushNotification::where('status', 'read')->count();

        return [
            'total' => $total,
            'sent' => $sent,
            'received' => $received,
            'read' => $read,
        ];
    }

    /**
     * 标记通知为已读
     *
     * @param int $id
     * @return bool
     */
    public function markAsRead($id)
    {
        $notification = PushNotification::find($id);

        if (!$notification) {
            return false;
        }

        $notification->status = 'read';
        $notification->read_at = date('Y-m-d H:i:s');

        return $notification->save();
    }

    /**
     * 获取未读通知数量
     *
     * @return int
     */
    public function getUnreadCount()
    {
        return PushNotification::where('status', 'received')->count();
    }
}
```

### 视图

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>推送通知客户端</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>推送通知客户端</h2>
            </div>

            <div class="nav-menu">
                <ul>
                    <li class="active"><a href="/push/index">主页</a></li>
                    <li><a href="/push/history">历史记录</a></li>
                    <li><a href="/push/settings">设置</a></li>
                </ul>
            </div>

            <div class="device-info">
                <h3>设备信息</h3>
                {if $device}
                <div class="device-details">
                    <p><strong>提供商：</strong> {$device.provider}</p>
                    <p><strong>平台：</strong> {$device.platform}</p>
                    <p><strong>注册时间：</strong> {$device.created_at}</p>
                    <p><strong>更新时间：</strong> {$device.updated_at}</p>
                    <button onclick="unregisterDevice()">注销设备</button>
                </div>
                {else}
                <div class="device-register">
                    <p>设备尚未注册，请注册设备以接收推送通知。</p>
                    <div class="register-form">
                        <input type="text" id="tokenInput" placeholder="设备令牌">
                        <select id="providerSelect">
                            <option value="firebase" {$provider == 'firebase' ? 'selected' : ''}>Firebase</option>
                            <option value="apns" {$provider == 'apns' ? 'selected' : ''}>APNS</option>
                            <option value="jpush" {$provider == 'jpush' ? 'selected' : ''}>极光推送</option>
                        </select>
                        <button onclick="registerDevice()">注册设备</button>
                    </div>
                </div>
                {/if}
            </div>
        </div>

        <div class="main-content">
            <div class="header">
                <h1>推送通知</h1>
                {if $device}
                <button onclick="sendTestNotification()">发送测试通知</button>
                {/if}
            </div>

            <div class="notifications">
                <h3>最近通知</h3>
                {if empty($notifications)}
                <p>暂无通知</p>
                {else}
                <div class="notification-list">
                    {volist name="notifications" id="notification"}
                    <div class="notification-item">
                        <div class="notification-header">
                            <h4>{$notification.title}</h4>
                            <span class="notification-time">{$notification.received_at ?: $notification.sent_at}</span>
                        </div>
                        <div class="notification-body">
                            <p>{$notification.body}</p>
                        </div>
                        <div class="notification-footer">
                            <span class="notification-status">{$notification.status}</span>
                            {if $notification.status == 'received'}
                            <button onclick="markAsRead({$notification.id})">标记为已读</button>
                            {/if}
                        </div>
                    </div>
                    {/volist}
                </div>
                <div class="view-more">
                    <a href="/push/history">查看更多</a>
                </div>
                {/if}
            </div>
        </div>
    </div>

    <script src="/static/js/app.js"></script>
</body>
</html>
```
