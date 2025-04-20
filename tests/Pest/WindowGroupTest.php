<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\WindowGroup;
use NativePHP\Think\WindowManager;
use NativePHP\Think\Native;
use NativePHP\Think\Tests\TestCase;

class WindowGroupTest extends TestCase
{
    protected $native;
    protected $manager;
    protected $group;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->native = mockNative();
        $this->manager = new WindowManager($this->native);
        $this->group = new WindowGroup($this->manager, 'test-group');
    }

    public function test_can_create_window_group()
    {
        expect($this->group)->toBeInstanceOf(WindowGroup::class);
        expect($this->group->getName())->toBe('test-group');
        expect($this->group->count())->toBe(0);
    }

    public function test_can_add_and_remove_windows()
    {
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);

        $this->group->add('window1');
        expect($this->group->has('window1'))->toBeTrue();
        expect($this->group->count())->toBe(1);

        $this->group->add('window2');
        expect($this->group->has('window2'))->toBeTrue();
        expect($this->group->count())->toBe(2);

        $this->group->remove('window1');
        expect($this->group->has('window1'))->toBeFalse();
        expect($this->group->count())->toBe(1);
    }

    public function test_can_arrange_windows()
    {
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);

        $this->group->add('window1')->add('window2');

        // 测试水平排列
        $this->group->arrangeHorizontal();
        $window1 = $this->manager->get('window1');
        $window2 = $this->manager->get('window2');
        
        expect($window1->getOptions()['x'])->toBe(0);
        expect($window2->getOptions()['x'])->toBeGreaterThan(0);

        // 测试垂直排列
        $this->group->arrangeVertical();
        expect($window1->getOptions()['y'])->toBe(0);
        expect($window2->getOptions()['y'])->toBeGreaterThan(0);
    }

    public function test_can_save_and_restore_state()
    {
        $this->manager->create('window1', [
            'width' => 800,
            'height' => 600,
            'x' => 100,
            'y' => 100
        ]);

        $this->group->add('window1');
        
        // 保存状态
        $state = $this->group->saveState();
        expect($state)->toBeArray();
        expect($state['window1'])->toBeArray();
        
        // 修改窗口位置
        $window = $this->manager->get('window1');
        $window->configure(['x' => 200, 'y' => 200]);
        
        // 恢复状态
        $this->group->restoreState($state);
        $restoredWindow = $this->manager->get('window1');
        expect($restoredWindow->getOptions()['x'])->toBe(100);
        expect($restoredWindow->getOptions()['y'])->toBe(100);
    }
}