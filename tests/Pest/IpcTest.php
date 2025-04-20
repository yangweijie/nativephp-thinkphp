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

test('IPC 可以发送和接收消息', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $received = null;
    
    $ipc->on('test-channel', function($data) use (&$received) {
        $received = $data;
    });
    
    $message = ['type' => 'test', 'content' => 'Hello'];
    $ipc->send('test-channel', $message);
    
    expect($received)->toBe($message);
});

test('IPC 支持双向通信', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $response = null;
    
    $ipc->on('request-channel', function($data) use ($ipc) {
        $ipc->send('response-channel', [
            'status' => 'success',
            'data' => $data
        ]);
    });
    
    $ipc->on('response-channel', function($data) use (&$response) {
        $response = $data;
    });
    
    $request = ['action' => 'getData'];
    $ipc->send('request-channel', $request);
    
    expect($response)->toMatchArray([
        'status' => 'success',
        'data' => $request
    ]);
});

test('IPC 可以移除监听器', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $callCount = 0;
    $handler = function() use (&$callCount) {
        $callCount++;
    };
    
    $ipc->on('remove-test', $handler);
    $ipc->send('remove-test');
    expect($callCount)->toBe(1);
    
    $ipc->off('remove-test', $handler);
    $ipc->send('remove-test');
    expect($callCount)->toBe(1);
});

test('IPC 支持一次性监听', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $callCount = 0;
    
    $ipc->once('once-test', function() use (&$callCount) {
        $callCount++;
    });
    
    $ipc->send('once-test');
    $ipc->send('once-test');
    
    expect($callCount)->toBe(1);
});

test('IPC 可以处理异步响应', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $done = false;
    
    $ipc->on('async-request', function($data) use ($ipc) {
        // 模拟异步操作
        usleep(100000); // 100ms
        $ipc->send('async-response', [
            'processed' => $data
        ]);
    });
    
    $ipc->on('async-response', function() use (&$done) {
        $done = true;
    });
    
    $ipc->send('async-request', ['task' => 'process']);
    
    // 等待异步响应
    $startTime = microtime(true);
    while (!$done && microtime(true) - $startTime < 1) {
        usleep(1000);
    }
    
    expect($done)->toBeTrue();
});

test('IPC 可以处理错误情况', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $error = null;
    
    $ipc->on('error-channel', function($data) use ($ipc) {
        if (!isset($data['required'])) {
            $ipc->send('error-response', [
                'error' => 'Missing required field'
            ]);
        }
    });
    
    $ipc->on('error-response', function($data) use (&$error) {
        $error = $data['error'];
    });
    
    $ipc->send('error-channel', ['optional' => 'value']);
    
    expect($error)->toBe('Missing required field');
});

test('IPC 支持广播消息', function() {
    $native = mockNative();
    $ipc = new Ipc($native);
    
    $receivers = [
        'receiver1' => false,
        'receiver2' => false,
        'receiver3' => false
    ];
    
    foreach ($receivers as $id => $received) {
        $ipc->on('broadcast', function() use ($id, &$receivers) {
            $receivers[$id] = true;
        });
    }
    
    $ipc->broadcast('broadcast', ['message' => 'Hello all']);
    
    expect($receivers)->toMatchArray([
        'receiver1' => true,
        'receiver2' => true,
        'receiver3' => true
    ]);
});

// 辅助函数：创建模拟 Native 实例
function mockNative() {
    return new class {
        protected $channels = [];
        
        public function on($channel, $callback) {
            if (!isset($this->channels[$channel])) {
                $this->channels[$channel] = [];
            }
            $this->channels[$channel][] = $callback;
        }
        
        public function emit($channel, $data = null) {
            if (isset($this->channels[$channel])) {
                foreach ($this->channels[$channel] as $callback) {
                    $callback($data);
                }
            }
        }
        
        public function removeListener($channel, $callback) {
            if (isset($this->channels[$channel])) {
                $this->channels[$channel] = array_filter(
                    $this->channels[$channel],
                    function($existingCallback) use ($callback) {
                        return $existingCallback !== $callback;
                    }
                );
            }
        }
    };
}
