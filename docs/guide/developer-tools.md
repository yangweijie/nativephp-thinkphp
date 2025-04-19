# 开发者工具

开发者工具（DeveloperTools）用于调试和性能分析，提供开发者工具面板和调试功能。

## 基本用法

### 启用开发者工具

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 启用开发者工具
DeveloperTools::enable();
```

### 禁用开发者工具

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 禁用开发者工具
DeveloperTools::disable();
```

### 切换开发者工具状态

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 切换开发者工具状态
DeveloperTools::toggle();
```

### 检查开发者工具是否启用

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 检查开发者工具是否启用
if (DeveloperTools::isEnabled()) {
    echo "开发者工具已启用";
} else {
    echo "开发者工具已禁用";
}
```

## 开发者工具面板

### 打开开发者工具面板

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 打开开发者工具面板
DeveloperTools::openDevTools();
```

### 关闭开发者工具面板

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 关闭开发者工具面板
DeveloperTools::closeDevTools();
```

### 切换开发者工具面板

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 切换开发者工具面板
DeveloperTools::toggleDevTools();
```

### 检查开发者工具面板是否打开

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 检查开发者工具面板是否打开
if (DeveloperTools::isDevToolsOpened()) {
    echo "开发者工具面板已打开";
} else {
    echo "开发者工具面板已关闭";
}
```

## 调试功能

### 打印调试信息

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 打印调试信息
DeveloperTools::log('这是一条调试信息');
DeveloperTools::info('这是一条信息');
DeveloperTools::warn('这是一条警告');
DeveloperTools::error('这是一条错误');
```

### 打印对象或数组

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 打印对象或数组
$user = [
    'id' => 1,
    'name' => '张三',
    'email' => 'zhangsan@example.com',
];

DeveloperTools::log('用户信息', $user);
```

### 分组打印

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 开始分组
DeveloperTools::group('用户操作');

// 打印分组内的信息
DeveloperTools::log('用户登录');
DeveloperTools::log('用户ID', 1);
DeveloperTools::log('用户名', '张三');

// 结束分组
DeveloperTools::groupEnd();
```

### 计时功能

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 开始计时
DeveloperTools::time('操作耗时');

// 执行一些操作
sleep(2);

// 结束计时并打印耗时
DeveloperTools::timeEnd('操作耗时');
```

### 打印调用堆栈

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 打印调用堆栈
DeveloperTools::trace('调用堆栈');
```

## 性能分析

### 开始性能分析

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 开始性能分析
DeveloperTools::startProfiling('查询性能');

// 执行一些操作
$result = db('users')->where('status', 1)->select();

// 结束性能分析
DeveloperTools::stopProfiling('查询性能');
```

### 记录性能标记

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 记录性能标记
DeveloperTools::mark('开始查询');

// 执行一些操作
$result = db('users')->where('status', 1)->select();

// 记录另一个性能标记
DeveloperTools::mark('查询完成');

// 测量两个标记之间的时间
DeveloperTools::measure('查询耗时', '开始查询', '查询完成');
```

### 清除性能标记

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 清除特定性能标记
DeveloperTools::clearMarks('开始查询');

// 清除所有性能标记
DeveloperTools::clearMarks();

// 清除特定性能测量
DeveloperTools::clearMeasures('查询耗时');

// 清除所有性能测量
DeveloperTools::clearMeasures();
```

## 内存分析

### 获取内存使用情况

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 获取当前内存使用情况
$memory = DeveloperTools::memory();
echo "当前内存使用: {$memory} 字节";

// 获取内存使用峰值
$peakMemory = DeveloperTools::memoryPeak();
echo "内存使用峰值: {$peakMemory} 字节";
```

### 监控内存使用

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 开始监控内存使用
DeveloperTools::startMemoryMonitor('内存监控');

// 执行一些操作
$data = [];
for ($i = 0; $i < 10000; $i++) {
    $data[] = "item-{$i}";
}

// 结束监控并获取内存使用情况
$memoryUsage = DeveloperTools::stopMemoryMonitor('内存监控');
echo "内存使用: {$memoryUsage['used']} 字节, 增加: {$memoryUsage['diff']} 字节";
```

## 配置

在 `config/native.php` 中配置开发者工具：

