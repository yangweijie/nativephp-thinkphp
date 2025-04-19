# 设备管理器示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的设备管理功能创建一个设备管理器应用。

## 功能

- 扫描和管理蓝牙设备
- 扫描和管理 USB 设备
- 连接和配对设备
- 发送和接收数据
- 设备信息显示
- 设备历史记录

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Bluetooth.php` - 蓝牙控制器
- `app/controller/Usb.php` - USB 控制器
- `app/model/Device.php` - 设备模型
- `app/service/DeviceService.php` - 设备服务
- `view/index/index.html` - 主页面
- `view/bluetooth/index.html` - 蓝牙页面
- `view/usb/index.html` - USB 页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use app\model\Device;
use app\service\DeviceService;
use Native\ThinkPHP\Facades\Device as DeviceFacade;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Logger;

class Bluetooth extends BaseController
{
    protected $deviceService;
    
    public function __construct(DeviceService $deviceService)
    {
        $this->deviceService = $deviceService;
    }
    
    public function index()
    {
        $devices = Device::where('type', 'bluetooth')->select();
        
        return view('bluetooth/index', [
            'devices' => $devices,
        ]);
    }
    
    public function scan()
    {
        // 扫描蓝牙设备
        $result = DeviceFacade::scanBluetoothDevices([
            'timeout' => 10000, // 10 秒
        ]);
        
        if ($result) {
            // 获取扫描结果
            $devices = DeviceFacade::getBluetoothDevices();
            
            // 保存设备到数据库
            foreach ($devices as $device) {
                $this->deviceService->saveBluetoothDevice($device);
            }
            
            Notification::send('扫描完成', '发现 ' . count($devices) . ' 个蓝牙设备');
            Logger::info('蓝牙设备扫描完成', ['count' => count($devices)]);
            
            return json(['success' => true, 'devices' => $devices]);
        } else {
            Logger::error('蓝牙设备扫描失败');
            return json(['success' => false, 'message' => '扫描失败']);
        }
    }
    
    public function connect()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 连接蓝牙设备
        $result = DeviceFacade::connectBluetoothDevice($deviceId);
        
        if ($result) {
            // 更新设备状态
            $this->deviceService->updateBluetoothDeviceStatus($deviceId, 'connected');
            
            Notification::send('连接成功', '已成功连接到蓝牙设备');
            Logger::info('蓝牙设备连接成功', ['device_id' => $deviceId]);
            
            return json(['success' => true]);
        } else {
            Logger::error('蓝牙设备连接失败', ['device_id' => $deviceId]);
            return json(['success' => false, 'message' => '连接失败']);
        }
    }
    
    public function disconnect()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 断开蓝牙设备
        $result = DeviceFacade::disconnectBluetoothDevice($deviceId);
        
        if ($result) {
            // 更新设备状态
            $this->deviceService->updateBluetoothDeviceStatus($deviceId, 'disconnected');
            
            Notification::send('断开连接', '已断开与蓝牙设备的连接');
            Logger::info('蓝牙设备断开连接', ['device_id' => $deviceId]);
            
            return json(['success' => true]);
        } else {
            Logger::error('蓝牙设备断开连接失败', ['device_id' => $deviceId]);
            return json(['success' => false, 'message' => '断开连接失败']);
        }
    }
    
    public function pair()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 配对蓝牙设备
        $result = DeviceFacade::pairBluetoothDevice($deviceId);
        
        if ($result) {
            // 更新设备状态
            $this->deviceService->updateBluetoothDeviceStatus($deviceId, 'paired');
            
            Notification::send('配对成功', '已成功配对蓝牙设备');
            Logger::info('蓝牙设备配对成功', ['device_id' => $deviceId]);
            
            return json(['success' => true]);
        } else {
            Logger::error('蓝牙设备配对失败', ['device_id' => $deviceId]);
            return json(['success' => false, 'message' => '配对失败']);
        }
    }
    
    public function unpair()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 取消配对蓝牙设备
        $result = DeviceFacade::unpairBluetoothDevice($deviceId);
        
        if ($result) {
            // 更新设备状态
            $this->deviceService->updateBluetoothDeviceStatus($deviceId, 'unpaired');
            
            Notification::send('取消配对', '已取消与蓝牙设备的配对');
            Logger::info('蓝牙设备取消配对', ['device_id' => $deviceId]);
            
            return json(['success' => true]);
        } else {
            Logger::error('蓝牙设备取消配对失败', ['device_id' => $deviceId]);
            return json(['success' => false, 'message' => '取消配对失败']);
        }
    }
    
    public function sendData()
    {
        $deviceId = input('device_id');
        $data = input('data');
        
        if (empty($deviceId) || empty($data)) {
            return json(['success' => false, 'message' => '设备ID和数据不能为空']);
        }
        
        // 发送数据到蓝牙设备
        $result = DeviceFacade::sendDataToBluetoothDevice($deviceId, $data);
        
        if ($result) {
            // 记录发送的数据
            $this->deviceService->logDeviceData($deviceId, 'sent', $data);
            
            Logger::info('数据发送成功', ['device_id' => $deviceId, 'data' => $data]);
            
            return json(['success' => true]);
        } else {
            Logger::error('数据发送失败', ['device_id' => $deviceId, 'data' => $data]);
            return json(['success' => false, 'message' => '数据发送失败']);
        }
    }
    
    public function receiveData()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 从蓝牙设备接收数据
        $data = DeviceFacade::receiveDataFromBluetoothDevice($deviceId);
        
        if ($data !== null) {
            // 记录接收的数据
            $this->deviceService->logDeviceData($deviceId, 'received', $data);
            
            Logger::info('数据接收成功', ['device_id' => $deviceId, 'data' => $data]);
            
            return json(['success' => true, 'data' => $data]);
        } else {
            Logger::error('数据接收失败', ['device_id' => $deviceId]);
            return json(['success' => false, 'message' => '数据接收失败']);
        }
    }
    
    public function getDeviceInfo()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 获取蓝牙设备信息
        $info = DeviceFacade::getDeviceInfo($deviceId, 'bluetooth');
        
        if ($info) {
            return json(['success' => true, 'info' => $info]);
        } else {
            return json(['success' => false, 'message' => '获取设备信息失败']);
        }
    }
    
    public function getDeviceHistory()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 获取设备历史记录
        $history = $this->deviceService->getDeviceHistory($deviceId);
        
        return json(['success' => true, 'history' => $history]);
    }
    
    public function clearDeviceHistory()
    {
        $deviceId = input('device_id');
        
        if (empty($deviceId)) {
            return json(['success' => false, 'message' => '设备ID不能为空']);
        }
        
        // 清除设备历史记录
        $result = $this->deviceService->clearDeviceHistory($deviceId);
        
        if ($result) {
            Notification::send('历史记录已清空', '设备历史记录已成功清空');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '清除历史记录失败']);
        }
    }
}
```

