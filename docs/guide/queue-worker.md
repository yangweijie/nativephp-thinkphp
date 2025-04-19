# 队列工作器

队列工作器（QueueWorker）用于管理队列工作进程，提供任务队列处理功能。

## 基本用法

### 启动队列工作进程

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 启动队列工作进程
QueueWorker::up('default', 'default', 3, 60, 3, false, true);
```

参数说明：
- `connection`：队列连接名称
- `queue`：队列名称
- `tries`：任务尝试次数
- `timeout`：任务超时时间（秒）
- `sleep`：空闲时休眠时间（秒）
- `force`：是否强制启动（如果已存在则重启）
- `persistent`：是否持久化（应用关闭后保持运行）

### 停止队列工作进程

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 停止队列工作进程
QueueWorker::down('default', 'default');
```

### 重启队列工作进程

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 重启队列工作进程
QueueWorker::restart('default', 'default');
```

## 队列工作进程管理

### 获取队列工作进程信息

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 获取单个队列工作进程信息
$worker = QueueWorker::get('default', 'default');

// 获取所有队列工作进程信息
$workers = QueueWorker::all();
```

### 获取队列工作进程状态

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 获取队列工作进程状态
$status = QueueWorker::status('default', 'default');

// 获取队列工作进程 PID
$pid = QueueWorker::getPid('default', 'default');

// 获取队列工作进程输出
$output = QueueWorker::getOutput('default', 'default');

// 获取队列工作进程错误输出
$error = QueueWorker::getError('default', 'default');
```

### 批量操作

```php
use Native\ThinkPHP\Facades\QueueWorker;

// 停止所有队列工作进程
$count = QueueWorker::downAll();

// 重启所有队列工作进程
$count = QueueWorker::restartAll();

// 清理队列工作进程
$count = QueueWorker::cleanup();
```

## 任务管理

### 添加任务到队列

```php
use think\facade\Queue;

// 添加任务到队列
$jobId = Queue::push('app\\job\\SendEmail', [
    'to' => 'test@example.com',
    'subject' => '测试邮件',
    'content' => '这是一封测试邮件',
], 'default');

// 添加延迟任务
$jobId = Queue::later(60, 'app\\job\\SendEmail', [
    'to' => 'test@example.com',
    'subject' => '延迟测试邮件',
    'content' => '这是一封延迟测试邮件',
], 'default');
```

### 创建任务类

```php
namespace app\job;

use think\queue\Job;
use Native\ThinkPHP\Facades\Notification;

class SendEmail
{
    public function fire(Job $job, $data)
    {
        // 获取任务数据
        $to = $data['to'] ?? 'test@example.com';
        $subject = $data['subject'] ?? '测试邮件';
        $content = $data['content'] ?? '这是一封测试邮件';
        
        try {
            // 执行任务
            $this->sendEmail($to, $subject, $content);
            
            // 发送通知
            Notification::send('邮件已发送', "主题: {$subject}, 收件人: {$to}");
            
            // 删除任务
            $job->delete();
        } catch (\Exception $e) {
            // 如果任务尝试次数超过3次，则删除任务
            if ($job->attempts() > 3) {
                $job->delete();
            } else {
                // 重新发布任务
                $job->release(10);
            }
        }
    }
    
    protected function sendEmail($to, $subject, $content)
    {
        // 发送邮件的实现
    }
}
```

## 配置

在 `config/native.php` 中配置队列工作器：

```php
'queue_worker' => [
    // 是否记录队列工作器事件
    'log_worker_events' => false,

    // 是否自动启动队列工作器
    'auto_start' => false,

    // 是否自动停止队列工作器
    'auto_stop' => true,

    // 自动启动的队列工作器
    'auto_start_workers' => [
        // 示例：启动默认队列工作器
        // [
        //     'connection' => 'default',
        //     'queue' => 'default',
        //     'tries' => 3,
        //     'timeout' => 60,
        //     'sleep' => 3,
        //     'persistent' => true,
        // ],
    ],

    // 队列工作器启动回调
    'on_worker_started' => null,

    // 队列工作器停止回调
    'on_worker_stopped' => null,

    // 队列工作器重启回调
    'on_worker_restarted' => null,

    // 队列工作器失败回调
    'on_worker_failed' => null,
],
```

## 事件

队列工作器会触发以下事件：

- `native.queue_worker.started`：当队列工作进程启动时触发
- `native.queue_worker.stopped`：当队列工作进程停止时触发
- `native.queue_worker.restarted`：当队列工作进程重启时触发
- `native.queue_worker.failed`：当队列工作进程失败时触发

你可以监听这些事件：

```php
use think\facade\Event;

// 监听队列工作进程启动事件
Event::listen('native.queue_worker.started', function ($event) {
    // 处理队列工作进程启动事件
});
```

## 示例

### 自动启动队列工作进程

在应用启动时自动启动队列工作进程：

```php
// 在应用服务提供者中
public function boot()
{
    // 启动队列工作进程
    QueueWorker::up('default', 'default', 3, 60, 3, false, true);
    QueueWorker::up('default', 'emails', 3, 60, 3, false, true);
    QueueWorker::up('default', 'reports', 3, 60, 3, false, true);
}
```

### 监控队列工作进程

```php
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Facades\Notification;

// 定期检查队列工作进程状态
$workers = QueueWorker::all();
foreach ($workers as $alias => $worker) {
    $status = QueueWorker::status($worker['connection'], $worker['queue']);
    
    if ($status !== 'running') {
        // 发送通知
        Notification::send('队列工作进程已停止', "连接: {$worker['connection']}, 队列: {$worker['queue']}");
        
        // 尝试重启
        QueueWorker::restart($worker['connection'], $worker['queue']);
    }
}
```

### 处理不同类型的任务

```php
// 发送邮件任务
Queue::push('app\\job\\SendEmail', [
    'to' => 'user@example.com',
    'subject' => '欢迎注册',
    'content' => '感谢您注册我们的应用！',
], 'emails');

// 生成报告任务
Queue::push('app\\job\\GenerateReport', [
    'type' => 'monthly',
    'date' => date('Y-m-d'),
    'format' => 'pdf',
], 'reports');

// 发送通知任务
Queue::push('app\\job\\SendNotification', [
    'user_id' => 123,
    'title' => '新消息',
    'message' => '您有一条新消息',
    'type' => 'info',
], 'notifications');
```

## 注意事项

1. 队列工作进程的输出和错误输出会被缓存，可能会占用大量内存。对于长时间运行的进程，应该定期清理输出缓存。
2. 持久化队列工作进程在应用关闭后会继续运行，直到手动停止或系统重启。
3. 任务类必须实现 `fire` 方法，并正确处理任务完成和失败的情况。
4. 对于生产环境，应该配置适当的队列连接和队列名称，以及任务尝试次数和超时时间。
5. 队列工作进程会占用系统资源，应该根据系统性能和任务负载合理设置队列工作进程数量。
