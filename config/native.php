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
    | 缓存配置
    |--------------------------------------------------------------------------
    |
    | 这里可以配置 NativePHP 的缓存驱动和相关参数。
    | 支持的驱动：memory, think, redis
    |
    */
    'cache' => [
        'driver' => env('NATIVEPHP_CACHE_DRIVER', 'memory'),
        'ttl' => env('NATIVEPHP_CACHE_TTL', 60),
        'prefix' => env('NATIVEPHP_CACHE_PREFIX', 'native:'),

        // Redis 配置
        'host' => env('NATIVEPHP_REDIS_HOST', '127.0.0.1'),
        'port' => env('NATIVEPHP_REDIS_PORT', 6379),
        'password' => env('NATIVEPHP_REDIS_PASSWORD', null),
        'database' => env('NATIVEPHP_REDIS_DATABASE', 0),
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
    | 全局快捷键配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义全局快捷键。
    | 可以使用以下格式：
    | 1. 'CommandOrControl+X': 'route.name' - 路由名称
    | 2. 'CommandOrControl+X': 'Controller@method' - 控制器方法
    | 3. 'CommandOrControl+X': function() { ... } - 回调函数
    | 4. 'CommandOrControl+X': ['action' => 'route.name', 'enabled' => true] - 配置数组
    |
    */
    'shortcuts' => [
        // 示例：打开开发者工具
        'CommandOrControl+Shift+I' => function() {
            \Native\ThinkPHP\Facades\Window::current()['webContents']['toggleDevTools']();
        },

        // 示例：刷新当前窗口
        'CommandOrControl+R' => function() {
            \Native\ThinkPHP\Facades\Window::reload();
        },

        // 示例：切换全屏
        'F11' => function() {
            $window = \Native\ThinkPHP\Facades\Window::current();
            $isFullscreen = $window['fullscreen'] ?? false;
            \Native\ThinkPHP\Facades\Window::setFullscreen(!$isFullscreen);
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | 系统托盘配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义系统托盘图标和菜单。
    |
    */
    'tray' => [
        // 是否自动显示托盘图标
        'auto_show' => true,

        // 托盘图标路径
        'icon' => public_path() . 'favicon.ico',

        // 托盘提示文本
        'tooltip' => env('APP_NAME', 'NativePHP'),

        // 点击动作：'show_window', 'hide_window', 'toggle_window', 'quit' 或自定义回调
        'click_action' => 'toggle_window',

        // 双击动作
        'double_click_action' => 'show_window',

        // 右键点击动作
        'right_click_action' => null, // 默认显示菜单

        // 当所有窗口关闭时保持应用运行
        'keep_running_when_all_windows_closed' => true,

        // 菜单项
        'menu_items' => [
            [
                'label' => '显示',
                'action' => 'show_window',
            ],
            [
                'label' => '隐藏',
                'action' => 'hide_window',
            ],
            [
                'type' => 'separator',
            ],
            [
                'label' => '退出',
                'action' => 'quit',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 剪贴板配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义剪贴板功能。
    |
    */
    'clipboard' => [
        // 是否记录剪贴板变化
        'log_changes' => false,

        // 剪贴板变化全局回调
        'on_change' => null,

        // 剪贴板监听器
        'listeners' => [
            // 示例：监听剪贴板变化
            // [
            //     'callback' => function($event) {
            //         // 处理剪贴板变化
            //     },
            // ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 上下文菜单配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义上下文菜单功能。
    |
    */
    'context_menu' => [
        // 菜单项
        'items' => [
            [
                'label' => '复制',
                'action' => 'copy',
            ],
            [
                'label' => '粘贴',
                'action' => 'paste',
            ],
            [
                'label' => '剪切',
                'action' => 'cut',
            ],
            [
                'type' => 'separator',
            ],
            [
                'label' => '全选',
                'action' => 'selectAll',
            ],
        ],

        // 菜单构建器，如果设置了该项，则会忽略 items 配置
        'builder' => null,

        // 点击处理器
        'click_handler' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 对话框配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义对话框功能。
    |
    */
    'dialog' => [
        // 是否显示启动对话框
        'show_startup_dialog' => false,

        // 启动对话框配置
        'startup_dialog' => [
            'type' => 'info',
            'message' => '应用已启动',
            'title' => env('APP_NAME', 'NativePHP'),
            'options' => [
                'buttons' => ['确定'],
            ],
        ],

        // 是否显示错误对话框
        'show_error_dialog' => true,

        // 错误对话框配置
        'error_dialog' => [
            'title' => '应用错误',
            'options' => [
                'buttons' => ['确定'],
            ],
        ],

        // 文件对话框默认路径
        'default_path' => null,

        // 文件对话框默认过滤器
        'default_filters' => [
            ['name' => '所有文件', 'extensions' => ['*']],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 文件系统配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义文件系统功能。
    |
    */
    'filesystem' => [
        // 是否记录文件系统操作
        'log_operations' => false,

        // 应用数据目录
        'app_data_path' => null,

        // 应用文档目录
        'app_documents_path' => null,

        // 应用缓存目录
        'app_cache_path' => null,

        // 应用日志目录
        'app_logs_path' => null,

        // 允许访问的目录
        'allowed_paths' => [
            // 示例：允许访问应用数据目录
            // '{app_data_path}',
        ],

        // 禁止访问的目录
        'denied_paths' => [
            // 示例：禁止访问系统目录
            // '/etc',
            // '/usr',
            // '/var',
            // 'C:\Windows',
            // 'C:\Program Files',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP 客户端配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义 HTTP 客户端功能。
    |
    */
    'http' => [
        // 请求超时时间（秒）
        'timeout' => 30,

        // 是否验证 SSL 证书
        'verify' => true,

        // 是否记录请求
        'log_requests' => false,

        // 是否记录响应
        'log_responses' => false,

        // 是否记录下载
        'log_downloads' => false,

        // 默认请求头
        'headers' => [
            'User-Agent' => 'NativePHP/' . env('NATIVEPHP_APP_VERSION', '1.0.0'),
            'Accept' => 'application/json',
        ],

        // 认证配置
        'auth' => [
            // 认证类型：'basic' 或 'bearer'
            'type' => null,

            // 基本认证用户名
            'username' => null,

            // 基本认证密码
            'password' => null,

            // Bearer 令牌
            'token' => null,
        ],

        // 代理配置
        'proxy' => [
            // 代理服务器地址
            'server' => null,

            // 代理服务器端口
            'port' => null,

            // 代理服务器用户名
            'username' => null,

            // 代理服务器密码
            'password' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 系统信息配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义系统信息功能。
    |
    */
    'system' => [
        // 是否记录系统信息
        'log_system_info' => false,

        // 是否记录系统操作
        'log_operations' => false,

        // 是否允许系统电源操作
        'allow_power_operations' => false,

        // 是否允许打开外部 URL
        'allow_open_external' => true,

        // 是否允许打开文件或目录
        'allow_open_path' => true,

        // 是否允许在文件管理器中显示文件
        'allow_show_item_in_folder' => true,

        // 是否允许移动文件到回收站
        'allow_move_item_to_trash' => true,

        // 是否允许播放系统提示音
        'allow_beep' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | 屏幕捕获配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义屏幕捕获功能。
    |
    */
    'screen' => [
        // 是否记录屏幕捕获操作
        'log_operations' => false,

        // 截图保存目录
        'screenshots_dir' => null,

        // 录制保存目录
        'recordings_dir' => null,

        // 截图默认格式
        'screenshot_format' => 'png',

        // 截图默认质量
        'screenshot_quality' => 100,

        // 录制默认格式
        'recording_format' => 'webm',

        // 录制默认帧率
        'recording_fps' => 30,

        // 录制默认音频
        'recording_audio' => false,

        // 录制默认音频设备
        'recording_audio_device' => null,

        // 录制默认视频设备
        'recording_video_device' => null,

        // 录制默认视频约束
        'recording_video_constraints' => [
            'mandatory' => [
                'chromeMediaSource' => 'desktop',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 自动更新配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义自动更新功能。
    |
    */
    'updater' => [
        // 是否启用自动更新
        'enabled' => true,

        // 是否在启动时检查更新
        'check_on_startup' => true,

        // 检查更新的间隔时间（秒）
        'check_interval' => 3600,

        // 更新服务器 URL
        'server_url' => env('NATIVEPHP_UPDATE_SERVER_URL'),

        // 是否自动下载更新
        'auto_download' => false,

        // 是否自动安装更新
        'auto_install' => false,

        // 是否显示更新通知
        'show_notification' => true,

        // 是否记录更新操作
        'log_operations' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 键盘配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义键盘功能。
    |
    */
    'keyboard' => [
        // 是否记录键盘操作
        'log_operations' => false,

        // 应用快捷键
        'shortcuts' => [
            // 示例：刷新当前窗口
            'CommandOrControl+R' => function() {
                \Native\ThinkPHP\Facades\Window::reload();
            },

            // 示例：切换全屏
            'F11' => function() {
                $window = \Native\ThinkPHP\Facades\Window::current();
                $isFullscreen = $window['fullscreen'] ?? false;
                \Native\ThinkPHP\Facades\Window::setFullscreen(!$isFullscreen);
            },

            // 示例：打开开发者工具
            'CommandOrControl+Shift+I' => function() {
                \Native\ThinkPHP\Facades\Window::current()['webContents']['toggleDevTools']();
            },
        ],

        // 全局快捷键
        'global_shortcuts' => [
            // 示例：显示/隐藏窗口
            'CommandOrControl+Shift+Space' => function() {
                $windows = \Native\ThinkPHP\Facades\Window::all();
                $allHidden = true;

                foreach ($windows as $window) {
                    if ($window['visible']) {
                        $allHidden = false;
                        break;
                    }
                }

                if ($allHidden || empty($windows)) {
                    // 如果所有窗口都隐藏或没有窗口，则显示窗口
                    if (empty($windows)) {
                        // 如果没有窗口，则打开主窗口
                        \Native\ThinkPHP\Facades\Window::open('/', [
                            'title' => config('native.name', 'NativePHP'),
                            'width' => 800,
                            'height' => 600,
                        ]);
                    } else {
                        // 显示所有窗口
                        foreach ($windows as $window) {
                            \Native\ThinkPHP\Facades\Window::show($window['id']);
                        }
                    }
                } else {
                    // 隐藏所有窗口
                    foreach ($windows as $window) {
                        \Native\ThinkPHP\Facades\Window::hide($window['id']);
                    }
                }
            },
        ],

        // 键盘布局
        'layout' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 打印机配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义打印机功能。
    |
    */
    'printer' => [
        // 是否记录打印操作
        'log_operations' => false,

        // 默认打印机
        'default' => env('NATIVEPHP_PRINTER_DEFAULT', null),

        // 临时目录
        'temp_path' => env('NATIVEPHP_PRINTER_TEMP_PATH', runtime_path() . 'temp/print'),

        // 默认打印选项
        'default_options' => [
            'silent' => false,
            'printBackground' => true,
            'color' => true,
            'landscape' => false,
            'scaleFactor' => 1.0,
            'pagesPerSheet' => 1,
            'collate' => true,
            'copies' => 1,
            'pageRanges' => [],
            'duplexMode' => 'simplex',
            'dpi' => 300,
        ],

        // PDF 选项
        'pdf_options' => [
            'marginsType' => 0,
            'pageSize' => 'A4',
            'printBackground' => true,
            'printSelectionOnly' => false,
            'landscape' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 桌面快捷方式配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义桌面快捷方式功能。
    |
    */
    'shortcut' => [
        // 是否记录快捷方式操作
        'log_operations' => false,

        // 是否自动创建桌面快捷方式
        'auto_create_desktop' => false,

        // 是否自动创建开始菜单快捷方式
        'auto_create_start_menu' => false,

        // 是否设置开机自启动
        'auto_start' => false,

        // 桌面快捷方式选项
        'desktop_options' => [
            'arguments' => '',
            'description' => env('APP_NAME', 'NativePHP'),
            'icon' => public_path() . 'favicon.ico',
            'iconIndex' => 0,
            'appUserModelId' => '',
        ],

        // 开始菜单快捷方式选项
        'start_menu_options' => [
            'arguments' => '',
            'description' => env('APP_NAME', 'NativePHP'),
            'icon' => public_path() . 'favicon.ico',
            'iconIndex' => 0,
            'appUserModelId' => '',
        ],

        // 开机自启动选项
        'auto_start_options' => [
            'openAtLogin' => true,
            'openAsHidden' => false,
            'path' => null,
            'args' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 语音识别和合成配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义语音识别和合成功能。
    |
    */
    'speech' => [
        // 是否记录语音操作
        'log_operations' => false,

        // 临时目录
        'temp_path' => env('NATIVEPHP_SPEECH_TEMP_PATH', runtime_path() . 'temp/speech'),

        // 语音识别默认选项
        'recognition_options' => [
            'lang' => 'zh-CN',
            'continuous' => true,
            'interimResults' => true,
            'maxAlternatives' => 1,
        ],

        // 语音合成默认选项
        'synthesis_options' => [
            'lang' => 'zh-CN',
            'volume' => 1.0,
            'rate' => 1.0,
            'pitch' => 1.0,
            'voice' => null,
        ],

        // 文本转音频默认选项
        'text_to_audio_options' => [
            'lang' => 'zh-CN',
            'format' => 'mp3',
            'voice' => null,
        ],

        // 音频转文本默认选项
        'audio_to_text_options' => [
            'lang' => 'zh-CN',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 通知配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义通知功能。
    |
    */
    'notification' => [
        // 是否记录通知操作
        'log_operations' => false,

        // 默认通知图标
        'icon' => public_path() . 'favicon.ico',

        // 默认通知声音
        'sound' => 'default',

        // 默认通知紧急程度
        'urgency' => 'normal',

        // 默认通知超时时间（毫秒）
        'timeout' => null,

        // 默认通知是否静默
        'silent' => false,

        // 默认通知是否可关闭
        'closable' => true,

        // 默认通知操作
        'actions' => [],

        // 默认通知回复占位符
        'reply_placeholder' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 电源管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义电源管理功能。
    |
    */
    'power' => [
        // 是否记录电源信息
        'log_power_info' => false,

        // 是否记录电源事件
        'log_power_events' => false,

        // 是否监听系统挂起事件
        'listen_suspend' => true,

        // 是否监听系统恢复事件
        'listen_resume' => true,

        // 是否监听系统锁定事件
        'listen_lock' => true,

        // 是否监听系统解锁事件
        'listen_unlock' => true,

        // 是否监听系统电源状态变化事件
        'listen_power_state_change' => true,

        // 是否监听电池电量变化事件
        'listen_battery_level_change' => true,

        // 是否监听电池充电状态变化事件
        'listen_battery_charging_change' => true,

        // 是否监听系统空闲状态变化事件
        'listen_idle_state_change' => false,

        // 系统空闲状态阈值（秒）
        'idle_threshold' => 60,

        // 系统挂起回调
        'on_suspend' => null,

        // 系统恢复回调
        'on_resume' => null,

        // 系统锁定回调
        'on_lock' => null,

        // 系统解锁回调
        'on_unlock' => null,

        // 系统电源状态变化回调
        'on_power_state_change' => null,

        // 电池电量变化回调
        'on_battery_level_change' => null,

        // 电池充电状态变化回调
        'on_battery_charging_change' => null,

        // 电池低电量阈值
        'low_battery_threshold' => 0.2,

        // 电池低电量通知
        'low_battery_notification' => true,

        // 电池充满通知
        'battery_full_notification' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | 应用程序菜单配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义应用程序菜单功能。
    |
    */
    'menu' => [
        // 是否记录菜单操作
        'log_operations' => false,

        // 是否自动创建默认菜单
        'auto_create_default_menu' => true,

        // 应用程序菜单
        'application_menu' => [
            // 示例：文件菜单
            // [
            //     'label' => '文件',
            //     'submenu' => [
            //         [
            //             'label' => '新建',
            //             'accelerator' => 'CommandOrControl+N',
            //             'action' => function() {
            //                 // 处理新建操作
            //             },
            //         ],
            //         [
            //             'label' => '打开',
            //             'accelerator' => 'CommandOrControl+O',
            //             'action' => function() {
            //                 // 处理打开操作
            //             },
            //         ],
            //         [
            //             'type' => 'separator',
            //         ],
            //         [
            //             'label' => '退出',
            //             'accelerator' => 'CommandOrControl+Q',
            //             'action' => function() {
            //                 // 处理退出操作
            //             },
            //         ],
            //     ],
            // ],
        ],

        // 应用程序菜单构建器
        'application_menu_builder' => null,

        // 菜单点击处理器
        'click_handler' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 窗口管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义窗口管理功能。
    |
    */
    'window' => [
        // 是否记录窗口事件
        'log_events' => false,

        // 是否自动创建主窗口
        'auto_create_main_window' => true,

        // 当所有窗口关闭时是否退出应用
        'quit_on_all_windows_closed' => false,

        // 主窗口配置
        'main_window' => [
            // 窗口 URL
            'url' => '/',

            // 窗口标题
            'title' => env('APP_NAME', 'NativePHP'),

            // 窗口宽度
            'width' => 800,

            // 窗口高度
            'height' => 600,

            // 窗口是否可调整大小
            'resizable' => true,

            // 窗口是否可最小化
            'minimizable' => true,

            // 窗口是否可最大化
            'maximizable' => true,

            // 窗口是否可关闭
            'closable' => true,

            // 窗口是否总是置顶
            'alwaysOnTop' => false,

            // 窗口是否全屏
            'fullscreen' => false,

            // 窗口 X 坐标
            'x' => null,

            // 窗口 Y 坐标
            'y' => null,

            // 窗口是否居中
            'center' => true,

            // 窗口是否有边框
            'frame' => true,

            // 窗口是否透明
            'transparent' => false,

            // 窗口背景色
            'backgroundColor' => null,

            // 窗口图标
            'icon' => null,
        ],

        // 窗口创建回调
        'on_create' => null,

        // 窗口关闭回调
        'on_close' => null,

        // 窗口聚焦回调
        'on_focus' => null,

        // 窗口失去焦点回调
        'on_blur' => null,

        // 窗口移动回调
        'on_move' => null,

        // 窗口调整大小回调
        'on_resize' => null,

        // 窗口最大化回调
        'on_maximize' => null,

        // 窗口最小化回调
        'on_minimize' => null,

        // 窗口恢复回调
        'on_restore' => null,

        // 窗口进入全屏回调
        'on_enter_fullscreen' => null,

        // 窗口退出全屏回调
        'on_leave_fullscreen' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 应用程序管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义应用程序管理功能。
    |
    */
    'app' => [
        // 是否记录应用信息
        'log_app_info' => false,

        // 是否记录应用事件
        'log_app_events' => false,

        // 初始应用徽章计数
        'initial_badge_count' => 0,

        // 初始应用开机自启动
        'initial_open_at_login' => false,

        // 应用退出回调
        'on_quit' => null,

        // 应用重启回调
        'on_restart' => null,

        // 应用聚焦回调
        'on_focus' => null,

        // 应用隐藏回调
        'on_hide' => null,

        // 应用徽章计数回调
        'on_badge_count' => null,

        // 应用添加最近文档回调
        'on_add_recent_document' => null,

        // 应用清除最近文档回调
        'on_clear_recent_documents' => null,

        // 应用开机自启动回调
        'on_open_at_login' => null,

        // 应用最小化回调
        'on_minimize' => null,

        // 应用最大化回调
        'on_maximize' => null,

        // 应用恢复回调
        'on_restore' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 资源管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义资源管理功能。
    |
    */
    'assets' => [
        // 资源目录
        'directory' => resource_path('native/assets'),

        // 是否缓存资源
        'cache' => true,

        // 缓存时间（秒）
        'cache_time' => 3600,

        // 允许的资源类型
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'text/plain',
            'text/html',
            'text/css',
            'text/javascript',
            'application/javascript',
            'application/json',
            'application/xml',
            'audio/mpeg',
            'audio/wav',
            'audio/ogg',
            'video/mp4',
            'video/webm',
            'font/ttf',
            'font/woff',
            'font/woff2',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 数据库管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义数据库管理功能。
    |
    */
    'database' => [
        // 数据库类型
        'type' => 'sqlite',

        // 数据库路径
        'database' => runtime_path() . 'database/native.sqlite',

        // 数据库前缀
        'prefix' => '',

        // 数据库编码
        'charset' => 'utf8',

        // 是否开启调试
        'debug' => false,

        // 是否开启外键约束
        'foreign_key_constraints' => true,

        // 是否自动备份
        'auto_backup' => true,

        // 备份路径
        'backup_path' => runtime_path() . 'database/backups',

        // 保留备份数量
        'backup_keep' => 5,

        // 是否自动优化
        'auto_optimize' => true,

        // 是否自动迁移
        'auto_migrate' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 安全性管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义安全性管理功能。
    |
    */
    'security' => [
        // 加密密钥
        'encryption_key' => env('NATIVEPHP_ENCRYPTION_KEY'),

        // 是否自动生成加密密钥
        'auto_generate_key' => true,

        // 存储路径
        'storage_path' => runtime_path() . 'security',

        // 是否自动清理过期数据
        'auto_cleanup' => true,

        // 过期时间（秒），0 表示不过期
        'expire_time' => 0,

        // 安全策略
        'policy' => [
            // 是否允许运行不安全的内容
            'allowRunningInsecureContent' => false,

            // 是否允许弹出窗口
            'allowPopups' => false,

            // 是否启用沙箱
            'sandbox' => true,

            // 是否启用 Web 安全
            'webSecurity' => true,

            // 是否启用上下文隔离
            'contextIsolation' => true,

            // 是否启用 Node 集成
            'nodeIntegration' => false,

            // 是否启用远程模块
            'enableRemoteModule' => false,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 广播系统配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义广播系统功能。
    |
    */
    'broadcasting' => [
        // 是否记录广播事件
        'log_broadcast_events' => false,

        // 默认频道
        'default_channels' => [
            'global',
            'windows',
            'components',
            'state',
        ],

        // 广播消息回调
        'on_broadcast_message' => null,

        // 频道创建回调
        'on_channel_created' => null,

        // 频道删除回调
        'on_channel_deleted' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 子进程管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义子进程管理功能。
    |
    */
    'child_process' => [
        // 是否记录子进程事件
        'log_process_events' => false,

        // 是否自动恢复持久化的子进程
        'auto_restore_persistent' => true,

        // 是否自动清理非持久化的子进程
        'auto_cleanup_non_persistent' => true,

        // 子进程超时时间（秒）
        'timeout' => 60,

        // 子进程最大内存限制（MB）
        'memory_limit' => 512,

        // 子进程最大数量
        'max_processes' => 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | 队列工作器配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义队列工作器功能。
    |
    */
    'queue_worker' => [
        // 是否记录队列工作器事件
        'log_worker_events' => false,

        // 是否自动启动队列工作器
        'auto_start' => false,

        // 是否自动停止队列工作器
        'auto_stop' => true,

        // 自动启动的队列工作器
        'auto_start_workers' => [
            // 示例：启动默认队列工作器
            // [
            //     'connection' => 'default',
            //     'queue' => 'default',
            //     'tries' => 3,
            //     'timeout' => 60,
            //     'sleep' => 3,
            //     'persistent' => true,
            // ],
        ],

        // 队列工作器启动回调
        'on_worker_started' => null,

        // 队列工作器停止回调
        'on_worker_stopped' => null,

        // 队列工作器重启回调
        'on_worker_restarted' => null,

        // 队列工作器失败回调
        'on_worker_failed' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 进度条配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义进度条功能。
    |
    */
    'progress_bar' => [
        // 是否记录进度条事件
        'log_events' => false,

        // 进度条样式
        'style' => 'default',

        // 进度条颜色
        'color' => '#007bff',

        // 进度条背景色
        'background_color' => '#f0f0f0',

        // 进度条高度
        'height' => 10,

        // 进度条圆角
        'border_radius' => 5,

        // 进度条开始回调
        'on_start' => null,

        // 进度条前进回调
        'on_advance' => null,

        // 进度条完成回调
        'on_finish' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Shell 命令执行配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义 Shell 命令执行功能。
    |
    */
    'shell' => [
        // 是否记录 Shell 事件
        'log_events' => false,

        // 是否允许执行危险命令
        'allow_dangerous_commands' => false,

        // 危险命令列表
        'dangerous_commands' => [
            'rm -rf',
            'format',
            'mkfs',
            'dd',
            'sudo',
            'su',
        ],

        // 打开文件回调
        'on_open_item' => null,

        // 在文件夹中显示文件回调
        'on_show_item_in_folder' => null,

        // 将文件移动到回收站回调
        'on_trash_item' => null,

        // 使用外部程序打开 URL 回调
        'on_open_external' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | 开发者工具配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义开发者工具功能。
    |
    */
    'developer' => [
        // 是否显示开发者工具
        'show_devtools' => false,

        // 是否允许检查元素
        'allow_inspect' => false,

        // 是否允许控制台
        'allow_console' => false,

        // 是否允许网络面板
        'allow_network' => false,

        // 是否允许源代码面板
        'allow_sources' => false,

        // 是否允许应用面板
        'allow_application' => false,

        // 是否允许内存面板
        'allow_memory' => false,

        // 是否允许性能面板
        'allow_performance' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | 网络管理配置
    |--------------------------------------------------------------------------
    |
    | 这些配置用于定义网络管理功能。
    |
    */
    'network' => [
        // 是否记录网络信息
        'log_network_info' => false,

        // 是否记录网络事件
        'log_network_events' => false,

        // 是否自动检查网络状态
        'auto_check_status' => true,

        // 检查间隔（秒）
        'check_interval' => 60,

        // 测试 URL
        'test_url' => 'https://www.baidu.com',

        // 超时时间（秒）
        'timeout' => 5,

        // 获取外部 IP 地址的 URL
        'ip_url' => 'https://api.ipify.org',

        // 网络状态变化回调
        'on_status_change' => null,

        // 网络上线回调
        'on_online' => null,

        // 网络离线回调
        'on_offline' => null,

        // 网络连接类型变化回调
        'on_connection_type_change' => null,
    ],
];
