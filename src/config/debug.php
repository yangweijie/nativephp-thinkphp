<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 调试工具设置
    |--------------------------------------------------------------------------
    */
    'enabled' => env('APP_DEBUG', false),

    'devtools' => [
        // 开发者工具设置
        'enabled' => env('APP_DEBUG', false),
        'mode' => env('DEVTOOLS_MODE', 'detach'), // detach, right, bottom
        'hotkeys' => [
            'toggle' => 'CommandOrControl+Shift+I',
            'reload' => 'CommandOrControl+R',
        ],
    ],

    'inspector' => [
        // Node.js 调试器设置
        'enabled' => env('INSPECTOR_ENABLED', false),
        'port' => env('INSPECTOR_PORT', 9229),
        'host' => env('INSPECTOR_HOST', '127.0.0.1'),
        'break_on_start' => env('INSPECTOR_BREAK_ON_START', false),
    ],

    'watch' => [
        // 文件监视设置
        'enabled' => env('FILE_WATCHER_ENABLED', true),
        'paths' => [
            'app',
            'config',
            'resources/views',
            'routes',
        ],
        'ignored' => [
            'vendor/*',
            'storage/*',
            'node_modules/*',
        ],
        'delay' => env('FILE_WATCHER_DELAY', 1000),
    ],

    'logging' => [
        // 调试日志设置
        'level' => env('APP_DEBUG', false) ? 'debug' : 'info',
        'max_files' => 30,
        'channels' => [
            'electron' => [
                'driver' => 'daily',
                'path' => runtime_path('logs/electron.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
            'ipc' => [
                'driver' => 'daily',
                'path' => runtime_path('logs/ipc.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
            'window' => [
                'driver' => 'daily',
                'path' => runtime_path('logs/window.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
        ],
    ],

    'error_reporting' => [
        // 错误报告设置
        'capture_ajax' => true,
        'capture_console' => true,
        'capture_uncaught' => true,
        'capture_unhandled_rejections' => true,
        'source_maps' => true,
        'report_crashes' => true,
        'max_breadcrumbs' => 100,
    ],

    'memory_limit' => env('DEBUG_MEMORY_LIMIT', '256M'),
];