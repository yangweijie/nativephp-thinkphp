<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static bool startRecognition(array $options = []) 开始语音识别
 * @method static bool stopRecognition() 停止语音识别
 * @method static bool isRecognizing() 检查语音识别是否正在进行
 * @method static array getRecognitionResult() 获取语音识别结果
 * @method static bool speak(string $text, array $options = []) 语音合成
 * @method static bool pause() 暂停语音合成
 * @method static bool resume() 恢复语音合成
 * @method static bool cancel() 取消语音合成
 * @method static bool isSpeaking() 检查语音合成是否正在进行
 * @method static array getVoices() 获取可用的语音
 * @method static bool textToAudio(string $text, string $outputPath, array $options = []) 将文本转换为音频文件
 * @method static string|null audioToText(string $audioPath, array $options = []) 将音频文件转换为文本
 * 
 * @see \Native\ThinkPHP\Speech
 */
class Speech extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.speech';
    }
}
