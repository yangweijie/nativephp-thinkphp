# 地图应用示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的地理位置服务功能创建一个地图应用。

## 功能

- 显示当前位置
- 搜索地点
- 路线规划
- 保存收藏地点
- 测量距离
- 地图标记
- 离线地图

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Map.php` - 地图控制器
- `app/model/Location.php` - 位置模型
- `app/service/MapService.php` - 地图服务
- `view/index/index.html` - 主页面
- `view/map/index.html` - 地图页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式
- `public/static/js/map.js` - 地图 JavaScript 库

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
use app\model\Location;
use app\service\MapService;
use Native\ThinkPHP\Facades\Geolocation;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;

class Map extends BaseController
{
    protected $mapService;
    
    public function __construct(MapService $mapService)
    {
        $this->mapService = $mapService;
    }
    
    public function index()
    {
        $locations = Location::where('type', 'favorite')->select();
        
        return view('map/index', [
            'locations' => $locations,
            'apiKey' => Settings::get('map.api_key', ''),
        ]);
    }
    
    public function getCurrentPosition()
    {
        $position = Geolocation::getCurrentPosition([
            'enableHighAccuracy' => true,
            'timeout' => 10000,
            'maximumAge' => 0,
        ]);
        
        if (!$position) {
            return json(['success' => false, 'message' => '无法获取当前位置']);
        }
        
        // 获取地址信息
        $address = Geolocation::getAddressFromCoordinates(
            $position['coords']['latitude'],
            $position['coords']['longitude']
        );
        
        return json([
            'success' => true,
            'position' => $position,
            'address' => $address,
        ]);
    }
    
    public function watchPosition()
    {
        $result = Geolocation::watchPosition([
            'enableHighAccuracy' => true,
            'timeout' => 10000,
            'maximumAge' => 0,
        ]);
        
        return json(['success' => $result]);
    }
    
    public function clearWatch()
    {
        $result = Geolocation::clearWatch();
        
        return json(['success' => $result]);
    }
    
    public function search()
    {
        $keyword = input('keyword');
        
        if (empty($keyword)) {
            return json(['success' => false, 'message' => '搜索关键词不能为空']);
        }
        
        $results = $this->mapService->searchLocation($keyword);
        
        return json(['success' => true, 'results' => $results]);
    }
    
    public function getRoute()
    {
        $origin = input('origin');
        $destination = input('destination');
        $mode = input('mode', 'driving');
        
        if (empty($origin) || empty($destination)) {
            return json(['success' => false, 'message' => '起点和终点不能为空']);
        }
        
        $route = $this->mapService->getRoute($origin, $destination, $mode);
        
        return json(['success' => true, 'route' => $route]);
    }
    
    public function saveLocation()
    {
        $name = input('name');
        $latitude = input('latitude');
        $longitude = input('longitude');
        $address = input('address');
        $type = input('type', 'favorite');
        
        if (empty($name) || empty($latitude) || empty($longitude)) {
            return json(['success' => false, 'message' => '名称、纬度和经度不能为空']);
        }
        
        $location = new Location;
        $location->name = $name;
        $location->latitude = $latitude;
        $location->longitude = $longitude;
        $location->address = $address;
        $location->type = $type;
        $location->created_at = date('Y-m-d H:i:s');
        $location->save();
        
        Notification::send('保存成功', '位置已保存');
        
        return json(['success' => true, 'id' => $location->id]);
    }
    
    public function deleteLocation()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '位置ID不能为空']);
        }
        
        $location = Location::find($id);
        
        if (!$location) {
            return json(['success' => false, 'message' => '位置不存在']);
        }
        
        $location->delete();
        
        Notification::send('删除成功', '位置已删除');
        
        return json(['success' => true]);
    }
    
    public function calculateDistance()
    {
        $lat1 = input('lat1');
        $lon1 = input('lon1');
        $lat2 = input('lat2');
        $lon2 = input('lon2');
        $unit = input('unit', 'km');
        
        if (empty($lat1) || empty($lon1) || empty($lat2) || empty($lon2)) {
            return json(['success' => false, 'message' => '坐标不能为空']);
        }
        
        $distance = Geolocation::calculateDistance($lat1, $lon1, $lat2, $lon2, $unit);
        
        return json(['success' => true, 'distance' => $distance, 'unit' => $unit]);
    }
    
    public function getAddressFromCoordinates()
    {
        $latitude = input('latitude');
        $longitude = input('longitude');
        
        if (empty($latitude) || empty($longitude)) {
            return json(['success' => false, 'message' => '坐标不能为空']);
        }
        
        $address = Geolocation::getAddressFromCoordinates($latitude, $longitude);
        
        return json(['success' => true, 'address' => $address]);
    }
    
    public function getCoordinatesFromAddress()
    {
        $address = input('address');
        
        if (empty($address)) {
            return json(['success' => false, 'message' => '地址不能为空']);
        }
        
        $coordinates = Geolocation::getCoordinatesFromAddress($address);
        
        return json(['success' => true, 'coordinates' => $coordinates]);
    }
    
    public function downloadOfflineMap()
    {
        $latitude = input('latitude');
        $longitude = input('longitude');
        $zoom = input('zoom', 12);
        $name = input('name', '离线地图');
        
        if (empty($latitude) || empty($longitude)) {
            return json(['success' => false, 'message' => '坐标不能为空']);
        }
        
        $result = $this->mapService->downloadOfflineMap($latitude, $longitude, $zoom, $name);
        
        if ($result) {
            Notification::send('下载成功', '离线地图已下载');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '下载失败']);
        }
    }
    
    public function getOfflineMaps()
    {
        $maps = $this->mapService->getOfflineMaps();
        
        return json(['success' => true, 'maps' => $maps]);
    }
    
    public function deleteOfflineMap()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '地图ID不能为空']);
        }
        
        $result = $this->mapService->deleteOfflineMap($id);
        
        if ($result) {
            Notification::send('删除成功', '离线地图已删除');
            return json(['success' => true]);
        } else {
            return json(['success' => false, 'message' => '删除失败']);
        }
    }
    
    public function setApiKey()
    {
        $apiKey = input('api_key');
        
        if (empty($apiKey)) {
            return json(['success' => false, 'message' => 'API密钥不能为空']);
        }
        
        Settings::set('map.api_key', $apiKey);
        
        Notification::send('设置成功', 'API密钥已设置');
        
        return json(['success' => true]);
    }
}
```

