# 进度条

进度条（ProgressBar）用于显示任务进度，提供自定义进度条样式和事件监听功能。

## 基本用法

### 创建进度条

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建进度条
$progressBar = ProgressBar::create(100); // 总步骤数为100
```

### 启动进度条

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建并启动进度条
$progressBar = ProgressBar::create(100);
$progressBar->start();
```

### 更新进度

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建并启动进度条
$progressBar = ProgressBar::create(100);
$progressBar->start();

// 前进一步
$progressBar->advance();

// 前进多步
$progressBar->advance(5);

// 设置当前步骤
$progressBar->setProgress(50);
```

### 完成进度条

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建并启动进度条
$progressBar = ProgressBar::create(100);
$progressBar->start();

// 完成进度条
$progressBar->finish();
```

## 自定义进度条

### 设置进度条样式

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建进度条
$progressBar = ProgressBar::create(100);

// 设置进度条样式
$progressBar->setStyle([
    'color' => '#4CAF50',
    'backgroundColor' => '#f0f0f0',
    'height' => 10,
    'borderRadius' => 5,
]);

// 启动进度条
$progressBar->start();
```

### 设置进度条标题和描述

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建进度条
$progressBar = ProgressBar::create(100);

// 设置标题和描述
$progressBar->setTitle('文件上传');
$progressBar->setDescription('正在上传文件...');

// 启动进度条
$progressBar->start();
```

## 进度条事件

### 监听进度条事件

```php
use Native\ThinkPHP\Facades\ProgressBar;
use think\facade\Event;

// 监听进度条开始事件
Event::listen('native.progress_bar.start', function ($event) {
    // 处理进度条开始事件
    echo "进度条开始: ID = {$event['id']}, 总步骤数 = {$event['max_steps']}\n";
});

// 监听进度条前进事件
Event::listen('native.progress_bar.advance', function ($event) {
    // 处理进度条前进事件
    echo "进度条前进: ID = {$event['id']}, 步骤 = {$event['step']}, 百分比 = {$event['percent']}%\n";
});

// 监听进度条完成事件
Event::listen('native.progress_bar.finish', function ($event) {
    // 处理进度条完成事件
    echo "进度条完成: ID = {$event['id']}\n";
});
```

## 高级用法

### 多个进度条

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建多个进度条
$progressBar1 = ProgressBar::create(100);
$progressBar1->setTitle('任务1');
$progressBar1->start();

$progressBar2 = ProgressBar::create(50);
$progressBar2->setTitle('任务2');
$progressBar2->start();

// 更新进度条
for ($i = 0; $i < 100; $i++) {
    // 更新任务1进度
    $progressBar1->advance();
    
    // 每两步更新一次任务2进度
    if ($i % 2 === 0) {
        $progressBar2->advance();
    }
    
    // 模拟工作
    usleep(100000); // 休眠0.1秒
}

// 完成进度条
$progressBar1->finish();
$progressBar2->finish();
```

### 进度条回调

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建进度条
$progressBar = ProgressBar::create(100);

// 设置回调
$progressBar->onStart(function ($id, $maxSteps) {
    echo "进度条开始: ID = {$id}, 总步骤数 = {$maxSteps}\n";
});

$progressBar->onAdvance(function ($id, $step, $percent) {
    echo "进度条前进: ID = {$id}, 步骤 = {$step}, 百分比 = {$percent}%\n";
});

$progressBar->onFinish(function ($id) {
    echo "进度条完成: ID = {$id}\n";
});

// 启动进度条
$progressBar->start();

// 更新进度
for ($i = 0; $i < 100; $i++) {
    $progressBar->advance();
    usleep(100000); // 休眠0.1秒
}

// 完成进度条
$progressBar->finish();
```

### 进度条组

```php
use Native\ThinkPHP\Facades\ProgressBar;

// 创建进度条组
$group = ProgressBar::createGroup([
    'task1' => 100,
    'task2' => 50,
    'task3' => 200,
]);

// 启动进度条组
$group->start();

// 更新任务1进度
for ($i = 0; $i < 100; $i++) {
    $group->advance('task1');
    usleep(50000); // 休眠0.05秒
}

// 更新任务2进度
for ($i = 0; $i < 50; $i++) {
    $group->advance('task2');
    usleep(100000); // 休眠0.1秒
}

// 更新任务3进度
for ($i = 0; $i < 200; $i++) {
    $group->advance('task3');
    usleep(25000); // 休眠0.025秒
}

// 完成进度条组
$group->finish();
```

