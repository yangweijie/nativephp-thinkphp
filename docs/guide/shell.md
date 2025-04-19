# Shell 命令执行

Shell 命令执行（Shell）用于执行系统命令和操作文件，提供安全的命令执行环境。

## 基本用法

### 执行命令

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令
$result = Shell::exec('echo Hello, World!');

// 输出结果
echo $result; // Hello, World!
```

### 执行命令并获取输出和退出码

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并获取输出和退出码
$output = '';
$exitCode = 0;
Shell::exec('echo Hello, World!', $output, $exitCode);

// 输出结果
echo "输出: {$output}, 退出码: {$exitCode}";
```

### 执行后台命令

```php
use Native\ThinkPHP\Facades\Shell;

// 执行后台命令
Shell::execInBackground('php -S localhost:8000 -t public');
```

## 文件操作

### 打开文件

```php
use Native\ThinkPHP\Facades\Shell;

// 打开文件
Shell::openItem('path/to/file.txt');
```

### 在文件管理器中显示文件

```php
use Native\ThinkPHP\Facades\Shell;

// 在文件管理器中显示文件
Shell::showItemInFolder('path/to/file.txt');
```

### 将文件移动到回收站

```php
use Native\ThinkPHP\Facades\Shell;

// 将文件移动到回收站
Shell::trashItem('path/to/file.txt');
```

### 使用外部程序打开 URL

```php
use Native\ThinkPHP\Facades\Shell;

// 使用默认浏览器打开 URL
Shell::openExternal('https://www.example.com');
```

## 高级用法

### 执行命令并实时获取输出

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并实时获取输出
Shell::execWithCallback('ping localhost -n 5', function ($output) {
    echo $output;
});
```

### 执行命令并设置工作目录

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并设置工作目录
$result = Shell::execInDirectory('npm install', 'path/to/project');
```

### 执行命令并设置环境变量

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并设置环境变量
$result = Shell::execWithEnv('node app.js', [
    'NODE_ENV' => 'production',
    'PORT' => '3000',
]);
```

### 执行命令并设置超时

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并设置超时（秒）
$result = Shell::execWithTimeout('php long_task.php', 30);
```

### 执行命令并捕获错误输出

```php
use Native\ThinkPHP\Facades\Shell;

// 执行命令并捕获错误输出
$output = '';
$errorOutput = '';
$exitCode = 0;
Shell::execWithErrorOutput('ls non_existent_directory', $output, $errorOutput, $exitCode);

// 输出结果
echo "标准输出: {$output}\n";
echo "错误输出: {$errorOutput}\n";
echo "退出码: {$exitCode}\n";
```

## 安全执行命令

### 检查命令是否安全

```php
use Native\ThinkPHP\Facades\Shell;

// 检查命令是否安全
$command = 'rm -rf /';
if (Shell::isCommandSafe($command)) {
    // 执行命令
    Shell::exec($command);
} else {
    echo "命令不安全: {$command}";
}
```

### 过滤命令参数

```php
use Native\ThinkPHP\Facades\Shell;

// 过滤命令参数
$fileName = $_GET['file']; // 用户输入
$safeFileName = Shell::escapeArgument($fileName);

// 执行命令
Shell::exec("cat {$safeFileName}");
```

### 使用白名单命令

```php
use Native\ThinkPHP\Facades\Shell;

// 定义白名单命令
$whitelistCommands = [
    'ls', 'dir', 'echo', 'cat', 'type',
];

// 检查命令是否在白名单中
$command = 'ls -la';
$commandName = explode(' ', $command)[0];

if (in_array($commandName, $whitelistCommands)) {
    // 执行命令
    Shell::exec($command);
} else {
    echo "命令不在白名单中: {$commandName}";
}
```

## 配置

在 `config/native.php` 中配置 Shell 命令执行：

```php
'shell' => [
    // 是否记录 Shell 事件
    'log_events' => false,

    // 是否允许执行危险命令
    'allow_dangerous_commands' => false,

    // 危险命令列表
    'dangerous_commands' => [
        'rm -rf',
        'format',
        'mkfs',
        'dd',
        'sudo',
        'su',
    ],

    // 打开文件回调
    'on_open_item' => null,

    // 在文件夹中显示文件回调
    'on_show_item_in_folder' => null,

    // 将文件移动到回收站回调
    'on_trash_item' => null,

    // 使用外部程序打开 URL 回调
    'on_open_external' => null,
],
```

