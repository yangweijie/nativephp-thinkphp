<?php

namespace tests\integration;

use think\App;
use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\QueueWorker;
use Native\ThinkPHP\ProgressBar;
use Native\ThinkPHP\Client\Client;
use Mockery;

/**
 * 队列工作器和进度条集成测试
 */
class QueueWorkerProgressBarTest extends TestCase
{
    /**
     * @var \Native\ThinkPHP\QueueWorker
     */
    protected $queueWorker;
    
    /**
     * @var \Native\ThinkPHP\ProgressBar
     */
    protected $progressBar;
    
    /**
     * @var \Mockery\MockInterface
     */
    protected $clientMock;
    
    /**
     * @var \Mockery\MockInterface
     */
    protected $appMock;
    
    /**
     * 设置测试环境
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 创建App模拟对象
        $this->appMock = Mockery::mock(App::class);
        
        // 创建事件模拟对象
        $eventMock = Mockery::mock('alias:think\facade\Event');
        $eventMock->shouldReceive('listen')->andReturn(true);
        $eventMock->shouldReceive('trigger')->andReturn(true);
        
        // 将事件模拟对象注入到App模拟对象中
        $this->appMock->shouldReceive('event')->andReturn($eventMock);
        
        // 创建Client模拟对象
        $this->clientMock = Mockery::mock(Client::class);
        
        // 创建QueueWorker实例
        $this->queueWorker = new QueueWorker($this->appMock);
        
        // 创建ProgressBar实例
        $this->progressBar = new ProgressBar($this->appMock);
        
        // 使用反射将模拟的Client注入到QueueWorker实例中
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, $this->clientMock);
        
        // 使用反射将模拟的Client注入到ProgressBar实例中
        $reflection = new \ReflectionClass($this->progressBar);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->progressBar, $this->clientMock);
    }
    
    /**
     * 清理测试环境
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
    
    /**
     * 测试队列工作进程进度监控
     */
    public function testQueueWorkerProgressMonitoring()
    {
        // 设置模拟响应 - 启动队列工作进程
        $upResponseMock = Mockery::mock();
        $upResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 创建进度条
        $createResponseMock = Mockery::mock();
        $createResponseMock->shouldReceive('json')->with('id')->andReturn('test-progress-bar');
        
        // 设置模拟响应 - 启动进度条
        $startResponseMock = Mockery::mock();
        $startResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 前进进度条
        $advanceResponseMock = Mockery::mock();
        $advanceResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 完成进度条
        $finishResponseMock = Mockery::mock();
        $finishResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 启动队列工作进程
        $this->clientMock->shouldReceive('post')
            ->with('queue-worker/up', [
                'connection' => 'default',
                'queue' => 'default',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'force' => false,
                'persistent' => true,
            ])
            ->andReturn($upResponseMock);
        
        // 设置Client模拟对象的行为 - 创建进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/create', ['max_steps' => 100])
            ->andReturn($createResponseMock);
        
        // 设置Client模拟对象的行为 - 启动进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/start', ['id' => 'test-progress-bar'])
            ->andReturn($startResponseMock);
        
        // 设置Client模拟对象的行为 - 前进进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/advance', ['id' => 'test-progress-bar', 'step' => 1])
            ->andReturn($advanceResponseMock);
        
        // 设置Client模拟对象的行为 - 完成进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/finish', ['id' => 'test-progress-bar'])
            ->andReturn($finishResponseMock);
        
        // 创建队列监控器类
        $queueMonitor = new class($this->queueWorker, $this->progressBar) {
            protected $queueWorker;
            protected $progressBar;
            
            public function __construct($queueWorker, $progressBar)
            {
                $this->queueWorker = $queueWorker;
                $this->progressBar = $progressBar;
            }
            
            public function monitorQueue($connection, $queue, $totalJobs)
            {
                // 启动队列工作进程
                $this->queueWorker->up($connection, $queue, 3, 60, 3, false, true);
                
                // 创建进度条
                $progressBar = $this->progressBar->create($totalJobs);
                $progressBar->start();
                
                // 模拟监控队列进度
                for ($i = 0; $i < $totalJobs; $i++) {
                    // 更新进度
                    $progressBar->advance();
                    
                    // 模拟处理延迟
                    usleep(10000); // 10毫秒
                }
                
                // 完成进度条
                $progressBar->finish();
                
                return true;
            }
        };
        
        // 监控队列进度
        $result = $queueMonitor->monitorQueue('default', 'default', 100);
        
        // 断言结果
        $this->assertTrue($result);
    }
    
