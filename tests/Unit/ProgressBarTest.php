<?php

namespace Native\ThinkPHP\Tests\Unit;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\ProgressBar;
use Native\ThinkPHP\Client\Client;
use Mockery;
use think\Response;

class ProgressBarTest extends TestCase
{
    /**
     * ProgressBar 实例
     *
     * @var \Native\ThinkPHP\ProgressBar
     */
    protected $progressBar;

    /**
     * 客户端模拟
     *
     * @var \Mockery\MockInterface
     */
    protected $client;

    /**
     * 设置测试环境
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 使用我们自己的 Response 类而不是 Mockery 的模拟对象
        $this->client = new class {
            public function post($url, $data = [])
            {
                return new \think\Response();
            }
        };
        $this->progressBar = new ProgressBar($this->app, 100);

        // 使用反射设置 client 属性
        $reflection = new \ReflectionClass($this->progressBar);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->progressBar, $this->client);
    }

    /**
     * 测试创建新实例
     *
     * @return void
     */
    public function testCreate()
    {
        $progressBar = ProgressBar::create(100);

        $this->assertInstanceOf(ProgressBar::class, $progressBar);

        // 使用反射获取 maxSteps 属性
        $reflection = new \ReflectionClass($progressBar);
        $property = $reflection->getProperty('maxSteps');
        $property->setAccessible(true);

        $this->assertEquals(100, $property->getValue($progressBar));
    }

    /**
     * 测试开始进度条
     *
     * @return void
     */
    public function testStart()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->start();

        // 使用反射获取 percent 属性
        $reflection = new \ReflectionClass($this->progressBar);
        $property = $reflection->getProperty('percent');
        $property->setAccessible(true);

        $this->assertEquals(0, $property->getValue($this->progressBar));
    }

    /**
     * 测试前进步骤
     *
     * @return void
     */
    public function testAdvance()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->start();
        $this->progressBar->advance(10);

        // 使用反射获取 percent 和 step 属性
        $reflection = new \ReflectionClass($this->progressBar);
        $percentProperty = $reflection->getProperty('percent');
        $percentProperty->setAccessible(true);
        $stepProperty = $reflection->getProperty('step');
        $stepProperty->setAccessible(true);

        $this->assertEquals(0.1, $percentProperty->getValue($this->progressBar));
        $this->assertEquals(10, $stepProperty->getValue($this->progressBar));
    }

    /**
     * 测试设置进度
     *
     * @return void
     */
    public function testSetProgress()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->setProgress(50);

        // 使用反射获取 percent 和 step 属性
        $reflection = new \ReflectionClass($this->progressBar);
        $percentProperty = $reflection->getProperty('percent');
        $percentProperty->setAccessible(true);
        $stepProperty = $reflection->getProperty('step');
        $stepProperty->setAccessible(true);

        $this->assertEquals(0.5, $percentProperty->getValue($this->progressBar));
        $this->assertEquals(50, $stepProperty->getValue($this->progressBar));
    }

    /**
     * 测试完成进度条
     *
     * @return void
     */
    public function testFinish()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->finish();

        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
    }

    /**
     * 测试显示进度条
     *
     * @return void
     */
    public function testDisplay()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->display();

        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
    }

    /**
     * 测试完整的进度条流程
     *
     * @return void
     */
    public function testFullProgressBarFlow()
    {
        // 直接调用方法，不使用 Mockery 的模拟方法
        $this->progressBar->start();
        $this->progressBar->advance(25);
        $this->progressBar->setProgress(50);
        $this->progressBar->advance(25);
        $this->progressBar->setProgress(100);
        $this->progressBar->finish();

        // 使用反射获取 percent 和 step 属性
        $reflection = new \ReflectionClass($this->progressBar);
        $percentProperty = $reflection->getProperty('percent');
        $percentProperty->setAccessible(true);
        $stepProperty = $reflection->getProperty('step');
        $stepProperty->setAccessible(true);

        $this->assertEquals(1, $percentProperty->getValue($this->progressBar));
        $this->assertEquals(100, $stepProperty->getValue($this->progressBar));
    }

    /**
     * 清理测试环境
     *
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
