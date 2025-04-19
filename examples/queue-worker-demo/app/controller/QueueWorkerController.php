<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Queue;
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Facades\Notification;

class QueueWorkerController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('queue_worker/index');
    }
    
    /**
     * 获取所有队列工作进程
     *
     * @return \think\Response
     */
    public function all()
    {
        $workers = QueueWorker::all();
        return json(['workers' => $workers]);
    }
    
    /**
     * 启动队列工作进程
     *
     * @return \think\Response
     */
    public function up()
    {
        $connection = Request::param('connection', 'default');
        $queue = Request::param('queue', 'default');
        $tries = Request::param('tries', 3);
        $timeout = Request::param('timeout', 60);
        $sleep = Request::param('sleep', 3);
        $force = Request::param('force', false);
        $persistent = Request::param('persistent', true);
        
        try {
            // 启动队列工作进程
            $result = QueueWorker::up($connection, $queue, $tries, $timeout, $sleep, $force, $persistent);
            
            if ($result) {
                // 发送通知
                Notification::send('队列工作进程启动', "连接: {$connection}, 队列: {$queue}");
                
                return json([
                    'success' => true, 
                    'message' => '队列工作进程已启动',
                    'connection' => $connection,
                    'queue' => $queue
                ]);
            } else {
                return json(['success' => false, 'message' => '启动队列工作进程失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '启动队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 停止队列工作进程
     *
     * @return \think\Response
     */
    public function down()
    {
        $connection = Request::param('connection', 'default');
        $queue = Request::param('queue', 'default');
        
        try {
            // 停止队列工作进程
            $result = QueueWorker::down($connection, $queue);
            
            if ($result) {
                // 发送通知
                Notification::send('队列工作进程停止', "连接: {$connection}, 队列: {$queue}");
                
                return json([
                    'success' => true, 
                    'message' => '队列工作进程已停止',
                    'connection' => $connection,
                    'queue' => $queue
                ]);
            } else {
                return json(['success' => false, 'message' => '停止队列工作进程失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '停止队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 重启队列工作进程
     *
     * @return \think\Response
     */
    public function restart()
    {
        $connection = Request::param('connection', 'default');
        $queue = Request::param('queue', 'default');
        $tries = Request::param('tries', 3);
        $timeout = Request::param('timeout', 60);
        $sleep = Request::param('sleep', 3);
        $persistent = Request::param('persistent', true);
        
        try {
            // 重启队列工作进程
            $result = QueueWorker::restart($connection, $queue, $tries, $timeout, $sleep, $persistent);
            
            if ($result) {
                // 发送通知
                Notification::send('队列工作进程重启', "连接: {$connection}, 队列: {$queue}");
                
                return json([
                    'success' => true, 
                    'message' => '队列工作进程已重启',
                    'connection' => $connection,
                    'queue' => $queue
                ]);
            } else {
                return json(['success' => false, 'message' => '重启队列工作进程失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '重启队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 获取队列工作进程状态
     *
     * @return \think\Response
     */
    public function status()
    {
        $connection = Request::param('connection', 'default');
        $queue = Request::param('queue', 'default');
        
        try {
            // 获取队列工作进程状态
            $status = QueueWorker::status($connection, $queue);
            
            // 获取队列工作进程信息
            $worker = QueueWorker::get($connection, $queue);
            
            // 获取队列工作进程PID
            $pid = QueueWorker::getPid($connection, $queue);
            
            // 获取队列工作进程输出
            $output = QueueWorker::getOutput($connection, $queue);
            
            // 获取队列工作进程错误
            $error = QueueWorker::getError($connection, $queue);
            
            return json([
                'success' => true,
                'status' => $status,
                'worker' => $worker,
                'pid' => $pid,
                'output' => $output,
                'error' => $error
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '获取队列工作进程状态失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 停止所有队列工作进程
     *
     * @return \think\Response
     */
    public function downAll()
    {
        try {
            // 停止所有队列工作进程
            $count = QueueWorker::downAll();
            
            // 发送通知
            Notification::send('所有队列工作进程停止', "已停止 {$count} 个队列工作进程");
            
            return json([
                'success' => true, 
                'message' => "已停止 {$count} 个队列工作进程"
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '停止所有队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 重启所有队列工作进程
     *
     * @return \think\Response
     */
    public function restartAll()
    {
        try {
            // 重启所有队列工作进程
            $count = QueueWorker::restartAll();
            
            // 发送通知
            Notification::send('所有队列工作进程重启', "已重启 {$count} 个队列工作进程");
            
            return json([
                'success' => true, 
                'message' => "已重启 {$count} 个队列工作进程"
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '重启所有队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 清理队列工作进程
     *
     * @return \think\Response
     */
    public function cleanup()
    {
        try {
            // 清理队列工作进程
            $count = QueueWorker::cleanup();
            
            // 发送通知
            Notification::send('队列工作进程清理', "已清理 {$count} 个队列工作进程");
            
            return json([
                'success' => true, 
                'message' => "已清理 {$count} 个队列工作进程"
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '清理队列工作进程失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 添加任务到队列
     *
     * @return \think\Response
     */
    public function addJob()
    {
        $jobType = Request::param('job_type', 'email');
        $data = Request::param('data', []);
        $queue = Request::param('queue', 'default');
        $delay = Request::param('delay', 0);
        
        try {
            // 根据任务类型创建任务数据
            switch ($jobType) {
                case 'email':
                    $jobData = [
                        'to' => $data['to'] ?? 'test@example.com',
                        'subject' => $data['subject'] ?? '测试邮件',
                        'content' => $data['content'] ?? '这是一封测试邮件',
                    ];
                    $jobClass = 'app\\job\\SendEmail';
                    break;
                    
                case 'notification':
                    $jobData = [
                        'title' => $data['title'] ?? '测试通知',
                        'message' => $data['message'] ?? '这是一条测试通知',
                    ];
                    $jobClass = 'app\\job\\SendNotification';
                    break;
                    
                case 'report':
                    $jobData = [
                        'type' => $data['type'] ?? 'daily',
                        'date' => $data['date'] ?? date('Y-m-d'),
                    ];
                    $jobClass = 'app\\job\\GenerateReport';
                    break;
                    
                default:
                    return json(['success' => false, 'message' => '未知的任务类型']);
            }
            
            // 添加任务到队列
            $jobId = Queue::push($jobClass, $jobData, $queue, $delay);
            
            if ($jobId) {
                // 发送通知
                Notification::send('任务已添加到队列', "任务类型: {$jobType}, 队列: {$queue}");
                
                return json([
                    'success' => true, 
                    'message' => '任务已添加到队列',
                    'job_id' => $jobId,
                    'job_type' => $jobType,
                    'queue' => $queue
                ]);
            } else {
                return json(['success' => false, 'message' => '添加任务到队列失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '添加任务到队列失败: ' . $e->getMessage()]);
        }
    }
}
