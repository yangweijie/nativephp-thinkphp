<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 应用名称
    |--------------------------------------------------------------------------
    |
    | 这个值将用于应用程序的标题和窗口标题。
    |
    */
    'name' => env('APP_NAME', 'NativePHP'),

    /*
    |--------------------------------------------------------------------------
    | 应用ID
    |--------------------------------------------------------------------------
    |
    | 这个值将用于应用程序的唯一标识符。
    |
    */
    'app_id' => env('NATIVEPHP_APP_ID', 'com.nativephp.app'),

    /*
    |--------------------------------------------------------------------------
    | 应用版本
    |--------------------------------------------------------------------------
    |
    | 这个值将用于应用程序的版本号。
    |
    */
    'version' => env('NATIVEPHP_APP_VERSION', '1.0.0'),

    /*
    |--------------------------------------------------------------------------
    | 开发服务器配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于开发服务器。
    |
    */
    'dev_server' => [
        'port' => env('NATIVEPHP_DEV_SERVER_PORT', 8000),
        'hostname' => env('NATIVEPHP_DEV_SERVER_HOSTNAME', 'localhost'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 窗口配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的主窗口。
    |
    */
    'window' => [
        'width' => env('NATIVEPHP_WINDOW_WIDTH', 800),
        'height' => env('NATIVEPHP_WINDOW_HEIGHT', 600),
        'min_width' => env('NATIVEPHP_WINDOW_MIN_WIDTH', 400),
        'min_height' => env('NATIVEPHP_WINDOW_MIN_HEIGHT', 400),
        'max_width' => env('NATIVEPHP_WINDOW_MAX_WIDTH', null),
        'max_height' => env('NATIVEPHP_WINDOW_MAX_HEIGHT', null),
        'resizable' => env('NATIVEPHP_WINDOW_RESIZABLE', true),
        'fullscreen' => env('NATIVEPHP_WINDOW_FULLSCREEN', false),
        'title' => env('NATIVEPHP_WINDOW_TITLE', env('APP_NAME', 'NativePHP')),
    ],

    /*
    |--------------------------------------------------------------------------
    | 菜单配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的菜单。
    |
    */
    'menus' => [
        'app' => true,
        'edit' => true,
        'view' => true,
        'window' => true,
        'help' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | 热键配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的全局热键。
    |
    */
    'hotkeys' => [
        // 示例: 'CommandOrControl+Shift+G' => 'Native\\ThinkPHP\\Actions\\MyAction',
    ],

    /*
    |--------------------------------------------------------------------------
    | 自动更新配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的自动更新。
    |
    */
    'updater' => [
        'enabled' => env('NATIVEPHP_UPDATER_ENABLED', true),
        'check_on_startup' => env('NATIVEPHP_UPDATER_CHECK_ON_STARTUP', true),
        'check_interval' => env('NATIVEPHP_UPDATER_CHECK_INTERVAL', 3600),
        'server_url' => env('NATIVEPHP_UPDATER_SERVER_URL', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | 开发者工具
    |--------------------------------------------------------------------------
    |
    | 这些配置用于开发者工具。
    |
    */
    'developer' => [
        'show_devtools' => env('NATIVEPHP_DEVELOPER_SHOW_DEVTOOLS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | 屏幕捕获配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于屏幕捕获功能。
    |
    */
    'screen' => [
        'screenshots_path' => env('NATIVEPHP_SCREENSHOTS_PATH', runtime_path() . 'screenshots'),
        'recordings_path' => env('NATIVEPHP_RECORDINGS_PATH', runtime_path() . 'recordings'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP 请求配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于 HTTP 请求。
    |
    */
    'http' => [
        'timeout' => env('NATIVEPHP_HTTP_TIMEOUT', 30),
        'verify' => env('NATIVEPHP_HTTP_VERIFY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | 数据库配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的本地数据库。
    |
    */
    'database' => [
        'path' => env('NATIVEPHP_DATABASE_PATH', runtime_path() . 'database/native.sqlite'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 设置配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的设置。
    |
    */
    'settings' => [
        'path' => env('NATIVEPHP_SETTINGS_PATH', runtime_path() . 'settings/settings.json'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 进程配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的进程管理。
    |
    */
    'process' => [
        'timeout' => env('NATIVEPHP_PROCESS_TIMEOUT', 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | 打印配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的打印功能。
    |
    */
    'printer' => [
        'default' => env('NATIVEPHP_PRINTER_DEFAULT', null),
        'temp_path' => env('NATIVEPHP_PRINTER_TEMP_PATH', runtime_path() . 'temp/print'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 语音识别和合成配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的语音识别和合成功能。
    |
    */
    'speech' => [
        'recognition' => [
            'lang' => env('NATIVEPHP_SPEECH_RECOGNITION_LANG', 'zh-CN'),
            'continuous' => env('NATIVEPHP_SPEECH_RECOGNITION_CONTINUOUS', true),
            'interim_results' => env('NATIVEPHP_SPEECH_RECOGNITION_INTERIM_RESULTS', true),
            'max_alternatives' => env('NATIVEPHP_SPEECH_RECOGNITION_MAX_ALTERNATIVES', 1),
        ],
        'synthesis' => [
            'lang' => env('NATIVEPHP_SPEECH_SYNTHESIS_LANG', 'zh-CN'),
            'volume' => env('NATIVEPHP_SPEECH_SYNTHESIS_VOLUME', 1.0),
            'rate' => env('NATIVEPHP_SPEECH_SYNTHESIS_RATE', 1.0),
            'pitch' => env('NATIVEPHP_SPEECH_SYNTHESIS_PITCH', 1.0),
            'voice' => env('NATIVEPHP_SPEECH_SYNTHESIS_VOICE', null),
        ],
        'audio_path' => env('NATIVEPHP_SPEECH_AUDIO_PATH', runtime_path() . 'audio'),
    ],

    /*
    |--------------------------------------------------------------------------
    | 设备管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的设备管理功能。
    |
    */
    'device' => [
        'bluetooth' => [
            'scan_timeout' => env('NATIVEPHP_DEVICE_BLUETOOTH_SCAN_TIMEOUT', 10000),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 地理位置服务配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的地理位置服务功能。
    |
    */
    'geolocation' => [
        'enable_high_accuracy' => env('NATIVEPHP_GEOLOCATION_ENABLE_HIGH_ACCURACY', false),
        'timeout' => env('NATIVEPHP_GEOLOCATION_TIMEOUT', 5000),
        'maximum_age' => env('NATIVEPHP_GEOLOCATION_MAXIMUM_AGE', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | 推送通知服务配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于应用程序的推送通知服务功能。
    |
    */
    'push' => [
        'provider' => env('NATIVEPHP_PUSH_PROVIDER', 'firebase'),
        'firebase' => [
            'server_key' => env('NATIVEPHP_PUSH_FIREBASE_SERVER_KEY'),
        ],
        'apns' => [
            'certificate' => env('NATIVEPHP_PUSH_APNS_CERTIFICATE'),
            'passphrase' => env('NATIVEPHP_PUSH_APNS_PASSPHRASE'),
            'sandbox' => env('NATIVEPHP_PUSH_APNS_SANDBOX', false),
        ],
        'jpush' => [
            'app_key' => env('NATIVEPHP_PUSH_JPUSH_APP_KEY'),
            'master_secret' => env('NATIVEPHP_PUSH_JPUSH_MASTER_SECRET'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | API 配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于 NativePHP API。
    |
    */
    'api_url' => env('NATIVEPHP_API_URL', 'http://localhost:31199/api'),
    'secret' => env('NATIVEPHP_SECRET', ''),
];
