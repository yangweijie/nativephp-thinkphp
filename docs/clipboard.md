# 剪贴板功能

NativePHP for ThinkPHP 提供了剪贴板功能，允许你的桌面应用程序读取和写入系统剪贴板。本文档将介绍如何使用这些功能。

## 基本概念

剪贴板功能允许你的应用程序读取和写入系统剪贴板，支持文本、HTML、图片、RTF 和文件路径等多种格式。这些功能可以用于实现复制粘贴、数据交换等功能。

## 使用 Clipboard Facade

NativePHP for ThinkPHP 提供了 `Clipboard` Facade，用于读取和写入系统剪贴板。

```php
use Native\ThinkPHP\Facades\Clipboard;
```

### 文本操作

```php
// 读取剪贴板文本
$text = Clipboard::text();

echo "剪贴板文本：{$text}";

// 写入文本到剪贴板
Clipboard::setText('这是一段文本');

// 清空剪贴板
Clipboard::clear();
```

### 图片操作

```php
// 读取剪贴板图片
$image = Clipboard::image();

if ($image) {
    // 图片以 Data URL 格式返回
    echo "剪贴板包含图片";
    
    // 可以将图片保存到文件
    $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $image));
    file_put_contents('/path/to/image.png', $data);
} else {
    echo "剪贴板不包含图片";
}

// 写入图片到剪贴板
Clipboard::setImage('/path/to/image.png');
```

### HTML 操作

```php
// 读取剪贴板 HTML
$html = Clipboard::html();

echo "剪贴板 HTML：{$html}";

// 写入 HTML 到剪贴板
Clipboard::setHtml('<b>这是一段 HTML 文本</b>');
```

### RTF 操作

```php
// 读取剪贴板 RTF
$rtf = Clipboard::rtf();

echo "剪贴板 RTF：{$rtf}";

// 写入 RTF 到剪贴板
Clipboard::setRtf('{\\rtf1\\ansi\\ansicpg936\\cocoartf2580\\cocoasubrtf220{\\fonttbl\\f0\\fswiss\\fcharset0 Helvetica;}\\f0\\fs24 \\cf0 这是一段 RTF 文本}');
```

### 文件路径操作

```php
// 读取剪贴板文件路径
$files = Clipboard::files();

if (!empty($files)) {
    echo "剪贴板包含以下文件：";
    foreach ($files as $file) {
        echo "- {$file}";
    }
} else {
    echo "剪贴板不包含文件";
}

// 写入文件路径到剪贴板
Clipboard::setFiles(['/path/to/file1.txt', '/path/to/file2.txt']);
```

### 格式检查

```php
// 检查剪贴板是否包含指定格式的数据
$hasText = Clipboard::has('text/plain');
$hasHtml = Clipboard::has('text/html');
$hasImage = Clipboard::has('image/png');

echo "剪贴板包含文本：" . ($hasText ? '是' : '否');
echo "剪贴板包含 HTML：" . ($hasHtml ? '是' : '否');
echo "剪贴板包含图片：" . ($hasImage ? '是' : '否');

// 获取剪贴板中可用的格式
$formats = Clipboard::formats();

echo "剪贴板中可用的格式：";
foreach ($formats as $format) {
    echo "- {$format}";
}
```

### 自定义格式

```php
// 读取剪贴板自定义格式数据
$data = Clipboard::readFormat('application/json');

if ($data) {
    $json = json_decode($data, true);
    echo "剪贴板 JSON 数据：" . json_encode($json, JSON_PRETTY_PRINT);
} else {
    echo "剪贴板不包含 JSON 数据";
}

// 写入自定义格式数据到剪贴板
$jsonData = json_encode(['name' => '张三', 'age' => 30]);
Clipboard::writeFormat('application/json', $jsonData);
```

### 监听剪贴板变化

```php
// 监听剪贴板变化
$id = Clipboard::onChange(function ($event) {
    echo "剪贴板内容已变化";
    
    // 读取新的剪贴板内容
    $text = Clipboard::text();
    echo "新的剪贴板文本：{$text}";
});

// 移除剪贴板变化监听器
$success = Clipboard::offChange($id);

if ($success) {
    echo "监听器已移除";
} else {
    echo "监听器移除失败";
}
```

## 实际应用场景

### 剪贴板管理器

