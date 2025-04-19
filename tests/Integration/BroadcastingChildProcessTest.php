<?php

namespace tests\integration;

use think\App;
use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\Broadcasting;
use Native\ThinkPHP\ChildProcess;
use Native\ThinkPHP\Client\Client;
use Mockery;

/**
 * 广播系统和子进程管理集成测试
 */
class BroadcastingChildProcessTest extends TestCase
{
    /**
     * @var \Native\ThinkPHP\Broadcasting
     */
    protected $broadcasting;
    
    /**
     * @var \Native\ThinkPHP\ChildProcess
     */
    protected $childProcess;
    
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
        
        // 创建Broadcasting实例
        $this->broadcasting = new Broadcasting($this->appMock);
        
        // 创建ChildProcess实例
        $this->childProcess = new ChildProcess($this->appMock);
        
        // 使用反射将模拟的Client注入到Broadcasting实例中
        $reflection = new \ReflectionClass($this->broadcasting);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->broadcasting, $this->clientMock);
        
        // 使用反射将模拟的Client注入到ChildProcess实例中
        $reflection = new \ReflectionClass($this->childProcess);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->childProcess, $this->clientMock);
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
     * 测试子进程事件广播
     */
    public function testChildProcessEventBroadcasting()
    {
        // 设置模拟响应 - 启动子进程
        $startResponseMock = Mockery::mock();
        $startResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 广播事件
        $broadcastResponseMock = Mockery::mock();
        $broadcastResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 启动子进程
        $this->clientMock->shouldReceive('post')
            ->with('child-process/start', [
                'cmd' => 'echo Hello, World!',
                'alias' => 'test-process',
                'cwd' => null,
                'persistent' => false,
                'env' => [],
            ])
            ->andReturn($startResponseMock);
        
        // 设置Client模拟对象的行为 - 广播事件
        $this->clientMock->shouldReceive('post')
            ->with('broadcasting/broadcast', [
                'channel' => 'processes',
                'event' => 'process-started',
                'data' => [
                    'alias' => 'test-process',
                    'cmd' => 'echo Hello, World!',
                ],
            ])
            ->andReturn($broadcastResponseMock);
        
        // 启动子进程
        $process = $this->childProcess->start('echo Hello, World!', 'test-process');
        
        // 广播子进程启动事件
        $result = $this->broadcasting->broadcast('processes', 'process-started', [
            'alias' => 'test-process',
            'cmd' => 'echo Hello, World!',
        ]);
        
        // 断言结果
        $this->assertInstanceOf(ChildProcess::class, $process);
        $this->assertTrue($result);
    }
    
    /**
     * 测试子进程输出广播
     */
    public function testChildProcessOutputBroadcasting()
    {
        // 设置模拟响应 - 获取子进程输出
        $outputResponseMock = Mockery::mock();
        $outputResponseMock->shouldReceive('json')->with('output')->andReturn('Hello, World!');
        
        // 设置模拟响应 - 广播事件
        $broadcastResponseMock = Mockery::mock();
        $broadcastResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 获取子进程输出
        $this->clientMock->shouldReceive('get')
            ->with('child-process/output', ['alias' => 'test-process'])
            ->andReturn($outputResponseMock);
        
        // 设置Client模拟对象的行为 - 广播事件
        $this->clientMock->shouldReceive('post')
            ->with('broadcasting/broadcast', [
                'channel' => 'processes',
                'event' => 'process-output',
                'data' => [
                    'alias' => 'test-process',
                    'output' => 'Hello, World!',
                ],
            ])
            ->andReturn($broadcastResponseMock);
        
        // 获取子进程输出
        $output = $this->childProcess->getOutput('test-process');
        
        // 广播子进程输出事件
        $result = $this->broadcasting->broadcast('processes', 'process-output', [
            'alias' => 'test-process',
            'output' => $output,
        ]);
        
        // 断言结果
        $this->assertEquals('Hello, World!', $output);
        $this->assertTrue($result);
    }
    
    /**
     * 测试子进程状态变化监听
     */
    public function testChildProcessStatusChangeListener()
    {
        // 设置模拟响应 - 监听事件
        $listenResponseMock = Mockery::mock();
        $listenResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 获取子进程状态
        $statusResponseMock = Mockery::mock();
        $statusResponseMock->shouldReceive('json')->with('status')->andReturn('running');
        
        // 设置Client模拟对象的行为 - 监听事件
        $this->clientMock->shouldReceive('post')
            ->with('broadcasting/listen', Mockery::on(function ($arg) {
                return $arg['channel'] === 'processes' && $arg['event'] === 'process-status-change' && isset($arg['id']);
            }))
            ->andReturn($listenResponseMock);
        
        // 设置Client模拟对象的行为 - 获取子进程状态
        $this->clientMock->shouldReceive('get')
            ->with('child-process/status', ['alias' => 'test-process'])
            ->andReturn($statusResponseMock);
        
        // 创建回调函数
        $callbackCalled = false;
        $callback = function ($data) use (&$callbackCalled) {
            $callbackCalled = true;
            return $data;
        };
        
        // 监听子进程状态变化事件
        $id = $this->broadcasting->listen('processes', 'process-status-change', $callback);
        
        // 获取子进程状态
        $status = $this->childProcess->getStatus('test-process');
        
        // 断言结果
        $this->assertIsString($id);
        $this->assertNotEmpty($id);
        $this->assertEquals('running', $status);
        
        // 模拟事件触发
        $reflection = new \ReflectionClass($this->broadcasting);
        $method = $reflection->getMethod('triggerCallback');
        $method->setAccessible(true);
        $method->invoke($this->broadcasting, $id, ['alias' => 'test-process', 'status' => 'running']);
        
        // 断言回调被调用
        $this->assertTrue($callbackCalled);
    }
    
    /**
     * 测试子进程管理器
     */
    public function testProcessManager()
    {
        // 创建进程管理器类
        $processManager = new class($this->broadcasting, $this->childProcess) {
            protected $broadcasting;
            protected $childProcess;
            
            public function __construct($broadcasting, $childProcess)
            {
                $this->broadcasting = $broadcasting;
                $this->childProcess = $childProcess;
            }
            
            public function startProcess($cmd, $alias)
            {
                // 启动子进程
                $process = $this->childProcess->start($cmd, $alias);
                
                // 广播子进程启动事件
                $this->broadcasting->broadcast('processes', 'process-started', [
                    'alias' => $alias,
                    'cmd' => $cmd,
                ]);
                
                return $process;
            }
            
            public function stopProcess($alias)
            {
                // 停止子进程
                $result = $this->childProcess->stop($alias);
                
                // 广播子进程停止事件
                $this->broadcasting->broadcast('processes', 'process-stopped', [
                    'alias' => $alias,
                ]);
                
                return $result;
            }
            
            public function monitorProcess($alias)
            {
                // 获取子进程状态
                $status = $this->childProcess->getStatus($alias);
                
                // 广播子进程状态事件
                $this->broadcasting->broadcast('processes', 'process-status', [
                    'alias' => $alias,
                    'status' => $status,
                ]);
                
                return $status;
            }
        };
        
        // 设置模拟响应 - 启动子进程
        $startResponseMock = Mockery::mock();
        $startResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 广播事件
        $broadcastResponseMock = Mockery::mock();
        $broadcastResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 获取子进程状态
        $statusResponseMock = Mockery::mock();
        $statusResponseMock->shouldReceive('json')->with('status')->andReturn('running');
        
        // 设置Client模拟对象的行为 - 启动子进程
        $this->clientMock->shouldReceive('post')
            ->with('child-process/start', [
                'cmd' => 'echo Hello, World!',
                'alias' => 'test-process',
                'cwd' => null,
                'persistent' => false,
                'env' => [],
            ])
            ->andReturn($startResponseMock);
        
        // 设置Client模拟对象的行为 - 广播事件
        $this->clientMock->shouldReceive('post')
            ->with('broadcasting/broadcast', Mockery::on(function ($arg) {
                return $arg['channel'] === 'processes' && in_array($arg['event'], ['process-started', 'process-status']);
            }))
            ->andReturn($broadcastResponseMock);
        
        // 设置Client模拟对象的行为 - 获取子进程状态
        $this->clientMock->shouldReceive('get')
            ->with('child-process/status', ['alias' => 'test-process'])
            ->andReturn($statusResponseMock);
        
        // 启动子进程
        $process = $processManager->startProcess('echo Hello, World!', 'test-process');
        
        // 监控子进程
        $status = $processManager->monitorProcess('test-process');
        
        // 断言结果
        $this->assertInstanceOf(ChildProcess::class, $process);
        $this->assertEquals('running', $status);
    }
}
