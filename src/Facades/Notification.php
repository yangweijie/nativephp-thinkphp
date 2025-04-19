<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static void send(string $title, string $body, array $options = []) 发送通知
 * @method static void sendWithIcon(string $title, string $body, string $icon, array $options = []) 发送带有图标的通知
 * @method static void sendWithSound(string $title, string $body, string $sound, array $options = []) 发送带有声音的通知
 * @method static void sendWithActions(string $title, string $body, array $actions, array $options = []) 发送带有操作的通知
 * 
 * @see \Native\ThinkPHP\Notification
 */
class Notification extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.notification';
    }
}
