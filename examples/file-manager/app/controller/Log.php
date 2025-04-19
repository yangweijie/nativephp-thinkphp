<?php

namespace app\controller;

use app\BaseController;
use app\service\LogService;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Log extends BaseController
{
    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;
    
    /**
     * 构造函数
     *
     * @param \app\service\LogService $logService
     */
    public function __construct(LogService $logService)
    {
        $this->logService = $logService;
    }
    
    /**
     * 显示日志
     *
     * @return \think\Response
     */
    public function index()
    {
        try {
            $lines = input('lines', 100);
            $logContent = $this->logService->get($lines);
            $logFile = $this->logService->getLogFile();
            
            View::assign([
                'logContent' => $logContent,
                'logFile' => $logFile,
                'lines' => $lines,
            ]);
            
            return view('log/index');
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }
    
    /**
     * 清除日志
     *
     * @return \think\Response
     */
    public function clear()
    {
        try {
            $result = $this->logService->clear();
            
            if ($result) {
                Notification::send('成功', '日志已清除');
            } else {
                Notification::send('错误', '清除日志失败');
            }
            
            return redirect('/log/index');
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/log/index');
        }
    }
    
    /**
     * 下载日志
     *
     * @return \think\Response
     */
    public function download()
    {
        try {
            $logFile = $this->logService->getLogFile();
            $logContent = $this->logService->get();
            
            return download($logContent, 'file_manager.log', true);
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/log/index');
        }
    }
    
    /**
     * 启用日志
     *
     * @return \think\Response
     */
    public function enable()
    {
        try {
            $this->logService->enable();
            Notification::send('成功', '日志已启用');
            
            return redirect('/log/index');
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/log/index');
        }
    }
    
    /**
     * 禁用日志
     *
     * @return \think\Response
     */
    public function disable()
    {
        try {
            $this->logService->disable();
            Notification::send('成功', '日志已禁用');
            
            return redirect('/log/index');
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/log/index');
        }
    }
}
