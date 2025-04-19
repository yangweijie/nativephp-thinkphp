# 子进程管理

子进程管理（ChildProcess）用于启动、监控和管理子进程，提供进程间通信功能。

## 基本用法

### 启动子进程

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 启动子进程
$process = ChildProcess::start('echo Hello, World!', 'hello-process');
```

### 获取子进程信息

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 获取单个子进程信息
$process = ChildProcess::get('hello-process');

// 获取所有子进程信息
$processes = ChildProcess::all();
```

### 停止子进程

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 停止子进程
ChildProcess::stop('hello-process');
```

### 重启子进程

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 重启子进程
ChildProcess::restart('hello-process');
```

## 进程间通信

### 向子进程发送消息

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 向子进程发送消息
ChildProcess::message('Hello from parent', 'hello-process');
```

### 获取子进程输出

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 获取子进程输出
$output = ChildProcess::getOutput('hello-process');

// 获取子进程错误输出
$error = ChildProcess::getError('hello-process');
```

## 高级用法

### 运行 PHP 脚本

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 运行 PHP 脚本
ChildProcess::php('scripts/long_task.php', 'long-task', ['arg1', 'arg2']);
```

### 运行 ThinkPHP 命令

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 运行 ThinkPHP 命令
ChildProcess::artisan('cache:clear', 'cache-clear', ['--force']);
```

### 设置工作目录和环境变量

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 设置工作目录和环境变量
ChildProcess::start('npm install', 'npm-install', __DIR__ . '/frontend', false, [
    'NODE_ENV' => 'production',
]);
```

### 持久化子进程

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 创建持久化子进程（应用关闭后继续运行）
ChildProcess::start('node server.js', 'node-server', null, true);
```

### 检查子进程状态

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 检查子进程是否存在
$exists = ChildProcess::exists('hello-process');

// 检查子进程是否正在运行
$isRunning = ChildProcess::isRunning('hello-process');

// 获取子进程状态
$status = ChildProcess::getStatus('hello-process');

// 获取子进程 PID
$pid = ChildProcess::getPid('hello-process');

// 获取子进程退出码
$exitCode = ChildProcess::getExitCode('hello-process');
```

### 清理子进程

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 清理已停止的子进程
$count = ChildProcess::cleanup();
```

## 配置

在 `config/native.php` 中配置子进程管理：

```php
'child_process' => [
    // 是否记录子进程事件
    'log_process_events' => false,

    // 是否自动恢复持久化的子进程
    'auto_restore_persistent' => true,

    // 是否自动清理非持久化的子进程
    'auto_cleanup_non_persistent' => true,

    // 子进程超时时间（秒）
    'timeout' => 60,

    // 子进程最大内存限制（MB）
    'memory_limit' => 512,

    // 子进程最大数量
    'max_processes' => 10,
],
```

## 事件

子进程管理会触发以下事件：

- `native.child_process.spawned`：当子进程启动时触发
- `native.child_process.exited`：当子进程退出时触发
- `native.child_process.message`：当子进程发送消息时触发
- `native.child_process.error`：当子进程发生错误时触发

你可以监听这些事件：

```php
use think\facade\Event;

// 监听子进程启动事件
Event::listen('native.child_process.spawned', function ($event) {
    // 处理子进程启动事件
});
```

## 示例

### 长时间运行的后台任务

```php
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\Notification;

// 启动长时间运行的后台任务
$process = ChildProcess::php('scripts/long_task.php', 'long-task', [60, 1], null, true);

// 监听任务完成
Event::listen('native.child_process.exited', function ($event) {
    if ($event['alias'] === 'long-task') {
        Notification::send('任务完成', '长时间运行的任务已完成');
    }
});
```

### 实时数据处理

```php
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\Broadcasting;

// 启动数据处理进程
$process = ChildProcess::start('tail -f /var/log/access.log', 'log-tail');

// 监听进程输出
Event::listen('native.child_process.message', function ($event) {
    if ($event['alias'] === 'log-tail') {
        // 解析日志行
        $logData = parseLogLine($event['data']);
        
        // 广播日志数据
        Broadcasting::broadcast('logs', 'new-log', $logData);
    }
});
```

### 多进程任务处理

```php
use Native\ThinkPHP\Facades\ChildProcess;

// 创建多个工作进程
for ($i = 0; $i < 4; $i++) {
    ChildProcess::php('scripts/worker.php', "worker-{$i}", [$i]);
}

// 向所有工作进程发送任务
$tasks = [/* 任务数据 */];
foreach ($tasks as $index => $task) {
    $workerIndex = $index % 4;
    ChildProcess::message(json_encode($task), "worker-{$workerIndex}");
}
```

## 注意事项

1. 子进程的输出和错误输出会被缓存，可能会占用大量内存。对于长时间运行的进程，应该定期清理输出缓存。
2. 持久化子进程在应用关闭后会继续运行，直到手动停止或系统重启。
3. 子进程的环境变量和工作目录与父进程不同，需要显式设置。
4. 在 Windows 系统上，某些命令可能需要使用 `cmd /c` 前缀。
5. 子进程管理不支持交互式命令，如需要用户输入的命令。
