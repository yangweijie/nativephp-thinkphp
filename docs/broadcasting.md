# 事件广播

NativePHP for ThinkPHP 提供了事件广播功能，允许你的桌面应用程序在不同窗口和组件之间进行通信。本文档将介绍如何使用这些功能。

## 基本概念

事件广播是一种消息传递机制，允许应用程序的不同部分通过发布和订阅事件来进行通信。这种机制特别适用于以下场景：

- 在多个窗口之间共享数据
- 实现松耦合的组件通信
- 构建响应式用户界面

## 使用 Broadcasting Facade

NativePHP for ThinkPHP 提供了 `Broadcasting` Facade，用于广播和监听事件。

### 广播事件

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 广播事件
$success = Broadcasting::broadcast('channel-name', 'event-name', [
    'key' => 'value',
    'message' => 'Hello, World!',
]);

if ($success) {
    // 事件广播成功
} else {
    // 事件广播失败
}
```

### 监听事件

```php
// 监听事件
$listenerId = Broadcasting::listen('channel-name', 'event-name', function ($data) {
    // 事件处理逻辑
    echo "收到事件：{$data['message']}";
});

// 取消监听事件
Broadcasting::unlisten($listenerId);
```

## 频道管理

### 创建频道

```php
// 创建频道
$success = Broadcasting::createChannel('channel-name');

if ($success) {
    // 频道创建成功
} else {
    // 频道创建失败
}
```

### 删除频道

```php
// 删除频道
$success = Broadcasting::deleteChannel('channel-name');

if ($success) {
    // 频道删除成功
} else {
    // 频道删除失败
}
```

### 清空频道

```php
// 清空频道
$success = Broadcasting::clearChannel('channel-name');

if ($success) {
    // 频道清空成功
} else {
    // 频道清空失败
}
```

### 检查频道是否存在

```php
// 检查频道是否存在
$exists = Broadcasting::channelExists('channel-name');

if ($exists) {
    // 频道存在
} else {
    // 频道不存在
}
```

## 获取频道信息

### 获取所有频道

```php
// 获取所有频道
$channels = Broadcasting::getChannels();

foreach ($channels as $channel) {
    echo "频道：{$channel}";
}
```

### 获取频道中的事件

```php
// 获取频道中的事件
$events = Broadcasting::getEvents('channel-name');

foreach ($events as $event) {
    echo "事件：{$event}";
}
```

### 获取频道中的监听器数量

```php
// 获取频道中的监听器数量
$count = Broadcasting::getListenerCount('channel-name');

echo "监听器数量：{$count}";
```

## 实际应用场景

### 多窗口通信

```php
// 在主窗口中
$mainWindowId = Window::current()['id'];

// 打开新窗口
$newWindowId = Window::open('/path/to/page', [
    'title' => '新窗口',
    'width' => 800,
    'height' => 600,
]);

// 在主窗口中监听事件
Broadcasting::listen('windows', 'data-updated', function ($data) use ($mainWindowId) {
    // 更新主窗口中的数据
    echo "主窗口 {$mainWindowId} 收到数据更新：{$data['message']}";
});

// 在新窗口中广播事件
Broadcasting::broadcast('windows', 'data-updated', [
    'message' => '数据已更新',
    'source' => $newWindowId,
]);
```

### 组件通信

```php
// 在组件 A 中
Broadcasting::listen('components', 'user-selected', function ($data) {
    // 处理用户选择事件
    echo "用户选择了：{$data['user']['name']}";
});

// 在组件 B 中
Broadcasting::broadcast('components', 'user-selected', [
    'user' => [
        'id' => 1,
        'name' => '张三',
        'email' => 'zhangsan@example.com',
    ],
]);
```

### 全局状态管理

```php
// 定义全局状态管理器
class StateManager
{
    private static $instance;
    private $state = [];
    private $listeners = [];

    private function __construct()
    {
        // 监听状态变化事件
        Broadcasting::listen('state', 'changed', function ($data) {
            $this->state[$data['key']] = $data['value'];
            
            // 触发监听器
            if (isset($this->listeners[$data['key']])) {
                foreach ($this->listeners[$data['key']] as $callback) {
                    call_user_func($callback, $data['value']);
                }
            }
        });
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        
        return self::$instance;
    }

    public function setState($key, $value)
    {
        $this->state[$key] = $value;
        
        // 广播状态变化事件
        Broadcasting::broadcast('state', 'changed', [
            'key' => $key,
            'value' => $value,
        ]);
    }

    public function getState($key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }

    public function subscribe($key, callable $callback)
    {
        if (!isset($this->listeners[$key])) {
            $this->listeners[$key] = [];
        }
        
        $this->listeners[$key][] = $callback;
    }
}

// 使用全局状态管理器
$stateManager = StateManager::getInstance();

// 订阅状态变化
$stateManager->subscribe('theme', function ($theme) {
    echo "主题已更改为：{$theme}";
});

// 设置状态
$stateManager->setState('theme', 'dark');
```

## 注意事项

- 事件广播仅在应用程序内部有效，不能跨应用程序进行通信。
- 频道和事件名称应该具有描述性，以便于理解和维护。
- 避免在事件处理程序中执行耗时操作，以免阻塞事件循环。
- 在不再需要监听事件时，应该及时取消监听，以避免内存泄漏。
