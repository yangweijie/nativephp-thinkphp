# 设备管理

NativePHP for ThinkPHP 提供了设备管理功能，允许你的桌面应用程序与蓝牙设备和 USB 设备进行交互。本文档将介绍如何使用这些功能。

## 基本概念

设备管理功能允许你的应用程序扫描、连接、配对和与蓝牙设备和 USB 设备进行通信。这些功能可以用于创建设备管理应用、物联网应用、硬件控制应用等。

## 使用 Device Facade

NativePHP for ThinkPHP 提供了 `Device` Facade，用于与蓝牙设备和 USB 设备进行交互。

### 蓝牙设备管理

#### 获取蓝牙设备列表

```php
use Native\ThinkPHP\Facades\Device;

// 获取蓝牙设备列表
$devices = Device::getBluetoothDevices();

foreach ($devices as $device) {
    echo "设备 ID：{$device['id']}，名称：{$device['name']}，已连接：" . ($device['connected'] ? '是' : '否') . "，已配对：" . ($device['paired'] ? '是' : '否');
}
```

#### 扫描蓝牙设备

```php
// 扫描蓝牙设备
$success = Device::scanBluetoothDevices([
    'timeout' => 10000, // 10 秒
    'filters' => [
        'services' => ['0000180d-0000-1000-8000-00805f9b34fb'], // 心率服务
        'name' => 'MyDevice',
    ],
]);

if ($success) {
    // 扫描成功，获取扫描结果
    $devices = Device::getBluetoothDevices();
    
    echo "发现 " . count($devices) . " 个蓝牙设备";
} else {
    // 扫描失败
    echo "蓝牙设备扫描失败";
}
```

#### 连接蓝牙设备

```php
// 连接蓝牙设备
$success = Device::connectBluetoothDevice('00:11:22:33:44:55');

if ($success) {
    // 连接成功
    echo "蓝牙设备连接成功";
} else {
    // 连接失败
    echo "蓝牙设备连接失败";
}
```

#### 断开蓝牙设备

```php
// 断开蓝牙设备
$success = Device::disconnectBluetoothDevice('00:11:22:33:44:55');

if ($success) {
    // 断开成功
    echo "蓝牙设备断开成功";
} else {
    // 断开失败
    echo "蓝牙设备断开失败";
}
```

#### 配对蓝牙设备

```php
// 配对蓝牙设备
$success = Device::pairBluetoothDevice('00:11:22:33:44:55');

if ($success) {
    // 配对成功
    echo "蓝牙设备配对成功";
} else {
    // 配对失败
    echo "蓝牙设备配对失败";
}
```

#### 取消配对蓝牙设备

```php
// 取消配对蓝牙设备
$success = Device::unpairBluetoothDevice('00:11:22:33:44:55');

if ($success) {
    // 取消配对成功
    echo "蓝牙设备取消配对成功";
} else {
    // 取消配对失败
    echo "蓝牙设备取消配对失败";
}
```

#### 向蓝牙设备发送数据

```php
// 向蓝牙设备发送数据
$success = Device::sendDataToBluetoothDevice('00:11:22:33:44:55', '测试数据');

if ($success) {
    // 发送成功
    echo "数据发送成功";
} else {
    // 发送失败
    echo "数据发送失败";
}
```

#### 从蓝牙设备接收数据

```php
// 从蓝牙设备接收数据
$data = Device::receiveDataFromBluetoothDevice('00:11:22:33:44:55');

if ($data !== null) {
    // 接收成功
    echo "接收到数据：{$data}";
} else {
    // 接收失败
    echo "数据接收失败";
}
```

### USB 设备管理

#### 获取 USB 设备列表

```php
// 获取 USB 设备列表
$devices = Device::getUsbDevices();

foreach ($devices as $device) {
    echo "设备 ID：{$device['id']}，名称：{$device['name']}，已打开：" . (isset($device['opened']) && $device['opened'] ? '是' : '否');
}
```

#### 打开 USB 设备

