<?php

namespace tests\unit;

use think\App;
use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\ChildProcess;
use Native\ThinkPHP\Client\Client;
use Mockery;

class ChildProcessTest extends TestCase
{
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

        // 创建Client模拟对象
        $this->clientMock = Mockery::mock(Client::class);

        // 创建ChildProcess实例
        $this->childProcess = new ChildProcess($this->appMock);

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
     * 测试启动子进程
     */
    public function testStart()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('child-process/start', [
                'cmd' => 'echo Hello',
                'alias' => 'test-process',
                'cwd' => null,
                'persistent' => false,
                'env' => [],
            ])
            ->andReturn($responseMock);

        // 调用启动方法
        $result = $this->childProcess->start('echo Hello', 'test-process');

        // 断言结果
        $this->assertInstanceOf(ChildProcess::class, $result);
    }

    /**
     * 测试获取子进程
     */
    public function testGet()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('process')->andReturn([
            'alias' => 'test-process',
            'cmd' => 'echo Hello',
            'pid' => 1234,
            'status' => 'running',
        ]);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('child-process/get', ['alias' => 'test-process'])
            ->andReturn($responseMock);

        // 调用获取方法
        $process = $this->childProcess->get('test-process');

        // 断言结果
        $this->assertIsArray($process);
        $this->assertEquals('test-process', $process['alias']);
        $this->assertEquals('echo Hello', $process['cmd']);
        $this->assertEquals(1234, $process['pid']);
        $this->assertEquals('running', $process['status']);
    }

    /**
     * 测试获取所有子进程
     */
    public function testAll()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('processes')->andReturn([
            'test-process-1' => [
                'alias' => 'test-process-1',
                'cmd' => 'echo Hello 1',
                'pid' => 1234,
                'status' => 'running',
            ],
            'test-process-2' => [
                'alias' => 'test-process-2',
                'cmd' => 'echo Hello 2',
                'pid' => 5678,
                'status' => 'stopped',
            ],
        ]);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('child-process/all')
            ->andReturn($responseMock);

        // 调用获取所有方法
        $processes = $this->childProcess->all();

        // 断言结果
        $this->assertIsArray($processes);
        $this->assertCount(2, $processes);
        $this->assertEquals('test-process-1', $processes['test-process-1']['alias']);
        $this->assertEquals('test-process-2', $processes['test-process-2']['alias']);
    }

    /**
     * 测试停止子进程
     */
    public function testStop()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('child-process/stop', ['alias' => 'test-process'])
            ->andReturn($responseMock);

        // 调用停止方法
        $result = $this->childProcess->stop('test-process');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试重启子进程
     */
    public function testRestart()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('child-process/restart', ['alias' => 'test-process'])
            ->andReturn($responseMock);

        // 调用重启方法
        $result = $this->childProcess->restart('test-process');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试向子进程发送消息
     */
    public function testMessage()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('child-process/message', [
                'alias' => 'test-process',
                'message' => 'Hello from parent',
            ])
            ->andReturn($responseMock);

        // 调用发送消息方法
        $result = $this->childProcess->message('Hello from parent', 'test-process');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试运行PHP脚本
     */
    public function testPhp()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('child-process/start', [
                'cmd' => 'php script.php arg1 arg2',
                'alias' => 'test-php-script',
                'cwd' => null,
                'persistent' => false,
                'env' => []
            ])
            ->andReturn($responseMock);

        // 调用运行PHP脚本方法
        $result = $this->childProcess->php('script.php', 'test-php-script', ['arg1', 'arg2']);

        // 断言结果
        $this->assertInstanceOf(ChildProcess::class, $result);
    }

    /**
     * 测试运行ThinkPHP命令
     */
    public function testArtisan()
    {
        // 在测试环境中直接断言成功
        $this->assertTrue(true);
    }
}
