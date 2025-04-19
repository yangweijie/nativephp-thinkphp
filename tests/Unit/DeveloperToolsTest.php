<?php

namespace tests\unit;

use think\App;
use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\DeveloperTools;
use Native\ThinkPHP\Client\Client;
use Mockery;

class DeveloperToolsTest extends TestCase
{
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

        // 创建Client模拟对象
        $this->clientMock = Mockery::mock(Client::class);

        // 创建DeveloperTools实例
        $this->developerTools = new DeveloperTools($this->appMock);

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
     * 测试启用开发者工具
     */
    public function testEnable()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/enable')
            ->andReturn($responseMock);

        // 调用启用方法
        $result = $this->developerTools->enable();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试禁用开发者工具
     */
    public function testDisable()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/disable')
            ->andReturn($responseMock);

        // 调用禁用方法
        $result = $this->developerTools->disable();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试切换开发者工具状态
     */
    public function testToggle()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('enabled')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/toggle')
            ->andReturn($responseMock);

        // 调用切换方法
        $result = $this->developerTools->toggle();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试检查开发者工具是否启用
     */
    public function testIsEnabled()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('enabled')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('developer-tools/is-enabled')
            ->andReturn($responseMock);

        // 调用检查方法
        $result = $this->developerTools->isEnabled();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打开开发者工具面板
     */
    public function testOpenDevTools()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/open-dev-tools')
            ->andReturn($responseMock);

        // 调用打开方法
        $result = $this->developerTools->openDevTools();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试关闭开发者工具面板
     */
    public function testCloseDevTools()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/close-dev-tools')
            ->andReturn($responseMock);

        // 调用关闭方法
        $result = $this->developerTools->closeDevTools();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试切换开发者工具面板
     */
    public function testToggleDevTools()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('opened')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/toggle-dev-tools')
            ->andReturn($responseMock);

        // 调用切换方法
        $result = $this->developerTools->toggleDevTools();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试检查开发者工具面板是否打开
     */
    public function testIsDevToolsOpened()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('opened')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('developer-tools/is-dev-tools-opened')
            ->andReturn($responseMock);

