# 系统信息

NativePHP for ThinkPHP 提供了系统信息功能，允许你的桌面应用程序获取和使用系统信息，以及执行系统操作。本文档将介绍如何使用这些功能。

## 基本概念

系统信息功能允许你的应用程序获取操作系统信息、硬件信息、用户信息等，以及执行系统操作，如打开文件、显示文件在文件管理器中、移动文件到回收站等。这些功能可以用于创建系统工具、系统监控应用、文件管理应用等。

## 使用 System Facade

NativePHP for ThinkPHP 提供了 `System` Facade，用于获取系统信息和执行系统操作。

```php
use Native\ThinkPHP\Facades\System;
```

### 获取系统信息

```php
// 获取操作系统类型
$os = System::getOS();

// 获取操作系统版本
$osVersion = System::getOSVersion();

// 获取 CPU 架构
$arch = System::getArch();

// 获取主机名
$hostname = System::getHostname();

// 获取用户主目录
$homePath = System::getHomePath();

// 获取临时目录
$tempPath = System::getTempPath();

// 获取应用数据目录
$appDataPath = System::getAppDataPath();
```

### 获取硬件信息

```php
// 获取系统内存信息
$memoryInfo = System::getMemoryInfo();

// 获取系统 CPU 信息
$cpuInfo = System::getCPUInfo();

// 获取系统网络接口信息
$networkInterfaces = System::getNetworkInterfaces();

// 获取系统显示器信息
$displays = System::getDisplays();

// 获取系统电池信息
$batteryInfo = System::getBatteryInfo();
```

### 获取其他信息

```php
// 获取系统语言
$language = System::getLanguage();
```

### 执行系统操作

```php
// 打开外部 URL
$success = System::openExternal('https://www.example.com');

// 打开外部 URL（带选项）
$success = System::openExternal('https://www.example.com', [
    'activate' => true, // 是否激活窗口
    'workingDirectory' => '/path/to/directory', // 工作目录
]);

// 打开文件或目录
$success = System::openPath('/path/to/file/or/directory');

// 显示文件或目录在文件管理器中
$success = System::showItemInFolder('/path/to/file/or/directory');

// 移动文件到回收站
$success = System::moveItemToTrash('/path/to/file/or/directory');

// 播放系统提示音
System::beep();

// 播放系统提示音（指定类型）
System::beep('info'); // 信息提示音
System::beep('warning'); // 警告提示音
System::beep('error'); // 错误提示音
```

### 系统电源操作

```php
// 设置系统休眠状态
$success = System::sleep();

// 设置系统锁屏状态
$success = System::lock();

// 设置系统注销状态
$success = System::logout();

// 重启系统
$success = System::restart();

// 关闭系统
$success = System::shutdown();
```

## 系统信息格式

### 操作系统信息

```php
// 操作系统类型
$os = System::getOS();
// 返回值：'windows'、'macos'、'linux' 等

// 操作系统版本
$osVersion = System::getOSVersion();
// 返回值：'10.0.19042'、'11.2.3' 等

// CPU 架构
$arch = System::getArch();
// 返回值：'x64'、'arm64' 等
```

### 内存信息

```php
$memoryInfo = System::getMemoryInfo();
// 返回值：
[
    'total' => 16000000000, // 总内存（字节）
    'free' => 8000000000, // 空闲内存（字节）
]
```

### CPU 信息

```php
$cpuInfo = System::getCPUInfo();
// 返回值：
[
    'model' => 'Intel(R) Core(TM) i7-9750H CPU @ 2.60GHz', // CPU 型号
    'speed' => 2600, // CPU 频率（MHz）
    'cores' => 12, // CPU 核心数
]
```

### 网络接口信息

```php
$networkInterfaces = System::getNetworkInterfaces();
// 返回值：
[
    'en0' => [
        [
            'address' => '192.168.1.100', // IP 地址
            'netmask' => '255.255.255.0', // 子网掩码
            'family' => 'IPv4', // 地址族
            'mac' => '00:00:00:00:00:00', // MAC 地址
            'internal' => false, // 是否内部接口
            'cidr' => '192.168.1.100/24', // CIDR 表示法
        ],
        [
            'address' => 'fe80::1',
            'netmask' => 'ffff:ffff:ffff:ffff::',
            'family' => 'IPv6',
            'mac' => '00:00:00:00:00:00',
            'internal' => false,
            'cidr' => 'fe80::1/64',
        ],
    ],
    'lo0' => [
        [
            'address' => '127.0.0.1',
            'netmask' => '255.0.0.0',
            'family' => 'IPv4',
            'mac' => '00:00:00:00:00:00',
            'internal' => true,
            'cidr' => '127.0.0.1/8',
        ],
    ],
]
```

