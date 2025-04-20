<?php

namespace NativePHP\Think\Tests\Pest;

use NativePHP\Think\Tests\TestCase;
use NativePHP\Think\Window;
use NativePHP\Think\WindowTransition;

class WindowTransitionTest extends TestCase
{
    protected $native;
    protected $window;
    protected $transition;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->native = mockNative();
        $this->window = new Window($this->native);
        $this->transition = new WindowTransition($this->window);
    }

    public function test_can_set_transition_options()
    {
        $this->transition
            ->duration(500)
            ->easing('easeOutBounce')
            ->setEnabled(true);

        $this->transition->moveTo(100, 100);

        // 验证发送到 IPC 的消息
        $lastMessage = $this->native->ipc()->getLastMessage();
        expect($lastMessage['event'])->toBe('window.transition');
        expect($lastMessage['data'])->toHaveKey('options');
        expect($lastMessage['data']['options'])->toMatchArray([
            'duration' => 500,
            'easing' => 'easeOutBounce',
            'enabled' => true
        ]);
    }

    public function test_can_move_window_with_transition()
    {
        $this->transition->moveTo(200, 300);

        $lastMessage = $this->native->ipc()->getLastMessage();
        expect($lastMessage['data'])->toHaveKey('from');
        expect($lastMessage['data'])->toHaveKey('to');
        expect($lastMessage['data']['to'])->toMatchArray([
            'x' => 200,
            'y' => 300
        ]);
    }

    public function test_can_resize_window_with_transition()
    {
        $this->transition->resizeTo(800, 600);

        $lastMessage = $this->native->ipc()->getLastMessage();
        expect($lastMessage['data'])->toHaveKey('from');
        expect($lastMessage['data'])->toHaveKey('to');
        expect($lastMessage['data']['to'])->toMatchArray([
            'width' => 800,
            'height' => 600
        ]);
    }

    public function test_can_apply_layout_with_transition()
    {
        $layout = [
            'x' => 100,
            'y' => 100,
            'width' => 500,
            'height' => 400
        ];

        $this->transition->layout($layout);

        $lastMessage = $this->native->ipc()->getLastMessage();
        expect($lastMessage['data'])->toHaveKey('from');
        expect($lastMessage['data'])->toHaveKey('to');
        expect($lastMessage['data']['to'])->toMatchArray($layout);
    }

    public function test_transition_is_disabled_when_animation_is_off()
    {
        $this->transition->setEnabled(false);

        $this->transition->moveTo(100, 100);

        // 验证窗口位置直接更新，没有通过 IPC 发送过渡动画消息
        expect($this->window->getOptions()['x'])->toBe(100);
        expect($this->window->getOptions()['y'])->toBe(100);
        expect($this->native->ipc()->getLastMessage())->toBeNull();
    }
}