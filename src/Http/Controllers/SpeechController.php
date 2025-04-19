<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Speech;

class SpeechController
{
    /**
     * 开始语音识别
     *
     * @param Request $request
     * @return Response
     */
    public function startRecognition(Request $request)
    {
        $options = $request->param('options', []);
        
        // 开始语音识别
        $success = Speech::startRecognition($options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 停止语音识别
     *
     * @return Response
     */
    public function stopRecognition()
    {
        // 停止语音识别
        $success = Speech::stopRecognition();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查语音识别是否正在进行
     *
     * @return Response
     */
    public function isRecognizing()
    {
        // 检查语音识别是否正在进行
        $isRecognizing = Speech::isRecognizing();
        
        return json([
            'is_recognizing' => $isRecognizing,
        ]);
    }
    
    /**
     * 获取语音识别结果
     *
     * @return Response
     */
    public function getRecognitionResult()
    {
        // 获取语音识别结果
        $result = Speech::getRecognitionResult();
        
        return json([
            'result' => $result,
        ]);
    }
    
    /**
     * 语音合成
     *
     * @param Request $request
     * @return Response
     */
    public function speak(Request $request)
    {
        $text = $request->param('text');
        $options = $request->param('options', []);
        
        // 语音合成
        $success = Speech::speak($text, $options);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 暂停语音合成
     *
     * @return Response
     */
    public function pause()
    {
        // 暂停语音合成
        $success = Speech::pause();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 恢复语音合成
     *
     * @return Response
     */
    public function resume()
    {
        // 恢复语音合成
        $success = Speech::resume();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 取消语音合成
     *
     * @return Response
     */
    public function cancel()
    {
        // 取消语音合成
        $success = Speech::cancel();
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查语音合成是否正在进行
     *
     * @return Response
     */
    public function isSpeaking()
    {
        // 检查语音合成是否正在进行
        $isSpeaking = Speech::isSpeaking();
        
        return json([
            'is_speaking' => $isSpeaking,
        ]);
    }
    
    /**
     * 获取可用的语音
     *
     * @return Response
     */
    public function getVoices()
    {
        // 获取可用的语音
        $voices = Speech::getVoices();
        
        return json([
            'voices' => $voices,
        ]);
    }
    
    /**
     * 将文本转换为音频文件
     *
     * @param Request $request
     * @return Response
     */
    public function textToAudio(Request $request)
    {
        $text = $request->param('text');
        $outputPath = $request->param('output_path');
        $options = $request->param('options', []);
        
        // 将文本转换为音频文件
        $success = Speech::textToAudio($text, $outputPath, $options);
        
        return json([
            'success' => $success,
            'output_path' => $outputPath,
        ]);
    }
    
    /**
     * 将音频文件转换为文本
     *
     * @param Request $request
     * @return Response
     */
    public function audioToText(Request $request)
    {
        $audioPath = $request->param('audio_path');
        $options = $request->param('options', []);
        
        // 将音频文件转换为文本
        $text = Speech::audioToText($audioPath, $options);
        
        return json([
            'success' => $text !== null,
            'text' => $text,
        ]);
    }
}
