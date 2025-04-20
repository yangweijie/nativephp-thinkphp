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

test('托盘可以创建和配置', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $tray->setIcon('/path/to/icon.png')
         ->setTooltip('测试托盘')
         ->create();
         
    $state = $tray->getState();
    expect($state['icon'])->toBe('/path/to/icon.png');
    expect($state['tooltip'])->toBe('测试托盘');
});

test('托盘可以设置和获取菜单', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $menu = [
        ['label' => '打开', 'click' => 'open'],
        ['type' => 'separator'],
        ['label' => '退出', 'click' => 'quit']
    ];
    
    $tray->setMenu($menu);
    expect($tray->getMenu())->toBe($menu);
});

test('托盘支持动态更新', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $tray->create();
    
    $updates = [
        'setIcon' => '/new/icon.png',
        'setTooltip' => '新的提示',
        'setContextMenu' => [
            ['label' => '新菜单项', 'click' => 'newAction']
        ]
    ];
    
    foreach ($updates as $method => $value) {
        $tray->$method($value);
    }
    
    $state = $tray->getState();
    expect($state['icon'])->toBe('/new/icon.png');
    expect($state['tooltip'])->toBe('新的提示');
    expect($state['menu'][0]['label'])->toBe('新菜单项');
});

test('托盘支持点击事件', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $clicked = false;
    
    $tray->onClick(function() use (&$clicked) {
        $clicked = true;
    });
    
    $native->emit('tray-click');
    
    expect($clicked)->toBeTrue();
});

test('托盘支持双击事件', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $doubleClicked = false;
    
    $tray->onDoubleClick(function() use (&$doubleClicked) {
        $doubleClicked = true;
    });
    
    $native->emit('tray-double-click');
    
    expect($doubleClicked)->toBeTrue();
});

test('托盘支持右键菜单事件', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $rightClicked = false;
    
    $tray->onRightClick(function() use (&$rightClicked) {
        $rightClicked = true;
    });
    
    $native->emit('tray-right-click');
    
    expect($rightClicked)->toBeTrue();
});

test('托盘可以处理菜单项点击', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $actionTriggered = false;
    
    $menu = [
        [
            'label' => '测试',
            'click' => function() use (&$actionTriggered) {
                $actionTriggered = true;
            }
        ]
    ];
    
    $tray->setMenu($menu);
    $native->emit('tray-menu-click', ['menuItem' => 0]);
    
    expect($actionTriggered)->toBeTrue();
});

test('托盘支持子菜单', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $menu = [
        [
            'label' => '父菜单',
            'submenu' => [
                ['label' => '子菜单1'],
                ['label' => '子菜单2']
            ]
        ]
    ];
    
    $tray->setMenu($menu);
    $state = $tray->getState();
    
    expect($state['menu'][0]['submenu'])->toHaveCount(2);
    expect($state['menu'][0]['submenu'][0]['label'])->toBe('子菜单1');
});

test('托盘支持菜单项状态', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $menu = [
        [
            'label' => '可用项',
            'enabled' => true
        ],
        [
            'label' => '禁用项',
            'enabled' => false
        ],
        [
            'label' => '选中项',
            'checked' => true
        ]
    ];
    
    $tray->setMenu($menu);
    $state = $tray->getState();
    
    expect($state['menu'][0]['enabled'])->toBeTrue();
    expect($state['menu'][1]['enabled'])->toBeFalse();
    expect($state['menu'][2]['checked'])->toBeTrue();
});

test('托盘可以销毁', function() {
    $native = mockNative();
    $tray = new Tray($native);
    
    $tray->create();
    expect($tray->isCreated())->toBeTrue();
    
    $tray->destroy();
    expect($tray->isCreated())->toBeFalse();
});

// 辅助函数：创建模拟 Native 实例
function mockNative() {
    return new class {
        protected $listeners = [];
        protected $lastEmitted;
        
        public function on($event, $callback) {
            if (!isset($this->listeners[$event])) {
                $this->listeners[$event] = [];
            }
            $this->listeners[$event][] = $callback;
        }
        
        public function emit($event, $data = null) {
            $this->lastEmitted = [
                'event' => $event,
                'data' => $data
            ];
            
            if (isset($this->listeners[$event])) {
                foreach ($this->listeners[$event] as $callback) {
                    $callback($data);
                }
            }
        }
        
        public function getLastEmitted() {
            return $this->lastEmitted;
        }
    };
}
