# 地理位置服务

NativePHP for ThinkPHP 提供了地理位置服务功能，允许你的桌面应用程序获取和使用地理位置信息。本文档将介绍如何使用这些功能。

## 基本概念

地理位置服务功能允许你的应用程序获取用户的当前位置、监视位置变化、计算距离、进行地理编码和反向地理编码等。这些功能可以用于创建地图应用、导航应用、位置感知应用等。

## 配置

在使用地理位置服务功能之前，你可以在 `config/native.php` 文件中配置地理位置服务：

```php
return [
    // 其他配置...
    
    'geolocation' => [
        'enable_high_accuracy' => env('NATIVEPHP_GEOLOCATION_ENABLE_HIGH_ACCURACY', false),
        'timeout' => env('NATIVEPHP_GEOLOCATION_TIMEOUT', 5000),
        'maximum_age' => env('NATIVEPHP_GEOLOCATION_MAXIMUM_AGE', 0),
    ],
];
```

## 使用 Geolocation Facade

NativePHP for ThinkPHP 提供了 `Geolocation` Facade，用于获取和使用地理位置信息。

### 获取当前位置

```php
use Native\ThinkPHP\Facades\Geolocation;

// 获取当前位置
$position = Geolocation::getCurrentPosition([
    'enableHighAccuracy' => true,
    'timeout' => 5000,
    'maximumAge' => 0,
]);

if ($position) {
    // 位置获取成功
    $latitude = $position['coords']['latitude'];
    $longitude = $position['coords']['longitude'];
    $accuracy = $position['coords']['accuracy'];
    $timestamp = $position['timestamp'];
    
    echo "纬度：{$latitude}，经度：{$longitude}，精度：{$accuracy}米";
} else {
    // 位置获取失败
    echo "无法获取当前位置";
}
```

### 监视位置变化

```php
// 开始监视位置
$success = Geolocation::watchPosition([
    'enableHighAccuracy' => true,
    'timeout' => 5000,
    'maximumAge' => 0,
]);

if ($success) {
    // 监视开始成功
    $watchId = Geolocation::getWatchId();
    echo "位置监视已开始，监视 ID：{$watchId}";
} else {
    // 监视开始失败
    echo "无法开始位置监视";
}

// 检查是否正在监视位置
$isWatching = Geolocation::isWatching();

if ($isWatching) {
    echo "正在监视位置";
} else {
    echo "未在监视位置";
}

// 停止监视位置
$success = Geolocation::clearWatch();

if ($success) {
    echo "位置监视已停止";
} else {
    echo "无法停止位置监视";
}
```

### 计算距离

```php
// 计算两点之间的距离
$distance = Geolocation::calculateDistance(39.9, 116.3, 31.2, 121.4, 'km');

echo "两点之间的距离为：{$distance} 公里";
```

### 地理编码和反向地理编码

```php
// 反向地理编码：根据坐标获取地址
$address = Geolocation::getAddressFromCoordinates(39.9, 116.3);

if ($address) {
    // 地址获取成功
    $country = $address['country'];
    $province = $address['province'];
    $city = $address['city'];
    $district = $address['district'];
    $street = $address['street'];
    $formattedAddress = $address['formattedAddress'];
    
    echo "地址：{$formattedAddress}";
} else {
    // 地址获取失败
    echo "无法获取地址信息";
}

// 地理编码：根据地址获取坐标
$coordinates = Geolocation::getCoordinatesFromAddress('北京市海淀区中关村');

if ($coordinates) {
    // 坐标获取成功
    $latitude = $coordinates['latitude'];
    $longitude = $coordinates['longitude'];
    
    echo "纬度：{$latitude}，经度：{$longitude}";
} else {
    // 坐标获取失败
    echo "无法获取坐标信息";
}
```

### 位置服务状态和权限

```php
// 检查位置服务是否可用
$isAvailable = Geolocation::isAvailable();

if ($isAvailable) {
    echo "位置服务可用";
} else {
    echo "位置服务不可用";
}

// 检查位置权限
$permission = Geolocation::checkPermission();

switch ($permission) {
    case 'granted':
        echo "已获得位置权限";
        break;
    case 'denied':
        echo "位置权限被拒绝";
        break;
    case 'prompt':
        echo "需要请求位置权限";
        break;
}

// 请求位置权限
$success = Geolocation::requestPermission();

if ($success) {
    echo "位置权限请求成功";
} else {
    echo "位置权限请求失败";
}
```

## 位置数据格式

### 位置对象

```php
[
    'coords' => [
        'latitude' => 39.9, // 纬度
        'longitude' => 116.3, // 经度
        'altitude' => 100, // 海拔（可能为 null）
        'accuracy' => 10, // 精度（米）
        'altitudeAccuracy' => 10, // 海拔精度（米，可能为 null）
        'heading' => 90, // 方向（度，可能为 null）
        'speed' => 5, // 速度（米/秒，可能为 null）
    ],
    'timestamp' => 1633012345000, // 时间戳（毫秒）
]
```

