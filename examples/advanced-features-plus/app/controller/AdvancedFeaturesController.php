<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\I18n;
use Native\ThinkPHP\Facades\Theme;
use Native\ThinkPHP\Facades\Device;
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\PushNotification;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Settings;
use think\facade\View;
use think\facade\Config;

class AdvancedFeaturesController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('advanced-features/index');
    }
    
    /**
     * 显示国际化页面
     *
     * @return \think\Response
     */
    public function i18n()
    {
        // 获取当前语言
        $currentLocale = I18n::getLocale();
        
        // 获取可用语言
        $availableLocales = I18n::getAvailableLocales();
        
        // 获取翻译
        $translations = [
            'welcome' => I18n::t('welcome'),
            'hello' => I18n::t('hello', ['name' => 'World']),
            'items' => I18n::t('items', ['count' => 5]),
        ];
        
        return View::fetch('advanced-features/i18n', [
            'currentLocale' => $currentLocale,
            'availableLocales' => $availableLocales,
            'translations' => $translations,
        ]);
    }
    
    /**
     * 切换语言
     *
     * @return \think\Response
     */
    public function switchLocale()
    {
        $locale = request()->param('locale');
        
        if (empty($locale)) {
            return json(['success' => false, 'message' => '语言不能为空']);
        }
        
        // 切换语言
        I18n::setLocale($locale);
        
        // 保存设置
        Settings::set('locale', $locale);
        
        return json(['success' => true]);
    }
    
    /**
     * 显示主题页面
     *
     * @return \think\Response
     */
    public function theme()
    {
        // 获取当前主题
        $currentTheme = Theme::getCurrent();
        
        // 获取可用主题
        $availableThemes = Theme::getAvailableThemes();
        
        // 获取主题配置
        $themeConfig = Theme::getConfig();
        
        return View::fetch('advanced-features/theme', [
            'currentTheme' => $currentTheme,
            'availableThemes' => $availableThemes,
            'themeConfig' => $themeConfig,
        ]);
    }
    
    /**
     * 切换主题
     *
     * @return \think\Response
     */
    public function switchTheme()
    {
        $theme = request()->param('theme');
        
        if (empty($theme)) {
            return json(['success' => false, 'message' => '主题不能为空']);
        }
        
        // 切换主题
        Theme::setCurrent($theme);
        
        // 保存设置
        Settings::set('theme', $theme);
        
        return json(['success' => true]);
    }
    
    /**
     * 保存主题配置
     *
     * @return \think\Response
     */
    public function saveThemeConfig()
    {
        $config = request()->param('config');
        
        if (empty($config)) {
            return json(['success' => false, 'message' => '配置不能为空']);
        }
        
        // 保存主题配置
        Theme::setConfig($config);
        
        return json(['success' => true]);
    }
    
    /**
     * 显示设备页面
     *
     * @return \think\Response
     */
    public function device()
    {
        // 获取蓝牙设备列表
        $bluetoothDevices = Device::getBluetoothDevices();
        
        // 获取 USB 设备列表
        $usbDevices = Device::getUsbDevices();
        
        return View::fetch('advanced-features/device', [
            'bluetoothDevices' => $bluetoothDevices,
            'usbDevices' => $usbDevices,
        ]);
    }
    
    /**
     * 扫描蓝牙设备
     *
     * @return \think\Response
     */
    public function scanBluetoothDevices()
    {
        // 扫描蓝牙设备
        $success = Device::scanBluetoothDevices([
            'timeout' => 10000, // 10 秒
        ]);
        
        if ($success) {
            // 获取蓝牙设备列表
            $devices = Device::getBluetoothDevices();
            
            return json(['success' => true, 'devices' => $devices]);
        } else {
            return json(['success' => false, 'message' => '扫描蓝牙设备失败']);
        }
    }
    
    /**
     * 连接蓝牙设备
     *
     * @return \think\Response
     */
    public function connectBluetoothDevice()
    {
        $deviceId = request()->param('deviceId');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备 ID 不能为空']);
        }
        
        // 连接蓝牙设备
        $success = Device::connectBluetoothDevice($deviceId);
        
        if ($success) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '连接蓝牙设备失败']);
        }
    }
    
    /**
     * 断开蓝牙设备
     *
     * @return \think\Response
     */
    public function disconnectBluetoothDevice()
    {
        $deviceId = request()->param('deviceId');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备 ID 不能为空']);
        }
        
        // 断开蓝牙设备
        $success = Device::disconnectBluetoothDevice($deviceId);
        
        if ($success) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '断开蓝牙设备失败']);
        }
    }
    
    /**
     * 向蓝牙设备发送数据
     *
     * @return \think\Response
     */
    public function sendDataToBluetoothDevice()
    {
        $deviceId = request()->param('deviceId');
        $data = request()->param('data');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备 ID 不能为空']);
        }
        
        if (empty($data)) {
            return json(['success' => false, 'message' => '数据不能为空']);
        }
        
        // 向蓝牙设备发送数据
        $success = Device::sendDataToBluetoothDevice($deviceId, $data);
        
        if ($success) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '发送数据失败']);
        }
    }
    
    /**
     * 显示地理位置页面
     *
     * @return \think\Response
     */
    public function geolocation()
    {
        // 检查位置服务是否可用
        $isAvailable = Geolocation::isAvailable();
        
        // 检查位置权限
        $permission = Geolocation::checkPermission();
        
        return View::fetch('advanced-features/geolocation', [
            'isAvailable' => $isAvailable,
            'permission' => $permission,
        ]);
    }
    
    /**
     * 获取当前位置
     *
     * @return \think\Response
     */
    public function getCurrentPosition()
    {
        // 获取当前位置
        $position = Geolocation::getCurrentPosition([
            'enableHighAccuracy' => true,
            'timeout' => 10000,
            'maximumAge' => 0,
        ]);
        
        if ($position) {
            return json(['success' => true, 'position' => $position]);
        } else {
            return json(['success' => false, 'message' => '获取当前位置失败']);
        }
    }
    
    /**
     * 开始监视位置
     *
     * @return \think\Response
     */
    public function watchPosition()
    {
        // 开始监视位置
        $success = Geolocation::watchPosition([
            'enableHighAccuracy' => true,
            'timeout' => 10000,
            'maximumAge' => 0,
        ]);
        
        if ($success) {
            return json(['success' => true, 'watchId' => Geolocation::getWatchId()]);
        } else {
            return json(['success' => false, 'message' => '开始监视位置失败']);
        }
    }
    
    /**
     * 停止监视位置
     *
     * @return \think\Response
     */
    public function clearWatch()
    {
        // 停止监视位置
        $success = Geolocation::clearWatch();
        
        if ($success) {
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '停止监视位置失败']);
        }
    }
    
    /**
     * 计算距离
     *
     * @return \think\Response
     */
    public function calculateDistance()
    {
        $lat1 = request()->param('lat1');
        $lon1 = request()->param('lon1');
        $lat2 = request()->param('lat2');
        $lon2 = request()->param('lon2');
        $unit = request()->param('unit', 'km');
        
        if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) {
            return json(['success' => false, 'message' => '坐标不能为空']);
        }
        
        // 计算距离
        $distance = Geolocation::calculateDistance($lat1, $lon1, $lat2, $lon2, $unit);
        
        return json(['success' => true, 'distance' => $distance]);
    }
    
    /**
     * 获取地址信息
     *
     * @return \think\Response
     */
    public function getAddressFromCoordinates()
    {
        $latitude = request()->param('latitude');
        $longitude = request()->param('longitude');
        
        if (empty($latitude) || empty($longitude)) {
            return json(['success' => false, 'message' => '坐标不能为空']);
        }
        
        // 获取地址信息
        $address = Geolocation::getAddressFromCoordinates($latitude, $longitude);
        
        if ($address) {
            return json(['success' => true, 'address' => $address]);
        } else {
            return json(['success' => false, 'message' => '获取地址信息失败']);
        }
    }
    
    /**
     * 获取坐标信息
     *
     * @return \think\Response
     */
    public function getCoordinatesFromAddress()
    {
        $address = request()->param('address');
        
        if (empty($address)) {
            return json(['success' => false, 'message' => '地址不能为空']);
        }
        
        // 获取坐标信息
        $coordinates = Geolocation::getCoordinatesFromAddress($address);
        
        if ($coordinates) {
            return json(['success' => true, 'coordinates' => $coordinates]);
        } else {
            return json(['success' => false, 'message' => '获取坐标信息失败']);
        }
    }
    
    /**
     * 显示推送通知页面
     *
     * @return \think\Response
     */
    public function push()
    {
        // 获取当前设备信息
        $device = Settings::get('push.device');
        
        // 获取推送历史
        $notifications = Settings::get('push.notifications', []);
        
        return View::fetch('advanced-features/push', [
            'device' => $device,
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
        $token = request()->param('token');
        $provider = request()->param('provider', 'firebase');
        
        if (empty($token)) {
            return json(['success' => false, 'message' => '设备令牌不能为空']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($provider);
        
        // 注册设备
        $result = PushNotification::registerDevice($token, [
            'platform' => $this->getPlatform(),
            'name' => 'NativePHP Device',
            'app_version' => Config::get('native.version', '1.0.0'),
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
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '设备注册失败']);
        }
    }
    
    /**
     * 注销设备
     *
     * @return \think\Response
     */
    public function unregisterDevice()
    {
        // 获取当前设备信息
        $device = Settings::get('push.device');
        
        if (!$device) {
            return json(['success' => false, 'message' => '设备未注册']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($device['provider']);
        
        // 注销设备
        $result = PushNotification::unregisterDevice($device['token']);
        
        if ($result) {
            // 清除设备信息
            Settings::forget('push.device');
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '设备注销失败']);
        }
    }
    
    /**
     * 发送推送通知
     *
     * @return \think\Response
     */
    public function sendPushNotification()
    {
        $title = request()->param('title');
        $body = request()->param('body');
        $data = request()->param('data', []);
        
        if (empty($title) || empty($body)) {
            return json(['success' => false, 'message' => '标题和内容不能为空']);
        }
        
        // 获取当前设备信息
        $device = Settings::get('push.device');
        
        if (!$device) {
            return json(['success' => false, 'message' => '设备未注册']);
        }
        
        // 设置推送服务提供商
        PushNotification::setProvider($device['provider']);
        
        // 发送推送通知
        $reference = PushNotification::send(
            $device['token'],
            $title,
            $body,
            $data,
            ['sound' => 'default']
        );
        
        if ($reference) {
            // 保存通知记录
            $this->saveNotification([
                'reference' => $reference,
                'title' => $title,
                'body' => $body,
                'data' => $data,
                'status' => 'sent',
                'sent_at' => date('Y-m-d H:i:s'),
            ]);
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '发送推送通知失败']);
        }
    }
    
    /**
     * 清空推送历史
     *
     * @return \think\Response
     */
    public function clearPushHistory()
    {
        // 清空推送历史
        Settings::forget('push.notifications');
        
        return json(['success' => true]);
    }
    
    /**
     * 获取平台
     *
     * @return string
     */
    protected function getPlatform()
    {
        $os = PHP_OS;
        
        if (strpos($os, 'WIN') !== false) {
            return 'windows';
        } elseif (strpos($os, 'Darwin') !== false) {
            return 'macos';
        } elseif (strpos($os, 'Linux') !== false) {
            return 'linux';
        } else {
            return 'unknown';
        }
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
    }
}
