<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\WindowGroup;
use NativePHP\Think\WindowManager;
use NativePHP\Think\WindowLayoutPresets;

class WindowGroupLayoutTest extends TestCase
{
    protected $native;
    protected $manager;
    protected $layoutPresets;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->native = mockNative();
        $this->manager = new WindowManager($this->native);
        $this->layoutPresets = new WindowLayoutPresets($this->manager);
    }

    public function test_can_create_and_apply_layout_preset()
    {
        // 定义一个自定义布局
        $this->layoutPresets->define('custom', function ($manager, $windows) {
            foreach ($windows as $index => $window) {
                $manager->getWindow($window)
                    ->x(100 * $index)
                    ->y(100 * $index)
                    ->show();
            }
        });

        // 创建测试窗口组
        $group = $this->manager->createGroup('test-layout');
        $this->manager->create('window1');
        $this->manager->create('window2');
        $group->add('window1')->add('window2');

        // 应用布局
        $this->layoutPresets->apply('custom', ['window1', 'window2']);

        // 验证窗口位置
        $window1 = $this->manager->get('window1');
        $window2 = $this->manager->get('window2');
        
        expect($window1->getOptions()['x'])->toBe(0);
        expect($window1->getOptions()['y'])->toBe(0);
        expect($window2->getOptions()['x'])->toBe(100);
        expect($window2->getOptions()['y'])->toBe(100);
    }

    public function test_can_sync_layout_between_groups()
    {
        // 创建两个窗口组
        $group1 = $this->manager->createGroup('group1');
        $group2 = $this->manager->createGroup('group2');

        // 添加窗口到组
        $this->manager->create('window1');
        $this->manager->create('window2');
        $group1->add('window1');
        
        $this->manager->create('window3');
        $this->manager->create('window4');
        $group2->add('window3');
        
        // 应用布局到第一个组
        $group1->arrangeHorizontal();
        
        // 同步布局到第二个组
        $group1->syncLayout('group2');
        
        // 验证两个组使用相同的布局
        expect($group1->getCurrentLayout())->toBe($group2->getCurrentLayout());
    }

    public function test_can_export_and_import_layout()
    {
        // 创建测试窗口组
        $group = $this->manager->createGroup('test-export');
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);
        $group->add('window1')->add('window2');

        // 应用布局
        $group->arrangeHorizontal();

        // 导出布局配置
        $config = $group->exportLayout();
        expect($config)->toBeArray();
        expect($config)->toHaveKey('layout');
        expect($config)->toHaveKey('windows');

        // 创建新组并导入布局
        $newGroup = $this->manager->createGroup('test-import');
        $this->manager->create('window3', ['width' => 800, 'height' => 600]);
        $this->manager->create('window4', ['width' => 600, 'height' => 400]);
        $newGroup->add('window3')->add('window4');

        $newGroup->importLayout($config);
        expect($newGroup->getCurrentLayout())->toBe($group->getCurrentLayout());
    }

    public function test_layout_change_event()
    {
        $layoutChanged = false;
        $group = $this->manager->createGroup('test-event');
        
        // 注册布局变更事件监听器
        $group->onLayoutChange(function () use (&$layoutChanged) {
            $layoutChanged = true;
        });

        // 触发布局变更
        $group->arrangeVertical();
        
        expect($layoutChanged)->toBeTrue();
    }
}