<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Screen;

class ScreenController
{
    /**
     * 获取所有屏幕
     *
     * @return Response
     */
    public function getAllDisplays()
    {
        // 获取所有屏幕
        $displays = Screen::getAllDisplays();
        
        return json([
            'displays' => $displays,
        ]);
    }
    
    /**
     * 获取主屏幕
     *
     * @return Response
     */
    public function getPrimaryDisplay()
    {
        // 获取主屏幕
        $display = Screen::getPrimaryDisplay();
        
        return json([
            'display' => $display,
        ]);
    }
    
    /**
     * 获取鼠标位置
     *
     * @return Response
     */
    public function getCursorPosition()
    {
        // 获取鼠标位置
        $position = Screen::getCursorPosition();
        
        return json([
            'success' => true,
            'x' => $position['x'],
            'y' => $position['y'],
        ]);
    }
    
    /**
     * 捕获屏幕截图
     *
     * @param Request $request
     * @return Response
     */
    public function captureScreenshot(Request $request)
    {
        $options = $request->param('options', []);
        
        // 捕获屏幕截图
        $path = Screen::captureScreenshot($options);
        
        return json([
            'success' => $path !== null,
            'path' => $path,
        ]);
    }
    
    /**
     * 捕获窗口截图
     *
     * @param Request $request
     * @return Response
     */
    public function captureWindow(Request $request)
    {
        $windowId = $request->param('id');
        $options = $request->param('options', []);
        
        // 捕获窗口截图
        $path = Screen::captureWindow($windowId, $options);
        
        return json([
            'success' => $path !== null,
            'path' => $path,
        ]);
    }
    
    /**
     * 开始屏幕录制
     *
     * @param Request $request
     * @return Response
     */
    public function startRecording(Request $request)
    {
        $options = $request->param('options', []);
        
        // 开始屏幕录制
        $success = Screen::startRecording($options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 停止屏幕录制
     *
     * @return Response
     */
    public function stopRecording()
    {
        // 停止屏幕录制
        $path = Screen::stopRecording();
        
        return json([
            'success' => $path !== null,
            'path' => $path,
        ]);
    }
    
    /**
     * 暂停屏幕录制
     *
     * @return Response
     */
    public function pauseRecording()
    {
        // 暂停屏幕录制
        $success = Screen::pauseRecording();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 继续屏幕录制
     *
     * @return Response
     */
    public function resumeRecording()
    {
        // 继续屏幕录制
        $success = Screen::resumeRecording();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查是否正在录制
     *
     * @return Response
     */
    public function isRecording()
    {
        // 检查是否正在录制
        $isRecording = Screen::isRecording();
        
        return json([
            'is_recording' => $isRecording,
        ]);
    }
    
    /**
     * 获取当前屏幕
     *
     * @return Response
     */
    public function getCurrentDisplay()
    {
        // 获取当前屏幕
        $display = Screen::getCurrentDisplay();
        
        return json([
            'display' => $display,
        ]);
    }
    
    /**
     * 获取屏幕尺寸
     *
     * @param Request $request
     * @return Response
     */
    public function getDisplaySize(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕尺寸
        $size = Screen::getDisplaySize($displayId);
        
        return json([
            'success' => true,
            'width' => $size['width'],
            'height' => $size['height'],
        ]);
    }
    
    /**
     * 获取屏幕工作区尺寸
     *
     * @param Request $request
     * @return Response
     */
    public function getDisplayWorkAreaSize(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕工作区尺寸
        $size = Screen::getDisplayWorkAreaSize($displayId);
        
        return json([
            'success' => true,
            'width' => $size['width'],
            'height' => $size['height'],
        ]);
    }
    
    /**
     * 获取屏幕缩放因子
     *
     * @param Request $request
     * @return Response
     */
    public function getDisplayScaleFactor(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕缩放因子
        $scaleFactor = Screen::getDisplayScaleFactor($displayId);
        
        return json([
            'success' => true,
            'scaleFactor' => $scaleFactor,
        ]);
    }
    
    /**
     * 获取屏幕亮度
     *
     * @param Request $request
     * @return Response
     */
    public function getBrightness(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕亮度
        $brightness = Screen::getBrightness($displayId);
        
        return json([
            'brightness' => $brightness,
        ]);
    }
    
    /**
     * 设置屏幕亮度
     *
     * @param Request $request
     * @return Response
     */
    public function setBrightness(Request $request)
    {
        $brightness = $request->param('brightness');
        $displayId = $request->param('displayId');
        
        // 设置屏幕亮度
        $success = Screen::setBrightness($brightness, $displayId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 获取屏幕方向
     *
     * @param Request $request
     * @return Response
     */
    public function getOrientation(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕方向
        $orientation = Screen::getOrientation($displayId);
        
        return json([
            'orientation' => $orientation,
        ]);
    }
    
    /**
     * 设置屏幕方向
     *
     * @param Request $request
     * @return Response
     */
    public function setOrientation(Request $request)
    {
        $orientation = $request->param('orientation');
        $displayId = $request->param('displayId');
        
        // 设置屏幕方向
        $success = Screen::setOrientation($orientation, $displayId);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 获取屏幕分辨率
     *
     * @param Request $request
     * @return Response
     */
    public function getResolution(Request $request)
    {
        $displayId = $request->param('displayId');
        
        // 获取屏幕分辨率
        $resolution = Screen::getResolution($displayId);
        
        return json([
            'width' => $resolution['width'],
            'height' => $resolution['height'],
        ]);
    }
    
    /**
     * 设置屏幕分辨率
     *
     * @param Request $request
     * @return Response
     */
    public function setResolution(Request $request)
    {
        $width = $request->param('width');
        $height = $request->param('height');
        $displayId = $request->param('displayId');
        
        // 设置屏幕分辨率
        $success = Screen::setResolution($width, $height, $displayId);
        
        return json([
            'success' => $success,
        ]);
    }
}
