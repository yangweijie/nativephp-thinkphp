# 语音识别和合成

NativePHP for ThinkPHP 提供了语音识别和合成功能，允许你的桌面应用程序进行语音交互。本文档将介绍如何使用这些功能。

## 基本概念

语音识别和合成功能允许你的应用程序将语音转换为文本（语音识别）和将文本转换为语音（语音合成）。这些功能可以用于创建语音助手、语音控制应用、无障碍应用等。

## 配置

在使用语音识别和合成功能之前，你可以在 `config/native.php` 文件中配置语音识别和合成：

```php
return [
    // 其他配置...
    
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
        ],
    ],
];
```

## 使用 Speech Facade

NativePHP for ThinkPHP 提供了 `Speech` Facade，用于进行语音识别和合成。

### 语音识别

```php
use Native\ThinkPHP\Facades\Speech;

// 开始语音识别
$success = Speech::startRecognition([
    'lang' => 'zh-CN',
    'continuous' => true,
    'interimResults' => true,
    'maxAlternatives' => 1,
]);

if ($success) {
    // 语音识别开始成功
    echo "语音识别已开始，请说话...";
} else {
    // 语音识别开始失败
    echo "语音识别开始失败";
}

// 检查语音识别是否正在进行
$isRecognizing = Speech::isRecognizing();

if ($isRecognizing) {
    echo "正在进行语音识别";
} else {
    echo "未在进行语音识别";
}

// 获取语音识别结果
$result = Speech::getRecognitionResult();

if ($result) {
    $text = $result['text'];
    $confidence = $result['confidence'];
    $isFinal = $result['isFinal'];
    
    echo "识别结果：{$text}，置信度：{$confidence}，是否最终结果：" . ($isFinal ? '是' : '否');
} else {
    echo "暂无识别结果";
}

// 停止语音识别
$success = Speech::stopRecognition();

if ($success) {
    // 语音识别停止成功
    echo "语音识别已停止";
} else {
    // 语音识别停止失败
    echo "语音识别停止失败";
}
```

### 语音合成

```php
// 语音合成
$success = Speech::speak('你好，欢迎使用 NativePHP');

if ($success) {
    // 语音合成开始成功
    echo "语音合成已开始";
} else {
    // 语音合成开始失败
    echo "语音合成开始失败";
}

// 语音合成（带选项）
$success = Speech::speak('你好，欢迎使用 NativePHP', [
    'lang' => 'zh-CN',
    'volume' => 1.0,
    'rate' => 1.0,
    'pitch' => 1.0,
    'voice' => null,
]);

// 检查语音合成是否正在进行
$isSpeaking = Speech::isSpeaking();

if ($isSpeaking) {
    echo "正在进行语音合成";
} else {
    echo "未在进行语音合成";
}

// 暂停语音合成
$success = Speech::pause();

if ($success) {
    // 语音合成暂停成功
    echo "语音合成已暂停";
} else {
    // 语音合成暂停失败
    echo "语音合成暂停失败";
}

// 恢复语音合成
$success = Speech::resume();

if ($success) {
    // 语音合成恢复成功
    echo "语音合成已恢复";
} else {
    // 语音合成恢复失败
    echo "语音合成恢复失败";
}

// 取消语音合成
$success = Speech::cancel();

if ($success) {
    // 语音合成取消成功
    echo "语音合成已取消";
} else {
    // 语音合成取消失败
    echo "语音合成取消失败";
}

// 获取可用的语音
$voices = Speech::getVoices();

foreach ($voices as $voice) {
    echo "语音名称：{$voice['name']}，语言：{$voice['lang']}，是否默认：" . ($voice['default'] ? '是' : '否');
}
```

### 文本和音频转换

```php
// 将文本转换为音频文件
$success = Speech::textToAudio('你好，欢迎使用 NativePHP', '/path/to/audio.mp3', [
    'lang' => 'zh-CN',
    'format' => 'mp3',
    'voice' => null,
]);

if ($success) {
    // 转换成功
    echo "文本已转换为音频文件：/path/to/audio.mp3";
} else {
    // 转换失败
    echo "文本转换为音频文件失败";
}

// 将音频文件转换为文本
$text = Speech::audioToText('/path/to/audio.mp3', [
    'lang' => 'zh-CN',
]);

if ($text !== null) {
    // 转换成功
    echo "音频文件转换为文本：{$text}";
} else {
    // 转换失败
    echo "音频文件转换为文本失败";
}
```

## 语音识别选项

开始语音识别时，可以指定以下选项：

- `lang`：语言代码，如 `zh-CN`、`en-US` 等
- `continuous`：是否连续识别，默认为 `true`
- `interimResults`：是否返回中间结果，默认为 `true`
- `maxAlternatives`：最大备选结果数，默认为 `1`

## 语音合成选项

进行语音合成时，可以指定以下选项：

- `lang`：语言代码，如 `zh-CN`、`en-US` 等
- `volume`：音量，范围为 0.0 到 1.0，默认为 `1.0`
- `rate`：语速，范围为 0.1 到 10.0，默认为 `1.0`
- `pitch`：音调，范围为 0.0 到 2.0，默认为 `1.0`
- `voice`：语音名称，如果为 `null`，则使用默认语音

## 语音识别结果格式

```php
[
    'text' => '识别的文本',
    'confidence' => 0.9, // 置信度，范围为 0.0 到 1.0
    'isFinal' => true, // 是否为最终结果
]
```

