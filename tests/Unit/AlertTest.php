<?php

namespace Native\ThinkPHP\Tests\Unit;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\Alert;
use Native\ThinkPHP\Client\Client;
use Mockery;
use think\Response;

class AlertTest extends TestCase
{
    /**
     * Alert 实例
     *
     * @var \Native\ThinkPHP\Alert
     */
    protected $alert;
    
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
        
        $this->client = Mockery::mock(Client::class);
        $this->alert = new Alert($this->app);
        
        // 使用反射设置 client 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->alert, $this->client);
    }
    
    /**
     * 测试创建新实例
     *
     * @return void
     */
    public function testNew()
    {
        $alert = Alert::new();
        
        $this->assertInstanceOf(Alert::class, $alert);
    }
    
    /**
     * 测试设置类型
     *
     * @return void
     */
    public function testType()
    {
        $result = $this->alert->type('info');
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 type 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('type');
        $property->setAccessible(true);
        
        $this->assertEquals('info', $property->getValue($this->alert));
    }
    
    /**
     * 测试设置标题
     *
     * @return void
     */
    public function testTitle()
    {
        $result = $this->alert->title('Test Title');
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 title 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('title');
        $property->setAccessible(true);
        
        $this->assertEquals('Test Title', $property->getValue($this->alert));
    }
    
    /**
     * 测试设置详情
     *
     * @return void
     */
    public function testDetail()
    {
        $result = $this->alert->detail('Test Detail');
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 detail 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('detail');
        $property->setAccessible(true);
        
        $this->assertEquals('Test Detail', $property->getValue($this->alert));
    }
    
    /**
     * 测试设置按钮
     *
     * @return void
     */
    public function testButtons()
    {
        $buttons = ['OK', 'Cancel'];
        $result = $this->alert->buttons($buttons);
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 buttons 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('buttons');
        $property->setAccessible(true);
        
        $this->assertEquals($buttons, $property->getValue($this->alert));
    }
    
    /**
     * 测试设置默认 ID
     *
     * @return void
     */
    public function testDefaultId()
    {
        $result = $this->alert->defaultId(0);
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 defaultId 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('defaultId');
        $property->setAccessible(true);
        
        $this->assertEquals(0, $property->getValue($this->alert));
    }
    
    /**
     * 测试设置取消 ID
     *
     * @return void
     */
    public function testCancelId()
    {
        $result = $this->alert->cancelId(1);
        
        $this->assertInstanceOf(Alert::class, $result);
        
        // 使用反射获取 cancelId 属性
        $reflection = new \ReflectionClass($this->alert);
        $property = $reflection->getProperty('cancelId');
        $property->setAccessible(true);
        
        $this->assertEquals(1, $property->getValue($this->alert));
    }
    
    /**
     * 测试显示警告消息
     *
     * @return void
     */
    public function testShow()
    {
        $this->client->shouldReceive('post')
            ->once()
            ->with('alert/message', [
                'message' => 'Test Message',
                'type' => null,
                'title' => null,
                'detail' => null,
                'buttons' => null,
                'defaultId' => null,
                'cancelId' => null,
            ])
            ->andReturn(new Response(['result' => 0]));
        
        $result = $this->alert->show('Test Message');
        
        $this->assertEquals(0, $result);
    }
    
    /**
     * 测试显示错误警告
     *
     * @return void
     */
    public function testError()
    {
        $this->client->shouldReceive('post')
            ->once()
            ->with('alert/error', [
                'title' => 'Test Title',
                'message' => 'Test Message',
            ])
            ->andReturn(new Response(['result' => true]));
        
        $result = $this->alert->error('Test Title', 'Test Message');
        
        $this->assertTrue($result);
    }
    
    /**
     * 测试完整的警告消息流程
     *
     * @return void
     */
    public function testFullAlertFlow()
    {
        $this->client->shouldReceive('post')
            ->once()
            ->with('alert/message', [
                'message' => 'Test Message',
                'type' => 'info',
                'title' => 'Test Title',
                'detail' => 'Test Detail',
                'buttons' => ['OK', 'Cancel'],
                'defaultId' => 0,
                'cancelId' => 1,
            ])
            ->andReturn(new Response(['result' => 0]));
        
        $result = $this->alert
            ->type('info')
            ->title('Test Title')
            ->detail('Test Detail')
            ->buttons(['OK', 'Cancel'])
            ->defaultId(0)
            ->cancelId(1)
            ->show('Test Message');
        
        $this->assertEquals(0, $result);
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
