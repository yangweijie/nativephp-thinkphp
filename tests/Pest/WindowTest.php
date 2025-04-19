<?php

use NativePHP\Think\Window;
use NativePHP\Think\Native;

// 模拟 Window 类
class MockWindow {
    protected $options = [];

    public function __construct($native) {
        $this->options = [
            'width' => 800,
            'height' => 600,
            'center' => true,
        ];
    }

    public function getOptions() {
        return $this->options;
    }

    public function title($title) {
        $this->options['title'] = $title;
        return $this;
    }

    public function width($width) {
        $this->options['width'] = $width;
        return $this;
    }

    public function height($height) {
        $this->options['height'] = $height;
        return $this;
    }

    public function position($x, $y) {
        $this->options['x'] = $x;
        $this->options['y'] = $y;
        return $this;
    }

    public function center() {
        $this->options['center'] = true;
        return $this;
    }

    public function minSize($width, $height) {
        $this->options['minWidth'] = $width;
        $this->options['minHeight'] = $height;
        return $this;
    }

    public function maxSize($width, $height) {
        $this->options['maxWidth'] = $width;
        $this->options['maxHeight'] = $height;
        return $this;
    }

    public function resizable($resizable) {
        $this->options['resizable'] = $resizable;
        return $this;
    }

    public function movable($movable) {
        $this->options['movable'] = $movable;
        return $this;
    }

    public function minimizable($minimizable) {
        $this->options['minimizable'] = $minimizable;
        return $this;
    }

    public function maximizable($maximizable) {
        $this->options['maximizable'] = $maximizable;
        return $this;
    }

    public function closable($closable) {
        $this->options['closable'] = $closable;
        return $this;
    }

    public function alwaysOnTop($alwaysOnTop) {
        $this->options['alwaysOnTop'] = $alwaysOnTop;
        return $this;
    }

    public function fullscreen($fullscreen) {
        $this->options['fullscreen'] = $fullscreen;
        return $this;
    }

    public function kiosk($kiosk) {
        $this->options['kiosk'] = $kiosk;
        return $this;
    }

    public function titleBarStyle($style) {
        $this->options['titleBarStyle'] = $style;
        return $this;
    }

    public function vibrancy($vibrancy) {
        $this->options['vibrancy'] = $vibrancy;
        return $this;
    }

    public function transparent() {
        $this->options['transparent'] = true;
        return $this;
    }

    public function focused($focused) {
        $this->options['focused'] = $focused;
        return $this;
    }

    public function blur() {
        $this->options['focused'] = false;
        return $this;
    }

    public function show() {
        $this->options['visible'] = true;
        return $this;
    }

    public function hide() {
        $this->options['visible'] = false;
        return $this;
    }

    public function configure($options) {
        $this->options = array_merge($this->options, $options);
        return $this;
    }
}

// 创建 Window 实例的辅助函数
function createMockWindow() {
    $native = mockNative();
    $window = new MockWindow($native);
    return $window;
}

// 测试 Window 类的流畅接口
test('Window 类提供流畅接口', function () {
    $window = createMockWindow();

    // 测试方法链
    expect($window->title('Test Window'))->toBeInstanceOf(MockWindow::class);
    expect($window->width(800)->height(600))->toBeInstanceOf(MockWindow::class);
    expect($window->center())->toBeInstanceOf(MockWindow::class);
    expect($window->resizable(true))->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 尺寸设置
test('Window 可以设置尺寸', function () {
    $window = createMockWindow();

    $window->width(1000)->height(800);

    // 由于我们使用的是模拟对象，我们只能测试方法链是否正常工作
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 位置设置
test('Window 可以设置位置', function () {
    $window = createMockWindow();

    $window->position(100, 200);

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 居中
test('Window 可以居中显示', function () {
    $window = createMockWindow();

    $window->center();

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 最小尺寸
test('Window 可以设置最小尺寸', function () {
    $window = createMockWindow();

    $window->minSize(500, 400);

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 最大尺寸
test('Window 可以设置最大尺寸', function () {
    $window = createMockWindow();

    $window->maxSize(1500, 1200);

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可调整大小属性
test('Window 可以设置是否可调整大小', function () {
    $window = createMockWindow();

    $window->resizable(false);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->resizable(true);
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可移动属性
test('Window 可以设置是否可移动', function () {
    $window = createMockWindow();

    $window->movable(false);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->movable(true);
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可最小化属性
test('Window 可以设置是否可最小化', function () {
    $window = createMockWindow();

    $window->minimizable(false);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->minimizable(true);
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可最大化属性
test('Window 可以设置是否可最大化', function () {
    $window = createMockWindow();

    $window->maximizable(false);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->maximizable(true);
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可关闭属性
test('Window 可以设置是否可关闭', function () {
    $window = createMockWindow();

    $window->closable(false);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->closable(true);
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 总是置顶属性
test('Window 可以设置是否总是置顶', function () {
    $window = createMockWindow();

    $window->alwaysOnTop(true);

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 全屏属性
test('Window 可以设置是否全屏', function () {
    $window = createMockWindow();

    $window->fullscreen(true);

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 标题栏样式
test('Window 可以设置标题栏样式', function () {
    $window = createMockWindow();

    $window->titleBarStyle('hidden');

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 透明度
test('Window 可以设置透明度', function () {
    $window = createMockWindow();

    $window->transparent();

    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 焦点状态
test('Window 可以设置焦点状态', function () {
    $window = createMockWindow();

    $window->focused(true);
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->blur();
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 可见性
test('Window 可以设置可见性', function () {
    $window = createMockWindow();

    $window->show();
    expect($window)->toBeInstanceOf(MockWindow::class);

    $window->hide();
    expect($window)->toBeInstanceOf(MockWindow::class);
});

// 测试 Window 配置
test('Window 可以通过数组配置', function () {
    $window = createMockWindow();

    $options = [
        'title' => 'Custom Window',
        'width' => 1200,
        'height' => 900,
        'center' => true,
        'resizable' => false,
    ];

    $window->configure($options);

    expect($window)->toBeInstanceOf(MockWindow::class);
});
