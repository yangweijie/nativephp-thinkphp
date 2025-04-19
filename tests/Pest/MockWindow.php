<?php

namespace NativePHP\Think\Tests\Pest;

/**
 * 模拟 Window 类，用于测试
 */
class MockWindow
{
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
