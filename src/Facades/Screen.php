<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static array all() 获取所有屏幕
 * @method static array getAllDisplays() 获取所有屏幕
 * @method static array primary() 获取主屏幕
 * @method static array getPrimaryDisplay() 获取主屏幕
 * @method static array getCursorPosition() 获取鼠标位置
 * @method static string|null captureScreenshot(array $options = []) 捕获屏幕截图
 * @method static string|null captureWindow(string|null $windowId = null, array $options = []) 捕获窗口截图
 * @method static bool startRecording(array $options = []) 开始屏幕录制
 * @method static string|null stopRecording() 停止屏幕录制
 * @method static bool pauseRecording() 暂停屏幕录制
 * @method static bool resumeRecording() 继续屏幕录制
 * @method static bool isRecording() 检查是否正在录制
 * @method static array getCurrentDisplay() 获取当前屏幕
 * @method static array getDisplaySize(int|null $displayId = null) 获取屏幕尺寸
 * @method static array getDisplayWorkAreaSize(int|null $displayId = null) 获取屏幕工作区尺寸
 * @method static float getDisplayScaleFactor(int|null $displayId = null) 获取屏幕缩放因子
 * @method static float getBrightness(int|null $displayId = null) 获取屏幕亮度
 * @method static bool setBrightness(float $brightness, int|null $displayId = null) 设置屏幕亮度
 * @method static string getOrientation(int|null $displayId = null) 获取屏幕方向
 * @method static bool setOrientation(string $orientation, int|null $displayId = null) 设置屏幕方向
 * @method static array getResolution(int|null $displayId = null) 获取屏幕分辨率
 * @method static bool setResolution(int $width, int $height, int|null $displayId = null) 设置屏幕分辨率
 *
 * @see \Native\ThinkPHP\Screen
 */
class Screen extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.screen';
    }
}