    /**
     * 测试队列任务批处理
     */
    public function testQueueBatchProcessing()
    {
        // 设置模拟响应 - 获取所有队列工作进程
        $allResponseMock = Mockery::mock();
        $allResponseMock->shouldReceive('json')->with('workers')->andReturn([
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
            'queue-worker-default-emails' => [
                'connection' => 'default',
                'queue' => 'emails',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
        ]);
        
        // 设置模拟响应 - 创建进度条
        $createResponseMock = Mockery::mock();
        $createResponseMock->shouldReceive('json')->with('id')->andReturn('test-progress-bar');
        
        // 设置模拟响应 - 启动进度条
        $startResponseMock = Mockery::mock();
        $startResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 前进进度条
        $advanceResponseMock = Mockery::mock();
        $advanceResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 完成进度条
        $finishResponseMock = Mockery::mock();
        $finishResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 获取所有队列工作进程
        $this->clientMock->shouldReceive('get')
            ->with('queue-worker/all')
            ->andReturn($allResponseMock);
        
        // 设置Client模拟对象的行为 - 创建进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/create', ['max_steps' => 2])
            ->andReturn($createResponseMock);
        
        // 设置Client模拟对象的行为 - 启动进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/start', ['id' => 'test-progress-bar'])
            ->andReturn($startResponseMock);
        
        // 设置Client模拟对象的行为 - 前进进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/advance', ['id' => 'test-progress-bar', 'step' => 1])
            ->andReturn($advanceResponseMock);
        
        // 设置Client模拟对象的行为 - 完成进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/finish', ['id' => 'test-progress-bar'])
            ->andReturn($finishResponseMock);
        
        // 创建队列批处理器类
        $queueBatchProcessor = new class($this->queueWorker, $this->progressBar) {
            protected $queueWorker;
            protected $progressBar;
            
            public function __construct($queueWorker, $progressBar)
            {
                $this->queueWorker = $queueWorker;
                $this->progressBar = $progressBar;
            }
            
            public function processQueues()
            {
                // 获取所有队列工作进程
                $workers = $this->queueWorker->all();
                
                // 创建进度条
                $progressBar = $this->progressBar->create(count($workers));
                $progressBar->start();
                
                // 处理每个队列
                foreach ($workers as $alias => $worker) {
                    // 处理队列
                    $this->processQueue($worker['connection'], $worker['queue']);
                    
                    // 更新进度
                    $progressBar->advance();
                }
                
                // 完成进度条
                $progressBar->finish();
                
                return count($workers);
            }
            
            protected function processQueue($connection, $queue)
            {
                // 模拟处理队列
                // 在实际应用中，这里可能会执行一些队列相关的操作
                return true;
            }
        };
        
        // 处理队列
        $count = $queueBatchProcessor->processQueues();
        
        // 断言结果
        $this->assertEquals(2, $count);
    }
    
    /**
     * 测试队列工作进程状态监控
     */
    public function testQueueWorkerStatusMonitoring()
    {
        // 设置模拟响应 - 获取队列工作进程状态
        $statusResponseMock = Mockery::mock();
        $statusResponseMock->shouldReceive('json')->with('status')->andReturn('running');
        
        // 设置模拟响应 - 创建进度条
        $createResponseMock = Mockery::mock();
        $createResponseMock->shouldReceive('json')->with('id')->andReturn('test-progress-bar');
        
        // 设置模拟响应 - 设置进度条标题
        $setTitleResponseMock = Mockery::mock();
        $setTitleResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 设置进度条描述
        $setDescriptionResponseMock = Mockery::mock();
        $setDescriptionResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 获取队列工作进程状态
        $this->clientMock->shouldReceive('get')
            ->with('queue-worker/status', [
                'connection' => 'default',
                'queue' => 'default',
            ])
            ->andReturn($statusResponseMock);
        
        // 设置Client模拟对象的行为 - 创建进度条
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/create', ['max_steps' => 100])
            ->andReturn($createResponseMock);
        
        // 设置Client模拟对象的行为 - 设置进度条标题
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/set-title', ['id' => 'test-progress-bar', 'title' => '队列状态监控'])
            ->andReturn($setTitleResponseMock);
        
        // 设置Client模拟对象的行为 - 设置进度条描述
        $this->clientMock->shouldReceive('post')
            ->with('progress-bar/set-description', ['id' => 'test-progress-bar', 'description' => '队列状态: running'])
            ->andReturn($setDescriptionResponseMock);
        
        // 创建队列状态监控器类
        $queueStatusMonitor = new class($this->queueWorker, $this->progressBar) {
            protected $queueWorker;
            protected $progressBar;
            
            public function __construct($queueWorker, $progressBar)
            {
                $this->queueWorker = $queueWorker;
                $this->progressBar = $progressBar;
            }
            
            public function monitorStatus($connection, $queue)
            {
                // 获取队列工作进程状态
                $status = $this->queueWorker->status($connection, $queue);
                
                // 创建进度条
                $progressBar = $this->progressBar->create(100);
                $progressBar->setTitle('队列状态监控');
                $progressBar->setDescription("队列状态: {$status}");
                
                return $status;
            }
        };
        
        // 监控队列状态
        $status = $queueStatusMonitor->monitorStatus('default', 'default');
        
        // 断言结果
        $this->assertEquals('running', $status);
    }
}
