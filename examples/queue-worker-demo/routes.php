<?php

use think\facade\Route;
use think\facade\View;
use Native\ThinkPHP\Facades\QueueWorker;

// 队列工作进程管理示例路由
Route::get('queue-worker-demo', function () {
    return View::fetch('queue-worker-demo/index');
});

// 启动队列工作进程
Route::post('queue-worker-demo/up', function () {
    $connection = request()->param('connection', 'default');
    $queue = request()->param('queue', 'default');
    $tries = request()->param('tries', 3);
    $timeout = request()->param('timeout', 60);
    $sleep = request()->param('sleep', 3);
    $force = request()->param('force', false);
    $persistent = request()->param('persistent', true);
    
    $success = QueueWorker::up($connection, $queue, $tries, $timeout, $sleep, $force, $persistent);
    
    return json(['success' => $success, 'message' => $success ? "队列工作进程 {$connection}:{$queue} 已启动" : "启动队列工作进程 {$connection}:{$queue} 失败"]);
});

// 停止队列工作进程
Route::post('queue-worker-demo/down', function () {
    $connection = request()->param('connection', 'default');
    $queue = request()->param('queue', 'default');
    
    $success = QueueWorker::down($connection, $queue);
    
    return json(['success' => $success, 'message' => $success ? "队列工作进程 {$connection}:{$queue} 已停止" : "停止队列工作进程 {$connection}:{$queue} 失败"]);
});

// 重启队列工作进程
Route::post('queue-worker-demo/restart', function () {
    $connection = request()->param('connection', 'default');
    $queue = request()->param('queue', 'default');
    $tries = request()->param('tries', 3);
    $timeout = request()->param('timeout', 60);
    $sleep = request()->param('sleep', 3);
    $persistent = request()->param('persistent', true);
    
    $success = QueueWorker::restart($connection, $queue, $tries, $timeout, $sleep, $persistent);
    
    return json(['success' => $success, 'message' => $success ? "队列工作进程 {$connection}:{$queue} 已重启" : "重启队列工作进程 {$connection}:{$queue} 失败"]);
});

// 获取所有队列工作进程
Route::get('queue-worker-demo/all', function () {
    $workers = QueueWorker::all();
    
    // 添加运行状态
    foreach ($workers as $alias => &$worker) {
        $worker['running'] = QueueWorker::isRunning($worker['connection'], $worker['queue']);
        $worker['pid'] = QueueWorker::getPid($worker['connection'], $worker['queue']);
    }
    
    return json(['workers' => $workers]);
});

// 获取队列工作进程状态
Route::get('queue-worker-demo/status', function () {
    $connection = request()->param('connection', 'default');
    $queue = request()->param('queue', 'default');
    
    $status = QueueWorker::status($connection, $queue);
    
    return json(['status' => $status]);
});

// 获取队列工作进程输出
Route::get('queue-worker-demo/output', function () {
    $connection = request()->param('connection', 'default');
    $queue = request()->param('queue', 'default');
    
    $output = QueueWorker::getOutput($connection, $queue);
    $error = QueueWorker::getError($connection, $queue);
    
    return json([
        'output' => $output,
        'error' => $error,
    ]);
});

// 停止所有队列工作进程
Route::post('queue-worker-demo/down-all', function () {
    $count = QueueWorker::downAll();
    
    return json(['success' => true, 'count' => $count, 'message' => "已停止 {$count} 个队列工作进程"]);
});

// 重启所有队列工作进程
Route::post('queue-worker-demo/restart-all', function () {
    $count = QueueWorker::restartAll();
    
    return json(['success' => true, 'count' => $count, 'message' => "已重启 {$count} 个队列工作进程"]);
});

// 清理已停止的队列工作进程
Route::post('queue-worker-demo/cleanup', function () {
    $count = QueueWorker::cleanup();
    
    return json(['success' => true, 'count' => $count, 'message' => "已清理 {$count} 个已停止的队列工作进程"]);
});
