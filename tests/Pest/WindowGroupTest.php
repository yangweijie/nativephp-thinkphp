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

    public function test_can_apply_transition_to_group()
    {
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);

        $this->group->add('window1')->add('window2');
        
        $this->group->transition()
            ->duration(500)
            ->easing('easeOutBounce');

        $this->group->arrangeHorizontalWithTransition();

        // 验证所有窗口都应用了过渡动画
        $window1 = $this->manager->get('window1');
        $window2 = $this->manager->get('window2');

        // 验证窗口1的过渡动画
        $lastTransition1 = $this->native->ipc()->getLastTransitionMessage('window1');
        expect($lastTransition1)->toHaveKey('options');
        expect($lastTransition1['options'])->toMatchArray([
            'duration' => 500,
            'easing' => 'easeOutBounce'
        ]);

        // 验证窗口2的过渡动画
        $lastTransition2 = $this->native->ipc()->getLastTransitionMessage('window2');
        expect($lastTransition2)->toHaveKey('options');
        expect($lastTransition2['options'])->toMatchArray([
            'duration' => 500,
            'easing' => 'easeOutBounce'
        ]);
    }

    public function test_can_sync_layout_with_transition()
    {
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);
        $this->group->add('window1')->add('window2');

        $group2 = $this->manager->createGroup('test-group-2');
        $this->manager->create('window3', ['width' => 800, 'height' => 600]);
        $this->manager->create('window4', ['width' => 600, 'height' => 400]);
        $group2->add('window3')->add('window4');

        // 设置源组的布局和过渡动画
        $this->group->transition()
            ->duration(600)
            ->easing('easeOutElastic');

        // 同步布局到目标组
        $this->group->syncLayoutWithTransition('test-group-2');

        // 验证目标组窗口的过渡动画
        $lastTransition3 = $this->native->ipc()->getLastTransitionMessage('window3');
        expect($lastTransition3)->toHaveKey('options');
        expect($lastTransition3['options'])->toMatchArray([
            'duration' => 600,
            'easing' => 'easeOutElastic'
        ]);

        $lastTransition4 = $this->native->ipc()->getLastTransitionMessage('window4');
        expect($lastTransition4)->toHaveKey('options');
        expect($lastTransition4['options'])->toMatchArray([
            'duration' => 600,
            'easing' => 'easeOutElastic'
        ]);
    }

    public function test_can_use_transition_presets()
    {
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);
        $this->group->add('window1')->add('window2');

        // 应用快速动画预设
        $this->group->transition()
            ->usePreset('fast');
        $this->group->arrangeHorizontalWithTransition();

        // 验证预设被正确应用
        $lastTransition = $this->native->ipc()->getLastTransitionMessage('window1');
        expect($lastTransition)->toHaveKey('options');
        expect($lastTransition['options'])->toMatchArray([
            'duration' => 150,
            'easing' => 'easeOutQuint'
        ]);
    }
}