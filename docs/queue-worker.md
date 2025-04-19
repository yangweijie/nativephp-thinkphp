# 队列工作进程管理

NativePHP 提供了队列工作进程管理功能，允许您在桌面应用程序中启动、监控和管理队列工作进程。这对于需要处理后台任务的应用程序非常有用。

## 基本用法

### 启动队列工作进程

您可以使用 `QueueWorker` Facade 启动队列工作进程：

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 启动默认队列工作进程
QueueWorker::up();

// 启动指定连接和队列的工作进程
QueueWorker::up('redis', 'emails');

// 启动带有自定义参数的工作进程
QueueWorker::up('redis', 'emails', 5, 120, 5, false, true);
```

### 停止队列工作进程

您可以停止队列工作进程：

```php
// 停止默认队列工作进程
QueueWorker::down();

// 停止指定连接和队列的工作进程
QueueWorker::down('redis', 'emails');
```

### 重启队列工作进程

您可以重启队列工作进程：

```php
// 重启默认队列工作进程
QueueWorker::restart();

// 重启指定连接和队列的工作进程
QueueWorker::restart('redis', 'emails');

// 重启带有自定义参数的工作进程
QueueWorker::restart('redis', 'emails', 5, 120, 5, true);
```

### 获取队列工作进程状态

您可以获取队列工作进程的状态：

```php
// 获取默认队列工作进程状态
$status = QueueWorker::status();

// 获取指定连接和队列的工作进程状态
$status = QueueWorker::status('redis', 'emails');
```

### 获取队列工作进程信息

您可以获取单个队列工作进程或所有队列工作进程的信息：

```php
// 获取单个队列工作进程
$worker = QueueWorker::get('redis', 'emails');

// 获取所有队列工作进程
$workers = QueueWorker::all();
```

### 清理队列工作进程

您可以清理已停止的队列工作进程：

```php
// 清理已停止的队列工作进程
$count = QueueWorker::cleanup();
```

### 停止所有队列工作进程

您可以停止所有队列工作进程：

```php
// 停止所有队列工作进程
$count = QueueWorker::downAll();
```

### 重启所有队列工作进程

您可以重启所有队列工作进程：

```php
// 重启所有队列工作进程
$count = QueueWorker::restartAll();
```

### 检查队列工作进程状态

您可以检查队列工作进程的状态：

```php
// 检查队列工作进程是否存在
$exists = QueueWorker::exists('redis', 'emails');

// 检查队列工作进程是否正在运行
$running = QueueWorker::isRunning('redis', 'emails');

// 获取队列工作进程 PID
$pid = QueueWorker::getPid('redis', 'emails');

// 获取队列工作进程输出
$output = QueueWorker::getOutput('redis', 'emails');

// 获取队列工作进程错误
$error = QueueWorker::getError('redis', 'emails');

// 获取队列工作进程退出码
$exitCode = QueueWorker::getExitCode('redis', 'emails');
```

## 高级用法

### 持久化队列工作进程

您可以创建持久化的队列工作进程，这些进程在应用程序重启后仍然存在：

```php
// 创建持久化队列工作进程
QueueWorker::up('redis', 'emails', 5, 120, 5, false, true);
```

### 自定义队列工作进程参数

您可以自定义队列工作进程的参数：

```php
// 自定义队列工作进程参数
QueueWorker::up(
    'redis',    // 连接名称
    'emails',   // 队列名称
    5,          // 尝试次数
    120,        // 超时时间（秒）
    5,          // 休眠时间（秒）
    false,      // 是否强制启动
    true        // 是否持久化
);
```

### 使用缓存提高性能

为了提高性能，QueueWorker 组件内置了缓存机制。默认情况下，`get` 和 `all` 方法会使用缓存，但您可以通过设置参数来禁用缓存：

```php
// 使用缓存获取队列工作进程（默认）
$worker = QueueWorker::get('redis', 'emails');

// 不使用缓存获取队列工作进程（强制刷新）
$worker = QueueWorker::get('redis', 'emails', false);

// 使用缓存获取所有队列工作进程（默认）
$workers = QueueWorker::all();

// 不使用缓存获取所有队列工作进程（强制刷新）
$workers = QueueWorker::all(false);
```

您还可以手动清除缓存或设置缓存过期时间：

```php
// 清除所有缓存
QueueWorker::clearCache();

// 清除指定连接的所有队列的缓存
QueueWorker::clearCache('redis');

// 清除指定连接和队列的缓存
QueueWorker::clearCache('redis', 'emails');

// 设置缓存过期时间为 10 秒（默认为 5 秒）
QueueWorker::setCacheExpiration(10);
```

注意：当您使用 `up`、`down`、`restart` 等方法修改队列工作进程状态时，缓存会自动清除。

## 示例

### 启动多个队列工作进程

```php
// 启动多个队列工作进程
QueueWorker::up('redis', 'emails');
QueueWorker::up('redis', 'sms');
QueueWorker::up('redis', 'notifications');

// 检查所有队列工作进程
$workers = QueueWorker::all();
foreach ($workers as $alias => $worker) {
    echo "工作进程 {$alias} 状态: {$worker['status']}";
}
```

### 监控队列工作进程

```php
// 监控队列工作进程
$alias = 'queue-worker-redis-emails';
if (QueueWorker::isRunning('redis', 'emails')) {
    echo "队列工作进程正在运行";
    echo "PID: " . QueueWorker::getPid('redis', 'emails');
    echo "输出: " . QueueWorker::getOutput('redis', 'emails');
} else {
    echo "队列工作进程未运行";
    echo "退出码: " . QueueWorker::getExitCode('redis', 'emails');
    echo "错误: " . QueueWorker::getError('redis', 'emails');
}
```

### 重启所有队列工作进程

```php
// 重启所有队列工作进程
$count = QueueWorker::restartAll();
echo "已重启 {$count} 个队列工作进程";
```

## API 参考

### 方法

- `up($connection = 'default', $queue = 'default', $tries = 3, $timeout = 60, $sleep = 3, $force = false, $persistent = true)` - 启动队列工作进程
- `down($connection = 'default', $queue = 'default')` - 停止队列工作进程
- `restart($connection = 'default', $queue = 'default', $tries = 3, $timeout = 60, $sleep = 3, $persistent = true)` - 重启队列工作进程
- `status($connection = 'default', $queue = 'default')` - 获取队列工作进程状态
- `all($useCache = true)` - 获取所有队列工作进程，可以指定是否使用缓存
- `get($connection = 'default', $queue = 'default', $useCache = true)` - 获取队列工作进程，可以指定是否使用缓存
- `cleanup()` - 清理所有队列工作进程
- `downAll()` - 停止所有队列工作进程
- `restartAll()` - 重启所有队列工作进程
- `exists($connection = 'default', $queue = 'default')` - 检查队列工作进程是否存在
- `isRunning($connection = 'default', $queue = 'default')` - 检查队列工作进程是否正在运行
- `getPid($connection = 'default', $queue = 'default')` - 获取队列工作进程 PID
- `getOutput($connection = 'default', $queue = 'default')` - 获取队列工作进程输出
- `getError($connection = 'default', $queue = 'default')` - 获取队列工作进程错误
- `getExitCode($connection = 'default', $queue = 'default')` - 获取队列工作进程退出码
- `clearCache($connection = null, $queue = null)` - 清除缓存，可以指定连接和队列
- `setCacheExpiration($seconds)` - 设置缓存过期时间（秒）
