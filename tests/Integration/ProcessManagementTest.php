<?php

namespace tests\Integration;

use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\Facades\ChildProcess;
use Native\ThinkPHP\Facades\QueueWorker;
use think\App;
use Mockery;

class ProcessManagementTest extends TestCase
{
    protected $app;

    protected function setUp(): void
    {
        parent::setUp();

        // 模拟 App 实例
        $this->app = Mockery::mock(App::class);
        $this->app->shouldReceive('getRootPath')->andReturn('/path/to/app/');
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * 测试 ChildProcess 和 QueueWorker 之间的交互
     */
    public function testChildProcessAndQueueWorkerInteraction()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-default-default')
            ->andReturn(false);

        ChildProcess::shouldReceive('artisan')
            ->with('queue:work', 'queue-worker-default-default', [
                'default',
                '--queue=default',
                '--tries=3',
                '--timeout=60',
                '--sleep=3',
            ], null, true)
            ->once();

        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-default-default')
            ->andReturn(true);

        ChildProcess::shouldReceive('isRunning')
            ->with('queue-worker-default-default')
            ->andReturn(true);

        ChildProcess::shouldReceive('getPid')
            ->with('queue-worker-default-default')
            ->andReturn(12345);

        ChildProcess::shouldReceive('stop')
            ->with('queue-worker-default-default')
            ->andReturn(true);

        // 启动队列工作进程
        $result = QueueWorker::up();
        $this->assertTrue($result);

        // 检查队列工作进程状态
        $status = QueueWorker::status();
        $this->assertEquals('running', $status);

        // 获取队列工作进程 PID
        $pid = QueueWorker::getPid();
        $this->assertEquals(12345, $pid);

        // 停止队列工作进程
        $result = QueueWorker::down();
        $this->assertTrue($result);
    }

    /**
     * 测试多个队列工作进程的管理
     */
    public function testMultipleQueueWorkers()
    {
        // 模拟 ChildProcess Facade 用于默认队列
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-default-default')
            ->andReturn(false);

        ChildProcess::shouldReceive('artisan')
            ->with('queue:work', 'queue-worker-default-default', [
                'default',
                '--queue=default',
                '--tries=3',
                '--timeout=60',
                '--sleep=3',
            ], null, true)
            ->once();

        // 模拟 ChildProcess Facade 用于邮件队列
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-redis-emails')
            ->andReturn(false);

        ChildProcess::shouldReceive('artisan')
            ->with('queue:work', 'queue-worker-redis-emails', [
                'redis',
                '--queue=emails',
                '--tries=3',
                '--timeout=60',
                '--sleep=3',
            ], null, true)
            ->once();

        // 模拟 ChildProcess Facade 用于通知队列
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-redis-notifications')
            ->andReturn(false);

        ChildProcess::shouldReceive('artisan')
            ->with('queue:work', 'queue-worker-redis-notifications', [
                'redis',
                '--queue=notifications',
                '--tries=3',
                '--timeout=60',
                '--sleep=3',
            ], null, true)
            ->once();

        // 模拟 ChildProcess Facade 用于获取所有进程
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-default-default')
            ->andReturn(true);
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-redis-emails')
            ->andReturn(true);
        ChildProcess::shouldReceive('exists')
            ->with('queue-worker-redis-notifications')
            ->andReturn(true);

        ChildProcess::shouldReceive('isRunning')
            ->with('queue-worker-default-default')
            ->andReturn(true);
        ChildProcess::shouldReceive('isRunning')
            ->with('queue-worker-redis-emails')
            ->andReturn(true);
        ChildProcess::shouldReceive('isRunning')
            ->with('queue-worker-redis-notifications')
            ->andReturn(true);

        ChildProcess::shouldReceive('getPid')
            ->with('queue-worker-default-default')
            ->andReturn(12345);
        ChildProcess::shouldReceive('getPid')
            ->with('queue-worker-redis-emails')
            ->andReturn(12346);
        ChildProcess::shouldReceive('getPid')
            ->with('queue-worker-redis-notifications')
            ->andReturn(12347);

        ChildProcess::shouldReceive('stop')
            ->with('queue-worker-default-default')
            ->andReturn(true);
        ChildProcess::shouldReceive('stop')
            ->with('queue-worker-redis-emails')
            ->andReturn(true);
        ChildProcess::shouldReceive('stop')
            ->with('queue-worker-redis-notifications')
            ->andReturn(true);

        // 启动多个队列工作进程
        $result1 = QueueWorker::up();
        $result2 = QueueWorker::up('redis', 'emails');
        $result3 = QueueWorker::up('redis', 'notifications');

        $this->assertTrue($result1);
        $this->assertTrue($result2);
        $this->assertTrue($result3);

        // 设置 workers 属性
        $reflection = new \ReflectionClass(QueueWorker::getFacadeRoot());
        $property = $reflection->getProperty('workers');
        $property->setAccessible(true);
        $property->setValue(QueueWorker::getFacadeRoot(), [
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
            'queue-worker-redis-notifications' => [
                'connection' => 'redis',
                'queue' => 'notifications',
                'tries' => 3,
                'timeout' => 60,
                'sleep' => 3,
                'persistent' => true,
                'status' => 'running',
            ],
        ]);

        // 停止所有队列工作进程
        $count = QueueWorker::downAll();
        $this->assertEquals(3, $count);
    }

    /**
     * 测试 ChildProcess 的高级功能
     */
    public function testAdvancedChildProcessFeatures()
    {
        // 模拟 ChildProcess Facade
        ChildProcess::shouldReceive('start')
            ->with('echo Hello World', 'echo-hello', null, false, [])
            ->andReturn(ChildProcess::getFacadeRoot());

        ChildProcess::shouldReceive('exists')
            ->with('echo-hello')
            ->andReturn(true);

        ChildProcess::shouldReceive('isRunning')
            ->with('echo-hello')
            ->andReturn(true);

        ChildProcess::shouldReceive('getPid')
            ->with('echo-hello')
            ->andReturn(12345);

        ChildProcess::shouldReceive('getOutput')
            ->with('echo-hello')
            ->andReturn('Hello World');

        ChildProcess::shouldReceive('message')
            ->with('Hello from parent', 'echo-hello')
            ->andReturn(true);

        ChildProcess::shouldReceive('restart')
            ->with('echo-hello')
            ->andReturn(true);

        ChildProcess::shouldReceive('stop')
            ->with('echo-hello')
            ->andReturn(true);

        // 启动子进程
        $result = ChildProcess::start('echo Hello World', 'echo-hello');
        $this->assertInstanceOf(get_class(ChildProcess::getFacadeRoot()), $result);

        // 检查子进程状态
        $exists = ChildProcess::exists('echo-hello');
        $this->assertTrue($exists);

        $running = ChildProcess::isRunning('echo-hello');
        $this->assertTrue($running);

        $pid = ChildProcess::getPid('echo-hello');
        $this->assertEquals(12345, $pid);

        $output = ChildProcess::getOutput('echo-hello');
        $this->assertEquals('Hello World', $output);

        // 向子进程发送消息
        $result = ChildProcess::message('Hello from parent', 'echo-hello');
        $this->assertTrue($result);

        // 重启子进程
        $result = ChildProcess::restart('echo-hello');
        $this->assertTrue($result);

        // 停止子进程
        $result = ChildProcess::stop('echo-hello');
        $this->assertTrue($result);
    }
}
