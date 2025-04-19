# 键盘功能

NativePHP for ThinkPHP 提供了键盘功能，允许你的桌面应用程序注册和管理快捷键、模拟键盘输入和监听键盘事件。本文档将介绍如何使用这些功能。

## 基本概念

键盘功能允许你的应用程序注册和管理应用程序快捷键和全局快捷键、模拟键盘输入和监听键盘事件。这些功能可以用于创建快捷键管理应用、键盘监听应用、自动化工具等。

## 使用 Keyboard Facade

NativePHP for ThinkPHP 提供了 `Keyboard` Facade，用于注册和管理快捷键、模拟键盘输入和监听键盘事件。

```php
use Native\ThinkPHP\Facades\Keyboard;
```

### 注册和管理快捷键

```php
// 注册快捷键
$id = Keyboard::register('CommandOrControl+Shift+K', function () {
    // 快捷键被触发时执行的代码
    echo "快捷键被触发";
});

// 检查快捷键是否已注册
$isRegistered = Keyboard::isRegistered('CommandOrControl+Shift+K');

if ($isRegistered) {
    echo "快捷键已注册";
} else {
    echo "快捷键未注册";
}

// 获取所有已注册的快捷键
$shortcuts = Keyboard::getRegisteredShortcuts();

foreach ($shortcuts as $id => $shortcut) {
    echo "快捷键 ID：{$id}，组合键：{$shortcut['accelerator']}";
}

// 注销快捷键
$success = Keyboard::unregister($id);

if ($success) {
    echo "快捷键已注销";
} else {
    echo "快捷键注销失败";
}

// 注销所有快捷键
Keyboard::unregisterAll();
```

### 注册和管理全局快捷键

```php
// 注册全局快捷键
$id = Keyboard::registerGlobal('CommandOrControl+Shift+G', function () {
    // 全局快捷键被触发时执行的代码
    echo "全局快捷键被触发";
});

// 检查全局快捷键是否已注册
$isRegistered = Keyboard::isGlobalRegistered('CommandOrControl+Shift+G');

if ($isRegistered) {
    echo "全局快捷键已注册";
} else {
    echo "全局快捷键未注册";
}

// 获取所有已注册的全局快捷键
$shortcuts = Keyboard::getRegisteredGlobalShortcuts();

foreach ($shortcuts as $id => $shortcut) {
    echo "全局快捷键 ID：{$id}，组合键：{$shortcut['accelerator']}";
}

// 注销全局快捷键
$success = Keyboard::unregisterGlobal($id);

if ($success) {
    echo "全局快捷键已注销";
} else {
    echo "全局快捷键注销失败";
}

// 注销所有全局快捷键
Keyboard::unregisterAllGlobal();
```

### 模拟键盘输入

```php
// 模拟按键
$success = Keyboard::sendKey('a');

// 模拟按键（带修饰键）
$success = Keyboard::sendKey('a', ['shift', 'control']);

// 模拟按键序列
$success = Keyboard::sendText('Hello, World!');

// 模拟按下并松开按键
$success = Keyboard::tapKey('a');

// 模拟按下并松开按键（带修饰键）
$success = Keyboard::tapKey('a', ['shift', 'control']);

// 模拟按下按键
$success = Keyboard::keyDown('a');

// 模拟按下按键（带修饰键）
$success = Keyboard::keyDown('a', ['shift', 'control']);

// 模拟松开按键
$success = Keyboard::keyUp('a');

// 模拟松开按键（带修饰键）
$success = Keyboard::keyUp('a', ['shift', 'control']);
```

### 监听键盘事件

```php
// 监听键盘事件
$id = Keyboard::on('keydown', function ($event) {
    // 键盘事件被触发时执行的代码
    $key = $event['key'];
    $code = $event['code'];
    $modifiers = $event['modifiers'];
    
    echo "按下按键：{$key}，键码：{$code}，修饰键：" . implode(', ', $modifiers);
});

// 移除键盘事件监听器
$success = Keyboard::off($id);

if ($success) {
    echo "键盘事件监听器已移除";
} else {
    echo "键盘事件监听器移除失败";
}
```

