<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 应用信息
    |--------------------------------------------------------------------------
    */
    'app' => [
        'name' => env('APP_NAME', 'ThinkPHP App'),
        'version' => env('APP_VERSION', '1.0.0'),
        'icon' => public_path('favicon.ico'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 窗口设置
    |--------------------------------------------------------------------------
    */
    'window' => [
        'default' => [
            'width' => 1200,
            'height' => 800,
            'center' => true,
            'title' => env('APP_NAME', 'ThinkPHP App'),
        ],

        // 预设窗口配置
        'presets' => [
            'dialog' => [
                'width' => 600,
                'height' => 400,
                'resizable' => false,
                'center' => true,
                'minimizable' => false,
                'maximizable' => false,
            ],
            'settings' => [
                'width' => 800,
                'height' => 600,
                'resizable' => true,
                'center' => true,
            ],
            'small' => [
                'width' => 400,
                'height' => 300,
                'resizable' => true,
                'center' => true,
            ],
            'fullscreen' => [
                'fullscreen' => true,
                'decorations' => false,
            ],
            'sidebar' => [
                'width' => 300,
                'height' => '100%',
                'resizable' => false,
                'decorations' => false,
                'alwaysOnTop' => true,
                'x' => 0,
                'y' => 0,
            ],
            'notification' => [
                'width' => 300,
                'height' => 100,
                'resizable' => false,
                'decorations' => false,
                'alwaysOnTop' => true,
                'skipTaskbar' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 窗口分组设置
    |--------------------------------------------------------------------------
    */
    'window_groups' => [
        'default' => [
            'state_file' => runtime_path('window_states.json'),
            'auto_restore' => true,
        ],
        
        // 预设分组配置
        'editor' => [
            'windows' => [
                'main' => [
                    'width' => 1200,
                    'height' => 800,
                ],
                'preview' => [
                    'width' => 600,
                    'height' => 800,
                ],
            ],
            'layout' => 'horizontal',
        ],
        'dashboard' => [
            'windows' => [
                'main' => [
                    'width' => 1000,
                    'height' => 600,
                ],
                'sidebar' => [
                    'width' => 300,
                    'height' => 600,
                ],
                'footer' => [
                    'width' => 1300,
                    'height' => 200,
                ],
            ],
            'layout' => 'custom',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 系统托盘设置
    |--------------------------------------------------------------------------
    */
    'tray' => [
        'icon' => public_path('favicon.ico'),
        'tooltip' => env('APP_NAME', 'ThinkPHP App'),
        'menu_items' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | 快捷键设置
    |--------------------------------------------------------------------------
    */
    'hotkeys' => [
        // 全局快捷键配置
        'global' => [
            'toggle-window' => 'CommandOrControl+Shift+T',
            'show-settings' => 'CommandOrControl+,',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 开发者工具
    |--------------------------------------------------------------------------
    */
    'dev' => [
        'enabled' => env('APP_DEBUG', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | 安全设置
    |--------------------------------------------------------------------------
    */
    'security' => [
        'csp' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'"],
            'style-src' => ["'self'"],
            'img-src' => ["'self'", 'data:'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 更新器设置
    |--------------------------------------------------------------------------
    */
    'updater' => [
        'enabled' => false,
        'url' => null,
        'interval' => 60 * 60 * 1000, // 1小时
    ],

    /*
    |--------------------------------------------------------------------------
    | 窗口动画设置
    |--------------------------------------------------------------------------
    */
    'transitions' => [
        'enabled' => true,
        'duration' => 300,
        'easing' => 'easeInOutCubic',
        'presets' => [
            'fast' => [
                'duration' => 150,
                'easing' => 'easeOutQuint'
            ],
            'slow' => [
                'duration' => 600,
                'easing' => 'easeInOutQuint'
            ],
            'bounce' => [
                'duration' => 500,
                'easing' => 'easeOutBounce'
            ],
            'elastic' => [
                'duration' => 600,
                'easing' => 'easeOutElastic'
            ]
        ]
    ],
];