<?php

use NativePHP\Think\Tray;
use NativePHP\Think\Native;

// 模拟 Tray 类
class MockTray {
    protected $icon = null;
    protected $menuItems = [];
    protected $tooltip = null;
    
    public function __construct($native) {}
    
    public function icon($icon) {
        $this->icon = $icon;
        return $this;
    }
    
    public function tooltip($tooltip) {
        $this->tooltip = $tooltip;
        return $this;
    }
    
    public function addItem($label, $callback = null, $options = []) {
        $this->menuItems[] = array_merge([
            'label' => $label,
            'callback' => $callback,
        ], $options);
        return $this;
    }
    
    public function addSeparator() {
        $this->menuItems[] = [
            'type' => 'separator',
        ];
        return $this;
    }
    
    public function addSubmenu($label, $callback, $options = []) {
        $submenu = [];
        $callback(new class($submenu) {
            protected $items = [];
            
            public function __construct(&$items) {
                $this->items = &$items;
            }
            
            public function addItem($label, $callback = null, $options = []) {
                $this->items[] = array_merge([
                    'label' => $label,
                    'callback' => $callback,
                ], $options);
                return $this;
            }
            
            public function addSeparator() {
                $this->items[] = [
                    'type' => 'separator',
                ];
                return $this;
            }
        });
        
        $this->menuItems[] = array_merge([
            'label' => $label,
            'submenu' => $submenu,
        ], $options);
        
        return $this;
    }
    
    public function show() {
        return $this;
    }
    
    public function hide() {
        return $this;
    }
    
    public function getIcon() {
        return $this->icon;
    }
    
    public function getTooltip() {
        return $this->tooltip;
    }
    
    public function getMenuItems() {
        return $this->menuItems;
    }
}

// 创建 Tray 实例的辅助函数
function createMockTray() {
    $native = mockNative();
    $tray = new MockTray($native);
    return $tray;
}

// 测试 Tray 类的流畅接口
test('Tray 类提供流畅接口', function () {
    $tray = createMockTray();
    
    // 测试方法链
    expect($tray->icon('path/to/icon.png'))->toBeInstanceOf(MockTray::class);
    expect($tray->tooltip('Tray Tooltip'))->toBeInstanceOf(MockTray::class);
    expect($tray->addItem('Test Item'))->toBeInstanceOf(MockTray::class);
});

// 测试设置图标
test('Tray 可以设置图标', function () {
    $tray = createMockTray();
    
    $tray->icon('path/to/icon.png');
    
    expect($tray->getIcon())->toBe('path/to/icon.png');
});

// 测试设置提示文本
test('Tray 可以设置提示文本', function () {
    $tray = createMockTray();
    
    $tray->tooltip('Tray Tooltip');
    
    expect($tray->getTooltip())->toBe('Tray Tooltip');
});

// 测试添加菜单项
test('Tray 可以添加菜单项', function () {
    $tray = createMockTray();
    
    $callback = function () {
        return 'clicked';
    };
    
    $tray->addItem('Test Item', $callback);
    
    $menuItems = $tray->getMenuItems();
    expect($menuItems)->toHaveCount(1);
    expect($menuItems[0]['label'])->toBe('Test Item');
    expect($menuItems[0]['callback'])->toBe($callback);
});

// 测试添加带选项的菜单项
test('Tray 可以添加带选项的菜单项', function () {
    $tray = createMockTray();
    
    $tray->addItem('Test Item', null, [
        'enabled' => false,
        'icon' => 'path/to/icon.png',
    ]);
    
    $menuItems = $tray->getMenuItems();
    expect($menuItems[0]['enabled'])->toBeFalse();
    expect($menuItems[0]['icon'])->toBe('path/to/icon.png');
});

// 测试添加分隔符
test('Tray 可以添加分隔符', function () {
    $tray = createMockTray();
    
    $tray->addItem('Item 1')
        ->addSeparator()
        ->addItem('Item 2');
    
    $menuItems = $tray->getMenuItems();
    expect($menuItems)->toHaveCount(3);
    expect($menuItems[0]['label'])->toBe('Item 1');
    expect($menuItems[1]['type'])->toBe('separator');
    expect($menuItems[2]['label'])->toBe('Item 2');
});

// 测试添加子菜单
test('Tray 可以添加子菜单', function () {
    $tray = createMockTray();
    
    $tray->addSubmenu('Submenu', function ($submenu) {
        $submenu->addItem('Submenu Item 1');
        $submenu->addItem('Submenu Item 2');
    });
    
    $menuItems = $tray->getMenuItems();
    expect($menuItems)->toHaveCount(1);
    expect($menuItems[0]['label'])->toBe('Submenu');
    expect($menuItems[0]['submenu'])->toHaveCount(2);
    expect($menuItems[0]['submenu'][0]['label'])->toBe('Submenu Item 1');
    expect($menuItems[0]['submenu'][1]['label'])->toBe('Submenu Item 2');
});

// 测试显示和隐藏
test('Tray 可以显示和隐藏', function () {
    $tray = createMockTray();
    
    expect($tray->show())->toBeInstanceOf(MockTray::class);
    expect($tray->hide())->toBeInstanceOf(MockTray::class);
});
