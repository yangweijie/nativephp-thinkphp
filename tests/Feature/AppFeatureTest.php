<?php

namespace Native\ThinkPHP\Tests\Feature;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\App;
use Native\ThinkPHP\Facades\Window;

class AppFeatureTest extends TestCase
{
    /**
     * 测试应用启动功能
     *
     * @return void
     */
    public function testAppStartup()
    {
        $app = new App($this->app);
        
        // 验证应用基本信息
        $this->assertEquals('NativePHP Test', $app->name());
        $this->assertEquals('com.nativephp.test', $app->id());
        $this->assertEquals('1.0.0', $app->version());
        
        // 这是一个简单的功能测试，实际应用中可能需要测试更复杂的交互
        $this->assertTrue(true);
    }
}
