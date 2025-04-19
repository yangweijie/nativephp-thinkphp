<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use think\facade\Db;
use Native\ThinkPHP\Facades\Broadcasting;
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\QueueWorker;
use Native\ThinkPHP\Facades\ProgressBar;
use Native\ThinkPHP\Facades\Shell;
use Native\ThinkPHP\Facades\DeveloperTools;
use Native\ThinkPHP\Facades\Notification;

class ComprehensiveController extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('comprehensive/index');
    }
    
    /**
     * 执行综合任务
     *
     * @return \think\Response
     */
    public function executeTask()
    {
        $taskType = Request::param('task_type', 'file_processing');
        $taskParams = Request::param('task_params', []);
        
        // 启用开发者工具
        DeveloperTools::enable();
        
        // 开始计时
        DeveloperTools::time('任务执行时间');
        
        // 创建进度条
        $progressBar = ProgressBar::create(100);
        $progressBar->setTitle('综合任务');
        $progressBar->setDescription('准备执行任务...');
        $progressBar->start();
        
        try {
            // 根据任务类型执行不同的任务
            switch ($taskType) {
                case 'file_processing':
                    $result = $this->executeFileProcessingTask($progressBar, $taskParams);
                    break;
                    
                case 'data_import':
                    $result = $this->executeDataImportTask($progressBar, $taskParams);
                    break;
                    
                case 'system_monitoring':
                    $result = $this->executeSystemMonitoringTask($progressBar, $taskParams);
                    break;
                    
                default:
                    throw new \Exception('未知的任务类型');
            }
            
            // 完成进度条
            $progressBar->finish();
            
            // 结束计时
            DeveloperTools::timeEnd('任务执行时间');
            
            // 发送通知
            Notification::send('任务完成', "任务类型: {$taskType}，结果: 成功");
            
            return json([
                'success' => true,
                'message' => '任务执行成功',
                'task_type' => $taskType,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            // 完成进度条
            $progressBar->finish();
            
            // 结束计时
            DeveloperTools::timeEnd('任务执行时间');
            
            // 记录错误
            DeveloperTools::error('任务执行失败', $e->getMessage());
            
            // 发送通知
            Notification::send('任务失败', "任务类型: {$taskType}，错误: {$e->getMessage()}");
            
            return json([
                'success' => false,
                'message' => '任务执行失败: ' . $e->getMessage(),
                'task_type' => $taskType
            ]);
        }
    }
    
    /**
     * 执行文件处理任务
     *
     * @param \Native\ThinkPHP\ProgressBar $progressBar
     * @param array $params
     * @return array
     */
    protected function executeFileProcessingTask($progressBar, $params)
    {
        // 获取任务参数
        $sourceDir = $params['source_dir'] ?? runtime_path() . 'temp';
        $targetDir = $params['target_dir'] ?? runtime_path() . 'processed';
        $fileType = $params['file_type'] ?? 'txt';
        
        // 更新进度条
        $progressBar->setDescription('准备文件处理任务...');
        $progressBar->advance(10);
        
        // 创建目录
        if (!is_dir($sourceDir)) {
            mkdir($sourceDir, 0755, true);
        }
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        
        // 创建测试文件
        $this->createTestFiles($sourceDir, $fileType);
        
        // 更新进度条
        $progressBar->setDescription('获取文件列表...');
        $progressBar->advance(10);
        
        // 获取文件列表
        $files = glob("{$sourceDir}/*.{$fileType}");
        $totalFiles = count($files);
        
        if ($totalFiles === 0) {
            throw new \Exception('没有找到要处理的文件');
        }
        
        // 更新进度条
        $progressBar->setDescription("开始处理 {$totalFiles} 个文件...");
        $progressBar->advance(10);
        
        // 创建子进程处理文件
        $processAlias = 'file-processor-' . time();
        $command = "php " . app()->getRootPath() . "scripts/process_files.php {$sourceDir} {$targetDir} {$fileType}";
        
        DeveloperTools::log('执行命令', $command);
        
        // 启动子进程
        ChildProcess::start($command, $processAlias);
        
        // 广播子进程启动事件
        Broadcasting::broadcast('tasks', 'process-started', [
            'alias' => $processAlias,
            'command' => $command,
            'type' => 'file_processing'
        ]);
        
        // 模拟处理进度
        $processedFiles = 0;
        $startTime = time();
        
        while ($processedFiles < $totalFiles) {
            // 检查子进程是否还在运行
            if (!ChildProcess::isRunning($processAlias)) {
                break;
            }
            
            // 获取子进程输出
            $output = ChildProcess::getOutput($processAlias);
            
            // 解析输出中的进度信息
            if (preg_match('/Processed: (\d+)\/(\d+)/', $output, $matches)) {
                $processedFiles = (int)$matches[1];
            }
            
            // 计算进度百分比
            $percent = ($processedFiles / $totalFiles) * 70;
            
            // 更新进度条
            $progressBar->setDescription("已处理 {$processedFiles}/{$totalFiles} 个文件...");
            $progressBar->setProgress(30 + $percent);
            
            // 广播进度事件
            Broadcasting::broadcast('tasks', 'process-progress', [
                'alias' => $processAlias,
                'processed' => $processedFiles,
                'total' => $totalFiles,
                'percent' => round(($processedFiles / $totalFiles) * 100)
            ]);
            
            // 防止CPU占用过高
            usleep(500000); // 休眠0.5秒
            
            // 超时检查
            if (time() - $startTime > 60) {
                ChildProcess::stop($processAlias);
                throw new \Exception('文件处理任务超时');
            }
        }
        
        // 获取子进程输出和错误
        $output = ChildProcess::getOutput($processAlias);
        $error = ChildProcess::getError($processAlias);
        
        // 检查是否有错误
        if (!empty($error)) {
            DeveloperTools::error('子进程错误', $error);
            throw new \Exception('文件处理失败: ' . $error);
        }
        
        // 停止子进程
        ChildProcess::stop($processAlias);
        
        // 广播子进程停止事件
        Broadcasting::broadcast('tasks', 'process-stopped', [
            'alias' => $processAlias,
            'output' => $output
        ]);
        
        // 更新进度条
        $progressBar->setDescription('文件处理完成，正在清理...');
        $progressBar->advance(10);
        
        // 返回结果
        return [
            'processed_files' => $processedFiles,
            'total_files' => $totalFiles,
            'source_dir' => $sourceDir,
            'target_dir' => $targetDir,
            'output' => $output
        ];
    }
    
    /**
     * 执行数据导入任务
     *
     * @param \Native\ThinkPHP\ProgressBar $progressBar
     * @param array $params
     * @return array
     */
    protected function executeDataImportTask($progressBar, $params)
    {
        // 获取任务参数
        $dataSource = $params['data_source'] ?? 'csv';
        $batchSize = $params['batch_size'] ?? 100;
        $totalRecords = $params['total_records'] ?? 1000;
        
        // 更新进度条
        $progressBar->setDescription('准备数据导入任务...');
        $progressBar->advance(10);
        
        // 创建测试表
        $this->createTestTable();
        
        // 更新进度条
        $progressBar->setDescription('准备导入数据...');
        $progressBar->advance(10);
        
        // 启动队列工作进程
        QueueWorker::up('default', 'data_import', 3, 60, 3, true, false);
        
        // 更新进度条
        $progressBar->setDescription('开始导入数据...');
        $progressBar->advance(10);
        
        // 开始内存监控
        DeveloperTools::startMemoryMonitor('数据导入');
        
        // 分批导入数据
        $batches = ceil($totalRecords / $batchSize);
        $importedRecords = 0;
        
        for ($batch = 0; $batch < $batches; $batch++) {
            // 计算当前批次的记录数
            $currentBatchSize = min($batchSize, $totalRecords - $importedRecords);
            
            // 生成测试数据
            $data = $this->generateTestData($currentBatchSize);
            
            // 导入数据
            Db::table('test_data')->insertAll($data);
            
            // 更新已导入记录数
            $importedRecords += $currentBatchSize;
            
            // 计算进度百分比
            $percent = ($importedRecords / $totalRecords) * 60;
            
            // 更新进度条
            $progressBar->setDescription("已导入 {$importedRecords}/{$totalRecords} 条记录...");
            $progressBar->setProgress(30 + $percent);
            
            // 广播进度事件
            Broadcasting::broadcast('tasks', 'import-progress', [
                'imported' => $importedRecords,
                'total' => $totalRecords,
                'percent' => round(($importedRecords / $totalRecords) * 100),
                'batch' => $batch + 1,
                'batches' => $batches
            ]);
            
            // 模拟处理延迟
            usleep(200000); // 休眠0.2秒
        }
        
        // 停止内存监控
        $memoryUsage = DeveloperTools::stopMemoryMonitor('数据导入');
        
        // 停止队列工作进程
        QueueWorker::down('default', 'data_import');
        
        // 更新进度条
        $progressBar->setDescription('数据导入完成，正在验证...');
        $progressBar->advance(10);
        
        // 验证导入的数据
        $actualRecords = Db::table('test_data')->count();
        
        if ($actualRecords !== $totalRecords) {
            throw new \Exception("数据导入验证失败: 预期 {$totalRecords} 条记录，实际 {$actualRecords} 条记录");
        }
        
        // 返回结果
        return [
            'imported_records' => $importedRecords,
            'total_records' => $totalRecords,
            'batches' => $batches,
            'memory_usage' => $memoryUsage
        ];
    }
    
    /**
     * 执行系统监控任务
     *
     * @param \Native\ThinkPHP\ProgressBar $progressBar
     * @param array $params
     * @return array
     */
    protected function executeSystemMonitoringTask($progressBar, $params)
    {
        // 获取任务参数
        $duration = $params['duration'] ?? 30; // 监控持续时间（秒）
        $interval = $params['interval'] ?? 5; // 监控间隔（秒）
        
        // 更新进度条
        $progressBar->setDescription('准备系统监控任务...');
        $progressBar->advance(10);
        
        // 创建监控结果数组
        $monitoringResults = [
            'cpu' => [],
            'memory' => [],
            'disk' => [],
            'processes' => []
        ];
        
        // 更新进度条
        $progressBar->setDescription('开始系统监控...');
        $progressBar->advance(10);
        
        // 计算监控次数
        $monitoringCount = ceil($duration / $interval);
        $currentCount = 0;
        
        // 开始监控
        $startTime = time();
        
        while (time() - $startTime < $duration) {
            // 获取CPU使用率
            $cpuUsage = $this->getCpuUsage();
            $monitoringResults['cpu'][] = $cpuUsage;
            
            // 获取内存使用情况
            $memoryUsage = $this->getMemoryUsage();
            $monitoringResults['memory'][] = $memoryUsage;
            
            // 获取磁盘使用情况
            $diskUsage = $this->getDiskUsage();
            $monitoringResults['disk'][] = $diskUsage;
            
            // 获取进程信息
            $processes = $this->getProcessInfo();
            $monitoringResults['processes'][] = $processes;
            
            // 更新监控计数
            $currentCount++;
            
            // 计算进度百分比
            $percent = ($currentCount / $monitoringCount) * 70;
            
            // 更新进度条
            $progressBar->setDescription("系统监控中... ({$currentCount}/{$monitoringCount})");
            $progressBar->setProgress(20 + $percent);
            
            // 广播监控数据
            Broadcasting::broadcast('tasks', 'monitoring-data', [
                'timestamp' => time(),
                'cpu' => $cpuUsage,
                'memory' => $memoryUsage,
                'disk' => $diskUsage,
                'processes' => count($processes)
            ]);
            
            // 等待下一次监控
            if (time() - $startTime < $duration) {
                sleep($interval);
            }
        }
        
        // 更新进度条
        $progressBar->setDescription('系统监控完成，正在分析数据...');
        $progressBar->advance(10);
        
        // 分析监控数据
        $analysis = $this->analyzeMonitoringData($monitoringResults);
        
        // 返回结果
        return [
            'duration' => $duration,
            'interval' => $interval,
            'monitoring_count' => $currentCount,
            'analysis' => $analysis
        ];
    }
    
    /**
     * 创建测试文件
     *
     * @param string $dir
     * @param string $fileType
     * @return void
     */
    protected function createTestFiles($dir, $fileType)
    {
        // 创建10个测试文件
        for ($i = 1; $i <= 10; $i++) {
            $fileName = "{$dir}/test_file_{$i}.{$fileType}";
            $content = "This is test file {$i}.\n";
            $content .= "Created at: " . date('Y-m-d H:i:s') . "\n";
            $content .= "Random content: " . md5(uniqid($i, true)) . "\n";
            
            file_put_contents($fileName, $content);
        }
    }
    
    /**
     * 创建测试表
     *
     * @return void
     */
    protected function createTestTable()
    {
        // 检查测试表是否存在
        $tables = Db::query('SHOW TABLES LIKE "test_data"');
        if (empty($tables)) {
            // 创建测试表
            Db::execute('CREATE TABLE test_data (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(50) NOT NULL,
                email VARCHAR(100) NOT NULL,
                value DECIMAL(10, 2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )');
        } else {
            // 清空测试表
            Db::execute('TRUNCATE TABLE test_data');
        }
    }
    
    /**
     * 生成测试数据
     *
     * @param int $count
     * @return array
     */
    protected function generateTestData($count)
    {
        $data = [];
        
        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                'name' => 'User ' . uniqid(),
                'email' => 'user' . uniqid() . '@example.com',
                'value' => rand(100, 10000) / 100
            ];
        }
        
        return $data;
    }
    
    /**
     * 获取CPU使用率
     *
     * @return array
     */
    protected function getCpuUsage()
    {
        // 在Windows上使用wmic命令获取CPU使用率
        if (PHP_OS_FAMILY === 'Windows') {
            $output = Shell::exec('wmic cpu get LoadPercentage');
            preg_match('/\d+/', $output, $matches);
            $cpuUsage = isset($matches[0]) ? (int)$matches[0] : 0;
        } else {
            // 在Linux上使用top命令获取CPU使用率
            $output = Shell::exec("top -bn1 | grep 'Cpu(s)' | awk '{print $2 + $4}'");
            $cpuUsage = (float)$output;
        }
        
        return [
            'timestamp' => time(),
            'usage' => $cpuUsage
        ];
    }
    
    /**
     * 获取内存使用情况
     *
     * @return array
     */
    protected function getMemoryUsage()
    {
        // 在Windows上使用wmic命令获取内存使用情况
        if (PHP_OS_FAMILY === 'Windows') {
            $output = Shell::exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize');
            preg_match('/(\d+)\s+(\d+)/', $output, $matches);
            
            if (isset($matches[1]) && isset($matches[2])) {
                $freeMemory = (int)$matches[1] * 1024; // KB to bytes
                $totalMemory = (int)$matches[2] * 1024; // KB to bytes
                $usedMemory = $totalMemory - $freeMemory;
                $usagePercent = round(($usedMemory / $totalMemory) * 100, 2);
            } else {
                $freeMemory = 0;
                $totalMemory = 0;
                $usedMemory = 0;
                $usagePercent = 0;
            }
        } else {
            // 在Linux上使用free命令获取内存使用情况
            $output = Shell::exec("free -b | grep 'Mem:'");
            preg_match('/Mem:\s+(\d+)\s+(\d+)\s+(\d+)/', $output, $matches);
            
            if (isset($matches[1]) && isset($matches[2])) {
                $totalMemory = (int)$matches[1];
                $usedMemory = (int)$matches[2];
                $freeMemory = (int)$matches[3];
                $usagePercent = round(($usedMemory / $totalMemory) * 100, 2);
            } else {
                $freeMemory = 0;
                $totalMemory = 0;
                $usedMemory = 0;
                $usagePercent = 0;
            }
        }
        
        return [
            'timestamp' => time(),
            'total' => $totalMemory,
            'used' => $usedMemory,
            'free' => $freeMemory,
            'percent' => $usagePercent
        ];
    }
    
    /**
     * 获取磁盘使用情况
     *
     * @return array
     */
    protected function getDiskUsage()
    {
        // 获取应用根目录所在磁盘的使用情况
        $rootPath = app()->getRootPath();
        $diskInfo = disk_free_space($rootPath);
        $diskTotal = disk_total_space($rootPath);
        $diskUsed = $diskTotal - $diskInfo;
        $diskPercent = round(($diskUsed / $diskTotal) * 100, 2);
        
        return [
            'timestamp' => time(),
            'path' => $rootPath,
            'total' => $diskTotal,
            'used' => $diskUsed,
            'free' => $diskInfo,
            'percent' => $diskPercent
        ];
    }
    
    /**
     * 获取进程信息
     *
     * @return array
     */
    protected function getProcessInfo()
    {
        // 获取子进程信息
        $childProcesses = ChildProcess::all();
        
        // 获取队列工作进程信息
        $queueWorkers = QueueWorker::all();
        
        return [
            'timestamp' => time(),
            'child_processes' => $childProcesses,
            'queue_workers' => $queueWorkers
        ];
    }
    
    /**
     * 分析监控数据
     *
     * @param array $data
     * @return array
     */
    protected function analyzeMonitoringData($data)
    {
        // 分析CPU使用率
        $cpuUsage = array_column($data['cpu'], 'usage');
        $cpuAvg = count($cpuUsage) > 0 ? array_sum($cpuUsage) / count($cpuUsage) : 0;
        $cpuMax = count($cpuUsage) > 0 ? max($cpuUsage) : 0;
        $cpuMin = count($cpuUsage) > 0 ? min($cpuUsage) : 0;
        
        // 分析内存使用情况
        $memoryPercent = array_column($data['memory'], 'percent');
        $memoryAvg = count($memoryPercent) > 0 ? array_sum($memoryPercent) / count($memoryPercent) : 0;
        $memoryMax = count($memoryPercent) > 0 ? max($memoryPercent) : 0;
        $memoryMin = count($memoryPercent) > 0 ? min($memoryPercent) : 0;
        
        // 分析磁盘使用情况
        $diskPercent = array_column($data['disk'], 'percent');
        $diskAvg = count($diskPercent) > 0 ? array_sum($diskPercent) / count($diskPercent) : 0;
        
        // 分析进程信息
        $processCount = [];
        foreach ($data['processes'] as $process) {
            $childCount = count($process['child_processes']);
            $workerCount = count($process['queue_workers']);
            $processCount[] = $childCount + $workerCount;
        }
        $processAvg = count($processCount) > 0 ? array_sum($processCount) / count($processCount) : 0;
        $processMax = count($processCount) > 0 ? max($processCount) : 0;
        
        return [
            'cpu' => [
                'avg' => round($cpuAvg, 2),
                'max' => $cpuMax,
                'min' => $cpuMin
            ],
            'memory' => [
                'avg' => round($memoryAvg, 2),
                'max' => $memoryMax,
                'min' => $memoryMin
            ],
            'disk' => [
                'avg' => round($diskAvg, 2)
            ],
            'processes' => [
                'avg' => round($processAvg, 2),
                'max' => $processMax
            ]
        ];
    }
}
