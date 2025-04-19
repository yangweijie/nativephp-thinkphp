<?php

namespace app\job;

use think\queue\Job;
use Native\ThinkPHP\Facades\Notification;

class SendNotification
{
    /**
     * 发送通知任务
     *
     * @param Job $job 队列任务实例
     * @param array $data 任务数据
     * @return void
     */
    public function fire(Job $job, $data)
    {
        // 获取任务数据
        $title = $data['title'] ?? '测试通知';
        $message = $data['message'] ?? '这是一条测试通知';
        
        try {
            // 记录任务开始
            trace("开始发送通知: {$title}", 'info');
            
            // 模拟发送通知
            $this->simulateSendNotification($title, $message);
            
            // 发送通知
            Notification::send('通知已发送', "标题: {$title}");
            
            // 记录任务完成
            trace("通知发送成功: {$title}", 'info');
            
            // 删除任务
            $job->delete();
        } catch (\Exception $e) {
            // 记录错误
            trace("通知发送失败: " . $e->getMessage(), 'error');
            
            // 发送通知
            Notification::send('通知发送失败', "标题: {$title}, 错误: " . $e->getMessage());
            
            // 如果任务尝试次数超过3次，则删除任务
            if ($job->attempts() > 3) {
                // 删除任务
                $job->delete();
                
                // 记录任务删除
                trace("通知发送任务已删除: 尝试次数超过3次", 'warning');
            } else {
                // 重新发布任务
                $job->release(5);
                
                // 记录任务重新发布
                trace("通知发送任务已重新发布: 尝试次数 " . $job->attempts(), 'warning');
            }
        }
    }
    
    /**
     * 模拟发送通知
     *
     * @param string $title 标题
     * @param string $message 消息
     * @return bool
     */
    protected function simulateSendNotification($title, $message)
    {
        // 模拟发送通知
        // 在实际应用中，这里应该使用真实的通知发送功能
        
        // 模拟发送延迟
        sleep(1);
        
        // 模拟随机失败
        if (rand(1, 10) === 1) {
            throw new \Exception('通知服务暂时不可用');
        }
        
        // 返回发送结果
        return true;
    }
}
