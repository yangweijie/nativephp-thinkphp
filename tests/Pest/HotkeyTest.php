<?php

use NativePHP\Think\Hotkey;
use NativePHP\Think\Native;

// 模拟 Hotkey 类
class MockHotkey {
    protected $registeredHotkeys = [];
    
    public function __construct($native) {}
    
    public function register($accelerator, $callback) {
        $this->registeredHotkeys[$accelerator] = $callback;
        return $this;
    }
    
    public function unregister($accelerator) {
        unset($this->registeredHotkeys[$accelerator]);
        return $this;
    }
    
    public function unregisterAll() {
        $this->registeredHotkeys = [];
        return $this;
    }
    
    public function getRegisteredHotkeys() {
        return $this->registeredHotkeys;
    }
    
    public function isRegistered($accelerator) {
        return isset($this->registeredHotkeys[$accelerator]);
    }
}

// 创建 Hotkey 实例的辅助函数
function createMockHotkey() {
    $native = mockNative();
    $hotkey = new MockHotkey($native);
    return $hotkey;
}

// 测试 Hotkey 类的流畅接口
test('Hotkey 类提供流畅接口', function () {
    $hotkey = createMockHotkey();
    
    // 测试方法链
    expect($hotkey->register('CommandOrControl+X', function () {}))->toBeInstanceOf(MockHotkey::class);
    expect($hotkey->unregister('CommandOrControl+X'))->toBeInstanceOf(MockHotkey::class);
    expect($hotkey->unregisterAll())->toBeInstanceOf(MockHotkey::class);
});

// 测试注册热键
test('Hotkey 可以注册热键', function () {
    $hotkey = createMockHotkey();
    
    $callback = function () {
        return 'hotkey pressed';
    };
    
    $hotkey->register('CommandOrControl+X', $callback);
    
    expect($hotkey->isRegistered('CommandOrControl+X'))->toBeTrue();
    expect($hotkey->getRegisteredHotkeys()['CommandOrControl+X'])->toBe($callback);
});

// 测试注册多个热键
test('Hotkey 可以注册多个热键', function () {
    $hotkey = createMockHotkey();
    
    $callback1 = function () {
        return 'hotkey 1 pressed';
    };
    
    $callback2 = function () {
        return 'hotkey 2 pressed';
    };
    
    $hotkey->register('CommandOrControl+X', $callback1)
           ->register('CommandOrControl+Y', $callback2);
    
    expect($hotkey->getRegisteredHotkeys())->toHaveCount(2);
    expect($hotkey->isRegistered('CommandOrControl+X'))->toBeTrue();
    expect($hotkey->isRegistered('CommandOrControl+Y'))->toBeTrue();
});

// 测试取消注册热键
test('Hotkey 可以取消注册热键', function () {
    $hotkey = createMockHotkey();
    
    $hotkey->register('CommandOrControl+X', function () {})
           ->register('CommandOrControl+Y', function () {});
    
    $hotkey->unregister('CommandOrControl+X');
    
    expect($hotkey->isRegistered('CommandOrControl+X'))->toBeFalse();
    expect($hotkey->isRegistered('CommandOrControl+Y'))->toBeTrue();
});

// 测试取消注册所有热键
test('Hotkey 可以取消注册所有热键', function () {
    $hotkey = createMockHotkey();
    
    $hotkey->register('CommandOrControl+X', function () {})
           ->register('CommandOrControl+Y', function () {});
    
    $hotkey->unregisterAll();
    
    expect($hotkey->getRegisteredHotkeys())->toBeEmpty();
});