```php
use Native\ThinkPHP\Facades\Clipboard;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\GlobalShortcut;

class ClipboardManagerController
{
    /**
     * 初始化剪贴板管理器
     *
     * @return \think\Response
     */
    public function initialize()
    {
        // 注册全局快捷键
        GlobalShortcut::register('CommandOrControl+Shift+C', function () {
            $this->copyWithFormat();
        });
        
        GlobalShortcut::register('CommandOrControl+Shift+V', function () {
            $this->pasteWithFormat();
        });
        
        // 监听剪贴板变化
        Clipboard::onChange(function ($event) {
            $this->handleClipboardChange();
        });
        
        return json(['success' => true, 'message' => '剪贴板管理器已初始化']);
    }
    
    /**
     * 处理剪贴板变化
     *
     * @return void
     */
    protected function handleClipboardChange()
    {
        // 获取剪贴板中可用的格式
        $formats = Clipboard::formats();
        
        // 保存剪贴板历史
        $history = $this->getClipboardHistory();
        
        $item = [
            'timestamp' => time(),
            'formats' => $formats,
        ];
        
        if (in_array('text/plain', $formats)) {
            $item['text'] = Clipboard::text();
        }
        
        if (in_array('text/html', $formats)) {
            $item['html'] = Clipboard::html();
        }
        
        if (in_array('image/png', $formats)) {
            $item['hasImage'] = true;
        }
        
        $history[] = $item;
        
        // 限制历史记录数量
        if (count($history) > 20) {
            array_shift($history);
        }
        
        $this->saveClipboardHistory($history);
        
        // 发送通知
        Notification::send('剪贴板已更新', '新内容已添加到剪贴板历史');
    }
    
    /**
     * 获取剪贴板历史
     *
     * @return array
     */
    public function getClipboardHistory()
    {
        $history = cache('clipboard_history');
        
        if (!$history) {
            $history = [];
        }
        
        return $history;
    }
    
    /**
     * 保存剪贴板历史
     *
     * @param array $history
     * @return bool
     */
    protected function saveClipboardHistory($history)
    {
        return cache('clipboard_history', $history);
    }
    
    /**
     * 显示剪贴板历史
     *
     * @return \think\Response
     */
    public function history()
    {
        $history = $this->getClipboardHistory();
        
        return view('clipboard/history', [
            'history' => $history,
        ]);
    }
    
    /**
     * 复制带格式的内容
     *
     * @return \think\Response
     */
    public function copyWithFormat()
    {
        // 获取当前选中的文本
        $text = Clipboard::text();
        
        // 尝试解析为 JSON
        $isJson = false;
        if ($text) {
            try {
                $json = json_decode($text, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $isJson = true;
                    
                    // 格式化 JSON
                    $formattedJson = json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                    
                    // 写入格式化后的 JSON
                    Clipboard::setText($formattedJson);
                    
                    // 同时写入 HTML 格式
                    $htmlJson = '<pre>' . htmlspecialchars($formattedJson) . '</pre>';
                    Clipboard::setHtml($htmlJson);
                    
                    Notification::send('JSON 已格式化', '已复制格式化后的 JSON');
                }
            } catch (\Exception $e) {
                // 不是有效的 JSON
            }
        }
        
        if (!$isJson) {
            // 如果不是 JSON，尝试其他格式处理
            // ...
            
            Notification::send('已复制', '内容已复制到剪贴板');
        }
        
        return json(['success' => true]);
    }
    
    /**
     * 粘贴带格式的内容
     *
     * @return \think\Response
     */
    public function pasteWithFormat()
    {
        // 获取剪贴板中可用的格式
        $formats = Clipboard::formats();
        
        if (in_array('text/html', $formats)) {
            // 优先使用 HTML 格式
            $html = Clipboard::html();
            
            // 处理 HTML
            // ...
            
            Notification::send('已粘贴 HTML', 'HTML 内容已粘贴');
        } elseif (in_array('text/plain', $formats)) {
            // 使用纯文本格式
            $text = Clipboard::text();
            
            // 处理文本
            // ...
            
            Notification::send('已粘贴文本', '文本内容已粘贴');
        } elseif (in_array('image/png', $formats)) {
            // 使用图片格式
            $image = Clipboard::image();
            
            // 处理图片
            // ...
            
            Notification::send('已粘贴图片', '图片内容已粘贴');
        }
        
        return json(['success' => true]);
    }
    
    /**
     * 清空剪贴板历史
     *
     * @return \think\Response
     */
    public function clearHistory()
    {
        $this->saveClipboardHistory([]);
        
        Notification::send('剪贴板历史已清空', '所有历史记录已删除');
        
        return json(['success' => true]);
    }
}
```

### 代码片段管理器