```php
// 打开 USB 设备
$success = Device::openUsbDevice('device-id');

if ($success) {
    // 打开成功
    echo "USB 设备打开成功";
} else {
    // 打开失败
    echo "USB 设备打开失败";
}
```

#### 关闭 USB 设备

```php
// 关闭 USB 设备
$success = Device::closeUsbDevice('device-id');

if ($success) {
    // 关闭成功
    echo "USB 设备关闭成功";
} else {
    // 关闭失败
    echo "USB 设备关闭失败";
}
```

#### 向 USB 设备发送数据

```php
// 向 USB 设备发送数据
$success = Device::sendDataToUsbDevice('device-id', '测试数据');

if ($success) {
    // 发送成功
    echo "数据发送成功";
} else {
    // 发送失败
    echo "数据发送失败";
}
```

#### 从 USB 设备接收数据

```php
// 从 USB 设备接收数据
$data = Device::receiveDataFromUsbDevice('device-id');

if ($data !== null) {
    // 接收成功
    echo "接收到数据：{$data}";
} else {
    // 接收失败
    echo "数据接收失败";
}
```

### 设备信息

```php
// 获取蓝牙设备信息
$info = Device::getDeviceInfo('00:11:22:33:44:55', 'bluetooth');

if ($info !== null) {
    // 获取成功
    echo "设备 ID：{$info['id']}，名称：{$info['name']}，已连接：" . ($info['connected'] ? '是' : '否') . "，已配对：" . ($info['paired'] ? '是' : '否');
} else {
    // 获取失败
    echo "设备信息获取失败";
}

// 获取 USB 设备信息
$info = Device::getDeviceInfo('device-id', 'usb');

if ($info !== null) {
    // 获取成功
    echo "设备 ID：{$info['id']}，名称：{$info['name']}，已打开：" . (isset($info['opened']) && $info['opened'] ? '是' : '否');
} else {
    // 获取失败
    echo "设备信息获取失败";
}
```

## 设备数据格式

### 蓝牙设备

```php
[
    'id' => '00:11:22:33:44:55', // 设备 ID
    'name' => 'Bluetooth Device', // 设备名称
    'connected' => false, // 是否已连接
    'paired' => false, // 是否已配对
    'services' => [ // 设备支持的服务（可选）
        '0000180d-0000-1000-8000-00805f9b34fb', // 心率服务
        '0000180f-0000-1000-8000-00805f9b34fb', // 电池服务
    ],
    'rssi' => -60, // 信号强度（可选）
    'manufacturer' => 'Manufacturer Name', // 制造商名称（可选）
]
```

### USB 设备

```php
[
    'id' => 'device-id', // 设备 ID
    'name' => 'USB Device', // 设备名称
    'opened' => false, // 是否已打开
    'vendorId' => 0x1234, // 厂商 ID（可选）
    'productId' => 0x5678, // 产品 ID（可选）
    'serialNumber' => '123456789', // 序列号（可选）
    'manufacturer' => 'Manufacturer Name', // 制造商名称（可选）
    'product' => 'Product Name', // 产品名称（可选）
]
```

## 实际应用场景

### 蓝牙设备管理器

