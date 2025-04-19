<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\ChildProcess;
use think\App;
use Mockery;

class ChildProcessTest extends TestCase
{
    protected $app;
    protected $childProcess;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        // 模拟 App 实例
        $this->app = Mockery::mock(App::class);

        // 模拟 Client 实例
        $this->client = Mockery::mock('Native\ThinkPHP\Client\Client');

        // 创建 ChildProcess 实例
        $this->childProcess = new ChildProcess($this->app);

        // 使用反射设置 client 属性
        $reflection = new \ReflectionClass($this->childProcess);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->childProcess, $this->client);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testStart()
    {
        // 模拟 Client 的 post 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('pid')->andReturn(12345);

        $this->client->shouldReceive('post')
            ->with('child-process/start', [
                'cmd' => 'echo Hello World',
                'alias' => 'test-process',
                'cwd' => null,
                'persistent' => false,
                'env' => [],
            ])
            ->andReturn($response);

        // 调用 start 方法
        $result = $this->childProcess->start('echo Hello World', 'test-process');

        // 验证结果
        $this->assertSame($this->childProcess, $result);
    }

    public function testGet()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('process')->andReturn([
            'cmd' => 'echo Hello World',
            'alias' => 'test-process',
            'cwd' => null,
            'persistent' => false,
            'env' => [],
            'pid' => 12345,
            'status' => 'running',
        ]);

        $this->client->shouldReceive('get')
            ->with('child-process/get', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 get 方法
        $result = $this->childProcess->get('test-process');

        // 验证结果
        $this->assertIsArray($result);
        $this->assertEquals('test-process', $result['alias']);
        $this->assertEquals(12345, $result['pid']);
        $this->assertEquals('running', $result['status']);
    }

    public function testAll()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('processes')->andReturn([
            'test-process-1' => [
                'cmd' => 'echo Hello World 1',
                'alias' => 'test-process-1',
                'pid' => 12345,
                'status' => 'running',
            ],
            'test-process-2' => [
                'cmd' => 'echo Hello World 2',
                'alias' => 'test-process-2',
                'pid' => 12346,
                'status' => 'running',
            ],
        ]);

        $this->client->shouldReceive('get')
            ->with('child-process/all')
            ->andReturn($response);

        // 调用 all 方法
        $result = $this->childProcess->all();

        // 验证结果
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('test-process-1', $result);
        $this->assertArrayHasKey('test-process-2', $result);
    }

    public function testStop()
    {
        // 模拟 Client 的 post 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('success')->andReturn(true);

        $this->client->shouldReceive('post')
            ->with('child-process/stop', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 stop 方法
        $result = $this->childProcess->stop('test-process');

        // 验证结果
        $this->assertTrue($result);
    }

    public function testRestart()
    {
        // 模拟 Client 的 post 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('success')->andReturn(true);
        $response->shouldReceive('json')->with('pid')->andReturn(12345);

        $this->client->shouldReceive('post')
            ->with('child-process/restart', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 restart 方法
        $result = $this->childProcess->restart('test-process');

        // 验证结果
        $this->assertTrue($result);
    }

    public function testMessage()
    {
        // 模拟 Client 的 post 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('success')->andReturn(true);

        $this->client->shouldReceive('post')
            ->with('child-process/message', [
                'message' => 'Hello from parent',
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 message 方法
        $result = $this->childProcess->message('Hello from parent', 'test-process');

        // 验证结果
        $this->assertTrue($result);
    }

    public function testPhp()
    {
        // 模拟 start 方法
        $childProcess = Mockery::mock(ChildProcess::class)->makePartial();
        $childProcess->shouldReceive('start')
            ->with('php ' . escapeshellarg('script.php'), 'test-php', null, false, [])
            ->andReturn($childProcess);

        // 调用 php 方法
        $result = $childProcess->php('script.php', 'test-php');

        // 验证结果
        $this->assertSame($childProcess, $result);
    }

    public function testArtisan()
    {
        // 模拟 App 的 getRootPath 方法
        $this->app->shouldReceive('getRootPath')->andReturn('/path/to/app/');

        // 模拟 start 方法
        $childProcess = Mockery::mock(ChildProcess::class)->makePartial();
        $childProcess->shouldReceive('start')
            ->with('php ' . escapeshellarg('/path/to/app/think') . ' migrate', 'test-artisan', null, false, [])
            ->andReturn($childProcess);

        // 使用反射设置 app 属性
        $reflection = new \ReflectionClass($childProcess);
        $property = $reflection->getProperty('app');
        $property->setAccessible(true);
        $property->setValue($childProcess, $this->app);

        // 调用 artisan 方法
        $result = $childProcess->artisan('migrate', 'test-artisan');

        // 验证结果
        $this->assertSame($childProcess, $result);
    }

    public function testExists()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('exists')->andReturn(true);

        $this->client->shouldReceive('get')
            ->with('child-process/exists', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 exists 方法
        $result = $this->childProcess->exists('test-process');

        // 验证结果
        $this->assertTrue($result);
    }

    public function testIsRunning()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('running')->andReturn(true);

        $this->client->shouldReceive('get')
            ->with('child-process/is-running', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 isRunning 方法
        $result = $this->childProcess->isRunning('test-process');

        // 验证结果
        $this->assertTrue($result);
    }

    public function testGetPid()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('pid')->andReturn(12345);

        $this->client->shouldReceive('get')
            ->with('child-process/get-pid', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 getPid 方法
        $result = $this->childProcess->getPid('test-process');

        // 验证结果
        $this->assertEquals(12345, $result);
    }

    public function testGetStatus()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('status')->andReturn('running');

        $this->client->shouldReceive('get')
            ->with('child-process/get-status', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 getStatus 方法
        $result = $this->childProcess->getStatus('test-process');

        // 验证结果
        $this->assertEquals('running', $result);
    }

    public function testGetOutput()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('output')->andReturn('Hello World');

        $this->client->shouldReceive('get')
            ->with('child-process/get-output', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 getOutput 方法
        $result = $this->childProcess->getOutput('test-process');

        // 验证结果
        $this->assertEquals('Hello World', $result);
    }

    public function testGetError()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('error')->andReturn('Error message');

        $this->client->shouldReceive('get')
            ->with('child-process/get-error', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 getError 方法
        $result = $this->childProcess->getError('test-process');

        // 验证结果
        $this->assertEquals('Error message', $result);
    }

    public function testGetExitCode()
    {
        // 模拟 Client 的 get 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('exit_code')->andReturn(0);

        $this->client->shouldReceive('get')
            ->with('child-process/get-exit-code', [
                'alias' => 'test-process',
            ])
            ->andReturn($response);

        // 调用 getExitCode 方法
        $result = $this->childProcess->getExitCode('test-process');

        // 验证结果
        $this->assertEquals(0, $result);
    }

    public function testCleanup()
    {
        // 模拟 Client 的 post 方法
        $response = Mockery::mock();
        $response->shouldReceive('json')->with('count')->andReturn(2);

        $this->client->shouldReceive('post')
            ->with('child-process/cleanup')
            ->andReturn($response);

        // 调用 cleanup 方法
        $result = $this->childProcess->cleanup();

        // 验证结果
        $this->assertEquals(2, $result);
    }
}
