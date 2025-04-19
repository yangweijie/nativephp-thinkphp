<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;

class FavoriteService
{
    /**
     * 收藏夹文件路径
     *
     * @var string
     */
    protected $favoritesFile;
    
    /**
     * 收藏夹数据
     *
     * @var array
     */
    protected $favorites = [];
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $appDataPath = $this->getAppDataPath();
        $this->favoritesFile = $appDataPath . DIRECTORY_SEPARATOR . 'favorites.json';
        
        // 确保目录存在
        if (!is_dir($appDataPath)) {
            FileSystem::makeDirectory($appDataPath, 0755, true);
        }
        
        // 加载收藏夹数据
        $this->loadFavorites();
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
     * 加载收藏夹数据
     *
     * @return void
     */
    protected function loadFavorites()
    {
        if (FileSystem::exists($this->favoritesFile)) {
            $content = FileSystem::read($this->favoritesFile);
            $data = json_decode($content, true);
            
            if (is_array($data)) {
                $this->favorites = $data;
            }
        } else {
            // 创建默认收藏夹
            $this->favorites = [
                [
                    'name' => '主目录',
                    'path' => System::getHomePath(),
                    'type' => 'directory',
                    'icon' => 'home',
                ],
                [
                    'name' => '文档',
                    'path' => System::getHomePath() . DIRECTORY_SEPARATOR . 'Documents',
                    'type' => 'directory',
                    'icon' => 'folder',
                ],
                [
                    'name' => '下载',
                    'path' => System::getHomePath() . DIRECTORY_SEPARATOR . 'Downloads',
                    'type' => 'directory',
                    'icon' => 'folder',
                ],
                [
                    'name' => '图片',
                    'path' => System::getHomePath() . DIRECTORY_SEPARATOR . 'Pictures',
                    'type' => 'directory',
                    'icon' => 'folder',
                ],
            ];
            
            $this->saveFavorites();
        }
    }
    
    /**
     * 保存收藏夹数据
     *
     * @return bool
     */
    protected function saveFavorites()
    {
        $content = json_encode($this->favorites, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return FileSystem::write($this->favoritesFile, $content);
    }
    
    /**
     * 获取所有收藏夹
     *
     * @return array
     */
    public function getAll()
    {
        // 检查路径是否存在
        foreach ($this->favorites as $key => $favorite) {
            $this->favorites[$key]['exists'] = FileSystem::exists($favorite['path']);
        }
        
        return $this->favorites;
    }
    
    /**
     * 添加收藏
     *
     * @param string $path 路径
     * @param string $name 名称（可选，默认使用路径的basename）
     * @return bool
     */
    public function add($path, $name = null)
    {
        if (!FileSystem::exists($path)) {
            return false;
        }
        
        // 检查是否已经收藏
        foreach ($this->favorites as $favorite) {
            if ($favorite['path'] === $path) {
                return true; // 已经收藏过了
            }
        }
        
        // 确定名称
        if (empty($name)) {
            $name = basename($path);
        }
        
        // 确定类型和图标
        $isDir = is_dir($path);
        $type = $isDir ? 'directory' : 'file';
        $icon = $isDir ? 'folder' : 'file';
        
        // 添加到收藏夹
        $this->favorites[] = [
            'name' => $name,
            'path' => $path,
            'type' => $type,
            'icon' => $icon,
            'exists' => true,
        ];
        
        return $this->saveFavorites();
    }
    
    /**
     * 删除收藏
     *
     * @param string $path 路径
     * @return bool
     */
    public function remove($path)
    {
        foreach ($this->favorites as $key => $favorite) {
            if ($favorite['path'] === $path) {
                unset($this->favorites[$key]);
                $this->favorites = array_values($this->favorites); // 重新索引
                return $this->saveFavorites();
            }
        }
        
        return false;
    }
    
    /**
     * 重命名收藏
     *
     * @param string $path 路径
     * @param string $newName 新名称
     * @return bool
     */
    public function rename($path, $newName)
    {
        foreach ($this->favorites as $key => $favorite) {
            if ($favorite['path'] === $path) {
                $this->favorites[$key]['name'] = $newName;
                return $this->saveFavorites();
            }
        }
        
        return false;
    }
    
    /**
     * 更新收藏路径
     *
     * @param string $oldPath 旧路径
     * @param string $newPath 新路径
     * @return bool
     */
    public function updatePath($oldPath, $newPath)
    {
        foreach ($this->favorites as $key => $favorite) {
            if ($favorite['path'] === $oldPath) {
                $this->favorites[$key]['path'] = $newPath;
                $this->favorites[$key]['exists'] = FileSystem::exists($newPath);
                return $this->saveFavorites();
            }
        }
        
        return false;
    }
    
    /**
     * 检查路径是否已收藏
     *
     * @param string $path 路径
     * @return bool
     */
    public function isFavorite($path)
    {
        foreach ($this->favorites as $favorite) {
            if ($favorite['path'] === $path) {
                return true;
            }
        }
        
        return false;
    }
}
