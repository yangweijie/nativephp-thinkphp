<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\QueueWorker;

class QueueWorkerController
{
    /**
     * 启动队列工作进程
     *
     * @param Request $request
     * @return Response
     */
    public function up(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        $tries = $request->param('tries', 3);
        $timeout = $request->param('timeout', 60);
        $sleep = $request->param('sleep', 3);
        $force = $request->param('force', false);
        $persistent = $request->param('persistent', true);
        
        // 启动队列工作进程
        $success = QueueWorker::up($connection, $queue, $tries, $timeout, $sleep, $force, $persistent);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 停止队列工作进程
     *
     * @param Request $request
     * @return Response
     */
    public function down(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 停止队列工作进程
        $success = QueueWorker::down($connection, $queue);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 重启队列工作进程
     *
     * @param Request $request
     * @return Response
     */
    public function restart(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        $tries = $request->param('tries', 3);
        $timeout = $request->param('timeout', 60);
        $sleep = $request->param('sleep', 3);
        $persistent = $request->param('persistent', true);
        
        // 重启队列工作进程
        $success = QueueWorker::restart($connection, $queue, $tries, $timeout, $sleep, $persistent);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 获取队列工作进程状态
     *
     * @param Request $request
     * @return Response
     */
    public function status(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程状态
        $status = QueueWorker::status($connection, $queue);
        
        return json([
            'status' => $status,
        ]);
    }
    
    /**
     * 获取所有队列工作进程
     *
     * @return Response
     */
    public function all()
    {
        // 获取所有队列工作进程
        $workers = QueueWorker::all();
        
        return json([
            'workers' => $workers,
        ]);
    }
    
    /**
     * 获取队列工作进程
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程
        $worker = QueueWorker::get($connection, $queue);
        
        return json([
            'worker' => $worker,
        ]);
    }
    
    /**
     * 清理所有队列工作进程
     *
     * @return Response
     */
    public function cleanup()
    {
        // 清理所有队列工作进程
        $count = QueueWorker::cleanup();
        
        return json([
            'count' => $count,
        ]);
    }
    
    /**
     * 停止所有队列工作进程
     *
     * @return Response
     */
    public function downAll()
    {
        // 停止所有队列工作进程
        $count = QueueWorker::downAll();
        
        return json([
            'count' => $count,
        ]);
    }
    
    /**
     * 重启所有队列工作进程
     *
     * @return Response
     */
    public function restartAll()
    {
        // 重启所有队列工作进程
        $count = QueueWorker::restartAll();
        
        return json([
            'count' => $count,
        ]);
    }
    
    /**
     * 检查队列工作进程是否存在
     *
     * @param Request $request
     * @return Response
     */
    public function exists(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 检查队列工作进程是否存在
        $exists = QueueWorker::exists($connection, $queue);
        
        return json([
            'exists' => $exists,
        ]);
    }
    
    /**
     * 检查队列工作进程是否正在运行
     *
     * @param Request $request
     * @return Response
     */
    public function isRunning(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 检查队列工作进程是否正在运行
        $running = QueueWorker::isRunning($connection, $queue);
        
        return json([
            'running' => $running,
        ]);
    }
    
    /**
     * 获取队列工作进程 PID
     *
     * @param Request $request
     * @return Response
     */
    public function getPid(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程 PID
        $pid = QueueWorker::getPid($connection, $queue);
        
        return json([
            'pid' => $pid,
        ]);
    }
    
    /**
     * 获取队列工作进程输出
     *
     * @param Request $request
     * @return Response
     */
    public function getOutput(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程输出
        $output = QueueWorker::getOutput($connection, $queue);
        
        return json([
            'output' => $output,
        ]);
    }
    
    /**
     * 获取队列工作进程错误
     *
     * @param Request $request
     * @return Response
     */
    public function getError(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程错误
        $error = QueueWorker::getError($connection, $queue);
        
        return json([
            'error' => $error,
        ]);
    }
    
    /**
     * 获取队列工作进程退出码
     *
     * @param Request $request
     * @return Response
     */
    public function getExitCode(Request $request)
    {
        $connection = $request->param('connection', 'default');
        $queue = $request->param('queue', 'default');
        
        // 获取队列工作进程退出码
        $exitCode = QueueWorker::getExitCode($connection, $queue);
        
        return json([
            'exit_code' => $exitCode,
        ]);
    }
}
