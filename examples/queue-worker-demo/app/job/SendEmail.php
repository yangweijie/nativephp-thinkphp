<?php

namespace app\job;

use think\queue\Job;
use Native\ThinkPHP\Facades\Notification;

class SendEmail
{
    /**
     * 发送邮件任务
     *
     * @param Job $job 队列任务实例
     * @param array $data 任务数据
     * @return void
     */
    public function fire(Job $job, $data)
    {
        // 获取任务数据
        $to = $data['to'] ?? 'test@example.com';
        $subject = $data['subject'] ?? '测试邮件';
        $content = $data['content'] ?? '这是一封测试邮件';
        
        try {
            // 记录任务开始
            trace("开始发送邮件: {$subject} 到 {$to}", 'info');
            
            // 模拟发送邮件
            $this->simulateSendEmail($to, $subject, $content);
            
            // 发送通知
            Notification::send('邮件已发送', "主题: {$subject}, 收件人: {$to}");
            
            // 记录任务完成
            trace("邮件发送成功: {$subject} 到 {$to}", 'info');
            
            // 删除任务
            $job->delete();
        } catch (\Exception $e) {
            // 记录错误
            trace("邮件发送失败: " . $e->getMessage(), 'error');
            
            // 发送通知
            Notification::send('邮件发送失败', "主题: {$subject}, 错误: " . $e->getMessage());
            
            // 如果任务尝试次数超过3次，则删除任务
            if ($job->attempts() > 3) {
                // 删除任务
                $job->delete();
                
                // 记录任务删除
                trace("邮件发送任务已删除: 尝试次数超过3次", 'warning');
            } else {
                // 重新发布任务
                $job->release(10);
                
                // 记录任务重新发布
                trace("邮件发送任务已重新发布: 尝试次数 " . $job->attempts(), 'warning');
            }
        }
    }
    
    /**
     * 模拟发送邮件
     *
     * @param string $to 收件人
     * @param string $subject 主题
     * @param string $content 内容
     * @return bool
     */
    protected function simulateSendEmail($to, $subject, $content)
    {
        // 模拟发送邮件
        // 在实际应用中，这里应该使用真实的邮件发送功能
        
        // 模拟发送延迟
        sleep(2);
        
        // 模拟随机失败
        if (rand(1, 10) === 1) {
            throw new \Exception('邮件服务器暂时不可用');
        }
        
        // 返回发送结果
        return true;
    }
}
