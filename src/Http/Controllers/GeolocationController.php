<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Geolocation;

class GeolocationController
{
    /**
     * 获取当前位置
     *
     * @param Request $request
     * @return Response
     */
    public function getCurrentPosition(Request $request)
    {
        $options = $request->param('options', []);
        
        // 获取当前位置
        $position = Geolocation::getCurrentPosition($options);
        
        return json([
            'success' => $position !== null,
            'position' => $position,
        ]);
    }
    
    /**
     * 开始监视位置
     *
     * @param Request $request
     * @return Response
     */
    public function watchPosition(Request $request)
    {
        $options = $request->param('options', []);
        
        // 开始监视位置
        $success = Geolocation::watchPosition($options);
        
        return json([
            'success' => $success,
            'watch_id' => Geolocation::getWatchId(),
        ]);
    }
    
    /**
     * 停止监视位置
     *
     * @return Response
     */
    public function clearWatch()
    {
        // 停止监视位置
        $success = Geolocation::clearWatch();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查是否正在监视位置
     *
     * @return Response
     */
    public function isWatching()
    {
        // 检查是否正在监视位置
        $isWatching = Geolocation::isWatching();
        
        return json([
            'is_watching' => $isWatching,
        ]);
    }
    
    /**
     * 获取监视 ID
     *
     * @return Response
     */
    public function getWatchId()
    {
        // 获取监视 ID
        $watchId = Geolocation::getWatchId();
        
        return json([
            'watch_id' => $watchId,
        ]);
    }
    
    /**
     * 计算两点之间的距离
     *
     * @param Request $request
     * @return Response
     */
    public function calculateDistance(Request $request)
    {
        $lat1 = $request->param('lat1');
        $lon1 = $request->param('lon1');
        $lat2 = $request->param('lat2');
        $lon2 = $request->param('lon2');
        $unit = $request->param('unit', 'km');
        
        // 计算两点之间的距离
        $distance = Geolocation::calculateDistance($lat1, $lon1, $lat2, $lon2, $unit);
        
        return json([
            'distance' => $distance,
            'unit' => $unit,
        ]);
    }
    
    /**
     * 获取地址信息
     *
     * @param Request $request
     * @return Response
     */
    public function getAddressFromCoordinates(Request $request)
    {
        $latitude = $request->param('latitude');
        $longitude = $request->param('longitude');
        
        // 获取地址信息
        $address = Geolocation::getAddressFromCoordinates($latitude, $longitude);
        
        return json([
            'success' => $address !== null,
            'address' => $address,
        ]);
    }
    
    /**
     * 获取坐标信息
     *
     * @param Request $request
     * @return Response
     */
    public function getCoordinatesFromAddress(Request $request)
    {
        $address = $request->param('address');
        
        // 获取坐标信息
        $coordinates = Geolocation::getCoordinatesFromAddress($address);
        
        return json([
            'success' => $coordinates !== null,
            'coordinates' => $coordinates,
        ]);
    }
    
    /**
     * 检查位置服务是否可用
     *
     * @return Response
     */
    public function isAvailable()
    {
        // 检查位置服务是否可用
        $isAvailable = Geolocation::isAvailable();
        
        return json([
            'is_available' => $isAvailable,
        ]);
    }
    
    /**
     * 检查位置权限
     *
     * @return Response
     */
    public function checkPermission()
    {
        // 检查位置权限
        $permission = Geolocation::checkPermission();
        
        return json([
            'permission' => $permission,
        ]);
    }
    
    /**
     * 请求位置权限
     *
     * @return Response
     */
    public function requestPermission()
    {
        // 请求位置权限
        $success = Geolocation::requestPermission();
        
        return json([
            'success' => $success,
        ]);
    }
}