### 服务

```php
<?php

namespace app\service;

use app\model\Device;
use app\model\DeviceData;
use Native\ThinkPHP\Facades\Logger;

class DeviceService
{
    /**
     * 保存蓝牙设备
     *
     * @param array $deviceInfo
     * @return \app\model\Device
     */
    public function saveBluetoothDevice(array $deviceInfo)
    {
        $device = Device::where('device_id', $deviceInfo['id'])
            ->where('type', 'bluetooth')
            ->find();
        
        if (!$device) {
            $device = new Device;
            $device->device_id = $deviceInfo['id'];
            $device->type = 'bluetooth';
            $device->name = $deviceInfo['name'] ?? 'Unknown Device';
            $device->created_at = date('Y-m-d H:i:s');
        }
        
        $device->status = $deviceInfo['connected'] ? 'connected' : 'disconnected';
        $device->paired = $deviceInfo['paired'] ? 1 : 0;
        $device->info = json_encode($deviceInfo);
        $device->updated_at = date('Y-m-d H:i:s');
        $device->save();
        
        return $device;
    }
    
    /**
     * 保存 USB 设备
     *
     * @param array $deviceInfo
     * @return \app\model\Device
     */
    public function saveUsbDevice(array $deviceInfo)
    {
        $device = Device::where('device_id', $deviceInfo['id'])
            ->where('type', 'usb')
            ->find();
        
        if (!$device) {
            $device = new Device;
            $device->device_id = $deviceInfo['id'];
            $device->type = 'usb';
            $device->name = $deviceInfo['name'] ?? 'Unknown Device';
            $device->created_at = date('Y-m-d H:i:s');
        }
        
        $device->status = isset($deviceInfo['opened']) && $deviceInfo['opened'] ? 'opened' : 'closed';
        $device->info = json_encode($deviceInfo);
        $device->updated_at = date('Y-m-d H:i:s');
        $device->save();
        
        return $device;
    }
    
    /**
     * 更新蓝牙设备状态
     *
     * @param string $deviceId
     * @param string $status
     * @return bool
     */
    public function updateBluetoothDeviceStatus($deviceId, $status)
    {
        $device = Device::where('device_id', $deviceId)
            ->where('type', 'bluetooth')
            ->find();
        
        if (!$device) {
            return false;
        }
        
        $device->status = $status;
        
        if ($status === 'paired') {
            $device->paired = 1;
        } elseif ($status === 'unpaired') {
            $device->paired = 0;
        }
        
        $device->updated_at = date('Y-m-d H:i:s');
        
        return $device->save();
    }
    
    /**
     * 更新 USB 设备状态
     *
     * @param string $deviceId
     * @param string $status
     * @return bool
     */
    public function updateUsbDeviceStatus($deviceId, $status)
    {
        $device = Device::where('device_id', $deviceId)
            ->where('type', 'usb')
            ->find();
        
        if (!$device) {
            return false;
        }
        
        $device->status = $status;
        $device->updated_at = date('Y-m-d H:i:s');
        
        return $device->save();
    }
    
    /**
     * 记录设备数据
     *
     * @param string $deviceId
     * @param string $direction
     * @param string $data
     * @return \app\model\DeviceData
     */
    public function logDeviceData($deviceId, $direction, $data)
    {
        $device = Device::where('device_id', $deviceId)->find();
        
        if (!$device) {
            Logger::warning('记录设备数据失败：设备不存在', ['device_id' => $deviceId]);
            return null;
        }
        
        $deviceData = new DeviceData;
        $deviceData->device_id = $device->id;
        $deviceData->direction = $direction;
        $deviceData->data = $data;
        $deviceData->created_at = date('Y-m-d H:i:s');
        $deviceData->save();
        
        return $deviceData;
    }
    
    /**
     * 获取设备历史记录
     *
     * @param string $deviceId
     * @param int $limit
     * @return array
     */
    public function getDeviceHistory($deviceId, $limit = 100)
    {
        $device = Device::where('device_id', $deviceId)->find();
        
        if (!$device) {
            return [];
        }
        
        $data = DeviceData::where('device_id', $device->id)
            ->order('created_at', 'desc')
            ->limit($limit)
            ->select();
        
        return $data;
    }
    
    /**
     * 清除设备历史记录
     *
     * @param string $deviceId
     * @return bool
     */
    public function clearDeviceHistory($deviceId)
    {
        $device = Device::where('device_id', $deviceId)->find();
        
        if (!$device) {
            return false;
        }
        
        return DeviceData::where('device_id', $device->id)->delete();
    }
    
    /**
     * 获取设备统计信息
     *
     * @return array
     */
    public function getDeviceStatistics()
    {
        $bluetoothCount = Device::where('type', 'bluetooth')->count();
        $usbCount = Device::where('type', 'usb')->count();
        $connectedCount = Device::where('status', 'connected')->whereOr('status', 'opened')->count();
        $pairedCount = Device::where('paired', 1)->count();
        
        return [
            'bluetooth' => $bluetoothCount,
            'usb' => $usbCount,
            'connected' => $connectedCount,
            'paired' => $pairedCount,
        ];
    }
}
```