### 键盘布局

```php
// 获取键盘布局
$layout = Keyboard::getLayout();

echo "当前键盘布局：{$layout}";

// 设置键盘布局
$success = Keyboard::setLayout('en-US');

if ($success) {
    echo "键盘布局已设置为 en-US";
} else {
    echo "键盘布局设置失败";
}
```

## 快捷键格式

快捷键组合使用 Electron 的 [Accelerator](https://www.electronjs.org/docs/latest/api/accelerator) 格式，支持以下修饰键：

- `Command` 或 `Cmd`：macOS 上的 Command 键
- `Control` 或 `Ctrl`：Control 键
- `CommandOrControl` 或 `CmdOrCtrl`：macOS 上的 Command 键，其他平台上的 Control 键
- `Alt`：Alt 键
- `Option`：macOS 上的 Option 键
- `AltGr`：Alt Graph 键
- `Shift`：Shift 键
- `Super`：Windows 或 Linux 上的 Windows 键，macOS 上的 Command 键

示例：

- `CommandOrControl+A`
- `CommandOrControl+Shift+Z`
- `Alt+F4`
- `Control+Space`

## 键盘事件类型

监听键盘事件时，可以指定以下事件类型：

- `keydown`：按键被按下时触发
- `keyup`：按键被松开时触发
- `keypress`：按键被按下并产生字符时触发

## 键盘事件数据格式

```php
[
    'key' => 'a', // 按键字符
    'code' => 'KeyA', // 按键代码
    'keyCode' => 65, // 按键码
    'modifiers' => ['shift', 'control'], // 修饰键
    'repeat' => false, // 是否重复按键
    'type' => 'keydown', // 事件类型
    'timestamp' => 1633012345000, // 时间戳（毫秒）
]
```

## 实际应用场景

### 快捷键管理器

```php
use Native\ThinkPHP\Facades\Keyboard;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class ShortcutController
{
    /**
     * 注册快捷键
     *
     * @param string $accelerator 快捷键组合
     * @param string $action 动作名称
     * @return \think\Response
     */
    public function register($accelerator, $action)
    {
        // 检查快捷键是否已注册
        if (Keyboard::isRegistered($accelerator)) {
            return json(['success' => false, 'message' => '快捷键已被注册']);
        }
        
        // 注册快捷键
        $id = Keyboard::register($accelerator, function () use ($action) {
            // 执行动作
            $this->executeAction($action);
        });
        
        if (!$id) {
            return json(['success' => false, 'message' => '快捷键注册失败']);
        }
        
        // 保存快捷键信息
        $shortcuts = Settings::get('shortcuts', []);
        $shortcuts[$id] = [
            'accelerator' => $accelerator,
            'action' => $action,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        Settings::set('shortcuts', $shortcuts);
        
        Notification::send('快捷键已注册', "快捷键 {$accelerator} 已注册为 {$action}");
        
        return json(['success' => true, 'id' => $id]);
    }
    
    /**
     * 注销快捷键
     *
     * @param string $id 快捷键 ID
     * @return \think\Response
     */
    public function unregister($id)
    {
        // 注销快捷键
        $success = Keyboard::unregister($id);
        
        if (!$success) {
            return json(['success' => false, 'message' => '快捷键注销失败']);
        }
        
        // 删除快捷键信息
        $shortcuts = Settings::get('shortcuts', []);
        unset($shortcuts[$id]);
        Settings::set('shortcuts', $shortcuts);
        
        Notification::send('快捷键已注销', '快捷键已成功注销');
        
        return json(['success' => true]);
    }
    
    /**
     * 获取所有快捷键
     *
     * @return \think\Response
     */
    public function getAll()
    {
        // 获取所有快捷键信息
        $shortcuts = Settings::get('shortcuts', []);
        
        return json(['shortcuts' => $shortcuts]);
    }
    
    /**
     * 执行动作
     *
     * @param string $action 动作名称
     * @return void
     */
    protected function executeAction($action)
    {
        // 根据动作名称执行相应的操作
        switch ($action) {
            case 'new_file':
                // 创建新文件
                break;
            case 'open_file':
                // 打开文件
                break;
            case 'save_file':
                // 保存文件
                break;
            case 'close_file':
                // 关闭文件
                break;
            case 'quit_app':
                // 退出应用
                break;
            default:
                // 未知动作
                break;
        }
        
        // 记录动作执行历史
        $history = Settings::get('action_history', []);
        $history[] = [
            'action' => $action,
            'executed_at' => date('Y-m-d H:i:s'),
        ];
        Settings::set('action_history', $history);
    }
}
```

### 键盘监听器

```php
use Native\ThinkPHP\Facades\Keyboard;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class KeyboardListenerController
{
    /**
     * 启动键盘监听器
     *
     * @param string $event 事件类型
     * @return \think\Response
     */
    public function start($event = 'keydown')
    {
        // 检查是否已有监听器
        $listenerId = Settings::get('keyboard_listener.id');
        if ($listenerId) {
            // 先移除旧的监听器
            Keyboard::off($listenerId);
        }
        
        // 注册新的监听器
        $id = Keyboard::on($event, function ($keyEvent) {
            // 记录按键事件
            $this->logKeyEvent($keyEvent);
        });
        
        if (!$id) {
            return json(['success' => false, 'message' => '键盘监听器启动失败']);
        }
        
        // 保存监听器信息
        Settings::set('keyboard_listener', [
            'id' => $id,
            'event' => $event,
            'started_at' => date('Y-m-d H:i:s'),
        ]);
        
        Notification::send('键盘监听器已启动', "正在监听 {$event} 事件");
        
        return json(['success' => true, 'id' => $id]);
    }
    
    /**
     * 停止键盘监听器
     *
     * @return \think\Response
     */
    public function stop()
    {
        // 获取监听器 ID
        $listenerId = Settings::get('keyboard_listener.id');
        
        if (!$listenerId) {
            return json(['success' => false, 'message' => '没有正在运行的键盘监听器']);
        }
        
        // 移除监听器
        $success = Keyboard::off($listenerId);
        
        if (!$success) {
            return json(['success' => false, 'message' => '键盘监听器停止失败']);
        }
        
        // 删除监听器信息
        Settings::delete('keyboard_listener');
        
        Notification::send('键盘监听器已停止', '键盘监听器已成功停止');
        
        return json(['success' => true]);
    }
    
    /**
     * 获取键盘事件记录
     *
     * @param int $limit 限制数量
     * @return \think\Response
     */
    public function getEvents($limit = 100)
    {
        // 获取键盘事件记录
        $events = Settings::get('keyboard_events', []);
        
        // 按时间倒序排序
        usort($events, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });
        
        // 限制数量
        $events = array_slice($events, 0, $limit);
        
        return json(['events' => $events]);
    }
    
    /**
     * 清空键盘事件记录
     *
     * @return \think\Response
     */
    public function clearEvents()
    {
        // 清空键盘事件记录
        Settings::set('keyboard_events', []);
        
        Notification::send('键盘事件记录已清空', '所有键盘事件记录已删除');
        
        return json(['success' => true]);
    }
    
    /**
     * 记录按键事件
     *
     * @param array $event 按键事件数据
     * @return void
     */
    protected function logKeyEvent($event)
    {
        // 获取现有事件记录
        $events = Settings::get('keyboard_events', []);
        
        // 添加新事件
        $events[] = [
            'key' => $event['key'],
            'code' => $event['code'],
            'modifiers' => $event['modifiers'],
            'type' => $event['type'],
            'timestamp' => time(),
            'formatted_time' => date('Y-m-d H:i:s'),
        ];
        
        // 限制记录数量，最多保存 1000 条
        if (count($events) > 1000) {
            $events = array_slice($events, -1000);
        }
        
        // 保存事件记录
        Settings::set('keyboard_events', $events);
    }
}
```

### 自动化工具

```php
use Native\ThinkPHP\Facades\Keyboard;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Process;

class AutomationController
{
    /**
     * 执行自动化任务
     *
     * @param string $task 任务名称
     * @return \think\Response
     */
    public function execute($task)
    {
        switch ($task) {
            case 'type_date':
                return $this->typeCurrentDate();
            case 'type_email':
                return $this->typeEmail();
            case 'type_signature':
                return $this->typeSignature();
            case 'open_applications':
                return $this->openApplications();
            default:
                return json(['success' => false, 'message' => '未知任务']);
        }
    }
    
    /**
     * 输入当前日期
     *
     * @return \think\Response
     */
    protected function typeCurrentDate()
    {
        // 获取当前日期
        $date = date('Y-m-d');
        
        // 模拟键盘输入
        $success = Keyboard::sendText($date);
        
        if ($success) {
            Notification::send('自动化任务', '当前日期已输入');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '日期输入失败']);
        }
    }
    
    /**
     * 输入电子邮件地址
     *
     * @return \think\Response
     */
    protected function typeEmail()
    {
        // 电子邮件地址
        $email = 'example@example.com';
        
        // 模拟键盘输入
        $success = Keyboard::sendText($email);
        
        if ($success) {
            Notification::send('自动化任务', '电子邮件地址已输入');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '电子邮件地址输入失败']);
        }
    }
    
    /**
     * 输入签名
     *
     * @return \think\Response
     */
    protected function typeSignature()
    {
        // 签名
        $signature = "此致\n敬礼\n张三";
        
        // 模拟键盘输入
        $success = Keyboard::sendText($signature);
        
        if ($success) {
            Notification::send('自动化任务', '签名已输入');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '签名输入失败']);
        }
    }
    
    /**
     * 打开应用程序
     *
     * @return \think\Response
     */
    protected function openApplications()
    {
        // 打开记事本
        Process::run('notepad.exe');
        
        // 等待 1 秒
        sleep(1);
        
        // 打开计算器
        Process::run('calc.exe');
        
        Notification::send('自动化任务', '应用程序已打开');
        
        return json(['success' => true]);
    }
}
```

## 最佳实践

1. **错误处理**：始终检查快捷键注册和其他操作的返回值，并妥善处理错误情况。

2. **冲突检测**：在注册快捷键之前，检查快捷键是否已被注册，避免冲突。

3. **用户体验**：提供友好的用户界面，显示已注册的快捷键和操作结果。

4. **资源管理**：在不需要时注销快捷键和移除事件监听器，避免资源泄漏。

5. **安全性**：谨慎使用模拟键盘输入功能，避免输入敏感信息。

6. **性能优化**：避免在键盘事件处理函数中执行耗时操作，以免影响用户体验。

7. **平台兼容性**：考虑不同操作系统之间的键盘布局和快捷键差异。

## 故障排除

### 快捷键注册失败

- 确保快捷键组合格式正确
- 检查快捷键是否已被其他应用程序注册
- 尝试使用不同的快捷键组合
- 检查应用程序是否有足够的权限

### 模拟键盘输入失败

- 确保目标窗口处于活动状态
- 检查应用程序是否有足够的权限
- 尝试使用不同的输入方法
- 检查目标应用程序是否接受键盘输入

### 键盘事件监听不工作

- 确保事件类型正确
- 检查监听器是否正确注册
- 尝试使用不同的事件类型
- 检查应用程序是否有足够的权限
