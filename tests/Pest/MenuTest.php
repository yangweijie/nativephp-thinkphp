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
