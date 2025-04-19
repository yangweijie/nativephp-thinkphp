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
