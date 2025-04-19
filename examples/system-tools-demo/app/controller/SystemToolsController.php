<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\PowerMonitor;
use Native\ThinkPHP\Facades\Network;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use think\facade\View;
use think\facade\Config;

class SystemToolsController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('system-tools/index');
    }
    
    /**
     * 显示文件系统页面
     *
     * @return \think\Response
     */
    public function filesystem()
    {
        // 获取当前目录
        $currentDir = request()->param('dir', '/');
        
        // 获取目录内容
        $contents = FileSystem::listContents($currentDir);
        
        return View::fetch('system-tools/filesystem', [
            'currentDir' => $currentDir,
            'contents' => $contents,
        ]);
    }
    
    /**
     * 创建目录
     *
     * @return \think\Response
     */
    public function createDirectory()
    {
        $path = request()->param('path');
        $name = request()->param('name');
        
        if (empty($path) || empty($name)) {
            return json(['success' => false, 'message' => '路径和名称不能为空']);
        }
        
        $fullPath = rtrim($path, '/') . '/' . $name;
        
        try {
            FileSystem::createDirectory($fullPath);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 创建文件
     *
     * @return \think\Response
     */
    public function createFile()
    {
        $path = request()->param('path');
        $name = request()->param('name');
        $content = request()->param('content', '');
        
        if (empty($path) || empty($name)) {
            return json(['success' => false, 'message' => '路径和名称不能为空']);
        }
        
        $fullPath = rtrim($path, '/') . '/' . $name;
        
        try {
            FileSystem::write($fullPath, $content);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 读取文件
     *
     * @return \think\Response
     */
    public function readFile()
    {
        $path = request()->param('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $content = FileSystem::read($path);
            return json(['success' => true, 'content' => $content]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 更新文件
     *
     * @return \think\Response
     */
    public function updateFile()
    {
        $path = request()->param('path');
        $content = request()->param('content');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            FileSystem::write($path, $content);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除文件或目录
     *
     * @return \think\Response
     */
    public function delete()
    {
        $path = request()->param('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            if (FileSystem::isDirectory($path)) {
                FileSystem::deleteDirectory($path);
            } else {
                FileSystem::delete($path);
            }
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 复制文件或目录
     *
     * @return \think\Response
     */
    public function copy()
    {
        $source = request()->param('source');
        $destination = request()->param('destination');
        
        if (empty($source) || empty($destination)) {
            return json(['success' => false, 'message' => '源路径和目标路径不能为空']);
        }
        
        try {
            if (FileSystem::isDirectory($source)) {
                FileSystem::copyDirectory($source, $destination);
            } else {
                FileSystem::copy($source, $destination);
            }
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 移动文件或目录
     *
     * @return \think\Response
     */
    public function move()
    {
        $source = request()->param('source');
        $destination = request()->param('destination');
        
        if (empty($source) || empty($destination)) {
            return json(['success' => false, 'message' => '源路径和目标路径不能为空']);
        }
        
        try {
            if (FileSystem::isDirectory($source)) {
                FileSystem::moveDirectory($source, $destination);
            } else {
                FileSystem::move($source, $destination);
            }
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示系统页面
     *
     * @return \think\Response
     */
    public function system()
    {
        // 获取系统信息
        $info = [
            'platform' => System::getPlatform(),
            'arch' => System::getArch(),
            'version' => System::getVersion(),
            'hostname' => System::getHostname(),
            'username' => System::getUsername(),
            'homedir' => System::getHomeDir(),
            'tempdir' => System::getTempDir(),
            'cpus' => System::getCPUs(),
            'memory' => System::getMemory(),
            'uptime' => System::getUptime(),
            'loadavg' => System::getLoadAvg(),
            'networkInterfaces' => System::getNetworkInterfaces(),
        ];
        
        return View::fetch('system-tools/system', [
            'info' => $info,
        ]);
    }
    
    /**
     * 执行系统命令
     *
     * @return \think\Response
     */
    public function executeCommand()
    {
        $command = request()->param('command');
        
        if (empty($command)) {
            return json(['success' => false, 'message' => '命令不能为空']);
        }
        
        try {
            $result = System::execute($command);
            return json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 获取进程列表
     *
     * @return \think\Response
     */
    public function getProcesses()
    {
        try {
            $processes = System::getProcesses();
            return json(['success' => true, 'processes' => $processes]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 杀死进程
     *
     * @return \think\Response
     */
    public function killProcess()
    {
        $pid = request()->param('pid');
        
        if (empty($pid)) {
            return json(['success' => false, 'message' => '进程 ID 不能为空']);
        }
        
        try {
            System::killProcess($pid);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示语音识别和合成页面
     *
     * @return \think\Response
     */
    public function speech()
    {
        // 获取可用的语音
        $voices = Speech::getVoices();
        
        return View::fetch('system-tools/speech', [
            'voices' => $voices,
        ]);
    }
    
    /**
     * 语音合成
     *
     * @return \think\Response
     */
    public function speak()
    {
        $text = request()->param('text');
        $voice = request()->param('voice');
        $rate = request()->param('rate', 1.0);
        $pitch = request()->param('pitch', 1.0);
        $volume = request()->param('volume', 1.0);
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        try {
            $options = [
                'voice' => $voice,
                'rate' => (float) $rate,
                'pitch' => (float) $pitch,
                'volume' => (float) $volume,
            ];
            
            Speech::speak($text, $options);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 开始语音识别
     *
     * @return \think\Response
     */
    public function startRecognition()
    {
        $language = request()->param('language', 'zh-CN');
        
        try {
            Speech::startRecognition(['language' => $language]);
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 停止语音识别
     *
     * @return \think\Response
     */
    public function stopRecognition()
    {
        try {
            $result = Speech::stopRecognition();
            return json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示电源监控页面
     *
     * @return \think\Response
     */
    public function power()
    {
        // 获取电源状态
        $status = [
            'onBattery' => PowerMonitor::isOnBattery(),
            'batteryLevel' => PowerMonitor::getBatteryLevel(),
            'batteryState' => PowerMonitor::getBatteryState(),
            'powerSource' => PowerMonitor::getPowerSource(),
        ];
        
        return View::fetch('system-tools/power', [
            'status' => $status,
        ]);
    }
    
    /**
     * 获取电源状态
     *
     * @return \think\Response
     */
    public function getPowerStatus()
    {
        try {
            $status = [
                'onBattery' => PowerMonitor::isOnBattery(),
                'batteryLevel' => PowerMonitor::getBatteryLevel(),
                'batteryState' => PowerMonitor::getBatteryState(),
                'powerSource' => PowerMonitor::getPowerSource(),
            ];
            
            return json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示网络状态页面
     *
     * @return \think\Response
     */
    public function network()
    {
        // 获取网络状态
        $status = [
            'status' => Network::getStatus(),
            'isOnline' => Network::isOnline(),
            'ipAddress' => Network::getIPAddress(),
            'macAddress' => Network::getMACAddress(),
            'connectionType' => Network::getConnectionType(),
            'interfaces' => Network::getInterfaces(),
        ];
        
        return View::fetch('system-tools/network', [
            'status' => $status,
        ]);
    }
    
    /**
     * 获取网络状态
     *
     * @return \think\Response
     */
    public function getNetworkStatus()
    {
        try {
            $status = [
                'status' => Network::getStatus(),
                'isOnline' => Network::isOnline(),
                'ipAddress' => Network::getIPAddress(),
                'macAddress' => Network::getMACAddress(),
                'connectionType' => Network::getConnectionType(),
                'downloadSpeed' => Network::getDownloadSpeed(),
                'uploadSpeed' => Network::getUploadSpeed(),
                'publicIP' => Network::getPublicIPAddress(),
                'dnsServers' => Network::getDNSServers(),
                'bandwidth' => Network::getBandwidth(),
                'usage' => Network::getUsage(),
            ];
            
            return json(['success' => true, 'status' => $status]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 测试网络连接
     *
     * @return \think\Response
     */
    public function testConnection()
    {
        $host = request()->param('host');
        $port = request()->param('port', 80);
        $timeout = request()->param('timeout', 5000);
        
        if (empty($host)) {
            return json(['success' => false, 'message' => '主机不能为空']);
        }
        
        try {
            $result = Network::testConnection($host, (int) $port, (int) $timeout);
            $ping = Network::getPing($host);
            
            return json([
                'success' => true,
                'result' => $result,
                'ping' => $ping,
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 解析域名
     *
     * @return \think\Response
     */
    public function resolveDomain()
    {
        $domain = request()->param('domain');
        
        if (empty($domain)) {
            return json(['success' => false, 'message' => '域名不能为空']);
        }
        
        try {
            $addresses = Network::resolveDomain($domain);
            return json(['success' => true, 'addresses' => $addresses]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
