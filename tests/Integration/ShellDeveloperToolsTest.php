<?php

namespace tests\integration;

use think\App;
use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\Shell;
use Native\ThinkPHP\DeveloperTools;
use Native\ThinkPHP\Client\Client;
use Mockery;

/**
 * Shell命令执行和开发者工具集成测试
 */
class ShellDeveloperToolsTest extends TestCase
{
    /**
     * @var \Native\ThinkPHP\Shell
     */
    protected $shell;
    
    /**
     * @var \Native\ThinkPHP\DeveloperTools
     */
    protected $developerTools;
    
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
        $eventMock->shouldReceive('trigger')->andReturn(true);
        
        // 将事件模拟对象注入到App模拟对象中
        $this->appMock->shouldReceive('event')->andReturn($eventMock);
        
        // 创建Client模拟对象
        $this->clientMock = Mockery::mock(Client::class);
        
        // 创建Shell实例
        $this->shell = new Shell($this->appMock);
        
        // 创建DeveloperTools实例
        $this->developerTools = new DeveloperTools($this->appMock);
        
        // 使用反射将模拟的Client注入到Shell实例中
        $reflection = new \ReflectionClass($this->shell);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->shell, $this->clientMock);
        
        // 使用反射将模拟的Client注入到DeveloperTools实例中
        $reflection = new \ReflectionClass($this->developerTools);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->developerTools, $this->clientMock);
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
     * 测试命令执行性能分析
     */
    public function testCommandExecutionProfiling()
    {
        // 设置模拟响应 - 开始性能分析
        $startProfilingResponseMock = Mockery::mock();
        $startProfilingResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 执行命令
        $execResponseMock = Mockery::mock();
        $execResponseMock->shouldReceive('json')->with('output')->andReturn('Hello, World!');
        
        // 设置模拟响应 - 结束性能分析
        $stopProfilingResponseMock = Mockery::mock();
        $stopProfilingResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 开始性能分析
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/start-profiling', ['label' => '命令执行'])
            ->andReturn($startProfilingResponseMock);
        
        // 设置Client模拟对象的行为 - 执行命令
        $this->clientMock->shouldReceive('post')
            ->with('shell/exec', ['command' => 'echo Hello, World!'])
            ->andReturn($execResponseMock);
        
        // 设置Client模拟对象的行为 - 结束性能分析
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/stop-profiling', ['label' => '命令执行'])
            ->andReturn($stopProfilingResponseMock);
        
        // 创建命令执行分析器类
        $commandProfiler = new class($this->shell, $this->developerTools) {
            protected $shell;
            protected $developerTools;
            
            public function __construct($shell, $developerTools)
            {
                $this->shell = $shell;
                $this->developerTools = $developerTools;
            }
            
            public function executeWithProfiling($command)
            {
                // 开始性能分析
                $this->developerTools->startProfiling('命令执行');
                
                // 执行命令
                $output = $this->shell->exec($command);
                
                // 结束性能分析
                $this->developerTools->stopProfiling('命令执行');
                
                return $output;
            }
        };
        
        // 执行命令并进行性能分析
        $output = $commandProfiler->executeWithProfiling('echo Hello, World!');
        
        // 断言结果
        $this->assertEquals('Hello, World!', $output);
    }
    
    /**
     * 测试命令执行时间测量
     */
    public function testCommandExecutionTiming()
    {
        // 设置模拟响应 - 开始计时
        $timeResponseMock = Mockery::mock();
        $timeResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 执行命令
        $execResponseMock = Mockery::mock();
        $execResponseMock->shouldReceive('json')->with('output')->andReturn('Hello, World!');
        
        // 设置模拟响应 - 结束计时
        $timeEndResponseMock = Mockery::mock();
        $timeEndResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置Client模拟对象的行为 - 开始计时
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/time', ['label' => '命令执行时间'])
            ->andReturn($timeResponseMock);
        
        // 设置Client模拟对象的行为 - 执行命令
        $this->clientMock->shouldReceive('post')
            ->with('shell/exec', ['command' => 'echo Hello, World!'])
            ->andReturn($execResponseMock);
        
        // 设置Client模拟对象的行为 - 结束计时
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/time-end', ['label' => '命令执行时间'])
            ->andReturn($timeEndResponseMock);
        
        // 创建命令执行计时器类
        $commandTimer = new class($this->shell, $this->developerTools) {
            protected $shell;
            protected $developerTools;
            
            public function __construct($shell, $developerTools)
            {
                $this->shell = $shell;
                $this->developerTools = $developerTools;
            }
            
            public function executeWithTiming($command)
            {
                // 开始计时
                $this->developerTools->time('命令执行时间');
                
                // 执行命令
                $output = $this->shell->exec($command);
                
                // 结束计时
                $this->developerTools->timeEnd('命令执行时间');
                
                return $output;
            }
        };
        
        // 执行命令并进行计时
        $output = $commandTimer->executeWithTiming('echo Hello, World!');
        
        // 断言结果
        $this->assertEquals('Hello, World!', $output);
    }
    
    /**
     * 测试命令执行内存监控
     */
    public function testCommandExecutionMemoryMonitoring()
    {
        // 设置模拟响应 - 开始内存监控
        $startMemoryMonitorResponseMock = Mockery::mock();
        $startMemoryMonitorResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 执行命令
        $execResponseMock = Mockery::mock();
        $execResponseMock->shouldReceive('json')->with('output')->andReturn('Hello, World!');
        
        // 设置模拟响应 - 结束内存监控
        $stopMemoryMonitorResponseMock = Mockery::mock();
        $stopMemoryMonitorResponseMock->shouldReceive('json')->with('memory_usage')->andReturn([
            'used' => 1024 * 1024,
            'diff' => 512 * 1024,
        ]);
        
        // 设置Client模拟对象的行为 - 开始内存监控
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/start-memory-monitor', ['label' => '命令执行内存'])
            ->andReturn($startMemoryMonitorResponseMock);
        
        // 设置Client模拟对象的行为 - 执行命令
        $this->clientMock->shouldReceive('post')
            ->with('shell/exec', ['command' => 'echo Hello, World!'])
            ->andReturn($execResponseMock);
        
        // 设置Client模拟对象的行为 - 结束内存监控
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/stop-memory-monitor', ['label' => '命令执行内存'])
            ->andReturn($stopMemoryMonitorResponseMock);
        
        // 创建命令执行内存监控器类
        $commandMemoryMonitor = new class($this->shell, $this->developerTools) {
            protected $shell;
            protected $developerTools;
            
            public function __construct($shell, $developerTools)
            {
                $this->shell = $shell;
                $this->developerTools = $developerTools;
            }
            
            public function executeWithMemoryMonitoring($command)
            {
                // 开始内存监控
                $this->developerTools->startMemoryMonitor('命令执行内存');
                
                // 执行命令
                $output = $this->shell->exec($command);
                
                // 结束内存监控
                $memoryUsage = $this->developerTools->stopMemoryMonitor('命令执行内存');
                
                return [
                    'output' => $output,
                    'memory_usage' => $memoryUsage,
                ];
            }
        };
        
        // 执行命令并进行内存监控
        $result = $commandMemoryMonitor->executeWithMemoryMonitoring('echo Hello, World!');
        
        // 断言结果
        $this->assertEquals('Hello, World!', $result['output']);
        $this->assertIsArray($result['memory_usage']);
        $this->assertEquals(1024 * 1024, $result['memory_usage']['used']);
        $this->assertEquals(512 * 1024, $result['memory_usage']['diff']);
    }
    
    /**
     * 测试命令执行调试
     */
    public function testCommandExecutionDebugging()
    {
        // 设置模拟响应 - 打印调试信息
        $logResponseMock = Mockery::mock();
        $logResponseMock->shouldReceive('json')->with('success')->andReturn(true);
        
        // 设置模拟响应 - 执行命令
        $execResponseMock = Mockery::mock();
        $execResponseMock->shouldReceive('json')->with('output')->andReturn('Hello, World!');
        $execResponseMock->shouldReceive('json')->with('exit_code')->andReturn(0);
        
        // 设置Client模拟对象的行为 - 打印调试信息
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/log', Mockery::on(function ($arg) {
                return isset($arg['message']) && (
                    $arg['message'] === '执行命令: echo Hello, World!' ||
                    $arg['message'] === '命令输出' ||
                    $arg['message'] === '命令退出码'
                );
            }))
            ->andReturn($logResponseMock);
        
        // 设置Client模拟对象的行为 - 执行命令
        $this->clientMock->shouldReceive('post')
            ->with('shell/exec', ['command' => 'echo Hello, World!'])
            ->andReturn($execResponseMock);
        
        // 创建命令执行调试器类
        $commandDebugger = new class($this->shell, $this->developerTools) {
            protected $shell;
            protected $developerTools;
            
            public function __construct($shell, $developerTools)
            {
                $this->shell = $shell;
                $this->developerTools = $developerTools;
            }
            
            public function executeWithDebugging($command)
            {
                // 打印调试信息
                $this->developerTools->log("执行命令: {$command}");
                
                // 执行命令
                $output = '';
                $exitCode = 0;
                $this->shell->exec($command, $output, $exitCode);
                
                // 打印输出和退出码
                $this->developerTools->log('命令输出', $output);
                $this->developerTools->log('命令退出码', $exitCode);
                
                return [
                    'output' => $output,
                    'exit_code' => $exitCode,
                ];
            }
        };
        
        // 执行命令并进行调试
        $result = $commandDebugger->executeWithDebugging('echo Hello, World!');
        
        // 断言结果
        $this->assertEquals('Hello, World!', $result['output']);
        $this->assertEquals(0, $result['exit_code']);
    }
}
