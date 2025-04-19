<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static array getBluetoothDevices() 获取蓝牙设备列表
 * @method static array getUsbDevices() 获取 USB 设备列表
 * @method static bool scanBluetoothDevices(array $options = []) 扫描蓝牙设备
 * @method static bool connectBluetoothDevice(string $deviceId) 连接蓝牙设备
 * @method static bool disconnectBluetoothDevice(string $deviceId) 断开蓝牙设备
 * @method static bool pairBluetoothDevice(string $deviceId) 配对蓝牙设备
 * @method static bool unpairBluetoothDevice(string $deviceId) 取消配对蓝牙设备
 * @method static bool sendDataToBluetoothDevice(string $deviceId, string $data) 向蓝牙设备发送数据
 * @method static string|null receiveDataFromBluetoothDevice(string $deviceId) 从蓝牙设备接收数据
 * @method static bool openUsbDevice(string $deviceId) 打开 USB 设备
 * @method static bool closeUsbDevice(string $deviceId) 关闭 USB 设备
 * @method static bool sendDataToUsbDevice(string $deviceId, string $data) 向 USB 设备发送数据
 * @method static string|null receiveDataFromUsbDevice(string $deviceId) 从 USB 设备接收数据
 * @method static array|null getDeviceInfo(string $deviceId, string $type = 'bluetooth') 获取设备信息
 * 
 * @see \Native\ThinkPHP\Device
 */
class Device extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.device';
    }
}