### 地址对象

```php
[
    'country' => '中国', // 国家
    'province' => '北京市', // 省/州
    'city' => '北京市', // 城市
    'district' => '海淀区', // 区/县
    'street' => '中关村大街', // 街道
    'streetNumber' => '1号', // 门牌号
    'postalCode' => '100080', // 邮政编码
    'formattedAddress' => '中国北京市海淀区中关村大街1号', // 格式化地址
]
```

### 坐标对象

```php
[
    'latitude' => 39.9, // 纬度
    'longitude' => 116.3, // 经度
]
```

## 实际应用场景

### 地图应用

```php
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\Notification;

class MapController
{
    public function index()
    {
        // 获取当前位置
        $position = Geolocation::getCurrentPosition([
            'enableHighAccuracy' => true,
        ]);
        
        if (!$position) {
            Notification::send('错误', '无法获取当前位置');
            return view('map/index', ['error' => '无法获取当前位置']);
        }
        
        // 获取地址信息
        $address = Geolocation::getAddressFromCoordinates(
            $position['coords']['latitude'],
            $position['coords']['longitude']
        );
        
        // 获取附近的兴趣点
        $pois = $this->getPOIs(
            $position['coords']['latitude'],
            $position['coords']['longitude']
        );
        
        return view('map/index', [
            'position' => $position,
            'address' => $address,
            'pois' => $pois,
        ]);
    }
    
    public function route()
    {
        $origin = input('origin');
        $destination = input('destination');
        
        // 获取路线
        $route = $this->getRoute($origin, $destination);
        
        // 计算距离
        $distance = Geolocation::calculateDistance(
            $route['origin']['latitude'],
            $route['origin']['longitude'],
            $route['destination']['latitude'],
            $route['destination']['longitude'],
            'km'
        );
        
        return view('map/route', [
            'route' => $route,
            'distance' => $distance,
        ]);
    }
    
    // 其他方法...
}
```

### 位置感知应用

```php
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\Notification;

class LocationAwareController
{
    public function index()
    {
        // 检查位置服务是否可用
        if (!Geolocation::isAvailable()) {
            return view('location/index', ['error' => '位置服务不可用']);
        }
        
        // 检查位置权限
        $permission = Geolocation::checkPermission();
        
        if ($permission !== 'granted') {
            // 请求位置权限
            $success = Geolocation::requestPermission();
            
            if (!$success) {
                return view('location/index', ['error' => '无法获取位置权限']);
            }
        }
        
        // 获取当前位置
        $position = Geolocation::getCurrentPosition();
        
        // 获取附近的内容
        $nearbyContent = $this->getNearbyContent(
            $position['coords']['latitude'],
            $position['coords']['longitude']
        );
        
        return view('location/index', [
            'position' => $position,
            'nearbyContent' => $nearbyContent,
        ]);
    }
    
    public function track()
    {
        // 开始监视位置
        $success = Geolocation::watchPosition([
            'enableHighAccuracy' => true,
        ]);
        
        if (!$success) {
            return json(['success' => false, 'message' => '无法开始位置监视']);
        }
        
        return json(['success' => true, 'watch_id' => Geolocation::getWatchId()]);
    }
    
    public function stopTracking()
    {
        // 停止监视位置
        $success = Geolocation::clearWatch();
        
        return json(['success' => $success]);
    }
    
    // 其他方法...
}
```

## 最佳实践

1. **权限检查**：在使用地理位置服务之前，始终检查位置权限，并在需要时请求权限。

2. **错误处理**：妥善处理位置获取失败的情况，提供友好的错误信息和备选方案。

3. **精度设置**：根据应用需求设置适当的精度，高精度会消耗更多电量。

4. **超时设置**：设置合理的超时时间，避免用户等待过长时间。

5. **缓存位置**：适当缓存位置信息，减少位置请求次数，节省电量和网络流量。

6. **用户隐私**：尊重用户隐私，只在必要时获取位置信息，并明确告知用户位置信息的用途。

7. **备选方案**：提供手动输入位置的选项，以防位置服务不可用或用户拒绝位置权限。

## 故障排除

### 无法获取位置

- 检查位置服务是否开启
- 检查应用是否有位置权限
- 检查网络连接
- 尝试降低精度要求
- 增加超时时间

### 位置不准确

- 检查是否启用了高精度模式
- 确保设备有良好的 GPS 信号
- 尝试在室外获取位置
- 等待位置信息更新

### 位置监视不工作

- 检查是否正确启动了位置监视
- 检查是否有其他应用正在使用位置服务
- 尝试停止并重新启动位置监视

### 地理编码失败

- 检查网络连接
- 确保提供的地址格式正确
- 尝试提供更详细的地址信息
- 检查是否超出了 API 调用限制