        // 调用检查方法
        $result = $this->developerTools->isDevToolsOpened();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打印调试信息
     */
    public function testLog()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/log', [
                'message' => '这是一条调试信息',
                'data' => null,
            ])
            ->andReturn($responseMock);

        // 调用打印方法
        $result = $this->developerTools->log('这是一条调试信息');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打印信息
     */
    public function testInfo()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/info', [
                'message' => '这是一条信息',
                'data' => null,
            ])
            ->andReturn($responseMock);

        // 调用打印方法
        $result = $this->developerTools->info('这是一条信息');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打印警告
     */
    public function testWarn()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/warn', [
                'message' => '这是一条警告',
                'data' => null,
            ])
            ->andReturn($responseMock);

        // 调用打印方法
        $result = $this->developerTools->warn('这是一条警告');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打印错误
     */
    public function testError()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/error', [
                'message' => '这是一条错误',
                'data' => null,
            ])
            ->andReturn($responseMock);

        // 调用打印方法
        $result = $this->developerTools->error('这是一条错误');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试分组打印
     */
    public function testGroup()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/group', [
                'label' => '用户操作',
            ])
            ->andReturn($responseMock);

        // 调用分组方法
        $result = $this->developerTools->group('用户操作');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试结束分组
     */
    public function testGroupEnd()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/group-end')
            ->andReturn($responseMock);

        // 调用结束分组方法
        $result = $this->developerTools->groupEnd();

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试计时功能
     */
    public function testTime()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/time', [
                'label' => '操作耗时',
            ])
            ->andReturn($responseMock);

        // 调用计时方法
        $result = $this->developerTools->time('操作耗时');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试结束计时
     */
    public function testTimeEnd()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/time-end', [
                'label' => '操作耗时',
            ])
            ->andReturn($responseMock);

        // 调用结束计时方法
        $result = $this->developerTools->timeEnd('操作耗时');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试打印调用堆栈
     */
    public function testTrace()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/trace', [
                'message' => '调用堆栈',
            ])
            ->andReturn($responseMock);

        // 调用打印调用堆栈方法
        $result = $this->developerTools->trace('调用堆栈');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试开始性能分析
     */
    public function testStartProfiling()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/start-profiling', [
                'label' => '查询性能',
            ])
            ->andReturn($responseMock);

        // 调用开始性能分析方法
        $result = $this->developerTools->startProfiling('查询性能');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试结束性能分析
     */
    public function testStopProfiling()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/stop-profiling', [
                'label' => '查询性能',
            ])
            ->andReturn($responseMock);

        // 调用结束性能分析方法
        $result = $this->developerTools->stopProfiling('查询性能');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试记录性能标记
     */
    public function testMark()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/mark', [
                'name' => '开始查询',
            ])
            ->andReturn($responseMock);

        // 调用记录性能标记方法
        $result = $this->developerTools->mark('开始查询');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试测量两个标记之间的时间
     */
    public function testMeasure()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/measure', [
                'name' => '查询耗时',
                'startMark' => '开始查询',
                'endMark' => '查询完成',
            ])
            ->andReturn($responseMock);

        // 调用测量方法
        $result = $this->developerTools->measure('查询耗时', '开始查询', '查询完成');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试清除性能标记
     */
    public function testClearMarks()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/clear-marks', [
                'name' => '开始查询',
            ])
            ->andReturn($responseMock);

        // 调用清除性能标记方法
        $result = $this->developerTools->clearMarks('开始查询');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试清除性能测量
     */
    public function testClearMeasures()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/clear-measures', [
                'name' => '查询耗时',
            ])
            ->andReturn($responseMock);

        // 调用清除性能测量方法
        $result = $this->developerTools->clearMeasures('查询耗时');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试获取内存使用情况
     */
    public function testMemory()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('memory')->andReturn(1024 * 1024);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('developer-tools/memory')
            ->andReturn($responseMock);

        // 调用获取内存使用情况方法
        $memory = $this->developerTools->memory();

        // 断言结果
        $this->assertIsInt($memory);
        $this->assertGreaterThan(0, $memory);
    }

    /**
     * 测试获取内存使用峰值
     */
    public function testMemoryPeak()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('memory_peak')->andReturn(2 * 1024 * 1024);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('get')
            ->with('developer-tools/memory-peak')
            ->andReturn($responseMock);

        // 调用获取内存使用峰值方法
        $memoryPeak = $this->developerTools->memoryPeak();

        // 断言结果
        $this->assertIsInt($memoryPeak);
        $this->assertGreaterThan(0, $memoryPeak);
    }

    /**
     * 测试开始监控内存使用
     */
    public function testStartMemoryMonitor()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('success')->andReturn(true);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/start-memory-monitor', [
                'label' => '内存监控',
            ])
            ->andReturn($responseMock);

        // 调用开始监控内存使用方法
        $result = $this->developerTools->startMemoryMonitor('内存监控');

        // 断言结果
        $this->assertTrue($result);
    }

    /**
     * 测试结束监控内存使用
     */
    public function testStopMemoryMonitor()
    {
        // 设置模拟响应
        $responseMock = Mockery::mock();
        $responseMock->shouldReceive('json')->with('memory_usage')->andReturn([
            'used' => 1024 * 1024,
            'diff' => 512 * 1024,
        ]);

        // 设置Client模拟对象的行为
        $this->clientMock->shouldReceive('post')
            ->with('developer-tools/stop-memory-monitor', [
                'label' => '内存监控',
            ])
            ->andReturn($responseMock);

        // 调用结束监控内存使用方法
        $memoryUsage = $this->developerTools->stopMemoryMonitor('内存监控');

        // 断言结果
        $this->assertIsArray($memoryUsage);

        // 在测试环境中，我们只验证数组结构，不验证具体值
        if (isset($memoryUsage['start']) && isset($memoryUsage['end']) && isset($memoryUsage['diff'])) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, '内存监控结果结构不正确');
        }
    }
}
