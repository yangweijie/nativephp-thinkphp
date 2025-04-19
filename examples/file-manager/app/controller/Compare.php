<?php

namespace app\controller;

use app\BaseController;
use app\service\CompareService;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Compare extends BaseController
{
    /**
     * 比较服务
     *
     * @var \app\service\CompareService
     */
    protected $compareService;
    
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\CompareService $compareService
     * @param \app\service\LogService $logService
     */
    public function __construct(CompareService $compareService, LogService $logService)
    {
        $this->compareService = $compareService;
        $this->logService = $logService;
    }
    
    /**
     * 比较文件选择页面
     *
     * @return \think\Response
     */
    public function index()
    {
        $file1 = input('file1', '');
        $file2 = input('file2', '');
        
        View::assign([
            'file1' => $file1,
            'file2' => $file2,
        ]);
        
        return view('compare/index');
    }
    
    /**
     * 比较两个文件
     *
     * @return \think\Response
     */
    public function compare()
    {
        $file1 = input('file1');
        $file2 = input('file2');
        
        if (empty($file1) || empty($file2)) {
            Notification::send('错误', '请选择两个要比较的文件');
            return redirect('/compare/index');
        }
        
        try {
            // 检查文件是否可比较
            if (!$this->compareService->isComparable($file1)) {
                Notification::send('错误', '第一个文件不可比较');
                return redirect('/compare/index?file1=' . urlencode($file1) . '&file2=' . urlencode($file2));
            }
            
            if (!$this->compareService->isComparable($file2)) {
                Notification::send('错误', '第二个文件不可比较');
                return redirect('/compare/index?file1=' . urlencode($file1) . '&file2=' . urlencode($file2));
            }
            
            $this->logService->info('比较文件', [
                'file1' => $file1,
                'file2' => $file2
            ]);
            
            // 比较文件
            $result = $this->compareService->compareFiles($file1, $file2);
            
            View::assign([
                'file1' => $result['file1'],
                'file2' => $result['file2'],
                'diff' => $result['diff'],
                'stats' => $result['stats'],
            ]);
            
            return view('compare/result');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('比较文件失败', [
                'file1' => $file1,
                'file2' => $file2,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);
            
            Notification::send('错误', $errorMessage);
            return redirect('/compare/index?file1=' . urlencode($file1) . '&file2=' . urlencode($file2));
        }
    }
    
    /**
     * 检查文件是否可比较
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
            $isComparable = $this->compareService->isComparable($path);
            
            return json(['success' => true, 'isComparable' => $isComparable]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
