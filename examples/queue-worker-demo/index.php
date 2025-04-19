<?php

use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Facades\Menu;

// 设置应用标题
App::setTitle('队列工作进程管理示例');

// 创建主窗口
$window = Window::create()
    ->width(800)
    ->height(600)
    ->title('队列工作进程管理示例')
    ->url('http://localhost:8000/queue-worker-demo')
    ->show();

// 创建菜单
Menu::new()
    ->appMenu(
        Menu::submenu('文件')
            ->add(
                Menu::item('启动默认队列工作进程')
                    ->onClick(function () {
                        // 启动默认队列工作进程
                        QueueWorker::up();
                    })
            )
            ->add(
                Menu::item('启动邮件队列工作进程')
                    ->onClick(function () {
                        // 启动邮件队列工作进程
                        QueueWorker::up('redis', 'emails', 3, 60, 3, false, true);
                    })
            )
            ->add(
                Menu::item('启动通知队列工作进程')
                    ->onClick(function () {
                        // 启动通知队列工作进程
                        QueueWorker::up('redis', 'notifications', 3, 60, 3, false, true);
                    })
            )
            ->add(Menu::separator())
            ->add(
                Menu::item('停止所有队列工作进程')
                    ->onClick(function () {
                        // 停止所有队列工作进程
                        QueueWorker::downAll();
                    })
            )
            ->add(Menu::separator())
            ->add(
                Menu::item('退出')
                    ->onClick(function () {
                        App::quit();
                    })
            )
    )
    ->submenu('操作')
        ->add(
            Menu::item('重启所有队列工作进程')
                ->onClick(function () {
                    // 重启所有队列工作进程
                    QueueWorker::restartAll();
                })
        )
        ->add(
            Menu::item('清理已停止的队列工作进程')
                ->onClick(function () {
                    // 清理已停止的队列工作进程
                    QueueWorker::cleanup();
                })
        )
    ->submenu('查看')
        ->add(
            Menu::item('查看所有队列工作进程')
                ->onClick(function () use ($window) {
                    // 获取所有队列工作进程
                    $workers = QueueWorker::all();
                    
                    // 格式化输出
                    $output = "当前队列工作进程列表：\n\n";
                    foreach ($workers as $alias => $worker) {
                        $status = QueueWorker::isRunning($worker['connection'], $worker['queue']) ? '运行中' : '已停止';
                        $pid = QueueWorker::getPid($worker['connection'], $worker['queue']);
                        $output .= "别名: {$alias}\n";
                        $output .= "连接: {$worker['connection']}\n";
                        $output .= "队列: {$worker['queue']}\n";
                        $output .= "PID: {$pid}\n";
                        $output .= "状态: {$status}\n";
                        $output .= "尝试次数: {$worker['tries']}\n";
                        $output .= "超时时间: {$worker['timeout']}秒\n";
                        $output .= "休眠时间: {$worker['sleep']}秒\n";
                        $output .= "持久化: " . ($worker['persistent'] ? '是' : '否') . "\n";
                        $output .= "-------------------\n";
                    }
                    
                    // 发送到窗口
                    $window->evaluate("
                        document.getElementById('worker-list').textContent = `{$output}`;
                    ");
                })
        )
        ->add(
            Menu::item('查看工作进程输出')
                ->onClick(function () use ($window) {
                    // 获取所有队列工作进程
                    $workers = QueueWorker::all();
                    
                    // 格式化输出
                    $output = "工作进程输出：\n\n";
                    foreach ($workers as $alias => $worker) {
                        $workerOutput = QueueWorker::getOutput($worker['connection'], $worker['queue']);
                        $workerError = QueueWorker::getError($worker['connection'], $worker['queue']);
                        $output .= "别名: {$alias}\n";
                        $output .= "标准输出:\n{$workerOutput}\n";
                        $output .= "错误输出:\n{$workerError}\n";
                        $output .= "-------------------\n";
                    }
                    
                    // 发送到窗口
                    $window->evaluate("
                        document.getElementById('worker-output').textContent = `{$output}`;
                    ");
                })
        )
    ->register();
