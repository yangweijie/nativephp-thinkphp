# 广播系统

广播系统（Broadcasting）用于在不同窗口和组件之间进行通信，提供频道管理和事件监听功能。

## 基本用法

### 广播事件

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 广播事件
Broadcasting::broadcast('channel', 'event', ['key' => 'value']);
```

### 监听事件

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 监听事件
$id = Broadcasting::listen('channel', 'event', function ($data) {
    // 处理事件数据
    var_dump($data);
});
```

### 取消监听

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 取消监听
Broadcasting::unlisten($id);
```

## 频道管理

### 创建频道

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 创建频道
Broadcasting::createChannel('my-channel');
```

### 获取频道列表

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 获取频道列表
$channels = Broadcasting::getChannels();
```

### 获取频道事件列表

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 获取频道事件列表
$events = Broadcasting::getEvents('my-channel');
```

### 删除频道

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 删除频道
Broadcasting::deleteChannel('my-channel');
```

### 清空频道

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 清空频道
Broadcasting::clearChannel('my-channel');
```

## 高级用法

### 使用通配符监听事件

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 监听所有频道的特定事件
Broadcasting::listen('*', 'user-login', function ($data) {
    // 处理所有频道的 user-login 事件
});

// 监听特定频道的所有事件
Broadcasting::listen('my-channel', '*', function ($data, $event) {
    // 处理 my-channel 频道的所有事件
    echo "事件: {$event}, 数据: " . json_encode($data);
});
```

### 使用前缀监听事件

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 监听以 user- 开头的所有事件
Broadcasting::listen('my-channel', 'user-*', function ($data, $event) {
    // 处理 my-channel 频道的所有以 user- 开头的事件
});
```

### 使用优先级

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 设置高优先级监听器
Broadcasting::listen('my-channel', 'event', function ($data) {
    // 高优先级处理
}, 100);

// 设置低优先级监听器
Broadcasting::listen('my-channel', 'event', function ($data) {
    // 低优先级处理
}, 1);
```

## 配置

在 `config/native.php` 中配置广播系统：

```php
'broadcasting' => [
    // 是否记录广播事件
    'log_broadcast_events' => false,

    // 默认频道
    'default_channels' => [
        'global',
        'windows',
        'components',
        'state',
    ],

    // 广播消息回调
    'on_broadcast_message' => null,

    // 频道创建回调
    'on_channel_created' => null,

    // 频道删除回调
    'on_channel_deleted' => null,
],
```

## 事件

广播系统会触发以下事件：

- `native.broadcasting.message`：当广播消息时触发
- `native.broadcasting.channel_created`：当创建频道时触发
- `native.broadcasting.channel_deleted`：当删除频道时触发

你可以监听这些事件：

```php
use think\facade\Event;

// 监听广播消息事件
Event::listen('native.broadcasting.message', function ($event) {
    // 处理广播消息事件
});
```

## 示例

### 在不同窗口之间通信

```php
// 窗口 A
use Native\ThinkPHP\Facades\Broadcasting;

// 监听事件
Broadcasting::listen('app', 'data-updated', function ($data) {
    // 更新界面
    updateUI($data);
});

// 窗口 B
use Native\ThinkPHP\Facades\Broadcasting;

// 广播事件
Broadcasting::broadcast('app', 'data-updated', [
    'id' => 1,
    'name' => '张三',
    'age' => 30,
]);
```

### 实现状态同步

```php
use Native\ThinkPHP\Facades\Broadcasting;

// 定义状态管理类
class StateManager
{
    protected $state = [];
    
    public function __construct()
    {
        // 监听状态更新事件
        Broadcasting::listen('state', 'update', function ($data) {
            $this->updateState($data);
        });
    }
    
    public function updateState($data)
    {
        foreach ($data as $key => $value) {
            $this->state[$key] = $value;
        }
        
        // 广播状态已更新事件
        Broadcasting::broadcast('state', 'updated', $this->state);
    }
    
    public function getState()
    {
        return $this->state;
    }
}
```

## 注意事项

1. 广播系统是进程内的，不能跨应用实例通信。如果需要跨应用通信，请使用其他通信方式，如 WebSocket 或 HTTP API。
2. 监听器回调函数中的异常不会影响其他监听器的执行，但会被记录到日志中。
3. 频道名称和事件名称区分大小写。
4. 广播的数据必须是可序列化的。
