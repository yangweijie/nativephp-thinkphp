<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Native\ThinkPHP\Broadcasting;
use think\App;

class BroadcastingTest extends TestCase
{
    /**
     * @var Broadcasting
     */
    protected $broadcasting;

    /**
     * 设置测试环境
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // 创建 App 实例
        $app = new App();
        
        // 创建 Broadcasting 实例
        $this->broadcasting = new Broadcasting($app);
    }

    /**
     * 测试创建频道
     *
     * @return void
     */
    public function testCreateChannel()
    {
        // 创建频道
        $result = $this->broadcasting->createChannel('test-channel');
        
        // 断言结果为 true
        $this->assertTrue($result);
        
        // 断言频道存在
        $this->assertTrue($this->broadcasting->channelExists('test-channel'));
    }

    /**
     * 测试删除频道
     *
     * @return void
     */
    public function testDeleteChannel()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 删除频道
        $result = $this->broadcasting->deleteChannel('test-channel');
        
        // 断言结果为 true
        $this->assertTrue($result);
        
        // 断言频道不存在
        $this->assertFalse($this->broadcasting->channelExists('test-channel'));
    }

    /**
     * 测试广播事件
     *
     * @return void
     */
    public function testBroadcast()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 广播事件
        $result = $this->broadcasting->broadcast('test-channel', 'test-event', [
            'message' => 'Hello, World!',
        ]);
        
        // 断言结果为 true
        $this->assertTrue($result);
    }

    /**
     * 测试监听事件
     *
     * @return void
     */
    public function testListen()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 监听事件
        $id = $this->broadcasting->listen('test-channel', 'test-event', function ($data) {
            // 断言数据包含 message 字段
            $this->assertArrayHasKey('message', $data);
            
            // 断言 message 字段的值为 Hello, World!
            $this->assertEquals('Hello, World!', $data['message']);
        });
        
        // 断言 ID 不为空
        $this->assertNotEmpty($id);
        
        // 广播事件
        $this->broadcasting->broadcast('test-channel', 'test-event', [
            'message' => 'Hello, World!',
        ]);
    }

    /**
     * 测试取消监听事件
     *
     * @return void
     */
    public function testUnlisten()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 监听事件
        $id = $this->broadcasting->listen('test-channel', 'test-event', function ($data) {
            // 这个回调不应该被调用
            $this->fail('This callback should not be called');
        });
        
        // 取消监听事件
        $result = $this->broadcasting->unlisten($id);
        
        // 断言结果为 true
        $this->assertTrue($result);
        
        // 广播事件
        $this->broadcasting->broadcast('test-channel', 'test-event', [
            'message' => 'Hello, World!',
        ]);
        
        // 如果没有异常，则测试通过
        $this->assertTrue(true);
    }

    /**
     * 测试获取所有频道
     *
     * @return void
     */
    public function testGetChannels()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel-1');
        $this->broadcasting->createChannel('test-channel-2');
        $this->broadcasting->createChannel('test-channel-3');
        
        // 获取所有频道
        $channels = $this->broadcasting->getChannels();
        
        // 断言频道数量为 3
        $this->assertCount(3, $channels);
        
        // 断言频道包含 test-channel-1
        $this->assertContains('test-channel-1', $channels);
        
        // 断言频道包含 test-channel-2
        $this->assertContains('test-channel-2', $channels);
        
        // 断言频道包含 test-channel-3
        $this->assertContains('test-channel-3', $channels);
    }

    /**
     * 测试获取频道中的事件
     *
     * @return void
     */
    public function testGetEvents()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 广播事件
        $this->broadcasting->broadcast('test-channel', 'test-event-1', []);
        $this->broadcasting->broadcast('test-channel', 'test-event-2', []);
        $this->broadcasting->broadcast('test-channel', 'test-event-3', []);
        
        // 获取频道中的事件
        $events = $this->broadcasting->getEvents('test-channel');
        
        // 断言事件数量为 3
        $this->assertCount(3, $events);
        
        // 断言事件包含 test-event-1
        $this->assertContains('test-event-1', $events);
        
        // 断言事件包含 test-event-2
        $this->assertContains('test-event-2', $events);
        
        // 断言事件包含 test-event-3
        $this->assertContains('test-event-3', $events);
    }

    /**
     * 测试清空频道
     *
     * @return void
     */
    public function testClearChannel()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 广播事件
        $this->broadcasting->broadcast('test-channel', 'test-event-1', []);
        $this->broadcasting->broadcast('test-channel', 'test-event-2', []);
        $this->broadcasting->broadcast('test-channel', 'test-event-3', []);
        
        // 清空频道
        $result = $this->broadcasting->clearChannel('test-channel');
        
        // 断言结果为 true
        $this->assertTrue($result);
        
        // 获取频道中的事件
        $events = $this->broadcasting->getEvents('test-channel');
        
        // 断言事件数量为 0
        $this->assertCount(0, $events);
    }

    /**
     * 测试获取频道中的监听器数量
     *
     * @return void
     */
    public function testGetListenerCount()
    {
        // 创建频道
        $this->broadcasting->createChannel('test-channel');
        
        // 监听事件
        $id1 = $this->broadcasting->listen('test-channel', 'test-event', function ($data) {});
        $id2 = $this->broadcasting->listen('test-channel', 'test-event', function ($data) {});
        $id3 = $this->broadcasting->listen('test-channel', 'test-event', function ($data) {});
        
        // 获取频道中的监听器数量
        $count = $this->broadcasting->getListenerCount('test-channel');
        
        // 断言监听器数量为 3
        $this->assertEquals(3, $count);
        
        // 取消监听事件
        $this->broadcasting->unlisten($id1);
        
        // 获取频道中的监听器数量
        $count = $this->broadcasting->getListenerCount('test-channel');
        
        // 断言监听器数量为 2
        $this->assertEquals(2, $count);
    }

    /**
     * 清理测试环境
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // 删除所有测试频道
        $channels = $this->broadcasting->getChannels();
        foreach ($channels as $channel) {
            if (strpos($channel, 'test-') === 0) {
                $this->broadcasting->deleteChannel($channel);
            }
        }
        
        parent::tearDown();
    }
}
