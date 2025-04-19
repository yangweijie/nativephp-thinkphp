<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\ChildProcess;

class ChildProcessController
{
    /**
     * 启动子进程
     *
     * @param Request $request
     * @return Response
     */
    public function start(Request $request)
    {
        $cmd = $request->param('cmd');
        $alias = $request->param('alias');
        $cwd = $request->param('cwd');
        $persistent = $request->param('persistent', false);
        $env = $request->param('env', []);
        
        // 启动子进程
        $process = ChildProcess::start($cmd, $alias, $cwd, $persistent, $env);
        
        // 获取进程 PID
        $pid = ChildProcess::getPid($alias);
        
        return json([
            'success' => true,
            'pid' => $pid,
        ]);
    }
    
    /**
     * 获取子进程
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程
        $process = ChildProcess::get($alias);
        
        return json([
            'success' => $process !== null,
            'process' => $process,
        ]);
    }
    
    /**
     * 获取所有子进程
     *
     * @return Response
     */
    public function all()
    {
        // 获取所有子进程
        $processes = ChildProcess::all();
        
        return json([
            'success' => true,
            'processes' => $processes,
        ]);
    }
    
    /**
     * 停止子进程
     *
     * @param Request $request
     * @return Response
     */
    public function stop(Request $request)
    {
        $alias = $request->param('alias');
        
        // 停止子进程
        $success = ChildProcess::stop($alias);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 重启子进程
     *
     * @param Request $request
     * @return Response
     */
    public function restart(Request $request)
    {
        $alias = $request->param('alias');
        
        // 重启子进程
        $success = ChildProcess::restart($alias);
        
        // 获取进程 PID
        $pid = ChildProcess::getPid($alias);
        
        return json([
            'success' => $success,
            'pid' => $pid,
        ]);
    }
    
    /**
     * 向子进程发送消息
     *
     * @param Request $request
     * @return Response
     */
    public function message(Request $request)
    {
        $message = $request->param('message');
        $alias = $request->param('alias');
        
        // 向子进程发送消息
        $success = ChildProcess::message($message, $alias);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查子进程是否存在
     *
     * @param Request $request
     * @return Response
     */
    public function exists(Request $request)
    {
        $alias = $request->param('alias');
        
        // 检查子进程是否存在
        $exists = ChildProcess::exists($alias);
        
        return json([
            'exists' => $exists,
        ]);
    }
    
    /**
     * 检查子进程是否正在运行
     *
     * @param Request $request
     * @return Response
     */
    public function isRunning(Request $request)
    {
        $alias = $request->param('alias');
        
        // 检查子进程是否正在运行
        $running = ChildProcess::isRunning($alias);
        
        return json([
            'running' => $running,
        ]);
    }
    
    /**
     * 获取子进程 PID
     *
     * @param Request $request
     * @return Response
     */
    public function getPid(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程 PID
        $pid = ChildProcess::getPid($alias);
        
        return json([
            'pid' => $pid,
        ]);
    }
    
    /**
     * 获取子进程状态
     *
     * @param Request $request
     * @return Response
     */
    public function getStatus(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程状态
        $status = ChildProcess::getStatus($alias);
        
        return json([
            'status' => $status,
        ]);
    }
    
    /**
     * 获取子进程输出
     *
     * @param Request $request
     * @return Response
     */
    public function getOutput(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程输出
        $output = ChildProcess::getOutput($alias);
        
        return json([
            'output' => $output,
        ]);
    }
    
    /**
     * 获取子进程错误
     *
     * @param Request $request
     * @return Response
     */
    public function getError(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程错误
        $error = ChildProcess::getError($alias);
        
        return json([
            'error' => $error,
        ]);
    }
    
    /**
     * 获取子进程退出码
     *
     * @param Request $request
     * @return Response
     */
    public function getExitCode(Request $request)
    {
        $alias = $request->param('alias');
        
        // 获取子进程退出码
        $exitCode = ChildProcess::getExitCode($alias);
        
        return json([
            'exit_code' => $exitCode,
        ]);
    }
    
    /**
     * 清理已停止的子进程
     *
     * @return Response
     */
    public function cleanup()
    {
        // 清理已停止的子进程
        $count = ChildProcess::cleanup();
        
        return json([
            'count' => $count,
        ]);
    }
}
