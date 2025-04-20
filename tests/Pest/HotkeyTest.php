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

test('可以注册全局快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $triggered = false;
    
    $hotkey->register('CommandOrControl+X', function() use (&$triggered) {
        $triggered = true;
    });
    
    $native->emit('hotkey', 'CommandOrControl+X');
    
    expect($triggered)->toBeTrue();
});

test('可以注册带修饰键的快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $keys = [
        'Shift+A' => false,
        'Alt+B' => false,
        'Control+C' => false,
        'Command+D' => false,
        'CommandOrControl+E' => false,
    ];
    
    foreach ($keys as $combination => $triggered) {
        $hotkey->register($combination, function() use (&$keys, $combination) {
            $keys[$combination] = true;
        });
    }
    
    foreach (array_keys($keys) as $combination) {
        $native->emit('hotkey', $combination);
    }
    
    foreach ($keys as $triggered) {
        expect($triggered)->toBeTrue();
    }
});

test('可以注册多个按键组合', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $combination = 'CommandOrControl+Shift+A';
    $triggered = false;
    
    $hotkey->register($combination, function() use (&$triggered) {
        $triggered = true;
    });
    
    $native->emit('hotkey', $combination);
    
    expect($triggered)->toBeTrue();
});

test('可以注册数字键快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $numbers = [];
    
    for ($i = 0; $i <= 9; $i++) {
        $combination = "CommandOrControl+{$i}";
        $number = $i;
        
        $hotkey->register($combination, function() use (&$numbers, $number) {
            $numbers[] = $number;
        });
        
        $native->emit('hotkey', $combination);
    }
    
    expect($numbers)->toBe(range(0, 9));
});

test('可以注册功能键快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $fkeys = [];
    
    for ($i = 1; $i <= 12; $i++) {
        $combination = "F{$i}";
        $key = $i;
        
        $hotkey->register($combination, function() use (&$fkeys, $key) {
            $fkeys[] = $key;
        });
        
        $native->emit('hotkey', $combination);
    }
    
    expect($fkeys)->toBe(range(1, 12));
});

test('可以注销快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $triggered = false;
    $combination = 'CommandOrControl+X';
    
    $hotkey->register($combination, function() use (&$triggered) {
        $triggered = true;
    });
    
    $hotkey->unregister($combination);
    
    $native->emit('hotkey', $combination);
    
    expect($triggered)->toBeFalse();
});

test('可以一次性注销所有快捷键', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $triggers = [
        'CommandOrControl+A' => false,
        'CommandOrControl+B' => false,
        'CommandOrControl+C' => false
    ];
    
    foreach ($triggers as $combination => $triggered) {
        $hotkey->register($combination, function() use (&$triggers, $combination) {
            $triggers[$combination] = true;
        });
    }
    
    $hotkey->unregisterAll();
    
    foreach (array_keys($triggers) as $combination) {
        $native->emit('hotkey', $combination);
    }
    
    foreach ($triggers as $triggered) {
        expect($triggered)->toBeFalse();
    }
});

test('快捷键注册失败时抛出异常', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    expect(function() use ($hotkey) {
        $hotkey->register('invalid-combination', function() {});
    })->toThrow(Exception::class);
});

test('重复注册相同快捷键时抛出异常', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    $combination = 'CommandOrControl+X';
    
    $hotkey->register($combination, function() {});
    
    expect(function() use ($hotkey, $combination) {
        $hotkey->register($combination, function() {});
    })->toThrow(Exception::class);
});

test('注销未注册的快捷键时抛出异常', function() {
    $native = mockNative();
    $hotkey = new Hotkey($native);
    
    expect(function() use ($hotkey) {
        $hotkey->unregister('CommandOrControl+X');
    })->toThrow(Exception::class);
});

// 辅助函数：创建模拟 Native 实例
function mockNative() {
    return new class {
        protected $listeners = [];
        
        public function on($event, $callback) {
            if (!isset($this->listeners[$event])) {
                $this->listeners[$event] = [];
            }
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
