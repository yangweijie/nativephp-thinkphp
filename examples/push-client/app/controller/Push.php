<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\PushNotification;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Dock;
use Native\ThinkPHP\Facades\Tray;

class Push extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取设备信息
        $device = $this->getCurrentDevice();
        
        // 获取通知历史
        $notifications = $this->getNotificationHistory();
        
        // 获取推送服务提供商
        $provider = Settings::get('push.provider', 'firebase');
        
        return view('push/index', [
            'device' => $device,
            'notifications' => $notifications,
            'provider' => $provider,
        ]);
    }
    
    /**
     * 显示设置页面
     *
     * @return \think\Response
     */
    public function settings()
    {
        // 获取推送服务提供商
        $provider = Settings::get('push.provider', 'firebase');
        
        // 获取推送服务配置
        $config = Settings::get('push', []);
        
        return view('push/settings', [
            'provider' => $provider,
            'config' => $config,
        ]);
    }
    
    /**
     * 显示历史记录页面
     *
     * @return \think\Response
     */
    public function history()
    {
        // 获取通知历史
        $notifications = $this->getNotificationHistory(50);
        
        return view('push/history', [
            'notifications' => $notifications,
        ]);
    }
    
    /**
     * 注册设备
     *
     * @return \think\Response
     */
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
        $result = PushNotification::registerDevice($token, [
            'platform' => $this->getPlatform(),
            'name' => 'NativePHP Device',
            'app_version' => config('native.version', '1.0.0'),
        ]);
        
        if ($result) {
            // 保存设备信息
            Settings::set('push.device', [
                'token' => $token,
                'provider' => $provider,
                'registered_at' => date('Y-m-d H:i:s'),
            ]);
            
            // 保存推送服务提供商
            Settings::set('push.provider', $provider);
            
            // 发送通知
            Notification::send('设备注册成功', '您的设备已成功注册，可以接收推送通知了');
            
            // 更新 Dock 徽章
            if (PHP_OS === 'Darwin') {
                Dock::setBadgeCount(0);
            }
            
            // 更新托盘图标
            Tray::setTitle('');
            
            return json(['success' => true, 'message' => '设备注册成功']);
        }
        
        return json(['success' => false, 'message' => '设备注册失败']);
    }
    
    /**
     * 注销设备
     *
     * @return \think\Response
     */
    public function unregisterDevice()
    {
        // 获取设备信息
        $device = $this->getCurrentDevice();
        
        if (!$device) {
            return json(['success' => false, 'message' => '设备未注册']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($device['provider']);
        
        // 注销设备
        $result = PushNotification::unregisterDevice($device['token']);
        
        if ($result) {
            // 删除设备信息
            Settings::forget('push.device');
            
            // 发送通知
            Notification::send('设备注销成功', '您的设备已成功注销，将不再接收推送通知');
            
            // 更新 Dock 徽章
            if (PHP_OS === 'Darwin') {
                Dock::clearBadge();
            }
            
            // 更新托盘图标
            Tray::setTitle('');
            
            return json(['success' => true, 'message' => '设备注销成功']);
        }
        
        return json(['success' => false, 'message' => '设备注销失败']);
    }
    
    /**
     * 保存设置
     *
     * @return \think\Response
     */
    public function saveSettings()
    {
        $provider = input('provider');
        $config = input('config/a', []);
        
        if (empty($provider)) {
            return json(['success' => false, 'message' => '推送服务提供商不能为空']);
        }
        
        // 保存推送服务提供商
        Settings::set('push.provider', $provider);
        
        // 保存推送服务配置
        Settings::set('push', array_merge(Settings::get('push', []), $config));
        
        // 发送通知
        Notification::send('设置保存成功', '推送通知设置已成功保存');
        
        return json(['success' => true, 'message' => '设置保存成功']);
    }
    
    /**
     * 发送测试通知
     *
     * @return \think\Response
     */
    public function sendTestNotification()
    {
        // 获取设备信息
        $device = $this->getCurrentDevice();
        
        if (!$device) {
            return json(['success' => false, 'message' => '设备未注册']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($device['provider']);
        
        // 发送测试通知
        $reference = PushNotification::send(
            $device['token'],
            '测试通知',
            '这是一条测试推送通知，用于验证推送功能是否正常工作。',
            ['type' => 'test'],
            ['sound' => 'default']
        );
        
        if ($reference) {
            // 保存通知记录
            $this->saveNotification([
                'reference' => $reference,
                'title' => '测试通知',
                'body' => '这是一条测试推送通知，用于验证推送功能是否正常工作。',
                'data' => ['type' => 'test'],
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
            ]);
            
            return json(['success' => true, 'message' => '测试通知已发送']);
        }
        
        return json(['success' => false, 'message' => '测试通知发送失败']);
    }
    
    /**
     * 获取通知历史
     *
     * @return \think\Response
     */
    public function getNotifications()
    {
        $limit = input('limit', 10);
        $offset = input('offset', 0);
        
        // 获取通知历史
        $notifications = $this->getNotificationHistory($limit, $offset);
        
        return json(['success' => true, 'notifications' => $notifications]);
    }
    
    /**
     * 清空历史记录
     *
     * @return \think\Response
     */
    public function clearHistory()
    {
        // 清空通知历史
        Settings::forget('push.notifications');
        
        // 发送通知
        Notification::send('历史记录已清空', '推送通知历史记录已成功清空');
        
        // 更新 Dock 徽章
        if (PHP_OS === 'Darwin') {
            Dock::clearBadge();
        }
        
        // 更新托盘图标
        Tray::setTitle('');
        
        return json(['success' => true, 'message' => '历史记录已清空']);
    }
    
    /**
     * 标记通知为已读
     *
     * @return \think\Response
     */
    public function markAsRead()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '通知ID不能为空']);
        }
        
        // 获取通知历史
        $notifications = Settings::get('push.notifications', []);
        
        foreach ($notifications as $key => $notification) {
            if ($notification['id'] == $id) {
                $notifications[$key]['status'] = 'read';
                $notifications[$key]['read_at'] = date('Y-m-d H:i:s');
                break;
            }
        }
        
        // 保存通知历史
        Settings::set('push.notifications', $notifications);
        
        // 更新未读通知数量
        $unreadCount = $this->getUnreadCount();
        
        // 更新 Dock 徽章
        if (PHP_OS === 'Darwin') {
            Dock::setBadgeCount($unreadCount);
        }
        
        // 更新托盘图标
        if ($unreadCount > 0) {
            Tray::setTitle($unreadCount);
        } else {
            Tray::setTitle('');
        }
        
        return json(['success' => true, 'message' => '通知已标记为已读']);
    }
    
    /**
     * 打开通知设置
     *
     * @return \think\Response
     */
    public function openNotificationSettings()
    {
        // 打开设置窗口
        Window::open('/push/settings', [
            'title' => '推送通知设置',
            'width' => 600,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    /**
     * 获取当前设备信息
     *
     * @return array|null
     */
    protected function getCurrentDevice()
    {
        return Settings::get('push.device');
    }
    
    /**
     * 获取通知历史
     *
     * @param int $limit
     * @param int $offset
     * @return array
     */
    protected function getNotificationHistory($limit = 10, $offset = 0)
    {
        $notifications = Settings::get('push.notifications', []);
        
        // 按时间倒序排序
        usort($notifications, function ($a, $b) {
            $aTime = strtotime($a['sent_at'] ?? $a['received_at'] ?? date('Y-m-d H:i:s'));
            $bTime = strtotime($b['sent_at'] ?? $b['received_at'] ?? date('Y-m-d H:i:s'));
            return $bTime - $aTime;
        });
        
        // 分页
        return array_slice($notifications, $offset, $limit);
    }
    
    /**
     * 保存通知记录
     *
     * @param array $notification
     * @return void
     */
    protected function saveNotification($notification)
    {
        // 生成通知ID
        $notification['id'] = md5(uniqid('notification', true));
        
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
        
        // 更新未读通知数量
        $unreadCount = $this->getUnreadCount();
        
        // 更新 Dock 徽章
        if (PHP_OS === 'Darwin') {
            Dock::setBadgeCount($unreadCount);
        }
        
        // 更新托盘图标
        if ($unreadCount > 0) {
            Tray::setTitle($unreadCount);
        } else {
            Tray::setTitle('');
        }
    }
    
    /**
     * 获取未读通知数量
     *
     * @return int
     */
    protected function getUnreadCount()
    {
        $notifications = Settings::get('push.notifications', []);
        
        $count = 0;
        foreach ($notifications as $notification) {
            if ($notification['status'] == 'received') {
                $count++;
            }
        }
        
        return $count;
    }
    
    /**
     * 获取平台信息
     *
     * @return string
     */
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
