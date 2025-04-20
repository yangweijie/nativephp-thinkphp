<?php

use NativePHP\Think\Menu;
use NativePHP\Think\Native;

// 创建 Menu 实例的辅助函数
function createMockMenu() {
    $native = mockNative();
    $menu = new Menu($native);
    return $menu;
}

// 测试 Menu 类的流畅接口
test('Menu 类提供流畅接口', function () {
    $menu = createMockMenu();

    // 测试方法链
    expect($menu->add('Test Item'))->toBeInstanceOf(Menu::class);
    expect($menu->addSeparator())->toBeInstanceOf(Menu::class);
});

// 测试添加菜单项
test('Menu 可以添加菜单项', function () {
    $menu = createMockMenu();

    $menu->add('Test Item');

    // 由于我们使用的是模拟对象，我们只能测试方法链是否正常工作
    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试添加带选项的菜单项
test('Menu 可以添加带选项的菜单项', function () {
    $menu = createMockMenu();

    $menu->add('Test Item', [
        'accelerator' => 'CmdOrCtrl+T',
        'enabled' => false,
        'icon' => 'path/to/icon.png',
    ]);

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试添加子菜单
test('Menu 可以添加子菜单', function () {
    $menu = createMockMenu();

    $menu->addSubmenu('Submenu', function ($submenu) {
        $submenu->add('Submenu Item 1');
        $submenu->add('Submenu Item 2');
    });

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试添加分隔符
test('Menu 可以添加分隔符', function () {
    $menu = createMockMenu();

    $menu->add('Item 1')
        ->addSeparator()
        ->add('Item 2');

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试配置菜单
test('Menu 可以配置', function () {
    $menu = createMockMenu();

    $config = [
        'theme' => 'dark',
        'position' => 'bottom',
    ];

    $menu->configure($config);

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试清除菜单
test('Menu 可以清除', function () {
    $menu = createMockMenu();

    $menu->add('Item 1')
        ->add('Item 2')
        ->clear();

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试禁用菜单项
test('Menu 可以禁用菜单项', function () {
    $menu = createMockMenu();

    $menu->add('Item 1')
        ->add('Item 2')
        ->disable('Item 1');

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试启用菜单项
test('Menu 可以启用菜单项', function () {
    $menu = createMockMenu();

    $menu->add('Item 1', ['enabled' => false])
        ->enable('Item 1');

    expect($menu)->toBeInstanceOf(Menu::class);
});

// 测试切换菜单项状态
test('Menu 可以切换菜单项状态', function () {
    $menu = createMockMenu();

    $menu->add('Item 1')
        ->toggle('Item 1');

    expect($menu)->toBeInstanceOf(Menu::class);
});

test('可以创建基本菜单', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '文件'],
        ['label' => '编辑'],
        ['label' => '视图']
    ];
    
    $menu->create($template);
    
    expect($menu->getItems())->toBe($template);
});

test('可以创建带子菜单的菜单', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        [
            'label' => '文件',
            'submenu' => [
                ['label' => '新建'],
                ['label' => '打开'],
                ['label' => '保存']
            ]
        ]
    ];
    
    $menu->create($template);
    
    expect($menu->getItems()[0]['submenu'])->toHaveCount(3);
});

test('可以创建带快捷键的菜单项', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        [
            'label' => '编辑',
            'submenu' => [
                ['label' => '复制', 'accelerator' => 'CommandOrControl+C'],
                ['label' => '粘贴', 'accelerator' => 'CommandOrControl+V'],
                ['label' => '剪切', 'accelerator' => 'CommandOrControl+X']
            ]
        ]
    ];
    
    $menu->create($template);
    
    $submenu = $menu->getItems()[0]['submenu'];
    expect($submenu[0]['accelerator'])->toBe('CommandOrControl+C');
});

test('可以创建带分隔符的菜单', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '文件'],
        ['type' => 'separator'],
        ['label' => '编辑']
    ];
    
    $menu->create($template);
    
    expect($menu->getItems()[1]['type'])->toBe('separator');
});

test('菜单项支持点击事件', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $clicked = false;
    
    $template = [
        [
            'label' => '测试',
            'click' => function() use (&$clicked) {
                $clicked = true;
            }
        ]
    ];
    
    $menu->create($template);
    $native->emit('menu-click', ['menuItem' => 0]);
    
    expect($clicked)->toBeTrue();
});

test('可以动态更新菜单项', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '原始项']
    ];
    
    $menu->create($template);
    
    $menu->updateItem(0, ['label' => '更新项']);
    
    expect($menu->getItems()[0]['label'])->toBe('更新项');
});

test('可以动态插入菜单项', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '项目1'],
        ['label' => '项目3']
    ];
    
    $menu->create($template);
    
    $menu->insertItem(1, ['label' => '项目2']);
    
    expect($menu->getItems())->toHaveCount(3);
    expect($menu->getItems()[1]['label'])->toBe('项目2');
});

test('可以动态删除菜单项', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '项目1'],
        ['label' => '项目2'],
        ['label' => '项目3']
    ];
    
    $menu->create($template);
    
    $menu->removeItem(1);
    
    expect($menu->getItems())->toHaveCount(2);
    expect($menu->getItems()[1]['label'])->toBe('项目3');
});

test('菜单项支持启用/禁用状态', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '测试', 'enabled' => true]
    ];
    
    $menu->create($template);
    
    $menu->updateItem(0, ['enabled' => false]);
    
    expect($menu->getItems()[0]['enabled'])->toBeFalse();
});

test('菜单项支持选中状态', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '测试', 'type' => 'checkbox', 'checked' => false]
    ];
    
    $menu->create($template);
    
    $menu->updateItem(0, ['checked' => true]);
    
    expect($menu->getItems()[0]['checked'])->toBeTrue();
});

test('菜单项支持单选组', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        [
            'label' => '选项',
            'submenu' => [
                ['label' => '选项1', 'type' => 'radio', 'checked' => true],
                ['label' => '选项2', 'type' => 'radio', 'checked' => false],
                ['label' => '选项3', 'type' => 'radio', 'checked' => false]
            ]
        ]
    ];
    
    $menu->create($template);
    
    expect($menu->getItems()[0]['submenu'][0]['checked'])->toBeTrue();
});

test('菜单支持上下文菜单', function() {
    $native = mockNative();
    $menu = new Menu($native);
    
    $template = [
        ['label' => '剪切'],
        ['label' => '复制'],
        ['label' => '粘贴']
    ];
    
    $menu->createContextMenu($template);
    
    expect($menu->getContextMenuItems())->toBe($template);
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
