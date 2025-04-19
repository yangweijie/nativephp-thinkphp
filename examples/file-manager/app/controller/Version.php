<?php

namespace app\controller;

use app\BaseController;
use app\service\VersionService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Version extends BaseController
{
    /**
     * 版本服务
     *
     * @var \app\service\VersionService
     */
    protected $versionService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\VersionService $versionService
     * @param \app\service\LogService $logService
     */
    public function __construct(VersionService $versionService, LogService $logService)
    {
        $this->versionService = $versionService;
        $this->logService = $logService;
    }
    
    /**
     * 版本历史列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $path = input('path');
        
        if (empty($path)) {
            Notification::send('错误', '文件路径不能为空');
            return redirect('/file/index');
        }
        
        try {
            // 检查文件是否可以创建版本
            $canCreateVersion = $this->versionService->canCreateVersion($path);
            
            // 获取版本列表
            $versions = $this->versionService->getVersions($path);
            
            View::assign([
                'path' => $path,
                'versions' => $versions,
                'canCreateVersion' => $canCreateVersion,
                'filename' => basename($path),
            ]);
            
            return view('version/index');
        } catch (\Exception $e) {
            $this->logService->error('获取版本历史失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }
    
    /**
     * 创建版本
     *
     * @return \think\Response
     */
    public function create()
    {
        $path = input('path');
        $comment = input('comment', '');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            // 检查文件是否可以创建版本
            if (!$this->versionService->canCreateVersion($path)) {
                return json(['success' => false, 'message' => '此文件不支持创建版本']);
            }
            
            $this->logService->info('创建文件版本', [
                'path' => $path,
                'comment' => $comment
            ]);
            
            $versionInfo = $this->versionService->createVersion($path, $comment);
            
            return json(['success' => true, 'version' => $versionInfo]);
        } catch (\Exception $e) {
            $this->logService->error('创建文件版本失败', [
                'path' => $path,
                'comment' => $comment,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 查看版本内容
     *
     * @return \think\Response
     */
    public function view()
    {
        $path = input('path');
        $versionId = input('version');
        
        if (empty($path) || empty($versionId)) {
            Notification::send('错误', '参数错误');
            return redirect('/version/index?path=' . urlencode($path));
        }
        
        try {
            $this->logService->info('查看文件版本', [
                'path' => $path,
                'version' => $versionId
            ]);
            
            // 获取版本内容
            $content = $this->versionService->getVersionContent($path, $versionId);
            
            // 获取版本列表
            $versions = $this->versionService->getVersions($path);
            
            // 查找当前版本信息
            $currentVersion = null;
            foreach ($versions as $version) {
                if ($version['id'] === $versionId) {
                    $currentVersion = $version;
                    break;
                }
            }
            
            if (!$currentVersion) {
                throw new \Exception('版本不存在');
            }
            
            View::assign([
                'path' => $path,
                'content' => $content,
                'version' => $currentVersion,
                'filename' => basename($path),
                'extension' => pathinfo($path, PATHINFO_EXTENSION),
            ]);
            
            return view('version/view');
        } catch (\Exception $e) {
            $this->logService->error('查看文件版本失败', [
                'path' => $path,
                'version' => $versionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/version/index?path=' . urlencode($path));
        }
    }
    
    /**
     * 恢复版本
     *
     * @return \think\Response
     */
    public function restore()
    {
        $path = input('path');
        $versionId = input('version');
        
        if (empty($path) || empty($versionId)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('恢复文件版本', [
                'path' => $path,
                'version' => $versionId
            ]);
            
            $result = $this->versionService->restoreVersion($path, $versionId);
            
            if ($result) {
                Notification::send('恢复成功', '文件已恢复到选定的版本');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '恢复失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('恢复文件版本失败', [
                'path' => $path,
                'version' => $versionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 删除版本
     *
     * @return \think\Response
     */
    public function delete()
    {
        $path = input('path');
        $versionId = input('version');
        
        if (empty($path) || empty($versionId)) {
            return json(['success' => false, 'message' => '参数错误']);
        }
        
        try {
            $this->logService->info('删除文件版本', [
                'path' => $path,
                'version' => $versionId
            ]);
            
            $result = $this->versionService->deleteVersion($path, $versionId);
            
            if ($result) {
                Notification::send('删除成功', '文件版本已删除');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '删除失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('删除文件版本失败', [
                'path' => $path,
                'version' => $versionId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 清除所有版本
     *
     * @return \think\Response
     */
    public function clear()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            $this->logService->info('清除文件所有版本', [
                'path' => $path
            ]);
            
            $result = $this->versionService->clearVersions($path);
            
            if ($result) {
                Notification::send('清除成功', '文件的所有版本已清除');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '清除失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('清除文件所有版本失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 比较版本
     *
     * @return \think\Response
     */
    public function compare()
    {
        $path = input('path');
        $version1 = input('version1');
        $version2 = input('version2');
        
        if (empty($path) || empty($version1) || empty($version2)) {
            Notification::send('错误', '参数错误');
            return redirect('/version/index?path=' . urlencode($path));
        }
        
        try {
            $this->logService->info('比较文件版本', [
                'path' => $path,
                'version1' => $version1,
                'version2' => $version2
            ]);
            
            // 获取版本列表
            $versions = $this->versionService->getVersions($path);
            
            // 查找版本信息
            $versionInfo1 = null;
            $versionInfo2 = null;
            
            foreach ($versions as $version) {
                if ($version['id'] === $version1) {
                    $versionInfo1 = $version;
                }
                if ($version['id'] === $version2) {
                    $versionInfo2 = $version;
                }
            }
            
            if (!$versionInfo1 || !$versionInfo2) {
                throw new \Exception('版本不存在');
            }
            
            // 比较版本
            $result = $this->versionService->compareVersions($path, $version1, $version2);
            
            View::assign([
                'path' => $path,
                'version1' => $versionInfo1,
                'version2' => $versionInfo2,
                'diff' => $result['diff'],
                'stats' => $result['stats'],
                'filename' => basename($path),
            ]);
            
            return view('version/compare');
        } catch (\Exception $e) {
            $this->logService->error('比较文件版本失败', [
                'path' => $path,
                'version1' => $version1,
                'version2' => $version2,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/version/index?path=' . urlencode($path));
        }
    }
    
    /**
     * 检查文件是否可以创建版本
     *
     * @return \think\Response
     */
    public function check()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }
        
        try {
            $canCreateVersion = $this->versionService->canCreateVersion($path);
            
            return json(['success' => true, 'canCreateVersion' => $canCreateVersion]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