```php
use Native\ThinkPHP\Facades\Device;
use Native\ThinkPHP\Facades\Notification;

class BluetoothController
{
    public function index()
    {
        // 获取蓝牙设备列表
        $devices = Device::getBluetoothDevices();
        
        return view('bluetooth/index', [
            'devices' => $devices,
        ]);
    }
    
    public function scan()
    {
        // 扫描蓝牙设备
        $success = Device::scanBluetoothDevices([
            'timeout' => 10000, // 10 秒
        ]);
        
        if ($success) {
            // 获取扫描结果
            $devices = Device::getBluetoothDevices();
            
            Notification::send('扫描完成', '发现 ' . count($devices) . ' 个蓝牙设备');
            
            return json(['success' => true, 'devices' => $devices]);
        } else {
            return json(['success' => false, 'message' => '扫描失败']);
        }
    }
    
    public function connect()
    {
        $deviceId = input('device_id');
        
        // 连接蓝牙设备
        $success = Device::connectBluetoothDevice($deviceId);
        
        if ($success) {
            Notification::send('连接成功', '蓝牙设备已连接');
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '连接失败']);
        }
    }
    
    public function disconnect()
    {
        $deviceId = input('device_id');
        
        // 断开蓝牙设备
        $success = Device::disconnectBluetoothDevice($deviceId);
        
        if ($success) {
            Notification::send('断开连接', '蓝牙设备已断开连接');
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '断开连接失败']);
        }
    }
    
    public function sendData()
    {
        $deviceId = input('device_id');
        $data = input('data');
        
        // 向蓝牙设备发送数据
        $success = Device::sendDataToBluetoothDevice($deviceId, $data);
        
        return json(['success' => $success]);
    }
    
    public function receiveData()
    {
        $deviceId = input('device_id');
        
        // 从蓝牙设备接收数据
        $data = Device::receiveDataFromBluetoothDevice($deviceId);
        
        return json(['success' => $data !== null, 'data' => $data]);
    }
}
```

### USB 设备管理器

```php
use Native\ThinkPHP\Facades\Device;
use Native\ThinkPHP\Facades\Notification;

class UsbController
{
    public function index()
    {
        // 获取 USB 设备列表
        $devices = Device::getUsbDevices();
        
        return view('usb/index', [
            'devices' => $devices,
        ]);
    }
    
    public function open()
    {
        $deviceId = input('device_id');
        
        // 打开 USB 设备
        $success = Device::openUsbDevice($deviceId);
        
        if ($success) {
            Notification::send('打开成功', 'USB 设备已打开');
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '打开失败']);
        }
    }
    
    public function close()
    {
        $deviceId = input('device_id');
        
        // 关闭 USB 设备
        $success = Device::closeUsbDevice($deviceId);
        
        if ($success) {
            Notification::send('关闭成功', 'USB 设备已关闭');
            
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '关闭失败']);
        }
    }
    
    public function sendData()
    {
        $deviceId = input('device_id');
        $data = input('data');
        
        // 向 USB 设备发送数据
        $success = Device::sendDataToUsbDevice($deviceId, $data);
        
        return json(['success' => $success]);
    }
    
    public function receiveData()
    {
        $deviceId = input('device_id');
        
        // 从 USB 设备接收数据
        $data = Device::receiveDataFromUsbDevice($deviceId);
        
        return json(['success' => $data !== null, 'data' => $data]);
    }
}
```

## 最佳实践

1. **错误处理**：始终检查设备操作的返回值，并妥善处理错误情况。

2. **设备连接状态**：在发送或接收数据之前，始终检查设备是否已连接或已打开。

3. **超时设置**：在扫描蓝牙设备时，设置合理的超时时间，避免用户等待过长时间。

4. **用户界面反馈**：在执行长时间操作（如扫描设备）时，提供适当的用户界面反馈，如进度条或加载指示器。

5. **设备断开处理**：妥善处理设备断开连接的情况，例如在设备断开连接时清理相关资源。

6. **权限检查**：在使用蓝牙或 USB 功能之前，确保应用程序有相应的权限。

7. **数据缓冲**：在发送或接收大量数据时，考虑使用数据缓冲和分块传输，以避免内存问题。

## 故障排除

### 蓝牙设备扫描失败

- 确保蓝牙适配器已启用
- 检查蓝牙权限
- 尝试重启蓝牙适配器
- 检查扫描过滤器是否正确

### 蓝牙设备连接失败

- 确保设备在范围内
- 检查设备是否已与其他设备配对
- 尝试先配对设备，然后再连接
- 检查设备是否支持所需的蓝牙配置文件

### USB 设备打开失败

- 确保设备已正确插入
- 检查设备驱动程序是否已安装
- 检查设备是否已被其他应用程序打开
- 尝试重新插拔设备

### 数据发送或接收失败

- 确保设备已连接或已打开
- 检查数据格式是否正确
- 尝试使用较小的数据块
- 检查设备是否支持所需的数据传输协议
