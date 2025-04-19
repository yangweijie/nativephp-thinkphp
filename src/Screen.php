<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Screen
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 当前录制状态
     *
     * @var bool
     */
    protected $isRecording = false;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 获取所有屏幕
     *
     * @return array
     */
    public function all()
    {
        $response = $this->client->get('screen/all-displays');
        return $response->json('displays') ?? [];
    }

    /**
     * 获取所有屏幕
     *
     * @return array
     */
    public function getAllDisplays()
    {
        return $this->all();
    }

    /**
     * 获取主屏幕
     *
     * @return array
     */
    public function primary()
    {
        $response = $this->client->get('screen/primary-display');
        return $response->json('display') ?? [];
    }

    /**
     * 获取主屏幕
     *
     * @return array
     */
    public function getPrimaryDisplay()
    {
        return $this->primary();
    }

    /**
     * 获取鼠标位置
     *
     * @return array
     */
    public function getCursorPosition()
    {
        $response = $this->client->get('screen/cursor-position');
        if ($response->json('success')) {
            return [
                'x' => $response->json('x'),
                'y' => $response->json('y'),
            ];
        }

        return [
            'x' => 0,
            'y' => 0,
        ];
    }

    /**
     * 捕获屏幕截图
     *
     * @param array $options 选项
     * @return string|null 图片路径
     */
    public function captureScreenshot(array $options = [])
    {
        // 默认选项
        $defaultOptions = [
            'path' => $this->app->getRuntimePath() . 'screenshots/' . date('YmdHis') . '.png',
            'format' => 'png',
            'quality' => 100,
        ];

        $options = array_merge($defaultOptions, $options);

        // 确保目录存在
        $dir = dirname($options['path']);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $response = $this->client->post('screen/capture-screenshot', [
            'options' => $options,
        ]);

        if ($response->json('success')) {
            return $response->json('path');
        }

        return null;
    }

    /**
     * 捕获窗口截图
     *
     * @param string|null $windowId 窗口ID，如果为 null 则捕获当前窗口
     * @param array $options 选项
     * @return string|null 图片路径
     */
    public function captureWindow($windowId = null, array $options = [])
    {
        // 默认选项
        $defaultOptions = [
            'path' => $this->app->getRuntimePath() . 'screenshots/' . date('YmdHis') . '.png',
            'format' => 'png',
            'quality' => 100,
        ];

        $options = array_merge($defaultOptions, $options);

        // 确保目录存在
        $dir = dirname($options['path']);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $response = $this->client->post('screen/capture-window', [
            'id' => $windowId,
            'options' => $options,
        ]);

        if ($response->json('success')) {
            return $response->json('path');
        }

        return null;
    }

    /**
     * 开始屏幕录制
     *
     * @param array $options 选项
     * @return bool
     */
    public function startRecording(array $options = [])
    {
        // 默认选项
        $defaultOptions = [
            'path' => $this->app->getRuntimePath() . 'recordings/' . date('YmdHis') . '.webm',
            'audio' => false,
            'videoConstraints' => [
                'mandatory' => [
                    'chromeMediaSource' => 'desktop',
                ],
            ],
        ];

        $options = array_merge($defaultOptions, $options);

        // 确保目录存在
        $dir = dirname($options['path']);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $response = $this->client->post('screen/start-recording', [
            'options' => $options,
        ]);

        if ($response->json('success')) {
            $this->isRecording = true;
            return true;
        }

        return false;
    }

    /**
     * 检查是否正在录制
     *
     * @return bool
     */
    public function isRecording()
    {
        return $this->isRecording;
    }

    /**
     * 获取当前屏幕
     *
     * @return array
     */
    public function getCurrentDisplay()
    {
        $response = $this->client->get('screen/current-display');
        return $response->json('display') ?? [];
    }

    /**
     * 获取屏幕尺寸
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return array
     */
    public function getDisplaySize($displayId = null)
    {
        $response = $this->client->post('screen/display-size', [
            'displayId' => $displayId,
        ]);

        if ($response->json('success')) {
            return [
                'width' => $response->json('width'),
                'height' => $response->json('height'),
            ];
        }

        return [
            'width' => 0,
            'height' => 0,
        ];
    }

    /**
     * 获取屏幕工作区尺寸
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return array
     */
    public function getDisplayWorkAreaSize($displayId = null)
    {
        $response = $this->client->post('screen/display-work-area-size', [
            'displayId' => $displayId,
        ]);

        if ($response->json('success')) {
            return [
                'width' => $response->json('width'),
                'height' => $response->json('height'),
            ];
        }

        return [
            'width' => 0,
            'height' => 0,
        ];
    }

    /**
     * 获取屏幕缩放因子
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return float
     */
    public function getDisplayScaleFactor($displayId = null)
    {
        $response = $this->client->post('screen/display-scale-factor', [
            'displayId' => $displayId,
        ]);

        return (float) ($response->json('scaleFactor') ?? 1.0);
    }

    /**
     * 停止屏幕录制
     *
     * @return string|null 录制文件路径
     */
    public function stopRecording()
    {
        if (!$this->isRecording) {
            return null;
        }

        $response = $this->client->post('screen/stop-recording');

        if ($response->json('success')) {
            $this->isRecording = false;
            return $response->json('path');
        }

        return null;
    }

    /**
     * 暂停屏幕录制
     *
     * @return bool
     */
    public function pauseRecording()
    {
        if (!$this->isRecording) {
            return false;
        }

        $response = $this->client->post('screen/pause-recording');

        return (bool) $response->json('success');
    }

    /**
     * 继续屏幕录制
     *
     * @return bool
     */
    public function resumeRecording()
    {
        if (!$this->isRecording) {
            return false;
        }

        $response = $this->client->post('screen/resume-recording');

        return (bool) $response->json('success');
    }

    /**
     * 获取屏幕亮度
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return float
     */
    public function getBrightness($displayId = null)
    {
        $response = $this->client->post('screen/brightness', [
            'displayId' => $displayId,
        ]);

        return (float) ($response->json('brightness') ?? 1.0);
    }

    /**
     * 设置屏幕亮度
     *
     * @param float $brightness 亮度值（0.0 - 1.0）
     * @param int|null $displayId 屏幕ID，如果为 null 则设置当前屏幕
     * @return bool
     */
    public function setBrightness($brightness, $displayId = null)
    {
        $brightness = max(0.0, min(1.0, (float) $brightness));

        $response = $this->client->post('screen/set-brightness', [
            'displayId' => $displayId,
            'brightness' => $brightness,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取屏幕方向
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return string 方向（'landscape', 'portrait', 'landscape-primary', 'landscape-secondary', 'portrait-primary', 'portrait-secondary'）
     */
    public function getOrientation($displayId = null)
    {
        $response = $this->client->post('screen/orientation', [
            'displayId' => $displayId,
        ]);

        return $response->json('orientation') ?? 'landscape';
    }

    /**
     * 设置屏幕方向
     *
     * @param string $orientation 方向（'landscape', 'portrait', 'landscape-primary', 'landscape-secondary', 'portrait-primary', 'portrait-secondary'）
     * @param int|null $displayId 屏幕ID，如果为 null 则设置当前屏幕
     * @return bool
     */
    public function setOrientation($orientation, $displayId = null)
    {
        $response = $this->client->post('screen/set-orientation', [
            'displayId' => $displayId,
            'orientation' => $orientation,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取屏幕分辨率
     *
     * @param int|null $displayId 屏幕ID，如果为 null 则获取当前屏幕
     * @return array
     */
    public function getResolution($displayId = null)
    {
        return $this->getDisplaySize($displayId);
    }

    /**
     * 设置屏幕分辨率
     *
     * @param int $width 宽度
     * @param int $height 高度
     * @param int|null $displayId 屏幕ID，如果为 null 则设置当前屏幕
     * @return bool
     */
    public function setResolution($width, $height, $displayId = null)
    {
        $response = $this->client->post('screen/set-resolution', [
            'displayId' => $displayId,
            'width' => (int) $width,
            'height' => (int) $height,
        ]);

        return (bool) $response->json('success');
    }
}
