<?php

use NativePHP\Think\Ipc;
use NativePHP\Think\Native;
use NativePHP\Think\Tests\Pest\MockIpc;

// 创建 Ipc 实例的辅助函数
function createMockIpc() {
    $native = mockNative();
    $ipc = new MockIpc($native);
    return $ipc;
}

// 测试 Ipc 类的流畅接口
test('Ipc 类提供流畅接口', function () {
    $ipc = createMockIpc();

    // 测试方法链
    expect($ipc->handle('test-channel', function () {}))->toBeInstanceOf(MockIpc::class);
    expect($ipc->send('test-channel', 'test-data'))->toBeInstanceOf(MockIpc::class);
});

// 测试注册处理器
test('Ipc 可以注册处理器', function () {
    $ipc = createMockIpc();

    $handler = function ($data) {
        return $data;
    };

    $ipc->handle('test-channel', $handler);

    $handlers = $ipc->getHandlers();
    expect($handlers)->toHaveKey('test-channel');
    expect($handlers['test-channel'])->toBe($handler);
});

// 测试发送消息
test('Ipc 可以发送消息', function () {
    $ipc = createMockIpc();

    $ipc->send('test-channel', 'test-data');

    $sentMessages = $ipc->getSentMessages();
    expect($sentMessages)->toHaveCount(1);
    expect($sentMessages[0]['channel'])->toBe('test-channel');
    expect($sentMessages[0]['data'])->toBe('test-data');
});

// 测试发送和接收消息
test('Ipc 可以发送和接收消息', function () {
    $ipc = createMockIpc();

    $receivedData = null;

    $ipc->handle('test-channel', function ($data) use (&$receivedData) {
        $receivedData = $data;
    });

    $ipc->send('test-channel', 'test-data');

    expect($receivedData)->toBe('test-data');
});

// 测试发送复杂数据
test('Ipc 可以发送复杂数据', function () {
    $ipc = createMockIpc();

    $complexData = [
        'string' => 'value',
        'number' => 123,
        'boolean' => true,
        'array' => [1, 2, 3],
        'object' => (object) ['key' => 'value'],
    ];

    $receivedData = null;

    $ipc->handle('test-channel', function ($data) use (&$receivedData) {
        $receivedData = $data;
    });

    $ipc->send('test-channel', $complexData);

    expect($receivedData)->toBe($complexData);
});

// 测试多个通道
test('Ipc 可以处理多个通道', function () {
    $ipc = createMockIpc();

    $channel1Data = null;
    $channel2Data = null;

    $ipc->handle('channel-1', function ($data) use (&$channel1Data) {
        $channel1Data = $data;
    });

    $ipc->handle('channel-2', function ($data) use (&$channel2Data) {
        $channel2Data = $data;
    });

    $ipc->send('channel-1', 'data-1');
    $ipc->send('channel-2', 'data-2');

    expect($channel1Data)->toBe('data-1');
    expect($channel2Data)->toBe('data-2');
});
