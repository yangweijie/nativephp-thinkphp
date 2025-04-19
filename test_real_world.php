<?php

require_once __DIR__ . '/vendor/autoload.php';

use NativePHP\Think\EventDispatcher;
use NativePHP\Think\Contract\EventDispatcherContract;
use NativePHP\Think\Native;
use think\App;

// 模拟 App 类
class MockApp extends App
{
    public function __construct()
    {
        // 空实现，不调用父类构造函数
    }

    public function invoke($callable, array $vars = [], bool $accessible = false)
    {
        if (is_array($callable) && is_object($callable[0]) && is_string($callable[1]) && method_exists($callable[0], $callable[1])) {
            return call_user_func_array($callable, $vars);
        }
        return null;
    }
}

// 模拟 Native 类
class MockNative extends Native
{
    public function __construct()
    {
        // 空实现，不调用父类构造函数
    }
}

echo "测试 EventDispatcher 类在实际应用中的兼容性...\n\n";

// 创建实例
$app = new MockApp();
$native = new MockNative();
$dispatcher = new EventDispatcher($native, $app);

// 测试 1: 注册和触发事件
echo "测试 1: 注册和触发事件\n";
$receivedPayload = null;
$dispatcher->listen('test.event', function ($payload) use (&$receivedPayload) {
    $receivedPayload = $payload;
    echo "  事件被触发，接收到数据: " . json_encode($payload) . "\n";
});

$payload = ['data' => 'test'];
$dispatcher->dispatch('test.event', $payload);

echo "  测试结果: " . ($receivedPayload === $payload ? "通过" : "失败") . "\n\n";

// 测试 2: 通配符监听器
echo "测试 2: 通配符监听器\n";
$receivedEvents = [];
$receivedPayloads = [];

$dispatcher->listen('*', function ($payload, $event) use (&$receivedEvents, &$receivedPayloads) {
    $receivedEvents[] = $event;
    $receivedPayloads[] = $payload;
    echo "  通配符监听器接收到事件: $event, 数据: " . json_encode($payload) . "\n";
});

$dispatcher->dispatch('test.event1', ['id' => 1]);
$dispatcher->dispatch('test.event2', ['id' => 2]);

echo "  测试结果: " . (in_array('test.event1', $receivedEvents) && in_array('test.event2', $receivedEvents) ? "通过" : "失败") . "\n\n";

// 测试 3: 移除特定监听器
echo "测试 3: 移除特定监听器\n";
$called = false;
$listener = function () use (&$called) {
    $called = true;
    echo "  监听器被调用\n";
};

$dispatcher->listen('test.event3', $listener);
echo "  已注册监听器\n";

$dispatcher->removeListener('test.event3', $listener);
echo "  已移除监听器\n";

$dispatcher->dispatch('test.event3');
echo "  测试结果: " . (!$called ? "通过" : "失败") . "\n\n";

// 测试 4: 移除所有监听器
echo "测试 4: 移除所有监听器\n";
$count = 0;
$dispatcher->listen('test.event4', function () use (&$count) {
    $count++;
    echo "  监听器 1 被调用\n";
});
$dispatcher->listen('test.event4', function () use (&$count) {
    $count++;
    echo "  监听器 2 被调用\n";
});
echo "  已注册两个监听器\n";

$dispatcher->remove('test.event4');
echo "  已移除所有监听器\n";

$dispatcher->dispatch('test.event4');
echo "  测试结果: " . ($count === 0 ? "通过" : "失败") . "\n\n";

// 测试 5: 获取注册的监听器
echo "测试 5: 获取注册的监听器\n";
$listener = function () {
    return true;
};

$dispatcher->listen('test.event5', $listener);
echo "  已注册监听器\n";

$listeners = $dispatcher->getListeners('test.event5');
echo "  获取到 " . count($listeners) . " 个监听器\n";
echo "  测试结果: " . (count($listeners) === 1 && in_array($listener, $listeners, true) ? "通过" : "失败") . "\n\n";

// 测试 6: 清除所有监听器
echo "测试 6: 清除所有监听器\n";
$dispatcher->listen('event6.1', function () {});
$dispatcher->listen('event6.2', function () {});
echo "  已注册两个不同事件的监听器\n";

$dispatcher->clear();
echo "  已清除所有监听器\n";

$listeners1 = $dispatcher->getListeners('event6.1');
$listeners2 = $dispatcher->getListeners('event6.2');
echo "  测试结果: " . (empty($listeners1) && empty($listeners2) ? "通过" : "失败") . "\n\n";

// 测试 7: 多个监听器同一事件
echo "测试 7: 多个监听器同一事件\n";
$count = 0;

$dispatcher->listen('test.event7', function () use (&$count) {
    $count++;
    echo "  监听器 1 被调用\n";
});
$dispatcher->listen('test.event7', function () use (&$count) {
    $count++;
    echo "  监听器 2 被调用\n";
});
echo "  已注册两个监听器到同一事件\n";

$dispatcher->dispatch('test.event7');
echo "  测试结果: " . ($count === 2 ? "通过" : "失败") . "\n\n";

echo "所有测试完成！\n";