## 事件

Shell 命令执行会触发以下事件：

- `native.shell.open_item`：当打开文件时触发
- `native.shell.show_item_in_folder`：当在文件管理器中显示文件时触发
- `native.shell.trash_item`：当将文件移动到回收站时触发
- `native.shell.open_external`：当使用外部程序打开 URL 时触发

你可以监听这些事件：

```php
use think\facade\Event;

// 监听打开文件事件
Event::listen('native.shell.open_item', function ($event) {
    // 处理打开文件事件
    echo "打开文件: {$event['path']}\n";
});
```

## 示例

### 执行系统命令并解析输出

```php
use Native\ThinkPHP\Facades\Shell;

// 获取系统信息
$output = Shell::exec('systeminfo');

// 解析输出
$lines = explode("\n", $output);
$systemInfo = [];

foreach ($lines as $line) {
    $parts = explode(':', $line, 2);
    if (count($parts) === 2) {
        $key = trim($parts[0]);
        $value = trim($parts[1]);
        $systemInfo[$key] = $value;
    }
}

// 输出系统信息
echo "操作系统: {$systemInfo['OS Name']}\n";
echo "系统版本: {$systemInfo['OS Version']}\n";
echo "处理器: {$systemInfo['Processor(s)']}\n";
echo "内存: {$systemInfo['Total Physical Memory']}\n";
```

### 执行 Git 命令

```php
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\Notification;

// 获取 Git 仓库状态
$output = Shell::execInDirectory('git status', 'path/to/repo');

// 检查是否有未提交的更改
if (strpos($output, 'nothing to commit') === false) {
    // 有未提交的更改
    Notification::send('Git 状态', '仓库有未提交的更改');
    
    // 获取未提交的文件列表
    $changedFiles = Shell::execInDirectory('git diff --name-only', 'path/to/repo');
    
    // 输出未提交的文件列表
    echo "未提交的文件:\n{$changedFiles}\n";
} else {
    // 没有未提交的更改
    Notification::send('Git 状态', '仓库没有未提交的更改');
}
```

### 批量处理文件

```php
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\ProgressBar;

// 获取目录中的所有图片文件
$output = Shell::exec('dir /b *.jpg *.png *.gif');
$files = explode("\n", $output);
$files = array_filter($files);

// 创建进度条
$progressBar = ProgressBar::create(count($files));
$progressBar->setTitle('图片处理');
$progressBar->start();

// 处理每个文件
foreach ($files as $file) {
    // 调整图片大小
    Shell::exec("convert {$file} -resize 800x600 resized_{$file}");
    
    // 更新进度条
    $progressBar->advance();
    $progressBar->setDescription("处理文件: {$file}");
    
    // 休眠一小段时间
    usleep(100000); // 休眠0.1秒
}

// 完成进度条
$progressBar->finish();

// 发送完成通知
Notification::send('图片处理完成', '所有图片已处理完成');
```

### 执行 PHP 脚本

```php
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\Notification;

// 执行 PHP 脚本
$output = Shell::exec('php scripts/generate_report.php --type=monthly --format=pdf');

// 检查脚本是否成功执行
if (strpos($output, 'Report generated successfully') !== false) {
    // 脚本执行成功
    preg_match('/Report saved to: (.+)/', $output, $matches);
    $reportPath = $matches[1] ?? '';
    
    // 发送成功通知
    Notification::send('报告生成成功', "报告已保存到: {$reportPath}");
    
    // 打开报告文件
    if ($reportPath) {
        Shell::openItem($reportPath);
    }
} else {
    // 脚本执行失败
    Notification::send('报告生成失败', '生成报告时出错，请检查日志');
}
```

## 注意事项

1. 执行命令时应该始终验证和过滤用户输入，以防止命令注入攻击。
2. 避免执行危险命令，如删除文件系统、格式化磁盘等。
3. 在生产环境中，应该使用白名单命令或限制命令执行权限。
4. 对于长时间运行的命令，应该考虑使用后台执行或子进程管理。
5. 在 Windows 系统上，某些命令可能需要使用 `cmd /c` 前缀。
6. 命令执行可能会受到系统权限的限制，特别是在沙箱环境中。
