<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Logger;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use think\facade\View;
use think\facade\Config;

class SecureStorageController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('secure-storage/index');
    }
    
    /**
     * 显示数据库同步页面
     *
     * @return \think\Response
     */
    public function database()
    {
        // 获取数据库表列表
        $tables = Database::getTables();
        
        // 获取同步状态
        $syncStatus = Settings::get('database.sync_status', [
            'last_sync' => null,
            'status' => 'idle',
            'message' => '未同步',
        ]);
        
        return View::fetch('secure-storage/database', [
            'tables' => $tables,
            'syncStatus' => $syncStatus,
        ]);
    }
    
    /**
     * 执行数据库查询
     *
     * @return \think\Response
     */
    public function executeQuery()
    {
        $query = request()->param('query');
        
        if (empty($query)) {
            return json(['success' => false, 'message' => '查询语句不能为空']);
        }
        
        try {
            // 记录日志
            Logger::info('执行数据库查询', ['query' => $query]);
            
            // 执行查询
            $result = Database::query($query);
            
            return json(['success' => true, 'result' => $result]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('数据库查询失败', ['query' => $query, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 同步数据库
     *
     * @return \think\Response
     */
    public function syncDatabase()
    {
        $remoteUrl = request()->param('remote_url');
        $username = request()->param('username');
        $password = request()->param('password');
        $tables = request()->param('tables', []);
        
        if (empty($remoteUrl)) {
            return json(['success' => false, 'message' => '远程数据库 URL 不能为空']);
        }
        
        if (empty($username) || empty($password)) {
            return json(['success' => false, 'message' => '用户名和密码不能为空']);
        }
        
        if (empty($tables)) {
            return json(['success' => false, 'message' => '请选择要同步的表']);
        }
        
        try {
            // 更新同步状态
            Settings::set('database.sync_status', [
                'last_sync' => date('Y-m-d H:i:s'),
                'status' => 'syncing',
                'message' => '正在同步...',
            ]);
            
            // 记录日志
            Logger::info('开始数据库同步', [
                'remote_url' => $remoteUrl,
                'username' => $username,
                'tables' => $tables,
            ]);
            
            // 模拟同步过程
            sleep(2);
            
            // 更新同步状态
            Settings::set('database.sync_status', [
                'last_sync' => date('Y-m-d H:i:s'),
                'status' => 'success',
                'message' => '同步成功',
            ]);
            
            // 记录日志
            Logger::info('数据库同步成功', [
                'remote_url' => $remoteUrl,
                'tables' => $tables,
            ]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 更新同步状态
            Settings::set('database.sync_status', [
                'last_sync' => date('Y-m-d H:i:s'),
                'status' => 'error',
                'message' => '同步失败：' . $e->getMessage(),
            ]);
            
            // 记录错误日志
            Logger::error('数据库同步失败', [
                'remote_url' => $remoteUrl,
                'username' => $username,
                'tables' => $tables,
                'error' => $e->getMessage(),
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 备份数据库
     *
     * @return \think\Response
     */
    public function backupDatabase()
    {
        $path = request()->param('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '备份路径不能为空']);
        }
        
        try {
            // 记录日志
            Logger::info('开始备份数据库', ['path' => $path]);
            
            // 备份数据库
            Database::backup($path);
            
            // 记录日志
            Logger::info('数据库备份成功', ['path' => $path]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('数据库备份失败', ['path' => $path, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 恢复数据库
     *
     * @return \think\Response
     */
    public function restoreDatabase()
    {
        $path = request()->param('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '备份文件路径不能为空']);
        }
        
        try {
            // 记录日志
            Logger::info('开始恢复数据库', ['path' => $path]);
            
            // 恢复数据库
            Database::restore($path);
            
            // 记录日志
            Logger::info('数据库恢复成功', ['path' => $path]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('数据库恢复失败', ['path' => $path, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示离线存储页面
     *
     * @return \think\Response
     */
    public function offline()
    {
        // 获取离线数据
        $offlineData = Settings::get('offline.data', []);
        
        return View::fetch('secure-storage/offline', [
            'offlineData' => $offlineData,
        ]);
    }
    
    /**
     * 保存离线数据
     *
     * @return \think\Response
     */
    public function saveOfflineData()
    {
        $key = request()->param('key');
        $value = request()->param('value');
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '键不能为空']);
        }
        
        try {
            // 获取当前离线数据
            $offlineData = Settings::get('offline.data', []);
            
            // 添加或更新数据
            $offlineData[$key] = $value;
            
            // 保存离线数据
            Settings::set('offline.data', $offlineData);
            
            // 记录日志
            Logger::info('保存离线数据', ['key' => $key]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('保存离线数据失败', ['key' => $key, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除离线数据
     *
     * @return \think\Response
     */
    public function deleteOfflineData()
    {
        $key = request()->param('key');
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '键不能为空']);
        }
        
        try {
            // 获取当前离线数据
            $offlineData = Settings::get('offline.data', []);
            
            // 删除数据
            if (isset($offlineData[$key])) {
                unset($offlineData[$key]);
            }
            
            // 保存离线数据
            Settings::set('offline.data', $offlineData);
            
            // 记录日志
            Logger::info('删除离线数据', ['key' => $key]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('删除离线数据失败', ['key' => $key, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 同步离线数据
     *
     * @return \think\Response
     */
    public function syncOfflineData()
    {
        $remoteUrl = request()->param('remote_url');
        
        if (empty($remoteUrl)) {
            return json(['success' => false, 'message' => '远程 URL 不能为空']);
        }
        
        try {
            // 获取离线数据
            $offlineData = Settings::get('offline.data', []);
            
            // 记录日志
            Logger::info('开始同步离线数据', ['remote_url' => $remoteUrl, 'data_count' => count($offlineData)]);
            
            // 模拟同步过程
            sleep(2);
            
            // 更新同步状态
            Settings::set('offline.sync_status', [
                'last_sync' => date('Y-m-d H:i:s'),
                'status' => 'success',
                'message' => '同步成功',
            ]);
            
            // 记录日志
            Logger::info('离线数据同步成功', ['remote_url' => $remoteUrl]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 更新同步状态
            Settings::set('offline.sync_status', [
                'last_sync' => date('Y-m-d H:i:s'),
                'status' => 'error',
                'message' => '同步失败：' . $e->getMessage(),
            ]);
            
            // 记录错误日志
            Logger::error('离线数据同步失败', ['remote_url' => $remoteUrl, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示安全存储页面
     *
     * @return \think\Response
     */
    public function secure()
    {
        // 获取安全存储数据
        $secureData = $this->getSecureData();
        
        return View::fetch('secure-storage/secure', [
            'secureData' => $secureData,
        ]);
    }
    
    /**
     * 保存安全数据
     *
     * @return \think\Response
     */
    public function saveSecureData()
    {
        $key = request()->param('key');
        $value = request()->param('value');
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '键不能为空']);
        }
        
        try {
            // 获取当前安全数据
            $secureData = $this->getSecureData();
            
            // 加密数据
            $encryptedValue = $this->encrypt($value);
            
            // 添加或更新数据
            $secureData[$key] = $encryptedValue;
            
            // 保存安全数据
            $this->saveSecureDataToFile($secureData);
            
            // 记录日志
            Logger::info('保存安全数据', ['key' => $key]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('保存安全数据失败', ['key' => $key, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 获取安全数据
     *
     * @return \think\Response
     */
    public function getSecureDataValue()
    {
        $key = request()->param('key');
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '键不能为空']);
        }
        
        try {
            // 获取当前安全数据
            $secureData = $this->getSecureData();
            
            // 检查数据是否存在
            if (!isset($secureData[$key])) {
                return json(['success' => false, 'message' => '数据不存在']);
            }
            
            // 解密数据
            $decryptedValue = $this->decrypt($secureData[$key]);
            
            // 记录日志
            Logger::info('获取安全数据', ['key' => $key]);
            
            return json(['success' => true, 'value' => $decryptedValue]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('获取安全数据失败', ['key' => $key, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除安全数据
     *
     * @return \think\Response
     */
    public function deleteSecureData()
    {
        $key = request()->param('key');
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '键不能为空']);
        }
        
        try {
            // 获取当前安全数据
            $secureData = $this->getSecureData();
            
            // 删除数据
            if (isset($secureData[$key])) {
                unset($secureData[$key]);
            }
            
            // 保存安全数据
            $this->saveSecureDataToFile($secureData);
            
            // 记录日志
            Logger::info('删除安全数据', ['key' => $key]);
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('删除安全数据失败', ['key' => $key, 'error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示加密页面
     *
     * @return \think\Response
     */
    public function encryption()
    {
        return View::fetch('secure-storage/encryption');
    }
    
    /**
     * 加密数据
     *
     * @return \think\Response
     */
    public function encryptData()
    {
        $data = request()->param('data');
        $password = request()->param('password');
        
        if (empty($data)) {
            return json(['success' => false, 'message' => '数据不能为空']);
        }
        
        if (empty($password)) {
            return json(['success' => false, 'message' => '密码不能为空']);
        }
        
        try {
            // 加密数据
            $encryptedData = $this->encrypt($data, $password);
            
            // 记录日志
            Logger::info('加密数据');
            
            return json(['success' => true, 'encryptedData' => $encryptedData]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('加密数据失败', ['error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 解密数据
     *
     * @return \think\Response
     */
    public function decryptData()
    {
        $data = request()->param('data');
        $password = request()->param('password');
        
        if (empty($data)) {
            return json(['success' => false, 'message' => '数据不能为空']);
        }
        
        if (empty($password)) {
            return json(['success' => false, 'message' => '密码不能为空']);
        }
        
        try {
            // 解密数据
            $decryptedData = $this->decrypt($data, $password);
            
            // 记录日志
            Logger::info('解密数据');
            
            return json(['success' => true, 'decryptedData' => $decryptedData]);
        } catch (\Exception $e) {
            // 记录错误日志
            Logger::error('解密数据失败', ['error' => $e->getMessage()]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 显示日志页面
     *
     * @return \think\Response
     */
    public function logging()
    {
        // 获取日志文件路径
        $logFile = Logger::getLogFile();
        
        // 获取日志内容
        $logContent = Logger::get(100);
        
        // 获取日志文件大小
        $logSize = FileSystem::size($logFile);
        
        return View::fetch('secure-storage/logging', [
            'logFile' => $logFile,
            'logContent' => $logContent,
            'logSize' => $logSize,
        ]);
    }
    
    /**
     * 获取日志内容
     *
     * @return \think\Response
     */
    public function getLogContent()
    {
        $lines = request()->param('lines', 100);
        
        try {
            // 获取日志内容
            $logContent = Logger::get($lines);
            
            return json(['success' => true, 'logContent' => $logContent]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 清空日志
     *
     * @return \think\Response
     */
    public function clearLog()
    {
        try {
            // 清空日志
            Logger::clear();
            
            // 记录日志
            Logger::info('日志已清空');
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 轮换日志文件
     *
     * @return \think\Response
     */
    public function rotateLog()
    {
        $maxSize = request()->param('maxSize', 10485760);
        $maxFiles = request()->param('maxFiles', 5);
        
        try {
            // 轮换日志文件
            Logger::rotate($maxSize, $maxFiles);
            
            // 记录日志
            Logger::info('日志已轮换');
            
            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 获取安全数据
     *
     * @return array
     */
    protected function getSecureData()
    {
        $secureDataFile = $this->getSecureDataFilePath();
        
        if (!FileSystem::exists($secureDataFile)) {
            return [];
        }
        
        $encryptedData = FileSystem::read($secureDataFile);
        
        if (empty($encryptedData)) {
            return [];
        }
        
        return json_decode($encryptedData, true) ?: [];
    }
    
    /**
     * 保存安全数据到文件
     *
     * @param array $data
     * @return bool
     */
    protected function saveSecureDataToFile($data)
    {
        $secureDataFile = $this->getSecureDataFilePath();
        
        // 确保目录存在
        $dir = dirname($secureDataFile);
        if (!FileSystem::exists($dir)) {
            FileSystem::makeDirectory($dir, 0755, true);
        }
        
        return FileSystem::write($secureDataFile, json_encode($data));
    }
    
    /**
     * 获取安全数据文件路径
     *
     * @return string
     */
    protected function getSecureDataFilePath()
    {
        return runtime_path() . 'secure/secure_data.json';
    }
    
    /**
     * 加密数据
     *
     * @param string $data
     * @param string|null $password
     * @return string
     */
    protected function encrypt($data, $password = null)
    {
        if (empty($data)) {
            return '';
        }
        
        $password = $password ?: $this->getEncryptionKey();
        
        $method = 'aes-256-cbc';
        $ivlen = openssl_cipher_iv_length($method);
        $iv = openssl_random_pseudo_bytes($ivlen);
        
        $encrypted = openssl_encrypt($data, $method, $password, 0, $iv);
        
        if ($encrypted === false) {
            throw new \RuntimeException('加密失败：' . openssl_error_string());
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * 解密数据
     *
     * @param string $data
     * @param string|null $password
     * @return string
     */
    protected function decrypt($data, $password = null)
    {
        if (empty($data)) {
            return '';
        }
        
        $password = $password ?: $this->getEncryptionKey();
        
        $data = base64_decode($data);
        
        $method = 'aes-256-cbc';
        $ivlen = openssl_cipher_iv_length($method);
        $iv = substr($data, 0, $ivlen);
        $encrypted = substr($data, $ivlen);
        
        $decrypted = openssl_decrypt($encrypted, $method, $password, 0, $iv);
        
        if ($decrypted === false) {
            throw new \RuntimeException('解密失败：' . openssl_error_string());
        }
        
        return $decrypted;
    }
    
    /**
     * 获取加密密钥
     *
     * @return string
     */
    protected function getEncryptionKey()
    {
        $key = Config::get('native.encryption_key');
        
        if (empty($key)) {
            $key = 'nativephp-thinkphp-encryption-key';
        }
        
        return $key;
    }
}
