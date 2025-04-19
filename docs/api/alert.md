# Alert 类 API 参考

`Alert` 类提供了显示警告对话框的功能，包括消息框、错误警告等。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\Alert;
```

## 方法

### `new()`

创建一个新的 Alert 实例。

**参数：** 无

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例

**示例：**

```php
$alert = Alert::new();
```

### `type($type)`

设置警告类型。

**参数：**
- `$type` (`string`) - 警告类型

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->type('info');
```

### `title($title)`

设置警告标题。

**参数：**
- `$title` (`string`) - 警告标题

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->title('警告');
```

### `detail($detail)`

设置警告详情。

**参数：**
- `$detail` (`string`) - 警告详情

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->detail('这是一条警告信息的详细内容。');
```

### `buttons($buttons)`

设置按钮列表。

**参数：**
- `$buttons` (`array`) - 按钮列表

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->buttons(['确定', '取消']);
```

### `defaultId($defaultId)`

设置默认按钮 ID。

**参数：**
- `$defaultId` (`int`) - 默认按钮 ID

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->defaultId(0);
```

### `cancelId($cancelId)`

设置取消按钮 ID。

**参数：**
- `$cancelId` (`int`) - 取消按钮 ID

**返回值：** `\Native\ThinkPHP\Alert` - Alert 实例，用于链式调用

**示例：**

```php
$alert = Alert::new()->cancelId(1);
```

### `show($message)`

显示警告消息。

**参数：**
- `$message` (`string`) - 警告消息

**返回值：** `int` - 用户点击的按钮 ID

**示例：**

```php
$result = Alert::new()
    ->title('警告')
    ->buttons(['确定', '取消'])
    ->defaultId(0)
    ->cancelId(1)
    ->show('确定要执行此操作吗？');

if ($result === 0) {
    // 用户点击了"确定"按钮
} else {
    // 用户点击了"取消"按钮
}
```

### `error($title, $message)`

显示错误警告。

**参数：**
- `$title` (`string`) - 错误标题
- `$message` (`string`) - 错误消息

**返回值：** `bool` - 操作是否成功

**示例：**

```php
$result = Alert::error('错误', '发生了一个错误，请稍后重试。');
```

## 完整示例

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Alert;

class Index extends BaseController
{
    public function showAlert()
    {
        $result = Alert::new()
            ->title('确认操作')
            ->buttons(['确定', '取消'])
            ->defaultId(0)
            ->cancelId(1)
            ->show('确定要删除这个文件吗？');
        
        if ($result === 0) {
            return json(['success' => true, 'message' => '用户确认删除']);
        } else {
            return json(['success' => false, 'message' => '用户取消删除']);
        }
    }
    
    public function showError()
    {
        Alert::error('删除失败', '无法删除文件，请检查文件权限。');
        
        return json(['success' => false, 'message' => '显示错误信息']);
    }
}
