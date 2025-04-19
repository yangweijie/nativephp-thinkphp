# 扩展功能

NativePHP for ThinkPHP 提供了丰富的扩展功能，用于构建更加强大和多样化的桌面应用程序。本文档将介绍这些扩展功能的使用方法。

## 语音识别和合成

语音识别和合成功能由 `Speech` 类提供，你可以通过 `Speech` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Speech;
```

### 语音识别

```php
// 开始语音识别
Speech::startRecognition([
    'lang' => 'zh-CN',
    'continuous' => true,
    'interimResults' => true,
    'maxAlternatives' => 1,
]);

// 停止语音识别
Speech::stopRecognition();

// 检查语音识别是否正在进行
$isRecognizing = Speech::isRecognizing();

// 获取语音识别结果
$result = Speech::getRecognitionResult();
```

### 语音合成

```php
// 语音合成
Speech::speak('你好，欢迎使用 NativePHP');

// 语音合成（带选项）
Speech::speak('你好，欢迎使用 NativePHP', [
    'lang' => 'zh-CN',
    'volume' => 1.0,
    'rate' => 1.0,
    'pitch' => 1.0,
    'voice' => null,
]);

// 暂停语音合成
Speech::pause();

// 恢复语音合成
Speech::resume();

// 取消语音合成
Speech::cancel();

// 检查语音合成是否正在进行
$isSpeaking = Speech::isSpeaking();

// 获取可用的语音
$voices = Speech::getVoices();
```

### 文本和音频转换

```php
// 将文本转换为音频文件
Speech::textToAudio('你好，欢迎使用 NativePHP', '/path/to/audio.mp3');

// 将音频文件转换为文本
$text = Speech::audioToText('/path/to/audio.mp3');
```

## 设备管理

设备管理功能由 `Device` 类提供，你可以通过 `Device` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Device;
```

### 蓝牙设备管理

```php
// 获取蓝牙设备列表
$devices = Device::getBluetoothDevices();

// 扫描蓝牙设备
Device::scanBluetoothDevices([
    'timeout' => 10000, // 10 秒
    'filters' => [],
]);

// 连接蓝牙设备
Device::connectBluetoothDevice('00:11:22:33:44:55');

// 断开蓝牙设备
Device::disconnectBluetoothDevice('00:11:22:33:44:55');

// 配对蓝牙设备
Device::pairBluetoothDevice('00:11:22:33:44:55');

// 取消配对蓝牙设备
Device::unpairBluetoothDevice('00:11:22:33:44:55');

// 向蓝牙设备发送数据
Device::sendDataToBluetoothDevice('00:11:22:33:44:55', '测试数据');

// 从蓝牙设备接收数据
$data = Device::receiveDataFromBluetoothDevice('00:11:22:33:44:55');
```

### USB 设备管理

```php
// 获取 USB 设备列表
$devices = Device::getUsbDevices();

// 打开 USB 设备
Device::openUsbDevice('device-id');

// 关闭 USB 设备
Device::closeUsbDevice('device-id');

// 向 USB 设备发送数据
Device::sendDataToUsbDevice('device-id', '测试数据');

// 从 USB 设备接收数据
$data = Device::receiveDataFromUsbDevice('device-id');
```

### 设备信息

```php
// 获取设备信息
$info = Device::getDeviceInfo('device-id', 'bluetooth');
```

## 地理位置服务

地理位置服务功能由 `Geolocation` 类提供，你可以通过 `Geolocation` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Geolocation;
```

### 获取位置

```php
// 获取当前位置
$position = Geolocation::getCurrentPosition([
    'enableHighAccuracy' => true,
    'timeout' => 5000,
    'maximumAge' => 0,
]);

// 开始监视位置
Geolocation::watchPosition([
    'enableHighAccuracy' => true,
    'timeout' => 5000,
    'maximumAge' => 0,
]);

// 停止监视位置
Geolocation::clearWatch();

// 检查是否正在监视位置
$isWatching = Geolocation::isWatching();

// 获取监视 ID
$watchId = Geolocation::getWatchId();
```

### 位置计算和转换

```php
// 计算两点之间的距离
$distance = Geolocation::calculateDistance(39.9, 116.3, 31.2, 121.4, 'km');

// 获取地址信息
$address = Geolocation::getAddressFromCoordinates(39.9, 116.3);

// 获取坐标信息
$coordinates = Geolocation::getCoordinatesFromAddress('北京市海淀区中关村');
```

### 位置服务状态

```php
// 检查位置服务是否可用
$isAvailable = Geolocation::isAvailable();

// 检查位置权限
$permission = Geolocation::checkPermission();

// 请求位置权限
Geolocation::requestPermission();
```

## 推送通知服务

推送通知服务功能由 `PushNotification` 类提供，你可以通过 `PushNotification` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\PushNotification;
```

### 配置推送服务

```php
// 设置推送服务提供商
PushNotification::setProvider('firebase');

// 设置推送服务配置
PushNotification::setConfig([
    'firebase' => [
        'server_key' => 'your-server-key',
    ],
]);

// 获取推送服务提供商
$provider = PushNotification::getProvider();

// 获取推送服务配置
$config = PushNotification::getConfig();
```

### 设备管理

```php
// 注册设备
PushNotification::registerDevice('device-token', [
    'platform' => 'android',
    'user_id' => 1,
]);

// 注销设备
PushNotification::unregisterDevice('device-token');

// 获取设备信息
$deviceInfo = PushNotification::getDeviceInfo('device-token');
```

### 发送推送通知

