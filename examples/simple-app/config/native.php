<?php

return [
    'app' => [
        'name' => 'NativePHP-ThinkPHP 示例应用',
        'version' => '1.0.0',
        'icon' => public_path('icon.png'),
    ],
    'window' => [
        'default' => [
            'width' => 800,
            'height' => 600,
            'center' => true,
            'title' => 'NativePHP-ThinkPHP 示例应用',
            'vibrancy' => 'under-window',
            'transparent' => false,
            'frame' => true,
            'hasShadow' => true,
        ],
    ],
    'tray' => [
        'icon' => public_path('icon.png'),
        'tooltip' => 'NativePHP-ThinkPHP 示例应用',
    ],
    'updater' => [
        'enabled' => false,
        'url' => null,
        'interval' => 60 * 60 * 1000, // 1小时
    ],
];
