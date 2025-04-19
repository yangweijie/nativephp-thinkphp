<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\Notification;

class ChildProcessController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('child_process/index');
    }
    
    /**
     * 获取所有子进程
     *
     * @return \think\Response
     */
    public function all()
    {
        $processes = ChildProcess::all();
        return json(['processes' => $processes]);
    }
    
    /**
     * 启动命令子进程
     *
     * @return \think\Response
     */
    public function startCommand()
    {
        $command = Request::param('command');
        $alias = Request::param('alias', 'cmd-' . time());
        $persistent = Request::param('persistent', false);
        
        if (empty($command)) {
            return json(['success' => false, 'message' => '命令不能为空']);
        }
        
        try {
            // 启动子进程
            ChildProcess::start($command, $alias, null, $persistent);
            
            // 发送通知
            Notification::send('子进程启动', "命令 '{$command}' 已启动，别名: {$alias}");
            
            return json(['success' => true, 'message' => '子进程已启动', 'alias' => $alias]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '启动子进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 启动PHP脚本子进程
     *
     * @return \think\Response
     */
    public function startPhpScript()
    {
        $script = Request::param('script');
        $alias = Request::param('alias', 'php-' . time());
        $args = Request::param('args', []);
        $persistent = Request::param('persistent', false);
        
        if (empty($script)) {
            return json(['success' => false, 'message' => '脚本不能为空']);
        }
        
        try {
            // 如果脚本是相对路径，转换为绝对路径
            if (!file_exists($script) && !str_starts_with($script, '/')) {
                $script = app()->getRootPath() . $script;
            }
            
            // 启动PHP脚本
            ChildProcess::php($script, $alias, $args, null, $persistent);
            
            // 发送通知
            Notification::send('PHP脚本启动', "脚本 '{$script}' 已启动，别名: {$alias}");
            
            return json(['success' => true, 'message' => 'PHP脚本已启动', 'alias' => $alias]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '启动PHP脚本失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 启动ThinkPHP命令子进程
     *
     * @return \think\Response
     */
    public function startThinkCommand()
    {
        $command = Request::param('command');
        $alias = Request::param('alias', 'think-' . time());
        $args = Request::param('args', []);
        $persistent = Request::param('persistent', false);
        
        if (empty($command)) {
            return json(['success' => false, 'message' => '命令不能为空']);
        }
        
        try {
            // 启动ThinkPHP命令
            ChildProcess::artisan($command, $alias, $args, null, $persistent);
            
            // 发送通知
            Notification::send('ThinkPHP命令启动', "命令 '{$command}' 已启动，别名: {$alias}");
            
            return json(['success' => true, 'message' => 'ThinkPHP命令已启动', 'alias' => $alias]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '启动ThinkPHP命令失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 停止子进程
     *
     * @return \think\Response
     */
    public function stop()
    {
        $alias = Request::param('alias');
        
        if (empty($alias)) {
            return json(['success' => false, 'message' => '别名不能为空']);
        }
        
        try {
            // 检查子进程是否存在
            if (!ChildProcess::exists($alias)) {
                return json(['success' => false, 'message' => "子进程 '{$alias}' 不存在"]);
            }
            
            // 停止子进程
            $result = ChildProcess::stop($alias);
            
            if ($result) {
                // 发送通知
                Notification::send('子进程停止', "子进程 '{$alias}' 已停止");
                
                return json(['success' => true, 'message' => '子进程已停止']);
            } else {
                return json(['success' => false, 'message' => '停止子进程失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '停止子进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 重启子进程
     *
     * @return \think\Response
     */
    public function restart()
    {
        $alias = Request::param('alias');
        
        if (empty($alias)) {
            return json(['success' => false, 'message' => '别名不能为空']);
        }
        
        try {
            // 检查子进程是否存在
            if (!ChildProcess::exists($alias)) {
                return json(['success' => false, 'message' => "子进程 '{$alias}' 不存在"]);
            }
            
            // 重启子进程
            $result = ChildProcess::restart($alias);
            
            if ($result) {
                // 发送通知
                Notification::send('子进程重启', "子进程 '{$alias}' 已重启");
                
                return json(['success' => true, 'message' => '子进程已重启']);
            } else {
                return json(['success' => false, 'message' => '重启子进程失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '重启子进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 向子进程发送消息
     *
     * @return \think\Response
     */
    public function sendMessage()
    {
        $alias = Request::param('alias');
        $message = Request::param('message');
        
        if (empty($alias)) {
            return json(['success' => false, 'message' => '别名不能为空']);
        }
        
        if (empty($message)) {
            return json(['success' => false, 'message' => '消息不能为空']);
        }
        
        try {
            // 检查子进程是否存在
            if (!ChildProcess::exists($alias)) {
                return json(['success' => false, 'message' => "子进程 '{$alias}' 不存在"]);
            }
            
            // 检查子进程是否正在运行
            if (!ChildProcess::isRunning($alias)) {
                return json(['success' => false, 'message' => "子进程 '{$alias}' 未运行"]);
            }
            
            // 向子进程发送消息
            $result = ChildProcess::message($message, $alias);
            
            if ($result) {
                return json(['success' => true, 'message' => '消息已发送']);
            } else {
                return json(['success' => false, 'message' => '发送消息失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '发送消息失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 获取子进程信息
     *
     * @return \think\Response
     */
    public function info()
    {
        $alias = Request::param('alias');
        
        if (empty($alias)) {
            return json(['success' => false, 'message' => '别名不能为空']);
        }
        
        try {
            // 检查子进程是否存在
            if (!ChildProcess::exists($alias)) {
                return json(['success' => false, 'message' => "子进程 '{$alias}' 不存在"]);
            }
            
            // 获取子进程信息
            $process = ChildProcess::get($alias);
            
            // 获取子进程输出
            $output = ChildProcess::getOutput($alias);
            
            // 获取子进程错误
            $error = ChildProcess::getError($alias);
            
            // 获取子进程状态
            $status = ChildProcess::getStatus($alias);
            
            // 获取子进程PID
            $pid = ChildProcess::getPid($alias);
            
            // 获取子进程退出码
            $exitCode = ChildProcess::getExitCode($alias);
            
            return json([
                'success' => true,
                'process' => $process,
                'output' => $output,
                'error' => $error,
                'status' => $status,
                'pid' => $pid,
                'exitCode' => $exitCode
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '获取子进程信息失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 清理已停止的子进程
     *
     * @return \think\Response
     */
    public function cleanup()
    {
        try {
            // 清理已停止的子进程
            $count = ChildProcess::cleanup();
            
            // 发送通知
            Notification::send('子进程清理', "已清理 {$count} 个已停止的子进程");
            
            return json(['success' => true, 'message' => "已清理 {$count} 个已停止的子进程"]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '清理子进程失败: ' . $e->getMessage()]);
        }
    }
}
