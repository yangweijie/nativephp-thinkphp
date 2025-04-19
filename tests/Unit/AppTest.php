<?php

namespace Native\ThinkPHP\Tests\Unit;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\App;

class AppTest extends TestCase
{
    /**
     * 测试获取应用名称
     *
     * @return void
     */
    public function testGetName()
    {
        $app = new App($this->app);
        
        $this->assertEquals('NativePHP Test', $app->name());
    }
    
    /**
     * 测试获取应用 ID
     *
     * @return void
     */
    public function testGetId()
    {
        $app = new App($this->app);
        
        $this->assertEquals('com.nativephp.test', $app->id());
    }
    
    /**
     * 测试获取应用版本
     *
     * @return void
     */
    public function testGetVersion()
    {
        $app = new App($this->app);
        
        $this->assertEquals('1.0.0', $app->version());
    }
    
    /**
     * 测试获取应用根路径
     *
     * @return void
     */
    public function testGetRootPath()
    {
        $app = new App($this->app);
        
        $this->assertNotEmpty($app->getRootPath());
        $this->assertTrue(is_dir($app->getRootPath()));
    }
}
