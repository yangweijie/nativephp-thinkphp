<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\System;

class TagService
{
    /**
     * 标签数据文件路径
     *
     * @var string
     */
    protected $tagsFile;
    
    /**
     * 标签数据
     *
     * @var array
     */
    protected $tags = [];
    
    /**
     * 构造函数
     */
    public function __construct()
    {
        $appDataPath = $this->getAppDataPath();
        $this->tagsFile = $appDataPath . DIRECTORY_SEPARATOR . 'tags.json';
        
        // 确保目录存在
        if (!is_dir($appDataPath)) {
            FileSystem::makeDirectory($appDataPath, 0755, true);
        }
        
        // 加载标签数据
        $this->loadTags();
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
     * 加载标签数据
     *
     * @return void
     */
    protected function loadTags()
    {
        if (FileSystem::exists($this->tagsFile)) {
            $content = FileSystem::read($this->tagsFile);
            $data = json_decode($content, true);
            
            if (is_array($data)) {
                $this->tags = $data;
            }
        } else {
            // 创建默认标签
            $this->tags = [
                'tags' => [
                    [
                        'id' => 1,
                        'name' => '重要',
                        'color' => '#ff0000',
                        'created' => time(),
                    ],
                    [
                        'id' => 2,
                        'name' => '工作',
                        'color' => '#0000ff',
                        'created' => time(),
                    ],
                    [
                        'id' => 3,
                        'name' => '个人',
                        'color' => '#00ff00',
                        'created' => time(),
                    ],
                ],
                'fileTags' => [],
                'nextId' => 4,
            ];
            
            $this->saveTags();
        }
    }
    
    /**
     * 保存标签数据
     *
     * @return bool
     */
    protected function saveTags()
    {
        $content = json_encode($this->tags, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        return FileSystem::write($this->tagsFile, $content);
    }
    
    /**
     * 获取所有标签
     *
     * @return array
     */
    public function getAllTags()
    {
        return $this->tags['tags'];
    }
    
    /**
     * 获取文件的标签
     *
     * @param string $path 文件路径
     * @return array
     */
    public function getFileTags($path)
    {
        $result = [];
        
        foreach ($this->tags['fileTags'] as $fileTag) {
            if ($fileTag['path'] === $path) {
                // 查找标签详情
                foreach ($this->tags['tags'] as $tag) {
                    if ($tag['id'] === $fileTag['tagId']) {
                        $result[] = $tag;
                        break;
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * 添加标签
     *
     * @param string $name 标签名称
     * @param string $color 标签颜色
     * @return array 新标签
     */
    public function addTag($name, $color = '#cccccc')
    {
        // 检查标签名称是否已存在
        foreach ($this->tags['tags'] as $tag) {
            if ($tag['name'] === $name) {
                throw new \Exception('标签名称已存在');
            }
        }
        
        // 创建新标签
        $newTag = [
            'id' => $this->tags['nextId'],
            'name' => $name,
            'color' => $color,
            'created' => time(),
        ];
        
        $this->tags['tags'][] = $newTag;
        $this->tags['nextId']++;
        
        $this->saveTags();
        
        return $newTag;
    }
    
    /**
     * 更新标签
     *
     * @param int $id 标签ID
     * @param string $name 标签名称
     * @param string $color 标签颜色
     * @return bool
     */
    public function updateTag($id, $name, $color)
    {
        // 检查标签名称是否已存在（排除当前标签）
        foreach ($this->tags['tags'] as $tag) {
            if ($tag['name'] === $name && $tag['id'] !== $id) {
                throw new \Exception('标签名称已存在');
            }
        }
        
        // 更新标签
        foreach ($this->tags['tags'] as &$tag) {
            if ($tag['id'] === $id) {
                $tag['name'] = $name;
                $tag['color'] = $color;
                $this->saveTags();
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 删除标签
     *
     * @param int $id 标签ID
     * @return bool
     */
    public function deleteTag($id)
    {
        // 删除标签
        foreach ($this->tags['tags'] as $key => $tag) {
            if ($tag['id'] === $id) {
                unset($this->tags['tags'][$key]);
                $this->tags['tags'] = array_values($this->tags['tags']);
                
                // 删除相关的文件标签
                foreach ($this->tags['fileTags'] as $fileTagKey => $fileTag) {
                    if ($fileTag['tagId'] === $id) {
                        unset($this->tags['fileTags'][$fileTagKey]);
                    }
                }
                
                $this->tags['fileTags'] = array_values($this->tags['fileTags']);
                
                $this->saveTags();
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 为文件添加标签
     *
     * @param string $path 文件路径
     * @param int $tagId 标签ID
     * @return bool
     */
    public function addFileTag($path, $tagId)
    {
        // 检查标签是否存在
        $tagExists = false;
        foreach ($this->tags['tags'] as $tag) {
            if ($tag['id'] === $tagId) {
                $tagExists = true;
                break;
            }
        }
        
        if (!$tagExists) {
            throw new \Exception('标签不存在');
        }
        
        // 检查文件标签是否已存在
        foreach ($this->tags['fileTags'] as $fileTag) {
            if ($fileTag['path'] === $path && $fileTag['tagId'] === $tagId) {
                return true; // 已经添加过了
            }
        }
        
        // 添加文件标签
        $this->tags['fileTags'][] = [
            'path' => $path,
            'tagId' => $tagId,
            'added' => time(),
        ];
        
        return $this->saveTags();
    }
    
    /**
     * 移除文件标签
     *
     * @param string $path 文件路径
     * @param int $tagId 标签ID
     * @return bool
     */
    public function removeFileTag($path, $tagId)
    {
        foreach ($this->tags['fileTags'] as $key => $fileTag) {
            if ($fileTag['path'] === $path && $fileTag['tagId'] === $tagId) {
                unset($this->tags['fileTags'][$key]);
                $this->tags['fileTags'] = array_values($this->tags['fileTags']);
                return $this->saveTags();
            }
        }
        
        return false;
    }
    
    /**
     * 清除文件的所有标签
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function clearFileTags($path)
    {
        $changed = false;
        
        foreach ($this->tags['fileTags'] as $key => $fileTag) {
            if ($fileTag['path'] === $path) {
                unset($this->tags['fileTags'][$key]);
                $changed = true;
            }
        }
        
        if ($changed) {
            $this->tags['fileTags'] = array_values($this->tags['fileTags']);
            return $this->saveTags();
        }
        
        return true;
    }
    
    /**
     * 获取带有指定标签的文件
     *
     * @param int $tagId 标签ID
     * @return array
     */
    public function getFilesByTag($tagId)
    {
        $result = [];
        
        foreach ($this->tags['fileTags'] as $fileTag) {
            if ($fileTag['tagId'] === $tagId) {
                $result[] = $fileTag['path'];
            }
        }
        
        return $result;
    }
    
    /**
     * 检查文件是否有标签
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function hasFileTags($path)
    {
        foreach ($this->tags['fileTags'] as $fileTag) {
            if ($fileTag['path'] === $path) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 检查文件是否有指定标签
     *
     * @param string $path 文件路径
     * @param int $tagId 标签ID
     * @return bool
     */
    public function hasFileTag($path, $tagId)
    {
        foreach ($this->tags['fileTags'] as $fileTag) {
            if ($fileTag['path'] === $path && $fileTag['tagId'] === $tagId) {
                return true;
            }
        }
        
        return false;
    }
}