### 显示器信息

```php
$displays = System::getDisplays();
// 返回值：
[
    [
        'id' => 0, // 显示器 ID
        'bounds' => [ // 显示器边界
            'x' => 0, // 左上角 X 坐标
            'y' => 0, // 左上角 Y 坐标
            'width' => 1920, // 宽度
            'height' => 1080, // 高度
        ],
        'workArea' => [ // 工作区域
            'x' => 0,
            'y' => 0,
            'width' => 1920,
            'height' => 1040,
        ],
        'scaleFactor' => 1.0, // 缩放因子
        'rotation' => 0, // 旋转角度
        'internal' => true, // 是否内置显示器
        'primary' => true, // 是否主显示器
    ],
    [
        'id' => 1,
        'bounds' => [
            'x' => 1920,
            'y' => 0,
            'width' => 1920,
            'height' => 1080,
        ],
        'workArea' => [
            'x' => 1920,
            'y' => 0,
            'width' => 1920,
            'height' => 1040,
        ],
        'scaleFactor' => 1.0,
        'rotation' => 0,
        'internal' => false,
        'primary' => false,
    ],
]
```

### 电池信息

```php
$batteryInfo = System::getBatteryInfo();
// 返回值：
[
    'level' => 0.8, // 电池电量（0.0 到 1.0）
    'charging' => true, // 是否正在充电
]
```

## 实际应用场景

### 系统信息应用

```php
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Notification;

class SystemInfoController
{
    /**
     * 显示系统信息
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取系统信息
        $os = System::getOS();
        $osVersion = System::getOSVersion();
        $arch = System::getArch();
        $hostname = System::getHostname();
        $memoryInfo = System::getMemoryInfo();
        $cpuInfo = System::getCPUInfo();
        $batteryInfo = System::getBatteryInfo();
        
        // 格式化内存信息
        $totalMemory = $this->formatBytes($memoryInfo['total']);
        $freeMemory = $this->formatBytes($memoryInfo['free']);
        $usedMemory = $this->formatBytes($memoryInfo['total'] - $memoryInfo['free']);
        $memoryUsage = round(($memoryInfo['total'] - $memoryInfo['free']) / $memoryInfo['total'] * 100, 2);
        
        // 格式化电池信息
        $batteryLevel = round($batteryInfo['level'] * 100, 2);
        $batteryStatus = $batteryInfo['charging'] ? '充电中' : '放电中';
        
        return view('system/index', [
            'os' => $os,
            'osVersion' => $osVersion,
            'arch' => $arch,
            'hostname' => $hostname,
            'totalMemory' => $totalMemory,
            'freeMemory' => $freeMemory,
            'usedMemory' => $usedMemory,
            'memoryUsage' => $memoryUsage,
            'cpuModel' => $cpuInfo['model'],
            'cpuSpeed' => $cpuInfo['speed'],
            'cpuCores' => $cpuInfo['cores'],
            'batteryLevel' => $batteryLevel,
            'batteryStatus' => $batteryStatus,
        ]);
    }
    
    /**
     * 格式化字节数
     *
     * @param int $bytes 字节数
     * @param int $precision 精度
     * @return string
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
    
    /**
     * 打开系统文件夹
     *
     * @param string $type 文件夹类型
     * @return \think\Response
     */
    public function openFolder($type)
    {
        $path = '';
        
        switch ($type) {
            case 'home':
                $path = System::getHomePath();
                break;
            case 'temp':
                $path = System::getTempPath();
                break;
            case 'appdata':
                $path = System::getAppDataPath();
                break;
            default:
                return json(['success' => false, 'message' => '未知的文件夹类型']);
        }
        
        $success = System::openPath($path);
        
        if ($success) {
            return json(['success' => true, 'message' => '文件夹已打开']);
        } else {
            return json(['success' => false, 'message' => '文件夹打开失败']);
        }
    }
    
    /**
     * 执行系统操作
     *
     * @param string $action 操作类型
     * @return \think\Response
     */
    public function executeAction($action)
    {
        $success = false;
        $message = '';
        
        switch ($action) {
            case 'sleep':
                $success = System::sleep();
                $message = '系统已进入休眠状态';
                break;
            case 'lock':
                $success = System::lock();
                $message = '系统已锁定';
                break;
            case 'logout':
                $success = System::logout();
                $message = '系统已注销';
                break;
            case 'restart':
                $success = System::restart();
                $message = '系统正在重启';
                break;
            case 'shutdown':
                $success = System::shutdown();
                $message = '系统正在关闭';
                break;
            default:
                return json(['success' => false, 'message' => '未知的操作类型']);
        }
        
        if ($success) {
            return json(['success' => true, 'message' => $message]);
        } else {
            return json(['success' => false, 'message' => '操作执行失败']);
        }
    }
}
```

