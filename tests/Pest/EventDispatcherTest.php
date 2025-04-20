<?php

use NativePHP\Think\EventDispatcher;
use NativePHP\Think\Contract\EventDispatcherContract;
use NativePHP\Think\Native;
use think\App;
use think\Config;

// 测试 EventDispatcher 接口兼容性
test('EventDispatcher 实现了 EventDispatcherContract 接口', function () {
    $reflection = new ReflectionClass(EventDispatcher::class);
    expect($reflection->implementsInterface(EventDispatcherContract::class))->toBeTrue();
});

// 测试 remove 方法签名兼容性
test('remove 方法签名与接口兼容', function () {
    $interface = new ReflectionClass(EventDispatcherContract::class);
    $interfaceMethod = $interface->getMethod('remove');

    $class = new ReflectionClass(EventDispatcher::class);
    $classMethod = $class->getMethod('remove');

    // 检查返回类型
    expect($classMethod->getReturnType()->getName())
        ->toBe($interfaceMethod->getReturnType()->getName());

    // 检查参数数量
    expect($classMethod->getParameters())
        ->toHaveCount(count($interfaceMethod->getParameters()));

    // 检查参数名称和类型
    foreach ($interfaceMethod->getParameters() as $i => $param) {
        $classParam = $classMethod->getParameters()[$i];

        expect($classParam->getName())->toBe($param->getName());

        if ($param->hasType() && $classParam->hasType()) {
            expect($classParam->getType()->getName())
                ->toBe($param->getType()->getName());
        }
    }
});

// 测试 removeListener 方法存在
test('removeListener 方法存在于接口和实现类中', function () {
    $interface = new ReflectionClass(EventDispatcherContract::class);
    $class = new ReflectionClass(EventDispatcher::class);

    expect($interface->hasMethod('removeListener'))->toBeTrue();
    expect($class->hasMethod('removeListener'))->toBeTrue();
});

// 测试 removeListener 方法签名兼容性
test('removeListener 方法签名与接口兼容', function () {
    $interface = new ReflectionClass(EventDispatcherContract::class);
    $interfaceMethod = $interface->getMethod('removeListener');

    $class = new ReflectionClass(EventDispatcher::class);
    $classMethod = $class->getMethod('removeListener');

    // 检查返回类型兼容性
    $interfaceReturnType = $interfaceMethod->getReturnType()->getName();
    $classReturnType = $classMethod->getReturnType()->getName();

    expect($classReturnType === $interfaceReturnType ||
           is_subclass_of($classReturnType, $interfaceReturnType) ||
           $classReturnType === EventDispatcherContract::class)
        ->toBeTrue();

    // 检查参数数量
    expect($classMethod->getParameters())
        ->toHaveCount(count($interfaceMethod->getParameters()));

    // 检查参数名称和类型
    foreach ($interfaceMethod->getParameters() as $i => $param) {
        $classParam = $classMethod->getParameters()[$i];

        expect($classParam->getName())->toBe($param->getName());

        if ($param->hasType() && $classParam->hasType()) {
            expect($classParam->getType()->getName())
                ->toBe($param->getType()->getName());
        }
    }
});

// 使用模拟对象测试 EventDispatcher 功能
test('EventDispatcher 可以监听和触发事件', function () {
    // 创建模拟 EventDispatcher 实例
    $eventDispatcher = new \NativePHP\Think\Tests\Pest\MockEventDispatcher();

    // 测试事件监听和触发
    $receivedPayload = null;

    $eventDispatcher->listen('test.event', function ($payload) use (&$receivedPayload) {
        $receivedPayload = $payload;
    });

    $payload = ['data' => 'test'];
    $eventDispatcher->dispatch('test.event', $payload);

    expect($receivedPayload)->toBe($payload);
});