## 语音对象格式

```php
[
    'name' => 'Chinese (China)', // 语音名称
    'lang' => 'zh-CN', // 语言代码
    'default' => true, // 是否为默认语音
]
```

## 实际应用场景

### 语音助手

```php
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Notification;

class VoiceAssistant
{
    /**
     * 启动语音助手
     */
    public function start()
    {
        // 开始语音识别
        Speech::startRecognition([
            'lang' => 'zh-CN',
            'continuous' => true,
            'interimResults' => true,
        ]);
        
        // 发送通知
        Notification::send('语音助手', '语音助手已启动，请说话...');
        
        // 在实际应用中，你需要定期检查语音识别结果
        // 这里简化为直接获取一次结果
        $result = Speech::getRecognitionResult();
        
        if ($result && $result['isFinal']) {
            // 处理语音命令
            $this->processCommand($result['text']);
        }
    }
    
    /**
     * 处理语音命令
     *
     * @param string $command
     */
    protected function processCommand($command)
    {
        // 简单的命令处理逻辑
        if (strpos($command, '你好') !== false) {
            Speech::speak('你好，有什么可以帮助你的吗？');
        } elseif (strpos($command, '时间') !== false) {
            $time = date('H:i');
            Speech::speak("现在的时间是 {$time}");
        } elseif (strpos($command, '天气') !== false) {
            Speech::speak('今天天气晴朗，气温 25 度');
        } elseif (strpos($command, '退出') !== false) {
            Speech::speak('再见，期待下次见到你');
            Speech::stopRecognition();
        } else {
            Speech::speak('抱歉，我不明白你的意思');
        }
    }
    
    /**
     * 停止语音助手
     */
    public function stop()
    {
        // 停止语音识别
        Speech::stopRecognition();
        
        // 发送通知
        Notification::send('语音助手', '语音助手已停止');
    }
}
```

### 语音控制应用

```php
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class VoiceControlApp
{
    /**
     * 启动语音控制
     */
    public function start()
    {
        // 开始语音识别
        Speech::startRecognition([
            'lang' => 'zh-CN',
            'continuous' => true,
            'interimResults' => false,
        ]);
        
        // 发送通知
        Notification::send('语音控制', '语音控制已启动，请说出命令...');
        
        // 在实际应用中，你需要定期检查语音识别结果
        // 这里简化为直接获取一次结果
        $result = Speech::getRecognitionResult();
        
        if ($result && $result['isFinal']) {
            // 处理语音命令
            $this->processCommand($result['text']);
        }
    }
    
    /**
     * 处理语音命令
     *
     * @param string $command
     */
    protected function processCommand($command)
    {
        // 窗口控制命令
        if (strpos($command, '打开窗口') !== false) {
            Window::open('https://example.com');
            Speech::speak('已打开窗口');
        } elseif (strpos($command, '关闭窗口') !== false) {
            Window::close();
            Speech::speak('已关闭窗口');
        } elseif (strpos($command, '最小化窗口') !== false) {
            Window::minimize();
            Speech::speak('已最小化窗口');
        } elseif (strpos($command, '最大化窗口') !== false) {
            Window::maximize();
            Speech::speak('已最大化窗口');
        } elseif (strpos($command, '恢复窗口') !== false) {
            Window::restore();
            Speech::speak('已恢复窗口');
        } elseif (strpos($command, '退出应用') !== false) {
            Speech::speak('正在退出应用');
            app()->make('native.app')->quit();
        } else {
            Speech::speak('抱歉，我不明白你的命令');
        }
    }
    
    /**
     * 停止语音控制
     */
    public function stop()
    {
        // 停止语音识别
        Speech::stopRecognition();
        
        // 发送通知
        Notification::send('语音控制', '语音控制已停止');
    }
}
```

## 最佳实践

1. **语言设置**：根据用户的语言设置选择合适的语言代码，以提高识别和合成的准确性。

2. **错误处理**：妥善处理语音识别和合成可能出现的错误，提供友好的错误信息和备选方案。

3. **用户反馈**：在语音识别和合成过程中，提供适当的用户界面反馈，如显示识别状态、合成进度等。

4. **性能优化**：避免频繁启动和停止语音识别，可以使用连续识别模式，并在不需要时暂停或停止识别。

5. **隐私保护**：告知用户语音数据的使用方式，并提供关闭语音功能的选项。

6. **备选输入方式**：提供文本输入等备选方式，以防语音识别不可用或不准确。

7. **测试**：在不同环境和设备上测试语音识别和合成功能，确保其在各种情况下都能正常工作。

## 故障排除

### 语音识别不工作

- 确保麦克风已连接并正常工作
- 检查麦克风权限
- 尝试调整麦克风音量
- 确保网络连接正常（如果使用在线语音识别服务）
- 尝试使用不同的语言设置

### 语音合成不工作

- 确保扬声器已连接并正常工作
- 检查扬声器音量
- 尝试使用不同的语音
- 确保网络连接正常（如果使用在线语音合成服务）
- 尝试使用不同的语言设置

### 识别准确率低

- 确保麦克风质量良好
- 减少环境噪音
- 尝试使用更准确的语音识别服务
- 调整语言设置
- 使用特定领域的词汇表或语法规则

### 合成语音不自然

- 尝试调整语速、音量和音调
- 使用更高质量的语音合成服务
- 选择更自然的语音
- 优化文本格式，如添加适当的标点符号和停顿
