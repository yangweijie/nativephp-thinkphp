<?php

namespace Native\ThinkPHP\Tests\Unit;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\ContextMenu;
use Native\ThinkPHP\Menu;
use Native\ThinkPHP\Client\Client;
use Mockery;
use think\Response;

class ContextMenuTest extends TestCase
{
    /**
     * ContextMenu 实例
     *
     * @var \Native\ThinkPHP\ContextMenu
     */
    protected $contextMenu;
    
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
        $this->contextMenu = new ContextMenu($this->app);
        
        // 使用反射设置 client 属性
        $reflection = new \ReflectionClass($this->contextMenu);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->contextMenu, $this->client);
    }
    
    /**
     * 测试注册上下文菜单
     *
     * @return void
     */
    public function testRegister()
    {
        $menu = Mockery::mock(Menu::class);
        $menuItems = [
            ['label' => 'Copy', 'click' => 'copy'],
            ['label' => 'Paste', 'click' => 'paste'],
        ];
        
        $menu->shouldReceive('getItems')
            ->once()
            ->andReturn($menuItems);
        
        $this->client->shouldReceive('post')
            ->once()
            ->with('context', [
                'entries' => $menuItems,
            ])
            ->andReturn(new Response(['success' => true]));
        
        $this->contextMenu->register($menu);
        
        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
    }
    
    /**
     * 测试移除上下文菜单
     *
     * @return void
     */
    public function testRemove()
    {
        $this->client->shouldReceive('delete')
            ->once()
            ->with('context')
            ->andReturn(new Response(['success' => true]));
        
        $this->contextMenu->remove();
        
        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
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
