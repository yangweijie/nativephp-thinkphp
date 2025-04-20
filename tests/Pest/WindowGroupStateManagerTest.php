<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\WindowGroupStateManager;
use NativePHP\Think\WindowManager;
use think\facade\Cache;

class WindowGroupStateManagerTest extends TestCase
{
    protected $native;
    protected $manager;
    protected $stateManager;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->native = mockNative();
        $this->manager = new WindowManager($this->native);
        $this->stateManager = new WindowGroupStateManager($this->manager);
    }

    public function test_can_auto_save_all_groups()
    {
        // 创建测试窗口组
        $group1 = $this->manager->createGroup('group1');
        $this->manager->create('window1', ['width' => 800, 'height' => 600]);
        $group1->add('window1');
        
        $group2 = $this->manager->createGroup('group2');
        $this->manager->create('window2', ['width' => 600, 'height' => 400]);
        $group2->add('window2');

        // 应用布局
        $group1->arrangeHorizontal();
        $group2->arrangeVertical();

        // 自动保存所有分组状态
        $this->stateManager->autoSaveAll();

        // 验证缓存中保存的状态
        $states = Cache::get('native_window_groups');
        expect($states)->toBeArray();
        expect($states)->toHaveKey('group1');
        expect($states)->toHaveKey('group2');
        expect($states['group1'])->toHaveKey('windows');
        expect($states['group1'])->toHaveKey('layout');
    }

    public function test_can_auto_restore_all_groups()
    {
        // 准备测试数据
        $testStates = [
            'group1' => [
                'windows' => [
                    'window1' => [
                        'width' => 800,
                        'height' => 600,
                        'x' => 0,
                        'y' => 0
                    ]
                ],
                'layout' => 'horizontal',
                'timestamp' => time()
            ]
        ];
        
        Cache::set('native_window_groups', $testStates);

        // 创建窗口和分组
        $group = $this->manager->createGroup('group1');
        $this->manager->create('window1');
        $group->add('window1');

        // 自动恢复所有分组状态
        $this->stateManager->autoRestoreAll();

        // 验证窗口状态已恢复
        $window = $this->manager->get('window1');
        $options = $window->getOptions();
        expect($options['width'])->toBe(800);
        expect($options['height'])->toBe(600);
    }

    public function test_can_clear_all_states()
    {
        // 先保存一些状态
        $this->stateManager->autoSaveAll();
        
        // 清除所有状态
        $this->stateManager->clearAll();
        
        // 验证缓存已清除
        expect(Cache::get('native_window_groups'))->toBeNull();
    }

    public function test_can_configure_cache_settings()
    {
        $customKey = 'custom_window_states';
        $customExpire = 3600;

        $this->stateManager
            ->setCacheKey($customKey)
            ->setExpireTime($customExpire);

        // 保存一些状态
        $this->stateManager->autoSaveAll();

        // 验证使用了自定义的缓存键
        expect(Cache::get($customKey))->not()->toBeNull();
    }
}