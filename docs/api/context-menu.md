# ContextMenu 类 API 参考

`ContextMenu` 类提供了上下文菜单管理功能，包括注册和移除上下文菜单。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\ContextMenu;
```

## 方法

### `register($menu)`

注册上下文菜单。

**参数：**
- `$menu` (`\Native\ThinkPHP\Menu`) - 菜单实例

**返回值：** `void`

**示例：**

```php
$menu = Menu::create()
    ->add('复制', ['click' => 'copy'])
    ->add('粘贴', ['click' => 'paste'])
    ->add('剪切', ['click' => 'cut']);

ContextMenu::register($menu);
```

### `remove()`

移除上下文菜单。

**参数：** 无

**返回值：** `void`

**示例：**

```php
ContextMenu::remove();
```

## 完整示例

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\ContextMenu;

class Index extends BaseController
{
    public function index()
    {
        // 创建上下文菜单
        $this->createContextMenu();
        
        return view('index/index');
    }
    
    protected function createContextMenu()
    {
        $menu = Menu::create()
            ->add('复制', ['click' => 'copy'])
            ->add('粘贴', ['click' => 'paste'])
            ->add('剪切', ['click' => 'cut'])
            ->add('分隔线', ['type' => 'separator'])
            ->add('全选', ['click' => 'selectAll']);
        
        ContextMenu::register($menu);
    }
    
    public function copy()
    {
        // 处理复制操作
        return json(['success' => true, 'action' => 'copy']);
    }
    
    public function paste()
    {
        // 处理粘贴操作
        return json(['success' => true, 'action' => 'paste']);
    }
    
    public function cut()
    {
        // 处理剪切操作
        return json(['success' => true, 'action' => 'cut']);
    }
    
    public function selectAll()
    {
        // 处理全选操作
        return json(['success' => true, 'action' => 'selectAll']);
    }
    
    public function removeContextMenu()
    {
        ContextMenu::remove();
        
        return json(['success' => true, 'message' => '上下文菜单已移除']);
    }
}
