<?php

use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\Menu;

// 设置应用标题
App::setTitle('子进程管理示例');

// 创建主窗口
$window = Window::create()
    ->width(800)
    ->height(600)
    ->title('子进程管理示例')
    ->url('http://localhost:8000/child-process-demo')
    ->show();

// 创建菜单
Menu::new()
    ->appMenu(
        Menu::submenu('文件')
            ->add(
                Menu::item('启动子进程')
                    ->onClick(function () {
                        // 启动一个简单的子进程
                        ChildProcess::start('echo Hello World', 'echo-hello');
                    })
            )
            ->add(
                Menu::item('启动 PHP 脚本')
                    ->onClick(function () {
                        // 启动一个 PHP 脚本
                        ChildProcess::php(__DIR__ . '/script.php', 'php-script');
                    })
            )
            ->add(
                Menu::item('启动长时间运行的进程')
                    ->onClick(function () {
                        // 启动一个长时间运行的进程
                        ChildProcess::start('ping localhost -t', 'ping-localhost', null, true);
                    })
            )
            ->add(Menu::separator())
            ->add(
                Menu::item('停止所有子进程')
                    ->onClick(function () {
                        // 获取所有子进程
                        $processes = ChildProcess::all();
                        
                        // 停止所有子进程
                        foreach ($processes as $alias => $process) {
                            ChildProcess::stop($alias);
                        }
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
    ->submenu('查看')
        ->add(
            Menu::item('查看所有子进程')
                ->onClick(function () use ($window) {
                    // 获取所有子进程
                    $processes = ChildProcess::all();
                    
                    // 格式化输出
                    $output = "当前子进程列表：\n\n";
                    foreach ($processes as $alias => $process) {
                        $status = ChildProcess::isRunning($alias) ? '运行中' : '已停止';
                        $pid = ChildProcess::getPid($alias);
                        $output .= "别名: {$alias}\n";
                        $output .= "PID: {$pid}\n";
                        $output .= "状态: {$status}\n";
                        $output .= "命令: {$process['cmd']}\n";
                        $output .= "-------------------\n";
                    }
                    
                    // 发送到窗口
                    $window->evaluate("
                        document.getElementById('process-list').textContent = `{$output}`;
                    ");
                })
        )
        ->add(
            Menu::item('查看进程输出')
                ->onClick(function () use ($window) {
                    // 获取所有子进程
                    $processes = ChildProcess::all();
                    
                    // 格式化输出
                    $output = "进程输出：\n\n";
                    foreach ($processes as $alias => $process) {
                        $processOutput = ChildProcess::getOutput($alias);
                        $processError = ChildProcess::getError($alias);
                        $output .= "别名: {$alias}\n";
                        $output .= "标准输出:\n{$processOutput}\n";
                        $output .= "错误输出:\n{$processError}\n";
                        $output .= "-------------------\n";
                    }
                    
                    // 发送到窗口
                    $window->evaluate("
                        document.getElementById('process-output').textContent = `{$output}`;
                    ");
                })
        )
    ->register();

// 注册事件监听器
event('Native\ThinkPHP\Events\ChildProcess\ProcessSpawned', function ($event) use ($window) {
    $window->evaluate("
        console.log('进程已启动: {$event->alias}, PID: {$event->pid}');
        document.getElementById('events').innerHTML += '<div>进程已启动: {$event->alias}, PID: {$event->pid}</div>';
    ");
});

event('Native\ThinkPHP\Events\ChildProcess\ProcessExited', function ($event) use ($window) {
    $window->evaluate("
        console.log('进程已退出: {$event->alias}, 退出码: {$event->code}');
        document.getElementById('events').innerHTML += '<div>进程已退出: {$event->alias}, 退出码: {$event->code}</div>';
    ");
});

event('Native\ThinkPHP\Events\ChildProcess\MessageReceived', function ($event) use ($window) {
    $window->evaluate("
        console.log('收到消息: {$event->alias}, 数据: {$event->data}');
        document.getElementById('events').innerHTML += '<div>收到消息: {$event->alias}, 数据: {$event->data}</div>';
    ");
});

event('Native\ThinkPHP\Events\ChildProcess\ErrorReceived', function ($event) use ($window) {
    $window->evaluate("
        console.log('收到错误: {$event->alias}, 数据: {$event->data}');
        document.getElementById('events').innerHTML += '<div>收到错误: {$event->alias}, 数据: {$event->data}</div>';
    ");
});
