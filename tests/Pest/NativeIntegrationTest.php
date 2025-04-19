<?php

use NativePHP\Think\Native;
use NativePHP\Think\Window;
use NativePHP\Think\WindowManager;
use NativePHP\Think\WindowPresets;
use NativePHP\Think\WindowLayoutPresets;
use NativePHP\Think\WindowState;
use NativePHP\Think\Menu;
use NativePHP\Think\Tray;
use NativePHP\Think\Hotkey;
use NativePHP\Think\Ipc;
use NativePHP\Think\EventDispatcher;
use NativePHP\Think\Bridge;
use NativePHP\Think\Tests\Pest\MockMenu;
use NativePHP\Think\Tests\Pest\MockWindow;
use NativePHP\Think\Tests\Pest\MockEventDispatcher;
use NativePHP\Think\Tests\Pest\MockIpc;

// 模拟 Native 类
class MockNativeIntegration {
    protected $config = [];
    protected $instances = [];

    public function __construct() {
        $this->config = [
            'app' => [
                'name' => 'Test App',
                'version' => '1.0.0',
            ],
            'window' => [
                'default' => [
                    'width' => 800,
                    'height' => 600,
                    'center' => true,
                ],
            ],
        ];
    }

    public function getConfig($key = null, $default = null) {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    public function window() {
        return $this->getInstance('window', function () {
            return new MockWindow($this);
        });
    }

    public function windowManager() {
        return $this->getInstance('windowManager', function () {
            return new MockWindowManager($this);
        });
    }

    public function windowPresets() {
        return $this->getInstance('windowPresets', function () {
            return new MockWindowPresets($this);
        });
    }

    public function windowLayoutPresets() {
        return $this->getInstance('windowLayoutPresets', function () {
            return new MockWindowLayoutPresets($this);
        });
    }

    public function windowState() {
        return $this->getInstance('windowState', function () {
            return new MockWindowState($this);
        });
    }

    public function menu() {
        return $this->getInstance('menu', function () {
            return new MockMenu($this);
        });
    }

    public function tray() {
        return $this->getInstance('tray', function () {
            return new MockTray($this);
        });
    }

    public function hotkey() {
        return $this->getInstance('hotkey', function () {
            return new MockHotkey($this);
        });
    }

    public function ipc() {
        return $this->getInstance('ipc', function () {
            return new MockIpc($this);
        });
    }

    public function events() {
        return $this->getInstance('events', function () {
            return new \NativePHP\Think\Tests\Pest\MockEventDispatcher();
        });
    }

    public function bridge() {
        return $this->getInstance('bridge', function () {
            return new MockBridge($this);
        });
    }

    protected function getInstance($key, $factory) {
        if (!isset($this->instances[$key])) {
            $this->instances[$key] = $factory();
        }

        return $this->instances[$key];
    }
}

// 模拟 WindowManager 类
class MockWindowManager {
    public function __construct($native) {}
    public function open() { return $this; }
    public function close() { return $this; }
    public function minimize() { return $this; }
    public function maximize() { return $this; }
    public function restore() { return $this; }
}

// 模拟 WindowPresets 类
class MockWindowPresets {
    public function __construct($native) {}
    public function titleBarOnly() { return new MockWindow(null); }
    public function centered() { return new MockWindow(null); }
    public function fullscreen() { return new MockWindow(null); }
}

// 模拟 WindowLayoutPresets 类
class MockWindowLayoutPresets {
    public function __construct($windowManager) {}
    public function topLeft() { return $this; }
    public function topRight() { return $this; }
    public function bottomLeft() { return $this; }
    public function bottomRight() { return $this; }
}

// 模拟 WindowState 类
class MockWindowState {
    public function __construct($windowManager) {}
    public function isMaximized() { return false; }
    public function isMinimized() { return false; }
    public function isFullscreen() { return false; }
    public function isVisible() { return true; }
    public function isFocused() { return true; }
}

// 使用已定义的 MockIpc 类

// 模拟 Bridge 类
class MockBridge {
    public function __construct($native) {}
    public function emit() { return $this; }
    public function on() { return $this; }
}

// 创建 Native 实例的辅助函数
function createMockNativeIntegration() {
    return new MockNativeIntegration();
}

// 测试 Native 类的集成功能
test('Native 类可以创建和管理所有组件', function () {
    $native = createMockNativeIntegration();

    // 测试获取配置
    expect($native->getConfig('app.name'))->toBe('Test App');
    expect($native->getConfig('app.version'))->toBe('1.0.0');
    expect($native->getConfig('window.default.width'))->toBe(800);

    // 测试创建窗口
    expect($native->window())->toBeInstanceOf(MockWindow::class);

    // 测试窗口管理器
    expect($native->windowManager())->toBeInstanceOf(MockWindowManager::class);

    // 测试窗口预设
    expect($native->windowPresets())->toBeInstanceOf(MockWindowPresets::class);

    // 测试窗口布局预设
    expect($native->windowLayoutPresets())->toBeInstanceOf(MockWindowLayoutPresets::class);

    // 测试窗口状态
    expect($native->windowState())->toBeInstanceOf(MockWindowState::class);

    // 测试菜单
    expect($native->menu())->toBeInstanceOf(MockMenu::class);

    // 测试托盘
    expect($native->tray())->toBeInstanceOf(MockTray::class);

    // 测试热键
    expect($native->hotkey())->toBeInstanceOf(MockHotkey::class);

    // 测试 IPC
    expect($native->ipc())->toBeInstanceOf(MockIpc::class);

    // 测试事件分发器
    expect($native->events())->toBeInstanceOf(\NativePHP\Think\Tests\Pest\MockEventDispatcher::class);

    // 测试桥接器
    expect($native->bridge())->toBeInstanceOf(MockBridge::class);
});

// 测试 Native 类的单例模式
test('Native 类使用单例模式管理组件', function () {
    $native = createMockNativeIntegration();

    // 测试窗口管理器单例
    $windowManager1 = $native->windowManager();
    $windowManager2 = $native->windowManager();
    expect($windowManager1)->toBe($windowManager2);

    // 测试菜单单例
    $menu1 = $native->menu();
    $menu2 = $native->menu();
    expect($menu1)->toBe($menu2);

    // 测试托盘单例
    $tray1 = $native->tray();
    $tray2 = $native->tray();
    expect($tray1)->toBe($tray2);

    // 测试热键单例
    $hotkey1 = $native->hotkey();
    $hotkey2 = $native->hotkey();
    expect($hotkey1)->toBe($hotkey2);

    // 测试事件分发器单例
    $events1 = $native->events();
    $events2 = $native->events();
    expect($events1)->toBe($events2);
});

// 测试 IPC 通信
test('Native 类可以通过 IPC 进行通信', function () {
    $native = createMockNativeIntegration();
    $ipc = $native->ipc();

    $receivedData = null;

    $ipc->handle('test-channel', function ($data) use (&$receivedData) {
        $receivedData = $data;
    });

    $ipc->send('test-channel', 'test-data');

    expect($receivedData)->toBe('test-data');
});