## 配置

在 `config/native.php` 中配置进度条：

```php
'progress_bar' => [
    // 是否记录进度条事件
    'log_events' => false,

    // 进度条样式
    'style' => 'default',

    // 进度条颜色
    'color' => '#007bff',

    // 进度条背景色
    'background_color' => '#f0f0f0',

    // 进度条高度
    'height' => 10,

    // 进度条圆角
    'border_radius' => 5,

    // 进度条开始回调
    'on_start' => null,

    // 进度条前进回调
    'on_advance' => null,

    // 进度条完成回调
    'on_finish' => null,
],
```

## 示例

### 文件上传进度

```php
use Native\ThinkPHP\Facades\ProgressBar;
use Native\ThinkPHP\Facades\Notification;

// 创建进度条
$progressBar = ProgressBar::create(100);
$progressBar->setTitle('文件上传');
$progressBar->setDescription('正在上传文件...');
$progressBar->start();

// 模拟文件上传
$fileSize = 1024 * 1024; // 1MB
$chunkSize = $fileSize / 100; // 每次上传10KB
$uploaded = 0;

for ($i = 0; $i < 100; $i++) {
    // 模拟上传一个块
    $uploaded += $chunkSize;
    
    // 更新进度
    $progressBar->advance();
    
    // 更新描述
    $percent = ($uploaded / $fileSize) * 100;
    $progressBar->setDescription(sprintf('正在上传文件... %.2f%%', $percent));
    
    // 每25%发送一次通知
    if ($i % 25 === 0) {
        Notification::send('文件上传进度', sprintf('已上传 %.2f%%', $percent));
    }
    
    // 模拟网络延迟
    usleep(100000); // 休眠0.1秒
}

// 完成进度条
$progressBar->finish();

// 发送完成通知
Notification::send('文件上传完成', '文件已成功上传');
```

### 多阶段任务进度

```php
use Native\ThinkPHP\Facades\ProgressBar;
use Native\ThinkPHP\Facades\Notification;

// 定义任务阶段
$stages = [
    'prepare' => 10,
    'process' => 50,
    'validate' => 20,
    'save' => 20,
];

// 计算总步骤数
$totalSteps = array_sum($stages);

// 创建进度条
$progressBar = ProgressBar::create($totalSteps);
$progressBar->setTitle('数据导入');
$progressBar->start();

// 执行各阶段任务
$currentStep = 0;

// 准备阶段
$progressBar->setDescription('准备数据...');
for ($i = 0; $i < $stages['prepare']; $i++) {
    // 执行准备工作
    $progressBar->advance();
    usleep(200000); // 休眠0.2秒
}

// 处理阶段
$progressBar->setDescription('处理数据...');
for ($i = 0; $i < $stages['process']; $i++) {
    // 执行处理工作
    $progressBar->advance();
    usleep(100000); // 休眠0.1秒
}

// 验证阶段
$progressBar->setDescription('验证数据...');
for ($i = 0; $i < $stages['validate']; $i++) {
    // 执行验证工作
    $progressBar->advance();
    usleep(150000); // 休眠0.15秒
}

// 保存阶段
$progressBar->setDescription('保存数据...');
for ($i = 0; $i < $stages['save']; $i++) {
    // 执行保存工作
    $progressBar->advance();
    usleep(200000); // 休眠0.2秒
}

// 完成进度条
$progressBar->finish();

// 发送完成通知
Notification::send('数据导入完成', '数据已成功导入');
```

## 注意事项

1. 进度条更新频率应该适中，过于频繁的更新会影响性能。
2. 对于长时间运行的任务，应该考虑使用后台进程或队列。
3. 进度条的总步骤数应该尽可能准确，以便正确显示进度百分比。
4. 在多线程或多进程环境中使用进度条时，需要注意线程安全问题。
5. 进度条事件和回调应该尽量简单，避免阻塞主线程。
