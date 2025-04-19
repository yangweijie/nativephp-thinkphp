<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use Native\ThinkPHP\Facades\ProgressBar;
use Native\ThinkPHP\Facades\Notification;

class ProgressBarController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('progress_bar/index');
    }
    
    /**
     * 处理任务
     *
     * @return \think\Response
     */
    public function processTask()
    {
        $totalItems = Request::param('total', 100);
        $delay = Request::param('delay', 100); // 毫秒
        
        // 创建进度条
        $progressBar = ProgressBar::create($totalItems);
        $progressBar->start();
        
        // 处理任务
        for ($i = 0; $i < $totalItems; $i++) {
            // 执行任务
            $this->doTask($i);
            
            // 更新进度
            $progressBar->advance();
            
            // 模拟耗时操作
            usleep($delay * 1000); // 转换为微秒
            
            // 每10%发送一次通知
            if ($i % ($totalItems / 10) === 0) {
                $percent = round(($i / $totalItems) * 100);
                Notification::send('任务进度', "已完成 {$percent}%");
            }
        }
        
        // 完成进度条
        $progressBar->finish();
        
        // 发送完成通知
        Notification::send('任务完成', "所有 {$totalItems} 个任务已完成");
        
        return json(['success' => true, 'message' => '任务已完成']);
    }
    
    /**
     * 执行任务
     *
     * @param int $index
     * @return void
     */
    protected function doTask($index)
    {
        // 模拟任务处理
        // 在实际应用中，这里可以是文件处理、数据导入等耗时操作
        $result = $index * $index;
        
        // 记录任务结果
        // 这里可以将结果保存到数据库或文件中
    }
    
    /**
     * 多阶段任务处理
     *
     * @return \think\Response
     */
    public function multiStageTask()
    {
        $stages = Request::param('stages', 3);
        $itemsPerStage = Request::param('items_per_stage', 50);
        $delay = Request::param('delay', 50); // 毫秒
        
        $totalItems = $stages * $itemsPerStage;
        
        // 创建进度条
        $progressBar = ProgressBar::create($totalItems);
        $progressBar->start();
        
        // 处理多阶段任务
        for ($stage = 0; $stage < $stages; $stage++) {
            // 发送阶段开始通知
            Notification::send('阶段开始', "阶段 " . ($stage + 1) . " 开始");
            
            // 处理当前阶段的任务
            for ($i = 0; $i < $itemsPerStage; $i++) {
                // 执行任务
                $this->doTask($stage * $itemsPerStage + $i);
                
                // 更新进度
                $progressBar->advance();
                
                // 模拟耗时操作
                usleep($delay * 1000); // 转换为微秒
            }
            
            // 发送阶段完成通知
            Notification::send('阶段完成', "阶段 " . ($stage + 1) . " 完成");
        }
        
        // 完成进度条
        $progressBar->finish();
        
        // 发送完成通知
        Notification::send('任务完成', "所有 {$stages} 个阶段的任务已完成");
        
        return json([
            'success' => true, 
            'message' => '多阶段任务已完成',
            'stages' => $stages,
            'items_per_stage' => $itemsPerStage,
            'total_items' => $totalItems
        ]);
    }
    
    /**
     * 文件处理任务
     *
     * @return \think\Response
     */
    public function fileProcessingTask()
    {
        $fileSize = Request::param('file_size', 1024); // KB
        $chunkSize = Request::param('chunk_size', 64); // KB
        $delay = Request::param('delay', 100); // 毫秒
        
        $totalChunks = ceil($fileSize / $chunkSize);
        
        // 创建进度条
        $progressBar = ProgressBar::create($totalChunks);
        $progressBar->start();
        
        // 模拟文件处理
        $processedSize = 0;
        for ($i = 0; $i < $totalChunks; $i++) {
            // 计算当前块大小
            $currentChunkSize = min($chunkSize, $fileSize - $processedSize);
            $processedSize += $currentChunkSize;
            
            // 模拟处理文件块
            $this->processFileChunk($i, $currentChunkSize);
            
            // 更新进度
            $progressBar->advance();
            
            // 模拟耗时操作
            usleep($delay * 1000); // 转换为微秒
            
            // 每25%发送一次通知
            if ($i % ($totalChunks / 4) === 0) {
                $percent = round(($i / $totalChunks) * 100);
                $processedMB = round($processedSize / 1024, 2);
                Notification::send('文件处理进度', "已处理 {$percent}% ({$processedMB} MB)");
            }
        }
        
        // 完成进度条
        $progressBar->finish();
        
        // 发送完成通知
        $totalMB = round($fileSize / 1024, 2);
        Notification::send('文件处理完成', "文件处理完成，总大小: {$totalMB} MB");
        
        return json([
            'success' => true, 
            'message' => '文件处理任务已完成',
            'file_size' => $fileSize,
            'chunk_size' => $chunkSize,
            'total_chunks' => $totalChunks
        ]);
    }
    
    /**
     * 处理文件块
     *
     * @param int $index
     * @param int $size
     * @return void
     */
    protected function processFileChunk($index, $size)
    {
        // 模拟文件块处理
        // 在实际应用中，这里可以是文件读写、图像处理、数据转换等操作
    }
}
