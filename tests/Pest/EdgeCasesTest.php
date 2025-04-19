<?php

use NativePHP\Think\Tests\Pest\MockEventDispatcher;
use NativePHP\Think\Tests\Pest\MockMenu;
use NativePHP\Think\Tests\Pest\MockWindow;

// 测试边界条件

// 测试 EventDispatcher 的边界条件
test('EventDispatcher 可以处理空事件名', function () {
    $eventDispatcher = new MockEventDispatcher();

    // 注册空事件名的监听器
    $eventDispatcher->listen('', function () {
        return true;
    });

    // 获取空事件名的监听器
    $listeners = $eventDispatcher->getListeners('');

    expect($listeners)->toHaveCount(1);
    expect($listeners[0])->toBeCallable();
});

test('EventDispatcher 可以处理特殊字符事件名', function () {
    $eventDispatcher = new MockEventDispatcher();

    // 注册带特殊字符的事件名
    $eventDispatcher->listen('test.event!@#$%^&*()', function () {
        return true;
    });

    // 获取带特殊字符的事件名的监听器
    $listeners = $eventDispatcher->getListeners('test.event!@#$%^&*()');

    expect($listeners)->toHaveCount(1);
    expect($listeners[0])->toBeCallable();
});

test('EventDispatcher 可以处理移除不存在的事件', function () {
    $eventDispatcher = new MockEventDispatcher();

    // 移除不存在的事件
    $eventDispatcher->remove('non-existent-event');

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

test('EventDispatcher 可以处理移除不存在的监听器', function () {
    $eventDispatcher = new MockEventDispatcher();

    // 注册事件监听器
    $eventDispatcher->listen('test.event', function () {
        return 'listener 1';
    });

    // 移除不存在的监听器
    $eventDispatcher->removeListener('test.event', function () {
        return 'listener 2';
    });

    // 原来的监听器应该还在
    $listeners = $eventDispatcher->getListeners('test.event');
    expect($listeners)->toHaveCount(1);
});

// 测试 Window 的边界条件
test('Window 可以处理极端尺寸', function () {
    $window = new MockWindow(mockNative());

    // 设置极小的尺寸
    $window->width(1)->height(1);

    // 设置极大的尺寸
    $window->width(PHP_INT_MAX)->height(PHP_INT_MAX);

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

test('Window 可以处理负坐标', function () {
    $window = new MockWindow(mockNative());

    // 设置负坐标
    $window->position(-100, -100);

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

// 测试 Menu 的边界条件
test('Menu 可以处理空菜单项', function () {
    $menu = new MockMenu(mockNative());

    // 添加空标签的菜单项
    $menu->add('');

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

test('Menu 可以处理嵌套子菜单', function () {
    $menu = new MockMenu(mockNative());

    // 添加嵌套子菜单
    $menu->addSubmenu('Level 1', function ($submenu1) {
        $submenu1->add('Item 1.1');
        $submenu1->add('Item 1.2');

        // 在实际应用中，这种嵌套可能会更深
        // 但在我们的模拟类中，我们只测试一层嵌套
    });

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

// 测试 Hotkey 的边界条件
test('Hotkey 可以处理无效的快捷键', function () {
    $hotkey = new MockHotkey(mockNative());

    // 注册空快捷键
    $hotkey->register('', function () {
        return true;
    });

    // 注册无效的快捷键
    $hotkey->register('InvalidKey', function () {
        return true;
    });

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

test('Hotkey 可以处理重复注册', function () {
    $hotkey = new MockHotkey(mockNative());

    $callback1 = function () {
        return 'callback 1';
    };

    $callback2 = function () {
        return 'callback 2';
    };

    // 注册快捷键
    $hotkey->register('CommandOrControl+X', $callback1);

    // 重复注册同一个快捷键，但使用不同的回调
    $hotkey->register('CommandOrControl+X', $callback2);

    // 后注册的应该覆盖先注册的
    expect($hotkey->getRegisteredHotkeys()['CommandOrControl+X'])->toBe($callback2);
});

// 测试 Tray 的边界条件
test('Tray 可以处理无图标', function () {
    $tray = new MockTray(mockNative());

    // 不设置图标
    $tray->addItem('Test Item');

    // 不应该抛出异常
    expect(true)->toBeTrue();
});

test('Tray 可以处理空菜单', function () {
    $tray = new MockTray(mockNative());

    // 设置图标但不添加菜单项
    $tray->icon('path/to/icon.png');

    // 不应该抛出异常
    expect(true)->toBeTrue();
});
