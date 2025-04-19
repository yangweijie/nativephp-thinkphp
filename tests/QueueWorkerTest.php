<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\QueueWorker;
use Native\ThinkPHP\Facades\ChildProcess;
use think\App;
use Mockery;

class QueueWorkerTest extends TestCase
{
    protected $app;
    protected $queueWorker;

    protected function setUp(): void
    {
        parent::setUp();

        // 模拟 App 实例
        $this->app = Mockery::mock(App::class);

        // 创建 QueueWorker 实例
        $this->queueWorker = new QueueWorker($this->app);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUp()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(false);
        ChildProcess::shouldReceive('artisan')
            ->with('queue:work', 'queue-worker-default-default', [
                'default',
                '--queue=default',
                '--tries=3',
                '--timeout=60',
                '--sleep=3',
            ], null, true)
            ->once();

        // 调用 up 方法
        $result = $this->queueWorker->up();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testUpWithExistingRunningWorker()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('artisan')->never();

        // 调用 up 方法
        $result = $this->queueWorker->up();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testUpWithExistingStoppedWorker()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(false);
        ChildProcess::shouldReceive('restart')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('artisan')->never();

        // 调用 up 方法
        $result = $this->queueWorker->up();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testDown()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('stop')->with('queue-worker-default-default')->andReturn(true);

        // 调用 down 方法
        $result = $this->queueWorker->down();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testDownWithNonExistingWorker()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(false);
        ChildProcess::shouldReceive('stop')->never();

        // 调用 down 方法
        $result = $this->queueWorker->down();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testRestart()
    {
        // 模拟 QueueWorker 的 down 和 up 方法
        $queueWorker = Mockery::mock(QueueWorker::class)->makePartial();
        $queueWorker->shouldReceive('down')->with('default', 'default')->andReturn(true);
        $queueWorker->shouldReceive('up')
            ->with('default', 'default', 3, 60, 3, true, true)
            ->andReturn(true);

        // 调用 restart 方法
        $result = $queueWorker->restart();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testStatus()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);

        // 调用 status 方法
        $result = $this->queueWorker->status();

        // 验证结果
        $this->assertEquals('running', $result);
    }

    public function testStatusWithNonExistingWorker()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(false);
        ChildProcess::shouldReceive('isRunning')->never();

        // 调用 status 方法
        $result = $this->queueWorker->status();