### 文件管理应用

```php
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Dialog;

class FileManagerController
{
    /**
     * 显示文件列表
     *
     * @param string $path 路径
     * @return \think\Response
     */
    public function index($path = null)
    {
        if ($path === null) {
            $path = System::getHomePath();
        }
        
        // 获取文件列表
        $files = FileSystem::listDirectory($path);
        
        // 获取父目录
        $parentPath = dirname($path);
        
        return view('file-manager/index', [
            'path' => $path,
            'parentPath' => $parentPath,
            'files' => $files,
        ]);
    }
    
    /**
     * 打开文件或目录
     *
     * @param string $path 路径
     * @return \think\Response
     */
    public function open($path)
    {
        $success = System::openPath($path);
        
        if ($success) {
            return json(['success' => true, 'message' => '文件或目录已打开']);
        } else {
            return json(['success' => false, 'message' => '文件或目录打开失败']);
        }
    }
    
    /**
     * 在文件管理器中显示文件
     *
     * @param string $path 路径
     * @return \think\Response
     */
    public function showInFolder($path)
    {
        $success = System::showItemInFolder($path);
        
        if ($success) {
            return json(['success' => true, 'message' => '文件已在文件管理器中显示']);
        } else {
            return json(['success' => false, 'message' => '文件显示失败']);
        }
    }
    
    /**
     * 移动文件到回收站
     *
     * @param string $path 路径
     * @return \think\Response
     */
    public function moveToTrash($path)
    {
        // 确认删除
        $confirm = Dialog::confirm('确认删除', '确定要将文件移动到回收站吗？', [
            'buttons' => ['取消', '确定'],
            'defaultId' => 1,
        ]);
        
        if (!$confirm) {
            return json(['success' => false, 'message' => '操作已取消']);
        }
        
        $success = System::moveItemToTrash($path);
        
        if ($success) {
            return json(['success' => true, 'message' => '文件已移动到回收站']);
        } else {
            return json(['success' => false, 'message' => '文件移动失败']);
        }
    }
}
```

## 最佳实践

1. **错误处理**：始终检查系统操作的返回值，并妥善处理错误情况。

2. **权限检查**：在执行系统操作之前，确保应用程序有足够的权限。

3. **用户确认**：在执行可能影响系统的操作（如重启、关机）之前，始终获取用户确认。

4. **平台兼容性**：注意不同操作系统之间的差异，确保代码在所有支持的平台上都能正常工作。

5. **资源监控**：定期监控系统资源使用情况，避免应用程序消耗过多资源。

6. **用户体验**：提供友好的用户界面，显示系统信息和操作结果。

7. **安全性**：避免执行可能危及系统安全的操作，如删除系统文件。

## 故障排除

### 系统操作失败

- 确保应用程序有足够的权限执行操作
- 检查操作是否受到系统策略限制
- 尝试以管理员身份运行应用程序
- 检查系统日志获取更多信息

### 系统信息不准确

- 确保使用最新版本的 NativePHP
- 检查系统是否支持获取特定信息
- 尝试使用其他方法获取信息
- 考虑使用第三方库获取更准确的信息

### 文件操作失败

- 确保文件路径正确
- 检查文件是否存在
- 确保应用程序有足够的权限操作文件
- 检查文件是否被其他程序占用
