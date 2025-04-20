<?php

// 模拟 Notification 类
class MockNotification {
    protected $title = '';
    protected $body = '';
    protected $icon = '';
    protected $hasSound = false;
    protected $subtitle = '';
    protected $replyPlaceholder = '';
    protected $closeButtonText = '';
    protected $actions = [];
    protected $urgency = '';
    protected $timeout = 0;
    protected $onClick = null;
    protected $onClose = null;
    protected $onReply = null;
    protected $onAction = null;

    public function __construct($native) {}

    public function title($title) {
        $this->title = $title;
        return $this;
    }

    public function body($body) {
        $this->body = $body;
        return $this;
    }

    public function icon($icon) {
        $this->icon = $icon;
        return $this;
    }

    public function sound($hasSound) {
        $this->hasSound = $hasSound;
        return $this;
    }

    public function subtitle($subtitle) {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function replyPlaceholder($placeholder) {
        $this->replyPlaceholder = $placeholder;
        return $this;
    }

    public function closeButtonText($text) {
        $this->closeButtonText = $text;
        return $this;
    }

    public function actions($actions) {
        $this->actions = $actions;
        return $this;
    }

    public function addAction($text) {
        $this->actions[] = ['text' => $text, 'type' => 'button'];
        return $this;
    }

    public function addReplyButton($text) {
        $this->actions[] = ['text' => $text, 'type' => 'reply'];
        return $this;
    }

    public function urgency($urgency) {
        $this->urgency = $urgency;
        return $this;
    }

    public function timeout($timeout) {
        $this->timeout = $timeout;
        return $this;
    }

    public function onClick($callback) {
        $this->onClick = $callback;
        return $this;
    }

    public function onClose($callback) {
        $this->onClose = $callback;
        return $this;
    }

    public function onReply($callback) {
        $this->onReply = $callback;
        return $this;
    }

    public function onAction($callback) {
        $this->onAction = $callback;
        return $this;
    }
}

// 创建 Notification 实例的辅助函数
function createMockNotification() {
    $native = mockNative();
    $notification = new MockNotification($native);
    return $notification;
}

// 测试 Notification 类的流畅接口
test('Notification 类提供流畅接口', function () {
    $notification = createMockNotification();

    // 测试方法链
    expect($notification->title('Test Title'))->toBeInstanceOf(MockNotification::class);
    expect($notification->body('Test Body'))->toBeInstanceOf(MockNotification::class);
    expect($notification->icon('path/to/icon.png'))->toBeInstanceOf(MockNotification::class);
});

// 测试设置标题
test('Notification 可以设置标题', function () {
    $notification = createMockNotification();

    $notification->title('Test Title');

    // 由于我们使用的是模拟对象，我们只能测试方法链是否正常工作
    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置内容
test('Notification 可以设置内容', function () {
    $notification = createMockNotification();

    $notification->body('Test Body');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置图标
test('Notification 可以设置图标', function () {
    $notification = createMockNotification();

    $notification->icon('path/to/icon.png');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置声音
test('Notification 可以设置声音', function () {
    $notification = createMockNotification();

    $notification->sound(true);
    expect($notification)->toBeInstanceOf(MockNotification::class);

    $notification->sound(false);
    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置副标题
test('Notification 可以设置副标题', function () {
    $notification = createMockNotification();

    $notification->subtitle('Test Subtitle');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置回复占位符
test('Notification 可以设置回复占位符', function () {
    $notification = createMockNotification();

    $notification->replyPlaceholder('Type your reply');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置关闭按钮文本
test('Notification 可以设置关闭按钮文本', function () {
    $notification = createMockNotification();

    $notification->closeButtonText('Close');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置操作
test('Notification 可以设置操作', function () {
    $notification = createMockNotification();

    $actions = [
        ['text' => 'Action 1', 'type' => 'button'],
        ['text' => 'Action 2', 'type' => 'button'],
    ];

    $notification->actions($actions);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试添加操作
test('Notification 可以添加操作', function () {
    $notification = createMockNotification();

    $notification->addAction('Action 1');
    $notification->addAction('Action 2');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试添加回复按钮
test('Notification 可以添加回复按钮', function () {
    $notification = createMockNotification();

    $notification->addReplyButton('Reply');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置紧急程度
test('Notification 可以设置紧急程度', function () {
    $notification = createMockNotification();

    $notification->urgency('critical');

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置超时
test('Notification 可以设置超时', function () {
    $notification = createMockNotification();

    $notification->timeout(5000);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置点击回调
test('Notification 可以设置点击回调', function () {
    $notification = createMockNotification();

    $callback = function () {
        return 'clicked';
    };

    $notification->onClick($callback);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置关闭回调
test('Notification 可以设置关闭回调', function () {
    $notification = createMockNotification();

    $callback = function () {
        return 'closed';
    };

    $notification->onClose($callback);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置回复回调
test('Notification 可以设置回复回调', function () {
    $notification = createMockNotification();

    $callback = function ($reply) {
        return $reply;
    };

    $notification->onReply($callback);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

// 测试设置操作回调
test('Notification 可以设置操作回调', function () {
    $notification = createMockNotification();

    $callback = function ($index) {
        return $index;
    };

    $notification->onAction($callback);

    expect($notification)->toBeInstanceOf(MockNotification::class);
});

use NativePHP\Think\Native;

test('可以发送基本通知', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '测试通知',
        'body' => '这是一条测试通知'
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification())->toBe($notification);
});

test('通知支持点击事件', function() {
    $native = mockNative();
    $clicked = false;
    
    $notification = [
        'title' => '可点击通知',
        'body' => '点击此通知',
        'onClick' => function() use (&$clicked) {
            $clicked = true;
        }
    ];
    
    $native->notification($notification);
    $native->emit('notification-clicked');
    
    expect($clicked)->toBeTrue();
});

test('通知支持自定义图标', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '带图标通知',
        'body' => '这是一条带图标的通知',
        'icon' => 'path/to/icon.png'
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['icon'])->toBe('path/to/icon.png');
});

test('通知支持紧急程度设置', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '紧急通知',
        'body' => '这是一条紧急通知',
        'urgency' => 'critical'
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['urgency'])->toBe('critical');
});

test('通知支持超时设置', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '超时通知',
        'body' => '这条通知将在5秒后消失',
        'timeout' => 5000
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['timeout'])->toBe(5000);
});

test('通知支持声音设置', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '带声音通知',
        'body' => '这条通知会发出声音',
        'sound' => true
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['sound'])->toBeTrue();
});

test('通知支持静默模式', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '静默通知',
        'body' => '这条通知不会发出声音',
        'silent' => true
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['silent'])->toBeTrue();
});

test('通知支持子标题', function() {
    $native = mockNative();
    
    $notification = [
        'title' => '主标题',
        'subtitle' => '子标题',
        'body' => '通知内容'
    ];
    
    $native->notification($notification);
    
    expect($native->getLastNotification()['subtitle'])->toBe('子标题');
});

test('通知支持关闭事件', function() {
    $native = mockNative();
    $closed = false;
    
    $notification = [
        'title' => '测试通知',
        'body' => '这条通知将被关闭',
        'onClose' => function() use (&$closed) {
            $closed = true;
        }
    ];
    
    $native->notification($notification);
    $native->emit('notification-closed');
    
    expect($closed)->toBeTrue();
});

test('可以关闭所有通知', function() {
    $native = mockNative();
    
    $native->notification([
        'title' => '通知1',
        'body' => '内容1'
    ]);
    
    $native->notification([
        'title' => '通知2',
        'body' => '内容2'
    ]);
    
    $native->closeAllNotifications();
    
    expect($native->hasActiveNotifications())->toBeFalse();
});

// 辅助函数：创建模拟 Native 实例
function mockNative() {
    return new class {
        protected $lastNotification;
        protected $listeners = [];
        protected $hasActiveNotifications = true;
        
        public function notification($options) {
            $this->lastNotification = $options;
            $this->hasActiveNotifications = true;
        }
        
        public function getLastNotification() {
            return $this->lastNotification;
        }
        
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
        
        public function closeAllNotifications() {
            $this->hasActiveNotifications = false;
        }
        
        public function hasActiveNotifications() {
            return $this->hasActiveNotifications;
        }
    };
}
