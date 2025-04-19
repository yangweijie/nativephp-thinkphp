# 进程管理

NativePHP for ThinkPHP 提供了进程管理功能，允许你的桌面应用程序运行和管理外部进程。本文档将介绍如何使用这些功能。

## 基本概念

进程管理功能允许你的应用程序运行命令、PHP 脚本和 ThinkPHP 命令，并管理这些进程的生命周期。这些功能可以用于执行后台任务、运行系统命令、与外部程序交互等。

## 使用 Process Facade

NativePHP for ThinkPHP 提供了 `Process` Facade，用于运行和管理进程。

```php
use Native\ThinkPHP\Facades\Process;
```

### 运行命令

```php
// 运行命令
$processId = Process::run('ls -la');

// 运行命令（带选项）
$processId = Process::run('ls -la', [
    'cwd' => '/path/to/directory', // 工作目录
    'env' => ['PATH' => '/usr/bin'], // 环境变量
    'detached' => false, // 是否分离进程
    'shell' => true, // 是否使用 shell
    'windowsHide' => true, // 是否隐藏 Windows 控制台窗口
    'stdio' => 'pipe', // 标准输入输出模式
]);

if ($processId) {
    // 命令运行成功
    echo "进程 ID：{$processId}";
} else {
    // 命令运行失败
    echo "命令运行失败";
}
```

### 运行 PHP 脚本

```php
// 运行 PHP 脚本
$processId = Process::runPhp('/path/to/script.php');

// 运行 PHP 脚本（带参数）
$processId = Process::runPhp('/path/to/script.php', ['arg1', 'arg2']);

// 运行 PHP 脚本（带参数和选项）
$processId = Process::runPhp('/path/to/script.php', ['arg1', 'arg2'], [
    'cwd' => '/path/to/directory',
    'env' => ['PATH' => '/usr/bin'],
]);
```

### 运行 ThinkPHP 命令

```php
// 运行 ThinkPHP 命令
$processId = Process::runThink('cache:clear');

// 运行 ThinkPHP 命令（带参数）
$processId = Process::runThink('make:controller', ['User']);

// 运行 ThinkPHP 命令（带参数和选项）
$processId = Process::runThink('make:controller', ['User'], [
    'env' => ['APP_DEBUG' => 'true'],
]);
```

### 获取进程信息

```php
// 获取进程信息
$process = Process::get($processId);

if ($process) {
    $command = $process['command'];
    $status = $process['status'];
    $pid = $process['pid'];
    $startTime = $process['startTime'];
    
    echo "命令：{$command}，状态：{$status}，PID：{$pid}，开始时间：" . date('Y-m-d H:i:s', $startTime);
} else {
    echo "进程不存在";
}

// 获取所有进程
$processes = Process::all();

foreach ($processes as $processId => $process) {
    echo "进程 ID：{$processId}，命令：{$process['command']}，状态：{$process['status']}";
}
```

### 获取进程输出

```php
// 获取进程输出
$output = Process::getOutput($processId);

echo "进程输出：{$output}";

// 获取进程错误
$error = Process::getError($processId);

echo "进程错误：{$error}";

// 获取进程退出码
$exitCode = Process::getExitCode($processId);

echo "进程退出码：{$exitCode}";
```

### 检查进程状态

```php
// 检查进程是否正在运行
$isRunning = Process::isRunning($processId);

if ($isRunning) {
    echo "进程正在运行";
} else {
    echo "进程已结束";
}
```

### 与进程交互

```php
// 向进程发送输入
$success = Process::write($processId, "input text\n");

if ($success) {
    echo "输入发送成功";
} else {
    echo "输入发送失败";
}

// 向进程发送信号
$success = Process::signal($processId, 'SIGINT');

if ($success) {
    echo "信号发送成功";
} else {
    echo "信号发送失败";
}

// 终止进程
$success = Process::kill($processId);

if ($success) {
    echo "进程已终止";
} else {
    echo "进程终止失败";
}

// 终止进程（指定信号）
$success = Process::kill($processId, 'SIGKILL');
```

### 等待进程结束

```php
// 等待进程结束
$success = Process::wait($processId);

if ($success) {
    echo "进程已结束";
} else {
    echo "等待超时或进程不存在";
}

// 等待进程结束（指定超时时间）
$success = Process::wait($processId, 10); // 10 秒超时
```

### 监听进程事件

```php
// 监听进程标准输出
Process::on($processId, 'stdout', function ($data) {
    echo "进程输出：{$data['data']}";
});

// 监听进程标准错误
Process::on($processId, 'stderr', function ($data) {
    echo "进程错误：{$data['data']}";
});

// 监听进程退出
Process::on($processId, 'exit', function ($data) {
    echo "进程退出，退出码：{$data['code']}";
});
```

### 清理进程

```php
// 清理已结束的进程
$count = Process::cleanup();

echo "已清理 {$count} 个进程";
```

## 进程对象格式

```php
[
    'command' => 'ls -la', // 命令
    'options' => [ // 选项
        'cwd' => '/path/to/directory',
        'env' => ['PATH' => '/usr/bin'],
        'detached' => false,
        'shell' => true,
        'windowsHide' => true,
        'stdio' => 'pipe',
    ],
    'pid' => 12345, // 进程 ID
    'status' => 'running', // 状态：running（运行中）、exited（已退出）、killed（已终止）
    'output' => '', // 标准输出
    'error' => '', // 标准错误
    'exitCode' => null, // 退出码（null 表示进程尚未退出）
    'startTime' => 1633012345, // 开始时间（时间戳）
]
```

## 实际应用场景

