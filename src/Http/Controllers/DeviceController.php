<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Device;

class DeviceController
{
    /**
     * 获取蓝牙设备列表
     *
     * @return Response
     */
    public function getBluetoothDevices()
    {
        // 获取蓝牙设备列表
        $devices = Device::getBluetoothDevices();
        
        return json([
            'devices' => $devices,
        ]);
    }
    
    /**
     * 获取 USB 设备列表
     *
     * @return Response
     */
    public function getUsbDevices()
    {
        // 获取 USB 设备列表
        $devices = Device::getUsbDevices();
        
        return json([
            'devices' => $devices,
        ]);
    }
    
    /**
     * 扫描蓝牙设备
     *
     * @param Request $request
     * @return Response
     */
    public function scanBluetoothDevices(Request $request)
    {
        $options = $request->param('options', []);
        
        // 扫描蓝牙设备
        $success = Device::scanBluetoothDevices($options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 连接蓝牙设备
     *
     * @param Request $request
     * @return Response
     */
    public function connectBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 连接蓝牙设备
        $success = Device::connectBluetoothDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 断开蓝牙设备
     *
     * @param Request $request
     * @return Response
     */
    public function disconnectBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 断开蓝牙设备
        $success = Device::disconnectBluetoothDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 配对蓝牙设备
     *
     * @param Request $request
     * @return Response
     */
    public function pairBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 配对蓝牙设备
        $success = Device::pairBluetoothDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 取消配对蓝牙设备
     *
     * @param Request $request
     * @return Response
     */
    public function unpairBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 取消配对蓝牙设备
        $success = Device::unpairBluetoothDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 向蓝牙设备发送数据
     *
     * @param Request $request
     * @return Response
     */
    public function sendDataToBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        $data = $request->param('data');
        
        // 向蓝牙设备发送数据
        $success = Device::sendDataToBluetoothDevice($deviceId, $data);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 从蓝牙设备接收数据
     *
     * @param Request $request
     * @return Response
     */
    public function receiveDataFromBluetoothDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 从蓝牙设备接收数据
        $data = Device::receiveDataFromBluetoothDevice($deviceId);
        
        return json([
            'success' => $data !== null,
            'data' => $data,
        ]);
    }
    
    /**
     * 打开 USB 设备
     *
     * @param Request $request
     * @return Response
     */
    public function openUsbDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 打开 USB 设备
        $success = Device::openUsbDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 关闭 USB 设备
     *
     * @param Request $request
     * @return Response
     */
    public function closeUsbDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 关闭 USB 设备
        $success = Device::closeUsbDevice($deviceId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 向 USB 设备发送数据
     *
     * @param Request $request
     * @return Response
     */
    public function sendDataToUsbDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        $data = $request->param('data');
        
        // 向 USB 设备发送数据
        $success = Device::sendDataToUsbDevice($deviceId, $data);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 从 USB 设备接收数据
     *
     * @param Request $request
     * @return Response
     */
    public function receiveDataFromUsbDevice(Request $request)
    {
        $deviceId = $request->param('device_id');
        
        // 从 USB 设备接收数据
        $data = Device::receiveDataFromUsbDevice($deviceId);
        
        return json([
            'success' => $data !== null,
            'data' => $data,
        ]);
    }
    
    /**
     * 获取设备信息
     *
     * @param Request $request
     * @return Response
     */
    public function getDeviceInfo(Request $request)
    {
        $deviceId = $request->param('device_id');
        $type = $request->param('type', 'bluetooth');
        
        // 获取设备信息
        $info = Device::getDeviceInfo($deviceId, $type);
        
        return json([
            'success' => $info !== null,
            'info' => $info,
        ]);
    }
}
