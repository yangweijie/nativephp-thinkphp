<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Filesystem;
use think\facade\Config;
use think\facade\Request;

class DragDrop extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取上传文件列表
        $uploadDir = public_path() . 'uploads';
        $files = [];
        
        if (is_dir($uploadDir)) {
            $fileList = scandir($uploadDir);
            
            foreach ($fileList as $file) {
                if ($file != '.' && $file != '..') {
                    $filePath = $uploadDir . DIRECTORY_SEPARATOR . $file;
                    
                    $files[] = [
                        'name' => $file,
                        'path' => '/uploads/' . $file,
                        'size' => filesize($filePath),
                        'type' => mime_content_type($filePath),
                        'modified' => date('Y-m-d H:i:s', filemtime($filePath)),
                    ];
                }
            }
        }
        
        return View::fetch('drag_drop/index', [
            'files' => $files,
        ]);
    }
    
    /**
     * 处理文件上传
     *
     * @return \think\Response
     */
    public function upload()
    {
        // 获取上传文件
        $files = request()->file();
        
        if (empty($files)) {
            return json(['success' => false, 'message' => '没有上传文件']);
        }
        
        $uploadedFiles = [];
        $errors = [];
        
        // 处理每个上传的文件
        foreach ($files as $file) {
            try {
                // 验证文件
                validate(['file' => 'fileSize:102400|fileExt:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,txt,zip,rar'])
                    ->check(['file' => $file]);
                
                // 保存文件
                $saveName = Filesystem::disk('public')->putFile('uploads', $file);
                
                // 获取文件信息
                $fileInfo = [
                    'name' => $file->getOriginalName(),
                    'path' => '/storage/' . $saveName,
                    'size' => $file->getSize(),
                    'type' => $file->getMime(),
                ];
                
                $uploadedFiles[] = $fileInfo;
            } catch (\Exception $e) {
                $errors[] = $file->getOriginalName() . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            return json(['success' => false, 'message' => implode(', ', $errors)]);
        }
        
        return json(['success' => true, 'message' => '文件上传成功', 'files' => $uploadedFiles]);
    }
    
    /**
     * 处理拖放文件上传
     *
     * @return \think\Response
     */
    public function dropUpload()
    {
        // 获取上传的文件路径
        $filePaths = input('file_paths/a', []);
        
        if (empty($filePaths)) {
            return json(['success' => false, 'message' => '没有上传文件']);
        }
        
        $uploadDir = public_path() . 'uploads';
        
        // 确保上传目录存在
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadedFiles = [];
        $errors = [];
        
        // 处理每个文件
        foreach ($filePaths as $filePath) {
            try {
                // 获取文件名
                $fileName = basename($filePath);
                
                // 生成唯一的文件名
                $saveName = uniqid() . '_' . $fileName;
                $saveFilePath = $uploadDir . DIRECTORY_SEPARATOR . $saveName;
                
                // 复制文件
                if (copy($filePath, $saveFilePath)) {
                    // 获取文件信息
                    $fileInfo = [
                        'name' => $fileName,
                        'path' => '/uploads/' . $saveName,
                        'size' => filesize($saveFilePath),
                        'type' => mime_content_type($saveFilePath),
                        'modified' => date('Y-m-d H:i:s', filemtime($saveFilePath)),
                    ];
                    
                    $uploadedFiles[] = $fileInfo;
                } else {
                    $errors[] = $fileName . ': 复制文件失败';
                }
            } catch (\Exception $e) {
                $errors[] = $fileName . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            return json(['success' => false, 'message' => implode(', ', $errors)]);
        }
        
        return json(['success' => true, 'message' => '文件上传成功', 'files' => $uploadedFiles]);
    }
    
    /**
     * 删除文件
     *
     * @return \think\Response
     */
    public function delete()
    {
        $filePath = input('file_path');
        
        if (empty($filePath)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        // 确保文件路径在上传目录中
        $uploadDir = public_path() . 'uploads';
        $realFilePath = $uploadDir . DIRECTORY_SEPARATOR . basename($filePath);
        
        if (!file_exists($realFilePath)) {
            return json(['success' => false, 'message' => '文件不存在']);
        }
        
        // 删除文件
        if (unlink($realFilePath)) {
            return json(['success' => true, 'message' => '文件删除成功']);
        } else {
            return json(['success' => false, 'message' => '文件删除失败']);
        }
    }
}
