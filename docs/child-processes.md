# 子进程管理

NativePHP 提供了强大的子进程管理功能，允许您在桌面应用程序中启动、监控和与子进程交互。这对于需要在后台运行长时间任务的应用程序非常有用。

## 基本用法

### 启动子进程

您可以使用 `ChildProcess` Facade 启动子进程：

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 启动一个简单的命令
ChildProcess::start('echo Hello World', 'echo-hello');

// 启动一个 PHP 脚本
ChildProcess::php('/path/to/script.php', 'php-script');

// 启动一个 ThinkPHP 命令
ChildProcess::artisan('migrate', 'db-migrate');
```

### 获取子进程信息

您可以获取单个子进程或所有子进程的信息：

```php
// 获取单个子进程
$process = ChildProcess::get('echo-hello');

// 获取所有子进程
$processes = ChildProcess::all();
```

### 停止和重启子进程

您可以停止和重启子进程：

```php
// 停止子进程
ChildProcess::stop('echo-hello');

// 重启子进程
ChildProcess::restart('echo-hello');
```

### 与子进程通信

您可以向子进程发送消息：

```php
// 向子进程发送消息
ChildProcess::message('Hello from parent', 'echo-hello');
```

### 检查子进程状态

您可以检查子进程的状态：

```php
// 检查子进程是否存在
$exists = ChildProcess::exists('echo-hello');

// 检查子进程是否正在运行
$running = ChildProcess::isRunning('echo-hello');

// 获取子进程 PID
$pid = ChildProcess::getPid('echo-hello');

// 获取子进程状态
$status = ChildProcess::getStatus('echo-hello');

// 获取子进程输出
$output = ChildProcess::getOutput('echo-hello');

// 获取子进程错误
$error = ChildProcess::getError('echo-hello');

// 获取子进程退出码
$exitCode = ChildProcess::getExitCode('echo-hello');
```

### 清理子进程

您可以清理已停止的子进程：

```php
// 清理已停止的子进程
$count = ChildProcess::cleanup();
```

## 高级用法

### 持久化子进程

您可以创建持久化的子进程，这些进程在应用程序重启后仍然存在：

```php
// 创建持久化子进程
ChildProcess::start('node server.js', 'node-server', null, true);
```

### 设置工作目录和环境变量

您可以为子进程设置工作目录和环境变量：

```php
// 设置工作目录和环境变量
ChildProcess::start('npm start', 'npm-server', '/path/to/project', false, [
    'NODE_ENV' => 'production',
]);
```

### 使用缓存提高性能

为了提高性能，ChildProcess 组件内置了缓存机制。默认情况下，`get` 和 `all` 方法会使用缓存，但您可以通过设置参数来禁用缓存：

```php
// 使用缓存获取子进程（默认）
$process = ChildProcess::get('echo-hello');

// 不使用缓存获取子进程（强制刷新）
$process = ChildProcess::get('echo-hello', false);

// 使用缓存获取所有子进程（默认）
$processes = ChildProcess::all();

// 不使用缓存获取所有子进程（强制刷新）
$processes = ChildProcess::all(false);
```

您还可以手动清除缓存或设置缓存过期时间：

```php
// 清除所有缓存
ChildProcess::clearCache();

// 清除指定子进程的缓存
ChildProcess::clearCache('echo-hello');

// 设置缓存过期时间为 10 秒（默认为 5 秒）
ChildProcess::setCacheExpiration(10);
```

注意：当您使用 `start`、`stop`、`restart` 等方法修改子进程状态时，缓存会自动清除。

### 监听子进程事件

您可以监听子进程事件，如进程启动、退出和消息接收：

```php
use Native\ThinkPHP\Events\ChildProcess\ProcessSpawned;
use Native\ThinkPHP\Events\ChildProcess\ProcessExited;
use Native\ThinkPHP\Events\ChildProcess\MessageReceived;
use Native\ThinkPHP\Events\ChildProcess\ErrorReceived;

// 监听进程启动事件
Event::listen(ProcessSpawned::class, function (ProcessSpawned $event) {
    echo "进程 {$event->alias} 已启动，PID: {$event->pid}";
});

// 监听进程退出事件
Event::listen(ProcessExited::class, function (ProcessExited $event) {
    echo "进程 {$event->alias} 已退出，退出码: {$event->code}";
});

// 监听消息接收事件
Event::listen(MessageReceived::class, function (MessageReceived $event) {
    echo "进程 {$event->alias} 收到消息: {$event->data}";
});

// 监听错误接收事件
Event::listen(ErrorReceived::class, function (ErrorReceived $event) {
    echo "进程 {$event->alias} 收到错误: {$event->data}";
});
```

## 示例

### 运行长时间任务

```php
// 启动一个长时间运行的任务
ChildProcess::php('long-running-task.php', 'long-task');

// 检查任务是否仍在运行
if (ChildProcess::isRunning('long-task')) {
    echo "任务仍在运行";
} else {
    echo "任务已完成";
}
```

### 运行 Web 服务器

```php
// 启动一个 Web 服务器
ChildProcess::start('php -S localhost:8000', 'web-server', public_path());

// 检查服务器是否正在运行
if (ChildProcess::isRunning('web-server')) {
    echo "Web 服务器正在运行";
} else {
    echo "Web 服务器未运行";
}
```

### 运行数据库迁移

```php
// 运行数据库迁移
ChildProcess::artisan('migrate', 'db-migrate');

// 检查迁移是否完成
if (!ChildProcess::isRunning('db-migrate')) {
    $exitCode = ChildProcess::getExitCode('db-migrate');
    if ($exitCode === 0) {
        echo "数据库迁移成功";
    } else {
        echo "数据库迁移失败，退出码: {$exitCode}";
        echo "错误信息: " . ChildProcess::getError('db-migrate');
    }
}
```

## API 参考

### 方法

- `start($cmd, $alias, $cwd = null, $persistent = false, array $env = [])` - 启动子进程
- `get($alias, $useCache = true)` - 获取子进程，可以指定是否使用缓存
- `all($useCache = true)` - 获取所有子进程，可以指定是否使用缓存
- `stop($alias)` - 停止子进程
- `restart($alias)` - 重启子进程
- `message($message, $alias)` - 向子进程发送消息
- `php($script, $alias, array $args = [], $cwd = null, $persistent = false, array $env = [])` - 运行 PHP 脚本
- `artisan($command, $alias, array $args = [], $cwd = null, $persistent = false, array $env = [])` - 运行 ThinkPHP 命令
- `exists($alias)` - 检查子进程是否存在
- `isRunning($alias)` - 检查子进程是否正在运行
- `getPid($alias)` - 获取子进程 PID
- `getStatus($alias)` - 获取子进程状态
- `getOutput($alias)` - 获取子进程输出
- `getError($alias)` - 获取子进程错误
- `getExitCode($alias)` - 获取子进程退出码
- `cleanup()` - 清理已停止的子进程
- `clearCache($alias = null)` - 清除缓存，如果指定别名，则只清除该别名的缓存
- `setCacheExpiration($seconds)` - 设置缓存过期时间（秒）

### 事件

- `ProcessSpawned` - 进程启动事件
- `ProcessExited` - 进程退出事件
- `MessageReceived` - 消息接收事件
- `ErrorReceived` - 错误接收事件