        // 验证结果
        $this->assertNull($result);
    }

    public function testStatusWithStoppedWorker()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(false);

        // 调用 status 方法
        $result = $this->queueWorker->status();

        // 验证结果
        $this->assertEquals('stopped', $result);
    }

    public function testAll()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, [
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
            'queue-worker-redis-emails' => [
                'connection' => 'redis',
                'queue' => 'emails',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
        ]);

        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getPid')->with('queue-worker-default-default')->andReturn(12345);

        ChildProcess::shouldReceive('exists')->with('queue-worker-redis-emails')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-redis-emails')->andReturn(false);
        ChildProcess::shouldReceive('getPid')->with('queue-worker-redis-emails')->andReturn(12346);

        // 调用 all 方法
        $result = $this->queueWorker->all();

        // 验证结果
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('queue-worker-default-default', $result);
        $this->assertArrayHasKey('queue-worker-redis-emails', $result);
        $this->assertEquals('running', $result['queue-worker-default-default']['status']);
        $this->assertEquals('stopped', $result['queue-worker-redis-emails']['status']);
        $this->assertEquals(12345, $result['queue-worker-default-default']['pid']);
        $this->assertEquals(12346, $result['queue-worker-redis-emails']['pid']);
    }

    public function testGet()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, [
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
        ]);

        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getPid')->with('queue-worker-default-default')->andReturn(12345);

        // 调用 get 方法
        $result = $this->queueWorker->get();

        // 验证结果
        $this->assertIsArray($result);
        $this->assertEquals('default', $result['connection']);
        $this->assertEquals('default', $result['queue']);
        $this->assertEquals(3, $result['tries']);
        $this->assertEquals(60, $result['timeout']);
        $this->assertEquals(3, $result['sleep']);
        $this->assertTrue($result['persistent']);
        $this->assertEquals('running', $result['status']);
        $this->assertEquals(12345, $result['pid']);
    }

    public function testGetWithNonExistingWorker()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, []);

        // 调用 get 方法
        $result = $this->queueWorker->get();

        // 验证结果
        $this->assertNull($result);
    }

    public function testCleanup()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, [
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
            ],
            'queue-worker-redis-emails' => [
                'connection' => 'redis',
                'queue' => 'emails',
            ],
        ]);

        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('stop')->with('queue-worker-default-default')->never();

        ChildProcess::shouldReceive('exists')->with('queue-worker-redis-emails')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-redis-emails')->andReturn(false);
        ChildProcess::shouldReceive('stop')->with('queue-worker-redis-emails')->once();

        // 调用 cleanup 方法
        $result = $this->queueWorker->cleanup();

        // 验证结果
        $this->assertEquals(1, $result);
    }

    public function testDownAll()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, [
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
            ],
            'queue-worker-redis-emails' => [
                'connection' => 'redis',
                'queue' => 'emails',
            ],
        ]);

        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('stop')->with('queue-worker-default-default')->once();

        ChildProcess::shouldReceive('exists')->with('queue-worker-redis-emails')->andReturn(true);
        ChildProcess::shouldReceive('stop')->with('queue-worker-redis-emails')->once();

        // 调用 downAll 方法
        $result = $this->queueWorker->downAll();

        // 验证结果
        $this->assertEquals(2, $result);
    }

    public function testRestartAll()
    {
        // 设置 workers 属性
        $reflection = new \ReflectionClass($this->queueWorker);
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue($this->queueWorker, [
            'queue-worker-default-default' => [
                'connection' => 'default',
                'queue' => 'default',
            ],
            'queue-worker-redis-emails' => [
                'connection' => 'redis',
                'queue' => 'emails',
            ],
        ]);

        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('restart')->with('queue-worker-default-default')->once();

        ChildProcess::shouldReceive('exists')->with('queue-worker-redis-emails')->andReturn(true);
        ChildProcess::shouldReceive('restart')->with('queue-worker-redis-emails')->once();

        // 调用 restartAll 方法
        $result = $this->queueWorker->restartAll();

        // 验证结果
        $this->assertEquals(2, $result);
    }

    public function testExists()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);

        // 调用 exists 方法
        $result = $this->queueWorker->exists();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testIsRunning()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('isRunning')->with('queue-worker-default-default')->andReturn(true);

        // 调用 isRunning 方法
        $result = $this->queueWorker->isRunning();

        // 验证结果
        $this->assertTrue($result);
    }

    public function testGetPid()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getPid')->with('queue-worker-default-default')->andReturn(12345);

        // 调用 getPid 方法
        $result = $this->queueWorker->getPid();

        // 验证结果
        $this->assertEquals(12345, $result);
    }

    public function testGetOutput()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getOutput')->with('queue-worker-default-default')->andReturn('Output text');

        // 调用 getOutput 方法
        $result = $this->queueWorker->getOutput();

        // 验证结果
        $this->assertEquals('Output text', $result);
    }

    public function testGetError()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getError')->with('queue-worker-default-default')->andReturn('Error text');

        // 调用 getError 方法
        $result = $this->queueWorker->getError();

        // 验证结果
        $this->assertEquals('Error text', $result);
    }

    public function testGetExitCode()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')->with('queue-worker-default-default')->andReturn(true);
        ChildProcess::shouldReceive('getExitCode')->with('queue-worker-default-default')->andReturn(0);

        // 调用 getExitCode 方法
        $result = $this->queueWorker->getExitCode();

        // 验证结果
        $this->assertEquals(0, $result);
    }
}
