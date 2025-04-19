<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\System;

class SystemController
{
    /**
     * 获取操作系统类型
     *
     * @return Response
     */
    public function getOS()
    {
        // 获取操作系统类型
        $os = System::getOS();
        
        return json([
            'os' => $os,
        ]);
    }
    
    /**
     * 获取操作系统版本
     *
     * @return Response
     */
    public function getOSVersion()
    {
        // 获取操作系统版本
        $version = System::getOSVersion();
        
        return json([
            'version' => $version,
        ]);
    }
    
    /**
     * 获取 CPU 架构
     *
     * @return Response
     */
    public function getArch()
    {
        // 获取 CPU 架构
        $arch = System::getArch();
        
        return json([
            'arch' => $arch,
        ]);
    }
    
    /**
     * 获取主机名
     *
     * @return Response
     */
    public function getHostname()
    {
        // 获取主机名
        $hostname = System::getHostname();
        
        return json([
            'hostname' => $hostname,
        ]);
    }
    
    /**
     * 获取用户主目录
     *
     * @return Response
     */
    public function getHomePath()
    {
        // 获取用户主目录
        $path = System::getHomePath();
        
        return json([
            'path' => $path,
        ]);
    }
    
    /**
     * 获取临时目录
     *
     * @return Response
     */
    public function getTempPath()
    {
        // 获取临时目录
        $path = System::getTempPath();
        
        return json([
            'path' => $path,
        ]);
    }
    
    /**
     * 获取应用数据目录
     *
     * @return Response
     */
    public function getAppDataPath()
    {
        // 获取应用数据目录
        $path = System::getAppDataPath();
        
        return json([
            'path' => $path,
        ]);
    }
    
    /**
     * 获取系统内存信息
     *
     * @return Response
     */
    public function getMemoryInfo()
    {
        // 获取系统内存信息
        $info = System::getMemoryInfo();
        
        return json([
            'info' => $info,
        ]);
    }
    
    /**
     * 获取系统 CPU 信息
     *
     * @return Response
     */
    public function getCPUInfo()
    {
        // 获取系统 CPU 信息
        $info = System::getCPUInfo();
        
        return json([
            'info' => $info,
        ]);
    }
    
    /**
     * 获取系统网络接口信息
     *
     * @return Response
     */
    public function getNetworkInterfaces()
    {
        // 获取系统网络接口信息
        $interfaces = System::getNetworkInterfaces();
        
        return json([
            'interfaces' => $interfaces,
        ]);
    }
    
    /**
     * 获取系统显示器信息
     *
     * @return Response
     */
    public function getDisplays()
    {
        // 获取系统显示器信息
        $displays = System::getDisplays();
        
        return json([
            'displays' => $displays,
        ]);
    }
    
    /**
     * 获取系统电池信息
     *
     * @return Response
     */
    public function getBatteryInfo()
    {
        // 获取系统电池信息
        $info = System::getBatteryInfo();
        
        return json([
            'info' => $info,
        ]);
    }
    
    /**
     * 获取系统语言
     *
     * @return Response
     */
    public function getLanguage()
    {
        // 获取系统语言
        $language = System::getLanguage();
        
        return json([
            'language' => $language,
        ]);
    }
    
    /**
     * 打开外部 URL
     *
     * @param Request $request
     * @return Response
     */
    public function openExternal(Request $request)
    {
        $url = $request->param('url');
        $options = $request->param('options', []);
        
        // 打开外部 URL
        $success = System::openExternal($url, $options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 打开文件或目录
     *
     * @param Request $request
     * @return Response
     */
    public function openPath(Request $request)
    {
        $path = $request->param('path');
        
        // 打开文件或目录
        $success = System::openPath($path);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 在文件管理器中显示文件
     *
     * @param Request $request
     * @return Response
     */
    public function showItemInFolder(Request $request)
    {
        $path = $request->param('path');
        
        // 在文件管理器中显示文件
        $success = System::showItemInFolder($path);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 移动文件到回收站
     *
     * @param Request $request
     * @return Response
     */
    public function moveItemToTrash(Request $request)
    {
        $path = $request->param('path');
        
        // 移动文件到回收站
        $success = System::moveItemToTrash($path);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 播放系统提示音
     *
     * @param Request $request
     * @return Response
     */
    public function beep(Request $request)
    {
        $type = $request->param('type', 'info');
        
        // 播放系统提示音
        System::beep($type);
        
        return json([
            'success' => true,
        ]);
    }
    
    /**
     * 设置系统休眠状态
     *
     * @return Response
     */
    public function sleep()
    {
        // 设置系统休眠状态
        $success = System::sleep();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 设置系统锁屏状态
     *
     * @return Response
     */
    public function lock()
    {
        // 设置系统锁屏状态
        $success = System::lock();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 设置系统注销状态
     *
     * @return Response
     */
    public function logout()
    {
        // 设置系统注销状态
        $success = System::logout();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 重启系统
     *
     * @return Response
     */
    public function restart()
    {
        // 重启系统
        $success = System::restart();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 关闭系统
     *
     * @return Response
     */
    public function shutdown()
    {
        // 关闭系统
        $success = System::shutdown();
        
        return json([
            'success' => $success,
        ]);
    }
}
