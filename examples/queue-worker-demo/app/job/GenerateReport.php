<?php

namespace app\job;

use think\queue\Job;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\FileSystem;

class GenerateReport
{
    /**
     * 生成报告任务
     *
     * @param Job $job 队列任务实例
     * @param array $data 任务数据
     * @return void
     */
    public function fire(Job $job, $data)
    {
        // 获取任务数据
        $type = $data['type'] ?? 'daily';
        $date = $data['date'] ?? date('Y-m-d');
        
        try {
            // 记录任务开始
            trace("开始生成{$type}报告: {$date}", 'info');
            
            // 模拟生成报告
            $reportPath = $this->simulateGenerateReport($type, $date);
            
            // 发送通知
            Notification::send('报告已生成', "类型: {$type}, 日期: {$date}, 路径: {$reportPath}");
            
            // 记录任务完成
            trace("报告生成成功: {$reportPath}", 'info');
            
            // 删除任务
            $job->delete();
        } catch (\Exception $e) {
            // 记录错误
            trace("报告生成失败: " . $e->getMessage(), 'error');
            
            // 发送通知
            Notification::send('报告生成失败', "类型: {$type}, 日期: {$date}, 错误: " . $e->getMessage());
            
            // 如果任务尝试次数超过3次，则删除任务
            if ($job->attempts() > 3) {
                // 删除任务
                $job->delete();
                
                // 记录任务删除
                trace("报告生成任务已删除: 尝试次数超过3次", 'warning');
            } else {
                // 重新发布任务
                $job->release(15);
                
                // 记录任务重新发布
                trace("报告生成任务已重新发布: 尝试次数 " . $job->attempts(), 'warning');
            }
        }
    }
    
    /**
     * 模拟生成报告
     *
     * @param string $type 报告类型
     * @param string $date 报告日期
     * @return string 报告路径
     */
    protected function simulateGenerateReport($type, $date)
    {
        // 模拟生成报告
        // 在实际应用中，这里应该使用真实的报告生成功能
        
        // 创建报告目录
        $reportDir = runtime_path() . 'reports';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }
        
        // 生成报告文件名
        $fileName = "{$type}_report_{$date}.html";
        $reportPath = $reportDir . '/' . $fileName;
        
        // 模拟报告内容
        $content = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"utf-8\">
    <title>{$type}报告 - {$date}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tr:nth-child(even) { background-color: #f9f9f9; }
    </style>
</head>
<body>
    <h1>{$type}报告 - {$date}</h1>
    <p>生成时间: " . date('Y-m-d H:i:s') . "</p>
    <table>
        <tr>
            <th>指标</th>
            <th>数值</th>
        </tr>
        <tr>
            <td>访问量</td>
            <td>" . rand(1000, 10000) . "</td>
        </tr>
        <tr>
            <td>用户数</td>
            <td>" . rand(100, 1000) . "</td>
        </tr>
        <tr>
            <td>订单数</td>
            <td>" . rand(10, 100) . "</td>
        </tr>
        <tr>
            <td>销售额</td>
            <td>¥" . rand(1000, 10000) . ".00</td>
        </tr>
    </table>
</body>
</html>";
        
        // 模拟生成延迟
        sleep(3);
        
        // 模拟随机失败
        if (rand(1, 10) === 1) {
            throw new \Exception('报告生成服务暂时不可用');
        }
        
        // 写入报告文件
        FileSystem::write($reportPath, $content);
        
        // 返回报告路径
        return $reportPath;
    }
}
