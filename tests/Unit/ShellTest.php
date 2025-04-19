<?php

namespace Native\ThinkPHP\Tests\Unit;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\Shell;
use Native\ThinkPHP\Client\Client;
use Mockery;
use think\Response;

class ShellTest extends TestCase
{
    /**
     * Shell 实例
     *
     * @var \Native\ThinkPHP\Shell
     */
    protected $shell;
    
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
        $this->shell = new Shell($this->app);
        
        // 使用反射设置 client 属性
        $reflection = new \ReflectionClass($this->shell);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($this->shell, $this->client);
    }
    
    /**
     * 测试在文件夹中显示文件
     *
     * @return void
     */
    public function testShowInFolder()
    {
        $path = '/path/to/file.txt';
        
        $this->client->shouldReceive('post')
            ->once()
            ->with('shell/show-item-in-folder', [
                'path' => $path,
            ])
            ->andReturn(new Response(['success' => true]));
        
        $this->shell->showInFolder($path);
        
        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
    }
    
    /**
     * 测试打开文件
     *
     * @return void
     */
    public function testOpenFile()
    {
        $path = '/path/to/file.txt';
        
        $this->client->shouldReceive('post')
            ->once()
            ->with('shell/open-item', [
                'path' => $path,
            ])
            ->andReturn(new Response(['result' => 'success']));
        
        $result = $this->shell->openFile($path);
        
        $this->assertEquals('success', $result);
    }
    
    /**
     * 测试将文件移动到回收站
     *
     * @return void
     */
    public function testTrashFile()
    {
        $path = '/path/to/file.txt';
        
        $this->client->shouldReceive('delete')
            ->once()
            ->with('shell/trash-item', [
                'path' => $path,
            ])
            ->andReturn(new Response(['success' => true]));
        
        $this->shell->trashFile($path);
        
        // 没有返回值，所以只要没有异常就算通过
        $this->assertTrue(true);
    }
    
    /**
     * 测试使用外部程序打开 URL
     *
     * @return void
     */
    public function testOpenExternal()
    {
        $url = 'https://www.example.com';
        
        $this->client->shouldReceive('post')
            ->once()
            ->with('shell/open-external', [
                'url' => $url,
            ])
            ->andReturn(new Response(['success' => true]));
        
        $this->shell->openExternal($url);
        
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
