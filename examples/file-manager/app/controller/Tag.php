<?php

namespace app\controller;

use app\BaseController;
use app\service\TagService;
use app\service\FileService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Tag extends BaseController
{
    /**
     * 标签服务
     *
     * @var \app\service\TagService
     */
    protected $tagService;
    
    /**
     * 文件服务
     *
     * @var \app\service\FileService
     */
    protected $fileService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\TagService $tagService
     * @param \app\service\FileService $fileService
     * @param \app\service\LogService $logService
     */
    public function __construct(TagService $tagService, FileService $fileService, LogService $logService)
    {
        $this->tagService = $tagService;
        $this->fileService = $fileService;
        $this->logService = $logService;
    }
    
    /**
     * 标签管理页面
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $tags = $this->tagService->getAllTags();
            
            View::assign([
                'tags' => $tags,
            ]);
            
            return view('tag/index');
        } catch (\Exception $e) {
            $this->logService->error('获取标签列表失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }
    
    /**
     * 添加标签
     *
     * @return \think\Response
     */
    public function add()
    {
        $name = input('name');
        $color = input('color', '#cccccc');
        
        if (empty($name)) {
            return json(['success' => false, 'message' => '标签名称不能为空']);
        }
        
        try {
            $this->logService->info('添加标签', [
                'name' => $name,
                'color' => $color
            ]);
            
            $tag = $this->tagService->addTag($name, $color);
            
            return json(['success' => true, 'tag' => $tag]);
        } catch (\Exception $e) {
            $this->logService->error('添加标签失败', [
                'name' => $name,
                'color' => $color,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 更新标签
     *
     * @return \think\Response
     */
    public function update()
    {
        $id = input('id/d');
        $name = input('name');
        $color = input('color');
        
        if (empty($id) || empty($name) || empty($color)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('更新标签', [
                'id' => $id,
                'name' => $name,
                'color' => $color
            ]);
            
            $result = $this->tagService->updateTag($id, $name, $color);
            
            if ($result) {
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '标签不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('更新标签失败', [
                'id' => $id,
                'name' => $name,
                'color' => $color,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除标签
     *
     * @return \think\Response
     */
    public function delete()
    {
        $id = input('id/d');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '标签ID不能为空']);
        }
        
        try {
            $this->logService->info('删除标签', [
                'id' => $id
            ]);
            
            $result = $this->tagService->deleteTag($id);
            
            if ($result) {
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '标签不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('删除标签失败', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 获取文件标签
     *
     * @return \think\Response
     */
    public function getFileTags()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            $tags = $this->tagService->getFileTags($path);
            
            return json(['success' => true, 'tags' => $tags]);
        } catch (\Exception $e) {
            $this->logService->error('获取文件标签失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 为文件添加标签
     *
     * @return \think\Response
     */
    public function addFileTag()
    {
        $path = input('path');
        $tagId = input('tagId/d');
        
        if (empty($path) || empty($tagId)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('为文件添加标签', [
                'path' => $path,
                'tagId' => $tagId
            ]);
            
            $result = $this->tagService->addFileTag($path, $tagId);
            
            if ($result) {
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '添加标签失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('为文件添加标签失败', [
                'path' => $path,
                'tagId' => $tagId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 移除文件标签
     *
     * @return \think\Response
     */
    public function removeFileTag()
    {
        $path = input('path');
        $tagId = input('tagId/d');
        
        if (empty($path) || empty($tagId)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('移除文件标签', [
                'path' => $path,
                'tagId' => $tagId
            ]);
            
            $result = $this->tagService->removeFileTag($path, $tagId);
            
            if ($result) {
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '移除标签失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('移除文件标签失败', [
                'path' => $path,
                'tagId' => $tagId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 清除文件的所有标签
     *
     * @return \think\Response
     */
    public function clearFileTags()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            $this->logService->info('清除文件标签', [
                'path' => $path
            ]);
            
            $result = $this->tagService->clearFileTags($path);
            
            if ($result) {
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '清除标签失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('清除文件标签失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 按标签查找文件
     *
     * @return \think\Response
     */
    public function findByTag()
    {
        $tagId = input('tagId/d');
        
        if (empty($tagId)) {
            return redirect('/tag/index');
        }
        
        try {
            // 获取标签信息
            $tags = $this->tagService->getAllTags();
            $currentTag = null;
            
            foreach ($tags as $tag) {
                if ($tag['id'] === $tagId) {
                    $currentTag = $tag;
                    break;
                }
            }
            
            if (!$currentTag) {
                Notification::send('错误', '标签不存在');
                return redirect('/tag/index');
            }
            
            // 获取带有此标签的文件
            $filePaths = $this->tagService->getFilesByTag($tagId);
            $files = [];
            
            foreach ($filePaths as $path) {
                if (file_exists($path)) {
                    $fileInfo = $this->fileService->getFileInfo($path);
                    if ($fileInfo) {
                        $files[] = $fileInfo;
                    }
                }
            }
            
            View::assign([
                'tag' => $currentTag,
                'files' => $files,
                'count' => count($files),
            ]);
            
            return view('tag/files');
        } catch (\Exception $e) {
            $this->logService->error('按标签查找文件失败', [
                'tagId' => $tagId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/tag/index');
        }
    }
    
    /**
     * 检查文件是否有标签
     *
     * @return \think\Response
     */
    public function hasFileTags()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            $hasTags = $this->tagService->hasFileTags($path);
            
            return json(['success' => true, 'hasTags' => $hasTags]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
