<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Screen;
use Native\ThinkPHP\Facades\Logger;

class ScreenPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'screen';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '屏幕捕获插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Screen plugin initialized');

        // 监听屏幕捕获事件
        $this->app->event->listen('native.screen.capture_screenshot', function ($event) {
            $this->handleCaptureScreenshot($event);
        });

        $this->app->event->listen('native.screen.capture_window', function ($event) {
            $this->handleCaptureWindow($event);
        });

        $this->app->event->listen('native.screen.start_recording', function ($event) {
            $this->handleStartRecording($event);
        });

        $this->app->event->listen('native.screen.stop_recording', function ($event) {
            $this->handleStopRecording($event);
        });

        $this->app->event->listen('native.screen.pause_recording', function ($event) {
            $this->handlePauseRecording($event);
        });

        $this->app->event->listen('native.screen.resume_recording', function ($event) {
            $this->handleResumeRecording($event);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Screen plugin started');

        // 创建屏幕捕获目录
        $this->createScreenCaptureDirectories();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 如果正在录制，停止录制
        if (Screen::isRecording()) {
            Screen::stopRecording();
        }

        // 记录插件卸载
        Logger::info('Screen plugin quit');
    }

    /**
     * 创建屏幕捕获目录
     *
     * @return void
     */
    protected function createScreenCaptureDirectories(): void
    {
        // 获取配置
        $config = config('native.screen', []);

        // 创建截图目录
        $screenshotsDir = $config['screenshots_dir'] ?? $this->app->getRuntimePath() . 'screenshots';
        if (!is_dir($screenshotsDir)) {
            mkdir($screenshotsDir, 0755, true);
        }

        // 创建录制目录
        $recordingsDir = $config['recordings_dir'] ?? $this->app->getRuntimePath() . 'recordings';
        if (!is_dir($recordingsDir)) {
            mkdir($recordingsDir, 0755, true);
        }

        // 保存目录到配置
        /** @phpstan-ignore-next-line */
        config(['native.screen.screenshots_dir' => $screenshotsDir]);
        /** @phpstan-ignore-next-line */
        config(['native.screen.recordings_dir' => $recordingsDir]);
    }

    /**
     * 处理捕获屏幕截图事件
     *
     * @param array $event
     * @return void
     */
    protected function handleCaptureScreenshot(array $event): void
    {
        // 记录捕获屏幕截图
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Capture screenshot', [
                'options' => $event['options'] ?? [],
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理捕获窗口截图事件
     *
     * @param array $event
     * @return void
     */
    protected function handleCaptureWindow(array $event): void
    {
        // 记录捕获窗口截图
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Capture window', [
                'window_id' => $event['window_id'] ?? null,
                'options' => $event['options'] ?? [],
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理开始屏幕录制事件
     *
     * @param array $event
     * @return void
     */
    protected function handleStartRecording(array $event): void
    {
        // 记录开始屏幕录制
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Start recording', [
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理停止屏幕录制事件
     *
     * @param array $event
     * @return void
     */
    protected function handleStopRecording(array $event): void
    {
        // 记录停止屏幕录制
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Stop recording', [
                'path' => $event['path'] ?? null,
            ]);
        }
    }

    /**
     * 处理暂停屏幕录制事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePauseRecording(array $event): void
    {
        // 记录暂停屏幕录制
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Pause recording');
        }
    }

    /**
     * 处理继续屏幕录制事件
     *
     * @param array $event
     * @return void
     */
    protected function handleResumeRecording(array $event): void
    {
        // 记录继续屏幕录制
        $config = config('native.screen', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Resume recording');
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 如果正在录制，停止录制
        if (Screen::isRecording()) {
            Screen::stopRecording();
        }

        // 记录插件卸载
        Logger::info('Screen plugin unloaded');
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}