```php
'developer' => [
    // 是否显示开发者工具
    'show_devtools' => false,

    // 是否允许检查元素
    'allow_inspect' => false,

    // 是否允许控制台
    'allow_console' => false,

    // 是否允许网络面板
    'allow_network' => false,

    // 是否允许源代码面板
    'allow_sources' => false,

    // 是否允许应用面板
    'allow_application' => false,

    // 是否允许内存面板
    'allow_memory' => false,

    // 是否允许性能面板
    'allow_performance' => false,
],
```

## 示例

### 调试 API 请求

```php
use Native\ThinkPHP\Facades\DeveloperTools;
use Native\ThinkPHP\Facades\Http;

// 启用开发者工具
DeveloperTools::enable();

// 开始计时
DeveloperTools::time('API 请求');

// 发送 API 请求
try {
    $response = Http::get('https://api.example.com/users');
    
    // 打印响应
    DeveloperTools::group('API 响应');
    DeveloperTools::log('状态码', $response->status());
    DeveloperTools::log('响应头', $response->headers());
    DeveloperTools::log('响应体', $response->json());
    DeveloperTools::groupEnd();
} catch (\Exception $e) {
    // 打印错误
    DeveloperTools::error('API 请求失败', $e->getMessage());
}

// 结束计时
DeveloperTools::timeEnd('API 请求');
```

### 性能分析数据库查询

```php
use Native\ThinkPHP\Facades\DeveloperTools;
use think\facade\Db;

// 启用开发者工具
DeveloperTools::enable();

// 分析简单查询
DeveloperTools::time('简单查询');
$users = Db::table('users')->where('status', 1)->select();
DeveloperTools::timeEnd('简单查询');
DeveloperTools::log('查询结果数量', count($users));

// 分析复杂查询
DeveloperTools::time('复杂查询');
$result = Db::table('users')
    ->alias('u')
    ->join('orders o', 'u.id = o.user_id')
    ->join('products p', 'o.product_id = p.id')
    ->where('u.status', 1)
    ->where('o.status', 'completed')
    ->field('u.id, u.name, COUNT(o.id) as order_count, SUM(o.total) as total_amount')
    ->group('u.id')
    ->having('order_count > 0')
    ->order('total_amount DESC')
    ->limit(10)
    ->select();
DeveloperTools::timeEnd('复杂查询');
DeveloperTools::log('复杂查询结果', $result);

// 比较查询性能
DeveloperTools::log('性能比较', '复杂查询比简单查询慢 ' . ($this->getTimeValue('复杂查询') / $this->getTimeValue('简单查询')) . ' 倍');
```

### 内存使用分析

```php
use Native\ThinkPHP\Facades\DeveloperTools;

// 启用开发者工具
DeveloperTools::enable();

// 记录初始内存使用
DeveloperTools::log('初始内存使用', DeveloperTools::memory());

// 分析数组操作的内存使用
DeveloperTools::startMemoryMonitor('数组操作');
$largeArray = [];
for ($i = 0; $i < 100000; $i++) {
    $largeArray[] = "item-{$i}";
}
$arrayMemory = DeveloperTools::stopMemoryMonitor('数组操作');
DeveloperTools::log('数组操作内存使用', $arrayMemory);

// 分析对象操作的内存使用
DeveloperTools::startMemoryMonitor('对象操作');
$objects = [];
for ($i = 0; $i < 10000; $i++) {
    $objects[] = new \stdClass();
    $objects[$i]->id = $i;
    $objects[$i]->name = "Object {$i}";
    $objects[$i]->data = str_repeat('x', 100);
}
$objectMemory = DeveloperTools::stopMemoryMonitor('对象操作');
DeveloperTools::log('对象操作内存使用', $objectMemory);

// 清理内存
$largeArray = null;
$objects = null;
gc_collect_cycles();

// 记录最终内存使用
DeveloperTools::log('最终内存使用', DeveloperTools::memory());
DeveloperTools::log('内存使用峰值', DeveloperTools::memoryPeak());
```

## 注意事项

1. 开发者工具应该只在开发环境中启用，生产环境中应该禁用。
2. 过多的调试信息可能会影响应用性能，特别是在循环中打印大量信息时。
3. 敏感信息不应该通过开发者工具打印，以防信息泄露。
4. 开发者工具的某些功能可能受到浏览器安全策略的限制。
5. 在多窗口应用中，每个窗口都有自己的开发者工具实例。
