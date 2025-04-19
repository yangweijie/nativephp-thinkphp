<?php

namespace NativePHP\Think\Tests\Pest;

/**
 * 模拟 Menu 类，用于测试
 */
class MockMenu
{
    protected $items = [];
    protected $config = [];
    
    public function __construct($native) {}
    
    public function add($label, $options = []) {
        $this->items[] = array_merge([
            'label' => $label,
        ], $options);
        return $this;
    }
    
    public function addSeparator() {
        $this->items[] = [
            'type' => 'separator',
        ];
        return $this;
    }
    
    public function addSubmenu($label, $callback, $options = []) {
        $submenu = [];
        $callback(new class($submenu) {
            protected $items = [];
            
            public function __construct(&$items) {
                $this->items = &$items;
            }
            
            public function add($label, $options = []) {
                $this->items[] = array_merge([
                    'label' => $label,
                ], $options);
                return $this;
            }
            
            public function addSeparator() {
                $this->items[] = [
                    'type' => 'separator',
                ];
                return $this;
            }
        });
        
        $this->items[] = array_merge([
            'label' => $label,
            'submenu' => $submenu,
        ], $options);
        
        return $this;
    }
    
    public function configure($config) {
        $this->config = array_merge($this->config, $config);
        return $this;
    }
    
    public function clear() {
        $this->items = [];
        return $this;
    }
    
    public function disable($label) {
        foreach ($this->items as &$item) {
            if (isset($item['label']) && $item['label'] === $label) {
                $item['enabled'] = false;
            }
        }
        return $this;
    }
    
    public function enable($label) {
        foreach ($this->items as &$item) {
            if (isset($item['label']) && $item['label'] === $label) {
                $item['enabled'] = true;
            }
        }
        return $this;
    }
    
    public function toggle($label) {
        foreach ($this->items as &$item) {
            if (isset($item['label']) && $item['label'] === $label) {
                $item['enabled'] = !($item['enabled'] ?? true);
            }
        }
        return $this;
    }
    
    public function getConfig() {
        return $this->config;
    }
    
    public function getItems() {
        return $this->items;
    }
}
