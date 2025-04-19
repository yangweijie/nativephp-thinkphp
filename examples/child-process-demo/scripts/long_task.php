<?php

/**
 * 长时间运行任务示例脚本
 * 
 * 这个脚本模拟一个长时间运行的任务，每秒输出一次进度信息。
 * 它可以接收命令行参数来控制运行时间和输出间隔。
 * 
 * 用法：php long_task.php [总步骤数] [间隔秒数]
 * 
 * 示例：php long_task.php 60 1
 * 这将运行一个60步的任务，每1秒输出一次进度。
 */

// 设置无限执行时间
set_time_limit(0);

// 解析命令行参数
$totalSteps = isset($argv[1]) ? (int)$argv[1] : 60;
$interval = isset($argv[2]) ? (int)$argv[2] : 1;

// 输出任务开始信息
echo "长时间运行任务开始\n";
echo "总步骤数: {$totalSteps}, 间隔: {$interval}秒\n";

// 监听标准输入以接收消息
$stdin = fopen('php://stdin', 'r');
stream_set_blocking($stdin, false);

// 执行任务
for ($step = 1; $step <= $totalSteps; $step++) {
    // 计算进度百分比
    $percent = round(($step / $totalSteps) * 100);
    
    // 输出进度信息
    echo "步骤 {$step}/{$totalSteps} ({$percent}% 完成)\n";
    
    // 检查是否有输入消息
    $input = fgets($stdin);
    if ($input !== false) {
        $message = trim($input);
        echo "收到消息: {$message}\n";
        
        // 如果收到停止命令，则提前结束任务
        if (strtolower($message) === 'stop') {
            echo "收到停止命令，任务提前结束\n";
            break;
        }
    }
    
    // 模拟工作
    sleep($interval);
}

// 关闭标准输入
fclose($stdin);

// 输出任务完成信息
echo "长时间运行任务完成\n";