```php
// 发送推送通知
PushNotification::send('device-token', '标题', '内容', [
    'url' => 'https://example.com',
]);

// 发送推送通知到多个设备
PushNotification::send(['device-token-1', 'device-token-2'], '标题', '内容');

// 发送推送通知（带选项）
PushNotification::send('device-token', '标题', '内容', [], [
    'badge' => 1,
    'sound' => 'default',
    'icon' => 'icon.png',
    'click_action' => 'OPEN_ACTIVITY',
    'tag' => 'tag',
    'color' => '#FF0000',
    'priority' => 'high',
    'content_available' => true,
    'mutable_content' => true,
    'time_to_live' => 3600,
    'collapse_key' => 'updates',
    'channel_id' => 'default',
]);
```

### 推送统计

```php
// 获取推送历史
$history = PushNotification::getHistory(10, 0);

// 获取推送统计
$statistics = PushNotification::getStatistics('2023-01-01', '2023-12-31');
```

## 日志工具

日志工具功能由 `Logger` 类提供，你可以通过 `Logger` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Logger;
```

### 记录日志

```php
// 记录调试信息
Logger::debug('调试信息');

// 记录信息
Logger::info('信息');

// 记录通知
Logger::notice('通知');

// 记录警告
Logger::warning('警告');

// 记录错误
Logger::error('错误');

// 记录严重错误
Logger::critical('严重错误');

// 记录警报
Logger::alert('警报');

// 记录紧急情况
Logger::emergency('紧急情况');

// 记录带上下文的日志
Logger::info('用户登录', ['user_id' => 1, 'username' => 'admin']);
```

### 日志配置

```php
// 设置日志级别
Logger::setLevel('warning');

// 设置日志文件路径
Logger::setLogFile('/path/to/log.log');

// 获取日志文件路径
$logFile = Logger::getLogFile();
```

### 日志管理

```php
// 获取日志内容
$content = Logger::get();

// 获取最近 100 行日志
$content = Logger::get(100);

// 清空日志
Logger::clear();

// 获取日志文件大小
$size = Logger::size();

// 轮换日志文件
Logger::rotate(10485760, 5);
```

## 缓存工具

缓存工具功能由 `Cache` 类提供，你可以通过 `Cache` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Cache;
```

### 缓存操作

```php
// 设置缓存目录
Cache::setCacheDir('/path/to/cache');

// 获取缓存目录
$cacheDir = Cache::getCacheDir();

// 设置缓存
Cache::set('key', 'value');

// 设置带过期时间的缓存
Cache::set('key', 'value', 3600);

// 获取缓存
$value = Cache::get('key');

// 获取缓存（带默认值）
$value = Cache::get('key', 'default');

// 检查缓存是否存在
$exists = Cache::has('key');

// 删除缓存
Cache::delete('key');

// 获取或设置缓存
$value = Cache::remember('key', function () {
    return 'value';
}, 3600);

// 清空所有缓存
Cache::clear();
```

### 缓存信息

```php
// 获取缓存信息
$info = Cache::getInfo('key');

// 获取所有缓存信息
$allInfo = Cache::getAllInfo();

// 获取缓存总大小
$size = Cache::getSize();

// 清理过期缓存
$count = Cache::gc();
```

## 事件工具

事件工具功能由 `Event` 类提供，你可以通过 `Event` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Event;
```

### 事件监听

```php
// 添加事件监听器
Event::on('app.start', function () {
    // 应用启动时执行的代码
});

// 添加带优先级的事件监听器
Event::on('app.start', function () {
    // 高优先级的事件监听器
}, 10);

// 添加一次性事件监听器
Event::once('app.start', function () {
    // 只执行一次的事件监听器
});

// 添加带优先级的一次性事件监听器
Event::once('app.start', function () {
    // 只执行一次的高优先级事件监听器
}, 10);
```

### 事件触发

```php
// 触发事件
Event::emit('app.start');

// 触发带参数的事件
Event::emit('user.login', $user);

// 触发带多个参数的事件
Event::emit('order.created', $order, $user, $items);
```

### 事件管理

```php
// 移除事件监听器
Event::off('app.start', $callback);

// 移除所有事件监听器
Event::off('app.start');

// 移除所有事件监听器
Event::removeAllListeners();

// 获取事件监听器数量
$count = Event::listenerCount('app.start');

// 获取所有事件监听器数量
$count = Event::listenerCount();

// 获取事件列表
$events = Event::eventNames();

// 获取事件监听器
$listeners = Event::listeners('app.start');
```

## 配置工具

配置工具功能由 `Config` 类提供，你可以通过 `Config` Facade 来使用它。

```php
use Native\ThinkPHP\Facades\Config;
```

### 配置操作

```php
// 设置配置文件路径
Config::setConfigFile('/path/to/config.json');

// 获取配置文件路径
$configFile = Config::getConfigFile();

// 获取配置值
$value = Config::get('app.theme');

// 获取配置值（带默认值）
$value = Config::get('app.theme', 'light');

// 设置配置值
Config::set('app.theme', 'dark');

// 检查配置是否存在
$exists = Config::has('app.theme');

// 删除配置
Config::delete('app.theme');

// 获取所有配置
$config = Config::all();

// 清空所有配置
Config::clear();
```

### 配置导入和导出

```php
// 导出配置
Config::export('/path/to/config.json');

// 导入配置
Config::import('/path/to/config.json');

// 合并配置
Config::merge([
    'app' => [
        'theme' => 'dark',
    ],
]);

// 替换配置
Config::replace([
    'app' => [
        'theme' => 'dark',
    ],
]);
```
