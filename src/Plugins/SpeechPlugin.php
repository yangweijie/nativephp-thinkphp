<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Logger;

class SpeechPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'speech';

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
    protected $description = '语音识别和合成插件';

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
        Logger::info('Speech plugin initialized');

        // 监听语音事件
        $this->app->event->listen('native.speech.recognition_start', function ($event) {
            $this->handleRecognitionStart($event);
        });

        $this->app->event->listen('native.speech.recognition_stop', function ($event) {
            $this->handleRecognitionStop($event);
        });

        $this->app->event->listen('native.speech.recognition_result', function ($event) {
            $this->handleRecognitionResult($event);
        });

        $this->app->event->listen('native.speech.speak', function ($event) {
            $this->handleSpeak($event);
        });

        $this->app->event->listen('native.speech.pause', function ($event) {
            $this->handlePause($event);
        });

        $this->app->event->listen('native.speech.resume', function ($event) {
            $this->handleResume($event);
        });

        $this->app->event->listen('native.speech.cancel', function ($event) {
            $this->handleCancel($event);
        });

        $this->app->event->listen('native.speech.text_to_audio', function ($event) {
            $this->handleTextToAudio($event);
        });

        $this->app->event->listen('native.speech.audio_to_text', function ($event) {
            $this->handleAudioToText($event);
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
        Logger::info('Speech plugin started');

        // 创建临时目录
        $this->createTempDirectory();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 如果正在进行语音识别，停止语音识别
        if (Speech::isRecognizing()) {
            Speech::stopRecognition();
        }

        // 如果正在进行语音合成，取消语音合成
        if (Speech::isSpeaking()) {
            Speech::cancel();
        }

        // 记录插件卸载
        Logger::info('Speech plugin quit');

        // 清理临时目录
        $this->cleanTempDirectory();
    }

    /**
     * 创建临时目录
     *
     * @return void
     */
    protected function createTempDirectory(): void
    {
        // 获取配置
        $config = config('native.speech', []);
        $tempPath = $config['temp_path'] ?? $this->app->getRuntimePath() . 'temp/speech';

        // 创建临时目录
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        // 保存临时目录到配置
        /** @phpstan-ignore-next-line */
        config(['native.speech.temp_path' => $tempPath]);
    }

    /**
     * 清理临时目录
     *
     * @return void
     */
    protected function cleanTempDirectory(): void
    {
        // 获取配置
        $config = config('native.speech', []);
        $tempPath = $config['temp_path'] ?? $this->app->getRuntimePath() . 'temp/speech';

        // 清理临时目录
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * 处理语音识别开始事件
     *
     * @param array $event
     * @return void
     */
    protected function handleRecognitionStart(array $event): void
    {
        // 记录语音识别开始
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech recognition started', [
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理语音识别停止事件
     *
     * @param array $event
     * @return void
     */
    protected function handleRecognitionStop(array $event): void
    {
        // 记录语音识别停止
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech recognition stopped');
        }
    }

    /**
     * 处理语音识别结果事件
     *
     * @param array $event
     * @return void
     */
    protected function handleRecognitionResult(array $event): void
    {
        // 记录语音识别结果
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech recognition result', [
                'text' => $event['text'] ?? '',
                'confidence' => $event['confidence'] ?? 0,
                'isFinal' => $event['isFinal'] ?? false,
            ]);
        }
    }

    /**
     * 处理语音合成事件
     *
     * @param array $event
     * @return void
     */
    protected function handleSpeak(array $event): void
    {
        // 记录语音合成
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech synthesis', [
                'text' => $event['text'] ?? '',
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理语音合成暂停事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePause(array $event): void
    {
        // 记录语音合成暂停
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech synthesis paused');
        }
    }

    /**
     * 处理语音合成恢复事件
     *
     * @param array $event
     * @return void
     */
    protected function handleResume(array $event): void
    {
        // 记录语音合成恢复
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech synthesis resumed');
        }
    }

    /**
     * 处理语音合成取消事件
     *
     * @param array $event
     * @return void
     */
    protected function handleCancel(array $event): void
    {
        // 记录语音合成取消
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Speech synthesis canceled');
        }
    }

    /**
     * 处理文本转音频事件
     *
     * @param array $event
     * @return void
     */
    protected function handleTextToAudio(array $event): void
    {
        // 记录文本转音频
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Text to audio', [
                'text' => $event['text'] ?? '',
                'output_path' => $event['output_path'] ?? '',
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 处理音频转文本事件
     *
     * @param array $event
     * @return void
     */
    protected function handleAudioToText(array $event): void
    {
        // 记录音频转文本
        $config = config('native.speech', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Audio to text', [
                'audio_path' => $event['audio_path'] ?? '',
                'options' => $event['options'] ?? [],
            ]);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 如果正在进行语音识别，停止语音识别
        if (Speech::isRecognizing()) {
            Speech::stopRecognition();
        }

        // 如果正在进行语音合成，取消语音合成
        if (Speech::isSpeaking()) {
            Speech::cancel();
        }

        // 记录插件卸载
        Logger::info('Speech plugin unloaded');

        // 清理临时目录
        $this->cleanTempDirectory();
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
