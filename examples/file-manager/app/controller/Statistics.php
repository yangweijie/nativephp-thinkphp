<?php

namespace app\controller;

use app\BaseController;
use app\service\StatisticsService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Statistics extends BaseController
{
    /**
     * 统计服务
     *
     * @var \app\service\StatisticsService
     */
    protected $statisticsService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\StatisticsService $statisticsService
     * @param \app\service\LogService $logService
     */
    public function __construct(StatisticsService $statisticsService, LogService $logService)
    {
        $this->statisticsService = $statisticsService;
        $this->logService = $logService;
    }
    
    /**
     * 目录统计页面
     *
     * @return \think\Response
     */
    public function index()
    {
        $path = input('path');
        $recursive = input('recursive/b', true);
        $maxDepth = input('maxDepth/d', 10);
        
        if (empty($path)) {
            Notification::send('错误', '目录路径不能为空');
            return redirect('/file/index');
        }
        
        if (!is_dir($path)) {
            Notification::send('错误', '路径不是一个有效的目录');
            return redirect('/file/index');
        }
        
        try {
            $this->logService->info('获取目录统计', [
                'path' => $path,
                'recursive' => $recursive,
                'maxDepth' => $maxDepth
            ]);
            
            // 获取统计信息
            $stats = $this->statisticsService->getDirectoryStatistics($path, $recursive, $maxDepth);
            
            // 获取文件类型名称和颜色
            $fileTypeNames = [];
            $fileTypeColors = [];
            
            foreach (array_keys($stats['fileTypes']) as $type) {
                $fileTypeNames[$type] = $this->statisticsService->getFileTypeName($type);
                $fileTypeColors[$type] = $this->statisticsService->getFileTypeColor($type);
            }
            
            View::assign([
                'path' => $path,
                'recursive' => $recursive,
                'maxDepth' => $maxDepth,
                'stats' => $stats,
                'fileTypeNames' => $fileTypeNames,
                'fileTypeColors' => $fileTypeColors,
            ]);
            
            return view('statistics/index');
        } catch (\Exception $e) {
            $this->logService->error('获取目录统计失败', [
                'path' => $path,
                'recursive' => $recursive,
                'maxDepth' => $maxDepth,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index?path=' . urlencode($path));
        }
    }
    
    /**
     * 获取目录统计数据（AJAX）
     *
     * @return \think\Response
     */
    public function getData()
    {
        $path = input('path');
        $recursive = input('recursive/b', true);
        $maxDepth = input('maxDepth/d', 10);
        
        if (empty($path)) {
            return json(['success' => false, 'message' => '目录路径不能为空']);
        }
        
        if (!is_dir($path)) {
            return json(['success' => false, 'message' => '路径不是一个有效的目录']);
        }
        
        try {
            $this->logService->info('获取目录统计数据', [
                'path' => $path,
                'recursive' => $recursive,
                'maxDepth' => $maxDepth
            ]);
            
            // 获取统计信息
            $stats = $this->statisticsService->getDirectoryStatistics($path, $recursive, $maxDepth);
            
            // 获取文件类型名称和颜色
            $fileTypeNames = [];
            $fileTypeColors = [];
            
            foreach (array_keys($stats['fileTypes']) as $type) {
                $fileTypeNames[$type] = $this->statisticsService->getFileTypeName($type);
                $fileTypeColors[$type] = $this->statisticsService->getFileTypeColor($type);
            }
            
            return json([
                'success' => true,
                'stats' => $stats,
                'fileTypeNames' => $fileTypeNames,
                'fileTypeColors' => $fileTypeColors,
            ]);
        } catch (\Exception $e) {
            $this->logService->error('获取目录统计数据失败', [
                'path' => $path,
                'recursive' => $recursive,
                'maxDepth' => $maxDepth,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
