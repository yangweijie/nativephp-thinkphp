<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;

class VersionService
{
    /**
     * 版本历史目录
     *
     * @var string
     */
    protected $versionsDir;
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $appDataPath = $this->getAppDataPath();
        $this->versionsDir = $appDataPath . DIRECTORY_SEPARATOR . 'versions';
        
        // 确保目录存在
        if (!is_dir($this->versionsDir)) {
            FileSystem::makeDirectory($this->versionsDir, 0755, true);
        }
    }
    
    /**
     * 获取应用数据目录
     *
     * @return string
     */
    protected function getAppDataPath()
    {
        $homePath = System::getHomePath();
        return $homePath . DIRECTORY_SEPARATOR . '.nativephp' . DIRECTORY_SEPARATOR . 'file-manager';
    }
    
    /**
     * 创建文件版本
     *
     * @param string $path 文件路径
     * @param string $comment 版本注释
     * @return array 版本信息
     * @throws \Exception 如果文件不存在或无法读取
     */
    public function createVersion($path, $comment = '')
    {
        // 检查文件是否存在
        if (!FileSystem::exists($path) || is_dir($path)) {
            throw new \Exception('文件不存在或不是一个有效的文件');
        }
        
        // 获取文件内容
        $content = FileSystem::read($path);
        
        // 生成版本ID
        $versionId = uniqid() . '_' . time();
        
        // 获取文件哈希
        $hash = md5($content);
        
        // 创建版本目录
        $fileId = $this->getFileId($path);
        $versionDir = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId;
        
        if (!is_dir($versionDir)) {
            FileSystem::makeDirectory($versionDir, 0755, true);
        }
        
        // 保存版本信息
        $versionInfo = [
            'id' => $versionId,
            'path' => $path,
            'hash' => $hash,
            'size' => FileSystem::size($path),
            'created' => time(),
            'comment' => $comment,
        ];
        
        $infoFile = $versionDir . DIRECTORY_SEPARATOR . $versionId . '.json';
        FileSystem::write($infoFile, json_encode($versionInfo, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        
        // 保存版本内容
        $contentFile = $versionDir . DIRECTORY_SEPARATOR . $versionId . '.content';
        FileSystem::write($contentFile, $content);
        
        return $versionInfo;
    }
    
    /**
     * 获取文件的所有版本
     *
     * @param string $path 文件路径
     * @return array 版本列表
     */
    public function getVersions($path)
    {
        $fileId = $this->getFileId($path);
        $versionDir = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId;
        
        if (!is_dir($versionDir)) {
            return [];
        }
        
        $versions = [];
        $files = scandir($versionDir);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || !preg_match('/\.json$/', $file)) {
                continue;
            }
            
            $infoFile = $versionDir . DIRECTORY_SEPARATOR . $file;
            $content = FileSystem::read($infoFile);
            $versionInfo = json_decode($content, true);
            
            if (is_array($versionInfo)) {
                $versions[] = $versionInfo;
            }
        }
        
        // 按创建时间降序排序
        usort($versions, function($a, $b) {
            return $b['created'] - $a['created'];
        });
        
        return $versions;
    }
    
    /**
     * 获取版本内容
     *
     * @param string $path 文件路径
     * @param string $versionId 版本ID
     * @return string 版本内容
     * @throws \Exception 如果版本不存在
     */
    public function getVersionContent($path, $versionId)
    {
        $fileId = $this->getFileId($path);
        $contentFile = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId . DIRECTORY_SEPARATOR . $versionId . '.content';
        
        if (!FileSystem::exists($contentFile)) {
            throw new \Exception('版本不存在');
        }
        
        return FileSystem::read($contentFile);
    }
    
    /**
     * 恢复版本
     *
     * @param string $path 文件路径
     * @param string $versionId 版本ID
     * @return bool 是否成功
     * @throws \Exception 如果版本不存在
     */
    public function restoreVersion($path, $versionId)
    {
        $fileId = $this->getFileId($path);
        $contentFile = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId . DIRECTORY_SEPARATOR . $versionId . '.content';
        
        if (!FileSystem::exists($contentFile)) {
            throw new \Exception('版本不存在');
        }
        
        // 先创建当前版本的备份
        $this->createVersion($path, '自动备份（恢复版本前）');
        
        // 恢复版本内容
        $content = FileSystem::read($contentFile);
        return FileSystem::write($path, $content);
    }
    
    /**
     * 删除版本
     *
     * @param string $path 文件路径
     * @param string $versionId 版本ID
     * @return bool 是否成功
     * @throws \Exception 如果版本不存在
     */
    public function deleteVersion($path, $versionId)
    {
        $fileId = $this->getFileId($path);
        $versionDir = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId;
        $infoFile = $versionDir . DIRECTORY_SEPARATOR . $versionId . '.json';
        $contentFile = $versionDir . DIRECTORY_SEPARATOR . $versionId . '.content';
        
        if (!FileSystem::exists($infoFile) || !FileSystem::exists($contentFile)) {
            throw new \Exception('版本不存在');
        }
        
        // 删除版本文件
        FileSystem::delete($infoFile);
        FileSystem::delete($contentFile);
        
        return true;
    }
    
    /**
     * 清除文件的所有版本
     *
     * @param string $path 文件路径
     * @return bool 是否成功
     */
    public function clearVersions($path)
    {
        $fileId = $this->getFileId($path);
        $versionDir = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId;
        
        if (!is_dir($versionDir)) {
            return true;
        }
        
        // 删除版本目录
        return FileSystem::deleteDirectory($versionDir);
    }
    
    /**
     * 获取文件ID
     *
     * @param string $path 文件路径
     * @return string 文件ID
     */
    protected function getFileId($path)
    {
        return md5($path);
    }
    
    /**
     * 比较两个版本
     *
     * @param string $path 文件路径
     * @param string $versionId1 版本1 ID
     * @param string $versionId2 版本2 ID
     * @return array 差异结果
     * @throws \Exception 如果版本不存在
     */
    public function compareVersions($path, $versionId1, $versionId2)
    {
        $fileId = $this->getFileId($path);
        $versionDir = $this->versionsDir . DIRECTORY_SEPARATOR . $fileId;
        
        $contentFile1 = $versionDir . DIRECTORY_SEPARATOR . $versionId1 . '.content';
        $contentFile2 = $versionDir . DIRECTORY_SEPARATOR . $versionId2 . '.content';
        
        if (!FileSystem::exists($contentFile1) || !FileSystem::exists($contentFile2)) {
            throw new \Exception('版本不存在');
        }
        
        $content1 = FileSystem::read($contentFile1);
        $content2 = FileSystem::read($contentFile2);
        
        // 使用 CompareService 进行比较
        $compareService = new CompareService();
        
        // 创建临时文件
        $tempFile1 = $this->versionsDir . DIRECTORY_SEPARATOR . 'temp_' . $versionId1;
        $tempFile2 = $this->versionsDir . DIRECTORY_SEPARATOR . 'temp_' . $versionId2;
        
        FileSystem::write($tempFile1, $content1);
        FileSystem::write($tempFile2, $content2);
        
        // 比较文件
        $result = $compareService->compareFiles($tempFile1, $tempFile2);
        
        // 删除临时文件
        FileSystem::delete($tempFile1);
        FileSystem::delete($tempFile2);
        
        return $result;
    }
    
    /**
     * 检查文件是否可以创建版本
     *
     * @param string $path 文件路径
     * @return bool 是否可以创建版本
     */
    public function canCreateVersion($path)
    {
        if (!FileSystem::exists($path) || is_dir($path)) {
            return false;
        }
        
        // 检查文件大小
        $maxSize = 10 * 1024 * 1024; // 10MB
        if (FileSystem::size($path) > $maxSize) {
            return false;
        }
        
        // 检查文件类型
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $binaryExtensions = [
            'exe', 'dll', 'so', 'dylib', 'bin', 'dat', 'db', 'sqlite', 'mdb',
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'ico', 'tif', 'tiff', 'webp',
            'mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a',
            'mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm',
            'zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'iso',
            'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
        ];
        
        if (in_array($extension, $binaryExtensions)) {
            return false;
        }
        
        return true;
    }
}