test('事件调度器可以注册和触发事件', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $called = false;
    $payload = null;
    
    $dispatcher->on('test-event', function($data) use (&$called, &$payload) {
        $called = true;
        $payload = $data;
    });
    
    $testData = ['message' => 'Hello World'];
    $dispatcher->dispatch('test-event', $testData);
    
    expect($called)->toBeTrue();
    expect($payload)->toBe($testData);
});

test('事件调度器支持一次性事件', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $callCount = 0;
    
    $dispatcher->once('one-time-event', function() use (&$callCount) {
        $callCount++;
    });
    
    $dispatcher->dispatch('one-time-event');
    $dispatcher->dispatch('one-time-event');
    
    expect($callCount)->toBe(1);
});

test('事件调度器可以移除监听器', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $callCount = 0;
    
    $handler = function() use (&$callCount) {
        $callCount++;
    };
    
    $dispatcher->on('test-event', $handler);
    $dispatcher->dispatch('test-event');
    expect($callCount)->toBe(1);
    
    $dispatcher->off('test-event', $handler);
    $dispatcher->dispatch('test-event');
    expect($callCount)->toBe(1);
});

test('事件调度器支持多个监听器', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $results = [];
    
    $dispatcher->on('multi-event', function() use (&$results) {
        $results[] = 'first';
    });
    
    $dispatcher->on('multi-event', function() use (&$results) {
        $results[] = 'second';
    });
    
    $dispatcher->dispatch('multi-event');
    
    expect($results)->toBe(['first', 'second']);
});

test('事件调度器支持通配符事件', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $events = [];
    
    $dispatcher->on('user.*', function($data, $event) use (&$events) {
        $events[] = $event;
    });
    
    $dispatcher->dispatch('user.login');
    $dispatcher->dispatch('user.logout');
    
    expect($events)->toBe(['user.login', 'user.logout']);
});

test('事件调度器支持事件优先级', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $sequence = [];
    
    $dispatcher->on('priority-event', function() use (&$sequence) {
        $sequence[] = 'normal';
    });
    
    $dispatcher->on('priority-event', function() use (&$sequence) {
        $sequence[] = 'high';
    }, 10);
    
    $dispatcher->on('priority-event', function() use (&$sequence) {
        $sequence[] = 'low';
    }, -10);
    
    $dispatcher->dispatch('priority-event');
    
    expect($sequence)->toBe(['high', 'normal', 'low']);
});

test('事件调度器可以停止事件传播', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $called = [];
    
    $dispatcher->on('stop-event', function($event) use (&$called) {
        $called[] = 'first';
        $event->stopPropagation();
    });
    
    $dispatcher->on('stop-event', function() use (&$called) {
        $called[] = 'second';
    });
    
    $dispatcher->dispatch('stop-event');
    
    expect($called)->toBe(['first']);
});

test('事件调度器支持订阅者模式', function() {
    $native = mockNative();
    $dispatcher = new EventDispatcher($native);
    
    $subscriber = new class {
        public $events = [];
        
        public function onUserLogin($data) {
            $this->events[] = ['login', $data];
        }
        
        public function onUserLogout() {
            $this->events[] = ['logout'];
        }
        
        public function subscribe($events) {
            $events->listen('user.login', [$this, 'onUserLogin']);
            $events->listen('user.logout', [$this, 'onUserLogout']);
        }
    };
    
    $dispatcher->subscribe($subscriber);
    
    $dispatcher->dispatch('user.login', ['id' => 1]);
    $dispatcher->dispatch('user.logout');
    
    expect($subscriber->events)->toBe([
        ['login', ['id' => 1]],
        ['logout']
    ]);
});

// 辅助函数：创建模拟 Native 实例
function mockNative() {
    return new class {
        protected $listeners = [];
        
        public function on($event, $callback) {
            $this->listeners[$event][] = $callback;
        }
        
        public function emit($event, $data = null) {
            if (isset($this->listeners[$event])) {
                foreach ($this->listeners[$event] as $callback) {
                    $callback($data);
                }
            }
        }
    };
}