### 后台任务处理器

```php
use Native\ThinkPHP\Facades\Process;
use Native\ThinkPHP\Facades\Notification;

class TaskController
{
    /**
     * 运行后台任务
     *
     * @param string $task 任务名称
     * @param array $params 任务参数
     * @return \think\Response
     */
    public function run($task, array $params = [])
    {
        // 构建参数字符串
        $paramsString = '';
        foreach ($params as $key => $value) {
            $paramsString .= " --{$key}=" . escapeshellarg($value);
        }
        
        // 运行 ThinkPHP 命令
        $processId = Process::runThink("task:run {$task}{$paramsString}");
        
        if (!$processId) {
            return json(['success' => false, 'message' => '任务启动失败']);
        }
        
        // 监听进程退出
        Process::on($processId, 'exit', function ($data) use ($task) {
            if ($data['code'] === 0) {
                Notification::send('任务完成', "任务 {$task} 已成功完成");
            } else {
                Notification::send('任务失败', "任务 {$task} 失败，退出码：{$data['code']}");
            }
        });
        
        return json([
            'success' => true,
            'message' => '任务已启动',
            'process_id' => $processId,
        ]);
    }
    
    /**
     * 获取任务状态
     *
     * @param int $processId 进程 ID
     * @return \think\Response
     */
    public function status($processId)
    {
        $process = Process::get($processId);
        
        if (!$process) {
            return json(['success' => false, 'message' => '任务不存在']);
        }
        
        $isRunning = Process::isRunning($processId);
        $output = Process::getOutput($processId);
        $error = Process::getError($processId);
        $exitCode = Process::getExitCode($processId);
        
        return json([
            'success' => true,
            'status' => $isRunning ? 'running' : 'completed',
            'output' => $output,
            'error' => $error,
            'exit_code' => $exitCode,
        ]);
    }
    
    /**
     * 取消任务
     *
     * @param int $processId 进程 ID
     * @return \think\Response
     */
    public function cancel($processId)
    {
        if (!Process::isRunning($processId)) {
            return json(['success' => false, 'message' => '任务已完成或不存在']);
        }
        
        $success = Process::kill($processId);
        
        if ($success) {
            return json(['success' => true, 'message' => '任务已取消']);
        } else {
            return json(['success' => false, 'message' => '任务取消失败']);
        }
    }
}
```

### 系统命令执行器

```php
use Native\ThinkPHP\Facades\Process;
use Native\ThinkPHP\Facades\Dialog;

class CommandController
{
    /**
     * 执行系统命令
     *
     * @param string $command 命令
     * @return \think\Response
     */
    public function execute($command)
    {
        // 运行命令
        $processId = Process::run($command, [
            'shell' => true,
        ]);
        
        if (!$processId) {
            return json(['success' => false, 'message' => '命令执行失败']);
        }
        
        // 等待命令完成
        Process::wait($processId, 30); // 最多等待 30 秒
        
        // 获取命令输出
        $output = Process::getOutput($processId);
        $error = Process::getError($processId);
        $exitCode = Process::getExitCode($processId);
        
        return json([
            'success' => $exitCode === 0,
            'output' => $output,
            'error' => $error,
            'exit_code' => $exitCode,
        ]);
    }
    
    /**
     * 执行交互式命令
     *
     * @param string $command 命令
     * @return \think\Response
     */
    public function executeInteractive($command)
    {
        // 运行命令
        $processId = Process::run($command, [
            'shell' => true,
        ]);
        
        if (!$processId) {
            return json(['success' => false, 'message' => '命令执行失败']);
        }
        
        // 监听进程输出
        Process::on($processId, 'stdout', function ($data) {
            // 处理命令输出
            $output = $data['data'];
            
            // 如果输出包含提示符，可能需要用户输入
            if (strpos($output, ':') !== false || strpos($output, '?') !== false) {
                // 显示对话框获取用户输入
                $input = Dialog::prompt('命令输入', $output);
                
                if ($input !== null) {
                    // 向进程发送用户输入
                    Process::write($processId, $input . "\n");
                } else {
                    // 用户取消，终止进程
                    Process::kill($processId);
                }
            }
        });
        
        return json([
            'success' => true,
            'message' => '交互式命令已启动',
            'process_id' => $processId,
        ]);
    }
}
```

## 最佳实践

1. **错误处理**：始终检查进程操作的返回值，并妥善处理错误情况。

2. **资源清理**：使用 `cleanup` 方法清理已结束的进程，避免内存泄漏。

3. **超时设置**：在等待进程结束时，设置合理的超时时间，避免应用程序无响应。

4. **安全性**：谨慎处理用户输入，避免命令注入攻击。

5. **进程监控**：使用事件监听器监控进程的输出和退出状态，及时响应进程状态变化。

6. **资源限制**：避免同时运行过多进程，以免消耗过多系统资源。

7. **日志记录**：记录进程的运行情况，方便调试和问题排查。

## 故障排除

### 进程启动失败

- 确保命令路径正确
- 检查命令参数是否正确
- 确保命令有执行权限
- 检查工作目录是否存在

### 进程无响应

- 检查进程是否已经退出
- 检查进程是否在等待输入
- 尝试向进程发送信号
- 如果进程长时间无响应，可能需要强制终止

### 进程输出乱码

- 检查进程输出的字符编码
- 尝试设置正确的环境变量，如 `LANG=zh_CN.UTF-8`
- 在处理输出前进行字符编码转换

### 进程退出码非零

- 检查进程错误输出
- 确保命令参数正确
- 检查命令执行环境
- 查看系统日志获取更多信息