### 视图

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>蓝牙设备管理</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>设备管理器</h2>
            </div>
            
            <div class="nav-menu">
                <ul>
                    <li><a href="/index/index">主页</a></li>
                    <li class="active"><a href="/bluetooth/index">蓝牙设备</a></li>
                    <li><a href="/usb/index">USB 设备</a></li>
                </ul>
            </div>
            
            <div class="actions">
                <button onclick="scanDevices()">扫描设备</button>
            </div>
        </div>
        
        <div class="main-content">
            <div class="header">
                <h1>蓝牙设备</h1>
            </div>
            
            <div class="devices">
                {if empty($devices)}
                <p>暂无设备</p>
                {else}
                <div class="device-list">
                    {volist name="devices" id="device"}
                    <div class="device-item" data-id="{$device.device_id}">
                        <div class="device-header">
                            <h3>{$device.name}</h3>
                            <span class="device-status {$device.status}">{$device.status}</span>
                        </div>
                        <div class="device-info">
                            <p><strong>ID:</strong> {$device.device_id}</p>
                            <p><strong>配对状态:</strong> {$device.paired ? '已配对' : '未配对'}</p>
                            <p><strong>最后更新:</strong> {$device.updated_at}</p>
                        </div>
                        <div class="device-actions">
                            {if $device.status == 'connected'}
                            <button onclick="disconnectDevice('{$device.device_id}')">断开连接</button>
                            {else}
                            <button onclick="connectDevice('{$device.device_id}')">连接</button>
                            {/if}
                            
                            {if $device.paired}
                            <button onclick="unpairDevice('{$device.device_id}')">取消配对</button>
                            {else}
                            <button onclick="pairDevice('{$device.device_id}')">配对</button>
                            {/if}
                            
                            <button onclick="showDeviceInfo('{$device.device_id}')">详细信息</button>
                            <button onclick="showDeviceHistory('{$device.device_id}')">历史记录</button>
                        </div>
                        
                        {if $device.status == 'connected'}
                        <div class="device-communication">
                            <div class="send-data">
                                <h4>发送数据</h4>
                                <textarea id="sendData-{$device.device_id}" placeholder="输入要发送的数据..."></textarea>
                                <button onclick="sendData('{$device.device_id}')">发送</button>
                            </div>
                            <div class="receive-data">
                                <h4>接收数据</h4>
                                <div id="receiveData-{$device.device_id}" class="data-display"></div>
                                <button onclick="receiveData('{$device.device_id}')">接收</button>
                            </div>
                        </div>
                        {/if}
                    </div>
                    {/volist}
                </div>
                {/if}
            </div>
        </div>
    </div>
    
    <div id="deviceInfoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>设备详细信息</h2>
            <div id="deviceInfoContent"></div>
        </div>
    </div>
    
    <div id="deviceHistoryModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>设备历史记录</h2>
            <div id="deviceHistoryContent"></div>
            <button id="clearHistoryBtn">清除历史记录</button>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```
