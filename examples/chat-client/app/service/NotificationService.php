<?php

namespace app\service;

use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\FileSystem;

class NotificationService
{
    /**
     * 发送通知
     *
     * @param string $title
     * @param string $body
     * @param array $options
     * @return void
     */
    public function notify($title, $body, $options = [])
    {
        // 检查通知设置
        if (!$this->isNotificationEnabled()) {
            return;
        }
        
        $notification = Notification::title($title)
            ->body($body);
        
        // 设置图标
        if (isset($options['icon'])) {
            $notification->icon($options['icon']);
        } else {
            $notification->icon(public_path() . '/static/img/icon.png');
        }
        
        // 设置点击事件
        if (isset($options['onClick'])) {
            $notification->onClick($options['onClick']);
        }
        
        // 设置声音
        if (isset($options['sound'])) {
            $notification->sound($options['sound']);
        } else {
            $notification->sound(public_path() . '/static/sound/notification.mp3');
        }
        
        // 显示通知
        $notification->show();
    }
    
    /**
     * 通知新消息
     *
     * @param array $message
     * @return void
     */
    public function notifyNewMessage($message)
    {
        // 检查通知设置
        if (!$this->isNotificationEnabled('message')) {
            return;
        }
        
        // 检查是否是自己发送的消息
        $currentUser = Settings::get('auth.user');
        if ($message['sender_id'] === $currentUser['id']) {
            return;
        }
        
        // 检查是否需要通知（如果当前窗口是该会话，则不通知）
        $currentWindow = Window::current();
        if ($currentWindow && $currentWindow->url === '/chat/conversation/' . $message['conversation_id']) {
            return;
        }
        
        // 准备通知内容
        $title = $message['sender_name'];
        $body = $message['content'];
        
        // 如果是图片消息
        if ($message['type'] === 'image') {
            $body = '[图片]';
        }
        
        // 如果是文件消息
        if ($message['type'] === 'file') {
            $body = '[文件] ' . $message['file_name'];
        }
        
        // 如果是语音消息
        if ($message['type'] === 'voice') {
            $body = '[语音]';
        }
        
        // 设置点击事件
        $onClick = function () use ($message) {
            Window::open('/chat/conversation/' . $message['conversation_id'], [
                'title' => $message['conversation_name'],
                'width' => 800,
                'height' => 600,
            ]);
        };
        
        // 发送通知
        $this->notify($title, $body, [
            'onClick' => $onClick,
            'icon' => $message['sender_avatar'] ?? null,
        ]);
    }
    
    /**
     * 通知通话请求
     *
     * @param array $call
     * @return void
     */
    public function notifyCall($call)
    {
        // 检查通知设置
        if (!$this->isNotificationEnabled('call')) {
            return;
        }
        
        // 检查是否是自己发起的通话
        $currentUser = Settings::get('auth.user');
        if ($call['caller_id'] === $currentUser['id']) {
            return;
        }
        
        // 准备通知内容
        $title = $call['caller_name'];
        $body = $call['type'] === 'audio' ? '语音通话' : '视频通话';
        
        // 设置点击事件
        $onClick = function () use ($call) {
            Window::open('/call/' . $call['type'] . '/' . $call['id'], [
                'title' => $call['type'] === 'audio' ? '语音通话' : '视频通话',
                'width' => $call['type'] === 'audio' ? 400 : 800,
                'height' => $call['type'] === 'audio' ? 300 : 600,
                'alwaysOnTop' => true,
            ]);
        };
        
        // 发送通知
        $this->notify($title, $body, [
            'onClick' => $onClick,
            'icon' => $call['caller_avatar'] ?? null,
            'sound' => public_path() . '/static/sound/call.mp3',
        ]);
    }
    
    /**
     * 检查通知是否启用
     *
     * @param string $type
     * @return bool
     */
    protected function isNotificationEnabled($type = null)
    {
        $settings = Settings::get('notification', [
            'enabled' => true,
            'message' => true,
            'call' => true,
        ]);
        
        if (!$settings['enabled']) {
            return false;
        }
        
        if ($type && isset($settings[$type])) {
            return $settings[$type];
        }
        
        return true;
    }
}
