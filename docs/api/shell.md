# Shell 类 API 参考

`Shell` 类提供了与系统 Shell 交互的功能，包括打开文件、显示文件在文件夹中、将文件移动到回收站等。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\Shell;
```

## 方法

### `showInFolder($path)`

在文件夹中显示文件。

**参数：**
- `$path` (`string`) - 文件路径

**返回值：** `void`

**示例：**

```php
Shell::showInFolder('/path/to/file.txt');
```

### `openFile($path)`

打开文件。

**参数：**
- `$path` (`string`) - 文件路径

**返回值：** `string` - 操作结果

**示例：**

```php
$result = Shell::openFile('/path/to/file.txt');
```

### `trashFile($path)`

将文件移动到回收站。

**参数：**
- `$path` (`string`) - 文件路径

**返回值：** `void`

**示例：**

```php
Shell::trashFile('/path/to/file.txt');
```

### `openExternal($url)`

使用外部程序打开 URL。

**参数：**
- `$url` (`string`) - URL

**返回值：** `void`

**示例：**

```php
Shell::openExternal('https://www.example.com');
```

## 完整示例

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Notification;

class Index extends BaseController
{
    public function openFile()
    {
        $file = Dialog::openFile([
            'title' => '选择文件',
            'filters' => [
                ['name' => '所有文件', 'extensions' => ['*']],
                ['name' => '文本文件', 'extensions' => ['txt', 'md']],
                ['name' => '图片文件', 'extensions' => ['jpg', 'png', 'gif']],
            ],
        ]);
        
        if (!$file) {
            return json(['success' => false, 'message' => '未选择文件']);
        }
        
        $result = Shell::openFile($file);
        
        return json(['success' => true, 'message' => '文件已打开', 'result' => $result]);
    }
    
    public function showInFolder()
    {
        $file = Dialog::openFile([
            'title' => '选择文件',
            'filters' => [
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$file) {
            return json(['success' => false, 'message' => '未选择文件']);
        }
        
        Shell::showInFolder($file);
        
        return json(['success' => true, 'message' => '文件已在文件夹中显示']);
    }
    
    public function trashFile()
    {
        $file = Dialog::openFile([
            'title' => '选择要删除的文件',
            'filters' => [
                ['name' => '所有文件', 'extensions' => ['*']],
            ],
        ]);
        
        if (!$file) {
            return json(['success' => false, 'message' => '未选择文件']);
        }
        
        Shell::trashFile($file);
        
        Notification::send('文件已删除', '文件已移动到回收站');
        
        return json(['success' => true, 'message' => '文件已移动到回收站']);
    }
    
    public function openWebsite()
    {
        $url = input('url', 'https://www.example.com');
        
        Shell::openExternal($url);
        
        return json(['success' => true, 'message' => '网站已在浏览器中打开']);
    }
}
