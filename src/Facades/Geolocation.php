<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static array|null getCurrentPosition(array $options = []) 获取当前位置
 * @method static bool watchPosition(array $options = []) 开始监视位置
 * @method static bool clearWatch() 停止监视位置
 * @method static bool isWatching() 检查是否正在监视位置
 * @method static int|null getWatchId() 获取监视 ID
 * @method static float calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2, string $unit = 'km') 计算两点之间的距离
 * @method static array|null getAddressFromCoordinates(float $latitude, float $longitude) 获取地址信息
 * @method static array|null getCoordinatesFromAddress(string $address) 获取坐标信息
 * @method static bool isAvailable() 检查位置服务是否可用
 * @method static string checkPermission() 检查位置权限
 * @method static bool requestPermission() 请求位置权限
 * 
 * @see \Native\ThinkPHP\Geolocation
 */
class Geolocation extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.geolocation';
    }
}
