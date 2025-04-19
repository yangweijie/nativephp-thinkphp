<?php

use think\facade\Route;
use think\facade\View;
use Native\ThinkPHP\Facades\ChildProcess;

// 子进程管理示例路由
Route::get('child-process-demo', function () {
    return View::fetch('child-process-demo/index');
});

// 启动子进程
Route::post('child-process-demo/start', function () {
    $cmd = request()->param('cmd');
    $alias = request()->param('alias');
    
    ChildProcess::start($cmd, $alias);
    
    return json(['success' => true, 'message' => "进程 {$alias} 已启动"]);
});

// 停止子进程
Route::post('child-process-demo/stop', function () {
    $alias = request()->param('alias');
    
    $success = ChildProcess::stop($alias);
    
    return json(['success' => $success, 'message' => $success ? "进程 {$alias} 已停止" : "停止进程 {$alias} 失败"]);
});

// 获取所有子进程
Route::get('child-process-demo/all', function () {
    $processes = ChildProcess::all();
    
    // 添加运行状态
    foreach ($processes as $alias => &$process) {
        $process['running'] = ChildProcess::isRunning($alias);
        $process['pid'] = ChildProcess::getPid($alias);
    }
    
    return json(['processes' => $processes]);
});

// 获取进程输出
Route::get('child-process-demo/output', function () {
    $alias = request()->param('alias');
    
    $output = ChildProcess::getOutput($alias);
    $error = ChildProcess::getError($alias);
    
    return json([
        'output' => $output,
        'error' => $error,
    ]);
});
