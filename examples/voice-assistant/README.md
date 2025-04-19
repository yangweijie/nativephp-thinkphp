# 语音助手示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的语音识别和合成功能创建一个语音助手应用。

## 功能

- 语音识别：将语音转换为文本
- 语音合成：将文本转换为语音
- 语音命令：通过语音控制应用
- 语音回答：通过语音回答问题
- 语音提醒：通过语音提醒用户

## 文件结构

- `app/controller/Index.php` - 主控制器
- `app/controller/Voice.php` - 语音控制器
- `app/service/AssistantService.php` - 语音助手服务
- `app/service/CommandService.php` - 命令处理服务
- `view/index/index.html` - 主页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use app\service\AssistantService;
use Native\ThinkPHP\Facades\Speech;
use Native\ThinkPHP\Facades\Notification;

class Voice extends BaseController
{
    protected $assistantService;
    
    public function __construct(AssistantService $assistantService)
    {
        $this->assistantService = $assistantService;
    }
    
    public function index()
    {
        return view('voice/index');
    }
    
    public function startRecognition()
    {
        $result = Speech::startRecognition([
            'lang' => 'zh-CN',
            'continuous' => true,
            'interimResults' => true,
        ]);
        
        return json(['success' => $result]);
    }
    
    public function stopRecognition()
    {
        $result = Speech::stopRecognition();
        
        return json(['success' => $result]);
    }
    
    public function getRecognitionResult()
    {
        $result = Speech::getRecognitionResult();
        
        return json($result);
    }
    
    public function speak()
    {
        $text = input('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        $result = Speech::speak($text, [
            'lang' => 'zh-CN',
            'volume' => 1.0,
            'rate' => 1.0,
            'pitch' => 1.0,
        ]);
        
        return json(['success' => $result]);
    }
    
    public function processCommand()
    {
        $text = input('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '命令不能为空']);
        }
        
        $response = $this->assistantService->processCommand($text);
        
        // 语音回答
        Speech::speak($response);
        
        return json(['success' => true, 'response' => $response]);
    }
    
    public function getVoices()
    {
        $voices = Speech::getVoices();
        
        return json(['voices' => $voices]);
    }
    
    public function setReminder()
    {
        $text = input('text');
        $time = input('time');
        
        if (empty($text) || empty($time)) {
            return json(['success' => false, 'message' => '提醒内容和时间不能为空']);
        }
        
        $result = $this->assistantService->setReminder($text, $time);
        
        return json(['success' => $result]);
    }
    
    public function textToAudio()
    {
        $text = input('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        $outputPath = runtime_path() . 'audio/' . md5($text) . '.mp3';
        
        $result = Speech::textToAudio($text, $outputPath);
        
        return json(['success' => $result, 'path' => $outputPath]);
    }
}
```

### 服务

```php
<?php

namespace app\service;

use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Speech;

class AssistantService
{
    protected $commandService;
    
    public function __construct(CommandService $commandService)
    {
        $this->commandService = $commandService;
    }
    
    public function processCommand($text)
    {
        // 检查是否是命令
        if ($this->isCommand($text)) {
            return $this->commandService->execute($text);
        }
        
        // 检查是否是问题
        if ($this->isQuestion($text)) {
            return $this->answerQuestion($text);
        }
        
        // 默认回复
        return '对不起，我不明白您的意思。';
    }
    
    protected function isCommand($text)
    {
        $commands = [
            '打开', '关闭', '启动', '停止', '播放', '暂停', '继续', '设置', '创建', '删除', '发送', '查询'
        ];
        
        foreach ($commands as $command) {
            if (strpos($text, $command) === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function isQuestion($text)
    {
        $questionWords = ['什么', '谁', '哪里', '哪个', '为什么', '怎么', '如何', '多少', '几', '是否', '能否', '可以'];
        
        foreach ($questionWords as $word) {
            if (strpos($text, $word) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    protected function answerQuestion($text)
    {
        // 简单的问答逻辑，实际应用中可以接入知识库或 AI 服务
        $qa = [
            '你是谁' => '我是基于 NativePHP 的语音助手。',
            '你能做什么' => '我可以回答问题、执行命令、设置提醒等。',
            '现在几点' => '现在是 ' . date('H:i') . '。',
            '今天是几号' => '今天是 ' . date('Y年m月d日') . '。',
            '今天星期几' => '今天是星期' . ['日', '一', '二', '三', '四', '五', '六'][date('w')] . '。',
        ];
        
        foreach ($qa as $question => $answer) {
            if (strpos($text, $question) !== false) {
                return $answer;
            }
        }
        
        return '对不起，我不知道答案。';
    }
    
    public function setReminder($text, $time)
    {
        // 解析时间
        $timestamp = strtotime($time);
        
        if ($timestamp === false) {
            return false;
        }
        
        // 计算延迟时间（秒）
        $delay = $timestamp - time();
        
        if ($delay <= 0) {
            return false;
        }
        
        // 设置定时器
        // 在实际应用中，应该使用更可靠的定时任务系统
        // 这里使用简单的 sleep 模拟
        go(function () use ($text, $delay) {
            sleep($delay);
            
            // 发送通知
            Notification::send('提醒', $text);
            
            // 语音提醒
            Speech::speak('提醒：' . $text);
        });
        
        return true;
    }
}
```

### 视图

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>语音助手</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <h1>语音助手</h1>
        
        <div class="voice-controls">
            <button id="startBtn" onclick="startRecognition()">开始语音识别</button>
            <button id="stopBtn" onclick="stopRecognition()" disabled>停止语音识别</button>
        </div>
        
        <div class="recognition-result">
            <h3>识别结果：</h3>
            <div id="result"></div>
        </div>
        
        <div class="text-input">
            <h3>文本输入：</h3>
            <textarea id="textInput" rows="3" placeholder="输入文本..."></textarea>
            <button onclick="speak()">语音播报</button>
            <button onclick="processCommand()">处理命令</button>
        </div>
        
        <div class="reminder">
            <h3>设置提醒：</h3>
            <input type="text" id="reminderText" placeholder="提醒内容...">
            <input type="datetime-local" id="reminderTime">
            <button onclick="setReminder()">设置提醒</button>
        </div>
        
        <div class="response">
            <h3>助手回复：</h3>
            <div id="response"></div>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```
