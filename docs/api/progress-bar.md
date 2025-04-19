# ProgressBar 类 API 参考

`ProgressBar` 类提供了进度条管理功能，包括创建、更新和完成进度条。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\ProgressBar;
```

## 方法

### `create($maxSteps)`

创建一个新的进度条实例。

**参数：**
- `$maxSteps` (`int`) - 最大步骤数

**返回值：** `\Native\ThinkPHP\ProgressBar` - ProgressBar 实例

**示例：**

```php
$progressBar = ProgressBar::create(100);
```

### `start()`

开始进度条。

**参数：** 无

**返回值：** `void`

**示例：**

```php
ProgressBar::create(100)->start();
```

### `advance($step = 1)`

前进步骤。

**参数：**
- `$step` (`int`) - 前进的步骤数，默认为 1

**返回值：** `void`

**示例：**

```php
$progressBar = ProgressBar::create(100);
$progressBar->start();
$progressBar->advance(10); // 前进 10 步
```

### `setProgress($step)`

设置进度。

**参数：**
- `$step` (`int`) - 当前步骤

**返回值：** `void`

**示例：**

```php
$progressBar = ProgressBar::create(100);
$progressBar->start();
$progressBar->setProgress(50); // 设置进度为 50%
```

### `finish()`

完成进度条。

**参数：** 无

**返回值：** `void`

**示例：**

```php
$progressBar = ProgressBar::create(100);
$progressBar->start();
$progressBar->setProgress(50);
$progressBar->finish();
```

### `display()`

显示进度条。

**参数：** 无

**返回值：** `void`

**示例：**

```php
$progressBar = ProgressBar::create(100);
$progressBar->start();
$progressBar->setProgress(50);
$progressBar->display();
```

## 完整示例

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\ProgressBar;

class Index extends BaseController
{
    public function processTask()
    {
        $totalItems = 100;
        $progressBar = ProgressBar::create($totalItems);
        $progressBar->start();
        
        for ($i = 0; $i < $totalItems; $i++) {
            // 执行任务
            $this->doTask($i);
            
            // 更新进度
            $progressBar->advance();
            
            // 模拟耗时操作
            usleep(100000); // 100ms
        }
        
        $progressBar->finish();
        
        return json(['success' => true, 'message' => '任务已完成']);
    }
    
    protected function doTask($index)
    {
        // 模拟任务处理
        // ...
    }
    
    public function processTaskWithSteps()
    {
        $steps = [
            '准备数据' => 10,
            '处理数据' => 30,
            '验证数据' => 20,
            '保存数据' => 40,
        ];
        
        $totalSteps = array_sum($steps);
        $progressBar = ProgressBar::create($totalSteps);
        $progressBar->start();
        
        $currentStep = 0;
        
        foreach ($steps as $stepName => $stepCount) {
            // 执行步骤
            $this->executeStep($stepName, $stepCount);
            
            // 更新进度
            $currentStep += $stepCount;
            $progressBar->setProgress($currentStep);
        }
        
        $progressBar->finish();
        
        return json(['success' => true, 'message' => '任务已完成']);
    }
    
    protected function executeStep($stepName, $stepCount)
    {
        // 模拟步骤执行
        // ...
        
        // 模拟耗时操作
        usleep($stepCount * 100000); // stepCount * 100ms
    }
}