```php
use Native\ThinkPHP\Facades\Clipboard;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;

class SnippetController
{
    /**
     * 保存代码片段
     *
     * @return \think\Response
     */
    public function save()
    {
        // 从剪贴板获取代码
        $code = Clipboard::text();
        
        if (empty($code)) {
            Notification::send('错误', '剪贴板为空，无法保存代码片段', ['type' => 'error']);
            
            return json(['success' => false, 'message' => '剪贴板为空']);
        }
        
        // 显示对话框获取代码片段名称和描述
        $name = Dialog::prompt('代码片段名称', '请输入代码片段名称：');
        
        if ($name === null) {
            return json(['success' => false, 'message' => '已取消']);
        }
        
        $description = Dialog::prompt('代码片段描述', '请输入代码片段描述：');
        
        // 保存代码片段
        $snippet = [
            'id' => uniqid(),
            'name' => $name,
            'description' => $description,
            'code' => $code,
            'language' => $this->detectLanguage($code),
            'created_at' => time(),
        ];
        
        $snippets = $this->getSnippets();
        $snippets[] = $snippet;
        
        $this->saveSnippets($snippets);
        
        Notification::send('代码片段已保存', "代码片段 '{$name}' 已保存");
        
        return json(['success' => true, 'snippet' => $snippet]);
    }
    
    /**
     * 复制代码片段到剪贴板
     *
     * @param string $id 代码片段 ID
     * @return \think\Response
     */
    public function copy($id)
    {
        $snippets = $this->getSnippets();
        
        foreach ($snippets as $snippet) {
            if ($snippet['id'] === $id) {
                // 复制代码到剪贴板
                Clipboard::setText($snippet['code']);
                
                // 如果是 HTML 或 XML，同时设置 HTML 格式
                if (in_array($snippet['language'], ['html', 'xml'])) {
                    Clipboard::setHtml($snippet['code']);
                }
                
                Notification::send('代码片段已复制', "代码片段 '{$snippet['name']}' 已复制到剪贴板");
                
                return json(['success' => true]);
            }
        }
        
        return json(['success' => false, 'message' => '代码片段不存在']);
    }
    
    /**
     * 获取所有代码片段
     *
     * @return array
     */
    public function getSnippets()
    {
        $snippets = cache('code_snippets');
        
        if (!$snippets) {
            $snippets = [];
        }
        
        return $snippets;
    }
    
    /**
     * 保存代码片段
     *
     * @param array $snippets
     * @return bool
     */
    protected function saveSnippets($snippets)
    {
        return cache('code_snippets', $snippets);
    }
    
    /**
     * 检测代码语言
     *
     * @param string $code
     * @return string
     */
    protected function detectLanguage($code)
    {
        // 简单的语言检测逻辑
        if (preg_match('/<\?php/i', $code)) {
            return 'php';
        } elseif (preg_match('/<html/i', $code)) {
            return 'html';
        } elseif (preg_match('/^(import|from) /m', $code)) {
            return 'python';
        } elseif (preg_match('/(function|var|let|const) /i', $code)) {
            return 'javascript';
        } elseif (preg_match('/^(public|private|protected) class /m', $code)) {
            return 'java';
        } else {
            return 'text';
        }
    }
}
```

## 最佳实践

1. **错误处理**：始终检查剪贴板操作的返回值，并妥善处理错误情况。

2. **格式检查**：在读取剪贴板内容之前，先检查剪贴板是否包含指定格式的数据。

3. **多格式支持**：在写入剪贴板时，考虑同时写入多种格式，以提高与其他应用程序的兼容性。

4. **性能优化**：避免频繁读取剪贴板，特别是在监听剪贴板变化时。

5. **用户体验**：提供友好的用户界面，显示剪贴板内容和操作结果。

6. **安全性**：注意处理剪贴板中的敏感信息，避免泄露用户隐私。

7. **兼容性**：考虑不同操作系统和应用程序之间的剪贴板格式差异。

## 故障排除

### 剪贴板读取失败

- 确保应用程序有足够的权限访问剪贴板
- 检查剪贴板是否包含指定格式的数据
- 尝试使用其他格式读取剪贴板内容

### 剪贴板写入失败

- 确保应用程序有足够的权限访问剪贴板
- 检查写入的数据格式是否正确
- 尝试使用其他格式写入剪贴板

### 剪贴板监听不工作

- 确保应用程序有足够的权限访问剪贴板
- 检查监听器是否正确注册
- 尝试重新注册监听器

### 剪贴板格式不兼容

- 尝试使用不同的格式写入剪贴板
- 考虑同时写入多种格式
- 检查目标应用程序支持的剪贴板格式
