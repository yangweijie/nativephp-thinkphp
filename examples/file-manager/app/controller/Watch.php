<?php

namespace app\controller;

use app\BaseController;
use app\service\WatchService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Watch extends BaseController
{
    /**
     * 监视服务
     *
     * @var \app\service\WatchService
     */
    protected $watchService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\WatchService $watchService
     * @param \app\service\LogService $logService
     */
    public function __construct(WatchService $watchService, LogService $logService)
    {
        $this->watchService = $watchService;
        $this->logService = $logService;
    }
    
    /**
     * 监视设置页面
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $config = $this->watchService->getConfig();
            $watchPaths = $this->watchService->getWatchPaths();
            
            View::assign([
                'config' => $config,
                'watchPaths' => $watchPaths,
            ]);
            
            return view('watch/index');
        } catch (\Exception $e) {
            $this->logService->error('获取监视设置失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }
    
    /**
     * 添加监视路径
     *
     * @return \think\Response
     */
    public function add()
    {
        $path = input('path');
        $recursive = input('recursive', false);
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $this->logService->info('添加监视路径', [
                'path' => $path,
                'recursive' => $recursive
            ]);
            
            $result = $this->watchService->addWatchPath($path, $recursive);
            
            if ($result) {
                Notification::send('添加成功', '已添加监视路径');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '添加失败，路径不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('添加监视路径失败', [
                'path' => $path,
                'recursive' => $recursive,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 移除监视路径
     *
     * @return \think\Response
     */
    public function remove()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $this->logService->info('移除监视路径', [
                'path' => $path
            ]);
            
            $result = $this->watchService->removeWatchPath($path);
            
            if ($result) {
                Notification::send('移除成功', '已移除监视路径');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '移除失败，路径不存在']);
            }
        } catch (\Exception $e) {
            $this->logService->error('移除监视路径失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 启用监视
     *
     * @return \think\Response
     */
    public function enable()
    {
        try {
            $this->logService->info('启用监视');
            
            $result = $this->watchService->enable();
            
            if ($result) {
                Notification::send('启用成功', '已启用文件监视');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '启用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('启用监视失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 禁用监视
     *
     * @return \think\Response
     */
    public function disable()
    {
        try {
            $this->logService->info('禁用监视');
            
            $result = $this->watchService->disable();
            
            if ($result) {
                Notification::send('禁用成功', '已禁用文件监视');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '禁用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('禁用监视失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 启用通知
     *
     * @return \think\Response
     */
    public function enableNotify()
    {
        try {
            $this->logService->info('启用通知');
            
            $result = $this->watchService->enableNotify();
            
            if ($result) {
                Notification::send('启用成功', '已启用文件变化通知');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '启用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('启用通知失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 禁用通知
     *
     * @return \think\Response
     */
    public function disableNotify()
    {
        try {
            $this->logService->info('禁用通知');
            
            $result = $this->watchService->disableNotify();
            
            if ($result) {
                Notification::send('禁用成功', '已禁用文件变化通知');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '禁用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('禁用通知失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 启用自动刷新
     *
     * @return \think\Response
     */
    public function enableAutoRefresh()
    {
        try {
            $this->logService->info('启用自动刷新');
            
            $result = $this->watchService->enableAutoRefresh();
            
            if ($result) {
                Notification::send('启用成功', '已启用自动刷新');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '启用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('启用自动刷新失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 禁用自动刷新
     *
     * @return \think\Response
     */
    public function disableAutoRefresh()
    {
        try {
            $this->logService->info('禁用自动刷新');
            
            $result = $this->watchService->disableAutoRefresh();
            
            if ($result) {
                Notification::send('禁用成功', '已禁用自动刷新');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '禁用失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('禁用自动刷新失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    /**
     * 检查路径是否被监视
     *
     * @return \think\Response
     */
    public function check()
    {
        $path = input('path');
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }
        
        try {
            $isWatched = $this->watchService->isWatched($path);
            
            return json(['success' => true, 'isWatched' => $isWatched]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
