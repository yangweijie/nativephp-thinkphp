# Logger 类 API 参考

`Logger` 类提供了日志记录功能，包括记录不同级别的日志、设置日志级别、轮换日志文件等。

## 命名空间

```php
namespace Native\ThinkPHP\Utils;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\Logger;
```

## 方法

### `setLevel($level)`

设置日志级别。

**参数：**
- `$level` (`string`) - 日志级别，可选值：`debug`、`info`、`notice`、`warning`、`error`、`critical`、`alert`、`emergency`

**返回值：** `\Native\ThinkPHP\Utils\Logger` - Logger 实例

**示例：**

```php
Logger::setLevel('warning');
```

### `setLogFile($logFile)`

设置日志文件路径。

**参数：**
- `$logFile` (`string`) - 日志文件路径

**返回值：** `\Native\ThinkPHP\Utils\Logger` - Logger 实例

**示例：**

```php
Logger::setLogFile('/path/to/log.log');
```

### `getLogFile()`

获取日志文件路径。

**参数：** 无

**返回值：** `string` - 日志文件路径

**示例：**

```php
$logFile = Logger::getLogFile();
```

### `log($level, $message, array $context = [])`

写入日志。

**参数：**
- `$level` (`string`) - 日志级别
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::log('info', '用户登录', ['user_id' => 1, 'username' => 'admin']);
```

### `debug($message, array $context = [])`

记录调试信息。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::debug('调试信息');
```

### `info($message, array $context = [])`

记录信息。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::info('用户登录', ['user_id' => 1, 'username' => 'admin']);
```

### `notice($message, array $context = [])`

记录通知。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::notice('系统通知');
```

### `warning($message, array $context = [])`

记录警告。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::warning('系统警告');
```

### `error($message, array $context = [])`

记录错误。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::error('系统错误', ['error_code' => 500]);
```

### `critical($message, array $context = [])`

记录严重错误。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::critical('严重错误');
```

### `alert($message, array $context = [])`

记录警报。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::alert('系统警报');
```

### `emergency($message, array $context = [])`

记录紧急情况。

**参数：**
- `$message` (`string`) - 日志消息
- `$context` (`array`) - 日志上下文

**返回值：** `bool` - 是否成功写入日志

**示例：**

```php
Logger::emergency('系统紧急情况');
```

### `clear()`

清空日志文件。

**参数：** 无

**返回值：** `bool` - 是否成功清空日志文件

**示例：**

```php
Logger::clear();
```

### `get($lines = 0)`

获取日志内容。

**参数：**
- `$lines` (`int`) - 获取的行数，0 表示获取所有行

**返回值：** `string` - 日志内容

**示例：**

```php
// 获取所有日志
$content = Logger::get();

// 获取最近 100 行日志
$content = Logger::get(100);
```

### `size()`

获取日志文件大小。

**参数：** 无

**返回值：** `int` - 日志文件大小（字节）

**示例：**

```php
$size = Logger::size();
```

### `rotate($maxSize = 10485760, $maxFiles = 5)`

轮换日志文件。

**参数：**
- `$maxSize` (`int`) - 最大文件大小（字节），默认为 10MB
- `$maxFiles` (`int`) - 最大文件数，默认为 5

**返回值：** `bool` - 是否成功轮换日志文件

**示例：**

```php
// 当日志文件大于 10MB 时轮换，最多保留 5 个备份
Logger::rotate();

// 当日志文件大于 5MB 时轮换，最多保留 3 个备份
Logger::rotate(5 * 1024 * 1024, 3);
```

## 日志级别

Logger 类支持以下日志级别，按严重程度从低到高排序：

1. `debug` - 调试信息，用于开发和调试
2. `info` - 一般信息，用于记录正常操作
3. `notice` - 通知，用于记录重要但不是错误的事件
4. `warning` - 警告，用于记录可能导致问题的事件
5. `error` - 错误，用于记录运行时错误
6. `critical` - 严重错误，用于记录可能导致应用部分功能不可用的错误
7. `alert` - 警报，用于记录需要立即处理的错误
8. `emergency` - 紧急情况，用于记录导致整个应用不可用的错误

当设置日志级别时，只有等于或高于设置级别的日志会被记录。例如，如果设置级别为 `warning`，则只有 `warning`、`error`、`critical`、`alert` 和 `emergency` 级别的日志会被记录，而 `debug`、`info` 和 `notice` 级别的日志会被忽略。

## 日志格式

Logger 类记录的日志格式如下：

```
[时间] [级别] 消息
```

例如：

```
[2023-01-01 12:00:00] [INFO] 用户登录 {"user_id":1,"username":"admin"}
```

## 日志上下文

Logger 类支持记录日志上下文，上下文是一个关联数组，包含与日志消息相关的额外信息。上下文会被转换为 JSON 格式并附加到日志消息后面。

例如：

```php
Logger::info('用户登录', ['user_id' => 1, 'username' => 'admin']);
```

会生成以下日志：

```
[2023-01-01 12:00:00] [INFO] 用户登录 {"user_id":1,"username":"admin"}
```

## 日志文件轮换

Logger 类支持日志文件轮换，当日志文件大小超过指定大小时，会将当前日志文件重命名为备份文件，并创建一个新的空日志文件。备份文件的命名格式为 `{日志文件名}.{时间戳}`。

例如，如果日志文件为 `app.log`，轮换后会生成 `app.log.20230101120000` 备份文件，并创建一个新的 `app.log` 文件。

当备份文件数量超过指定数量时，会删除最旧的备份文件。