### 服务

```php
<?php

namespace app\service;

use app\model\Location;
use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Settings;

class MapService
{
    /**
     * 搜索位置
     *
     * @param string $keyword
     * @return array
     */
    public function searchLocation($keyword)
    {
        $apiKey = Settings::get('map.api_key', '');
        
        if (empty($apiKey)) {
            return [];
        }
        
        // 使用地图API搜索位置
        // 这里使用Google Maps API作为示例
        $response = Http::get('https://maps.googleapis.com/maps/api/place/textsearch/json', [
            'query' => $keyword,
            'key' => $apiKey,
        ]);
        
        if (!$response['success']) {
            return [];
        }
        
        $data = json_decode($response['data'], true);
        
        if (!isset($data['results']) || !is_array($data['results'])) {
            return [];
        }
        
        $results = [];
        
        foreach ($data['results'] as $result) {
            $results[] = [
                'name' => $result['name'],
                'address' => $result['formatted_address'],
                'latitude' => $result['geometry']['location']['lat'],
                'longitude' => $result['geometry']['location']['lng'],
            ];
        }
        
        return $results;
    }
    
    /**
     * 获取路线
     *
     * @param string $origin
     * @param string $destination
     * @param string $mode
     * @return array
     */
    public function getRoute($origin, $destination, $mode = 'driving')
    {
        $apiKey = Settings::get('map.api_key', '');
        
        if (empty($apiKey)) {
            return [];
        }
        
        // 使用地图API获取路线
        // 这里使用Google Maps API作为示例
        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $origin,
            'destination' => $destination,
            'mode' => $mode,
            'key' => $apiKey,
        ]);
        
        if (!$response['success']) {
            return [];
        }
        
        $data = json_decode($response['data'], true);
        
        if (!isset($data['routes']) || !is_array($data['routes']) || empty($data['routes'])) {
            return [];
        }
        
        $route = $data['routes'][0];
        
        $result = [
            'distance' => $route['legs'][0]['distance']['text'],
            'duration' => $route['legs'][0]['duration']['text'],
            'start_address' => $route['legs'][0]['start_address'],
            'end_address' => $route['legs'][0]['end_address'],
            'steps' => [],
            'polyline' => $route['overview_polyline']['points'],
        ];
        
        foreach ($route['legs'][0]['steps'] as $step) {
            $result['steps'][] = [
                'distance' => $step['distance']['text'],
                'duration' => $step['duration']['text'],
                'instructions' => strip_tags($step['html_instructions']),
                'polyline' => $step['polyline']['points'],
            ];
        }
        
        return $result;
    }
    
    /**
     * 下载离线地图
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $zoom
     * @param string $name
     * @return bool
     */
    public function downloadOfflineMap($latitude, $longitude, $zoom = 12, $name = '离线地图')
    {
        $apiKey = Settings::get('map.api_key', '');
        
        if (empty($apiKey)) {
            return false;
        }
        
        // 创建离线地图目录
        $offlineMapsDir = runtime_path() . 'offline_maps/';
        
        if (!is_dir($offlineMapsDir)) {
            FileSystem::makeDirectory($offlineMapsDir, 0755, true);
        }
        
        // 生成地图ID
        $mapId = md5($latitude . $longitude . $zoom . time());
        
        // 创建地图目录
        $mapDir = $offlineMapsDir . $mapId . '/';
        FileSystem::makeDirectory($mapDir, 0755, true);
        
        // 下载地图瓦片
        // 这里简化处理，实际应用中需要下载多个瓦片
        $url = "https://maps.googleapis.com/maps/api/staticmap?center={$latitude},{$longitude}&zoom={$zoom}&size=1280x1280&key={$apiKey}";
        $mapFile = $mapDir . 'map.png';
        
        $result = Http::download($url, $mapFile);
        
        if (!$result) {
            return false;
        }
        
        // 保存地图信息
        $mapInfo = [
            'id' => $mapId,
            'name' => $name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'zoom' => $zoom,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        $mapsInfo = Settings::get('map.offline_maps', []);
        $mapsInfo[] = $mapInfo;
        
        Settings::set('map.offline_maps', $mapsInfo);
        
        return true;
    }
    
    /**
     * 获取离线地图列表
     *
     * @return array
     */
    public function getOfflineMaps()
    {
        return Settings::get('map.offline_maps', []);
    }
    
    /**
     * 删除离线地图
     *
     * @param string $id
     * @return bool
     */
    public function deleteOfflineMap($id)
    {
        $mapsInfo = Settings::get('map.offline_maps', []);
        
        foreach ($mapsInfo as $key => $map) {
            if ($map['id'] === $id) {
                // 删除地图文件
                $mapDir = runtime_path() . 'offline_maps/' . $id . '/';
                FileSystem::deleteDirectory($mapDir, true);
                
                // 从列表中移除
                unset($mapsInfo[$key]);
                
                // 更新设置
                Settings::set('map.offline_maps', array_values($mapsInfo));
                
                return true;
            }
        }
        
        return false;
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
    <title>地图应用</title>
    <link rel="stylesheet" href="/static/css/app.css">
    <script src="https://maps.googleapis.com/maps/api/js?key={$apiKey}&libraries=places"></script>
    <script src="/static/js/map.js"></script>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="sidebar-header">
                <h2>地图应用</h2>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchInput" placeholder="搜索地点...">
                <button onclick="search()">搜索</button>
            </div>
            
            <div class="route-planner">
                <h3>路线规划</h3>
                <div class="route-inputs">
                    <input type="text" id="originInput" placeholder="起点">
                    <input type="text" id="destinationInput" placeholder="终点">
                    <select id="modeSelect">
                        <option value="driving">驾车</option>
                        <option value="walking">步行</option>
                        <option value="bicycling">骑行</option>
                        <option value="transit">公交</option>
                    </select>
                    <button onclick="getRoute()">规划路线</button>
                </div>
            </div>
            
            <div class="favorite-locations">
                <h3>收藏地点</h3>
                <ul id="favoriteLocations">
                    {volist name="locations" id="location"}
                    <li data-id="{$location.id}" data-lat="{$location.latitude}" data-lng="{$location.longitude}">
                        <span class="location-name">{$location.name}</span>
                        <span class="location-address">{$location.address}</span>
                        <div class="location-actions">
                            <button onclick="showLocation({$location.latitude}, {$location.longitude})">显示</button>
                            <button onclick="deleteLocation({$location.id})">删除</button>
                        </div>
                    </li>
                    {/volist}
                </ul>
            </div>
            
            <div class="tools">
                <h3>工具</h3>
                <button onclick="getCurrentPosition()">定位</button>
                <button onclick="toggleWatchPosition()" id="watchBtn">跟踪位置</button>
                <button onclick="measureDistance()">测量距离</button>
                <button onclick="addMarker()">添加标记</button>
                <button onclick="clearMarkers()">清除标记</button>
            </div>
            
            <div class="offline-maps">
                <h3>离线地图</h3>
                <button onclick="downloadOfflineMap()">下载当前区域</button>
                <button onclick="showOfflineMaps()">查看离线地图</button>
            </div>
            
            <div class="settings">
                <h3>设置</h3>
                <div class="api-key-setting">
                    <input type="text" id="apiKeyInput" placeholder="Google Maps API Key" value="{$apiKey}">
                    <button onclick="setApiKey()">保存</button>
                </div>
            </div>
        </div>
        
        <div class="main-content">
            <div id="map"></div>
            
            <div class="info-panel">
                <div class="current-location">
                    <h3>当前位置</h3>
                    <div id="currentLocation"></div>
                </div>
                
                <div class="route-info">
                    <h3>路线信息</h3>
                    <div id="routeInfo"></div>
                </div>
                
                <div class="distance-info">
                    <h3>距离信息</h3>
                    <div id="distanceInfo"></div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```
