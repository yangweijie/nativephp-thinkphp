# 屏幕功能

NativePHP for ThinkPHP 提供了屏幕功能，允许你的桌面应用程序获取屏幕信息、捕获屏幕截图和录制屏幕。本文档将介绍如何使用这些功能。

## 基本概念

屏幕功能允许你的应用程序获取屏幕信息（如屏幕尺寸、分辨率、缩放因子等）、捕获屏幕截图和录制屏幕。这些功能可以用于创建屏幕捕获应用、屏幕录制应用、屏幕共享应用等。

## 使用 Screen Facade

NativePHP for ThinkPHP 提供了 `Screen` Facade，用于获取屏幕信息、捕获屏幕截图和录制屏幕。

```php
use Native\ThinkPHP\Facades\Screen;
```

### 获取屏幕信息

```php
// 获取所有屏幕
$displays = Screen::getAllDisplays();

foreach ($displays as $display) {
    echo "屏幕 ID：{$display['id']}，宽度：{$display['bounds']['width']}，高度：{$display['bounds']['height']}";
}

// 获取主屏幕
$primaryDisplay = Screen::getPrimaryDisplay();

echo "主屏幕宽度：{$primaryDisplay['bounds']['width']}，高度：{$primaryDisplay['bounds']['height']}";

// 获取当前屏幕
$currentDisplay = Screen::getCurrentDisplay();

echo "当前屏幕宽度：{$currentDisplay['bounds']['width']}，高度：{$currentDisplay['bounds']['height']}";

// 获取鼠标位置
$cursorPosition = Screen::getCursorPosition();

echo "鼠标位置：X = {$cursorPosition['x']}，Y = {$cursorPosition['y']}";

// 获取屏幕尺寸
$size = Screen::getDisplaySize();

echo "屏幕宽度：{$size['width']}，高度：{$size['height']}";

// 获取屏幕工作区尺寸
$workAreaSize = Screen::getDisplayWorkAreaSize();

echo "工作区宽度：{$workAreaSize['width']}，高度：{$workAreaSize['height']}";

// 获取屏幕缩放因子
$scaleFactor = Screen::getDisplayScaleFactor();

echo "屏幕缩放因子：{$scaleFactor}";

// 获取屏幕亮度
$brightness = Screen::getBrightness();

echo "屏幕亮度：{$brightness}";

// 获取屏幕方向
$orientation = Screen::getOrientation();

echo "屏幕方向：{$orientation}";

// 获取屏幕分辨率
$resolution = Screen::getResolution();

echo "屏幕分辨率：{$resolution['width']} x {$resolution['height']}";
```

### 设置屏幕属性

```php
// 设置屏幕亮度
$success = Screen::setBrightness(0.8);

if ($success) {
    echo "屏幕亮度设置成功";
} else {
    echo "屏幕亮度设置失败";
}

// 设置屏幕方向
$success = Screen::setOrientation('landscape');

if ($success) {
    echo "屏幕方向设置成功";
} else {
    echo "屏幕方向设置失败";
}

// 设置屏幕分辨率
$success = Screen::setResolution(1920, 1080);

if ($success) {
    echo "屏幕分辨率设置成功";
} else {
    echo "屏幕分辨率设置失败";
}
```

### 捕获屏幕截图

```php
// 捕获屏幕截图
$screenshotPath = Screen::captureScreenshot();

if ($screenshotPath) {
    echo "屏幕截图已保存到：{$screenshotPath}";
} else {
    echo "屏幕截图捕获失败";
}

// 捕获屏幕截图（带选项）
$screenshotPath = Screen::captureScreenshot([
    'path' => '/path/to/screenshot.png',
    'format' => 'png',
    'quality' => 100,
]);

// 捕获窗口截图
$windowScreenshotPath = Screen::captureWindow();

if ($windowScreenshotPath) {
    echo "窗口截图已保存到：{$windowScreenshotPath}";
} else {
    echo "窗口截图捕获失败";
}

// 捕获指定窗口截图
$windowScreenshotPath = Screen::captureWindow('window-id');

// 捕获窗口截图（带选项）
$windowScreenshotPath = Screen::captureWindow('window-id', [
    'path' => '/path/to/window-screenshot.png',
    'format' => 'png',
    'quality' => 100,
]);
```

### 屏幕录制

```php
// 开始屏幕录制
$success = Screen::startRecording();

if ($success) {
    echo "屏幕录制已开始";
} else {
    echo "屏幕录制开始失败";
}

// 开始屏幕录制（带选项）
$success = Screen::startRecording([
    'path' => '/path/to/recording.webm',
    'audio' => true,
    'videoConstraints' => [
        'mandatory' => [
            'chromeMediaSource' => 'desktop',
        ],
    ],
]);

// 检查是否正在录制
$isRecording = Screen::isRecording();

if ($isRecording) {
    echo "正在录制屏幕";
} else {
    echo "未在录制屏幕";
}

// 暂停屏幕录制
$success = Screen::pauseRecording();

if ($success) {
    echo "屏幕录制已暂停";
} else {
    echo "屏幕录制暂停失败";
}

// 继续屏幕录制
$success = Screen::resumeRecording();

if ($success) {
    echo "屏幕录制已继续";
} else {
    echo "屏幕录制继续失败";
}

// 停止屏幕录制
$recordingPath = Screen::stopRecording();

if ($recordingPath) {
    echo "屏幕录制已停止，录制文件已保存到：{$recordingPath}";
} else {
    echo "屏幕录制停止失败";
}
```

## 屏幕信息格式

### 屏幕对象

```php
[
    'id' => 0, // 屏幕 ID
    'bounds' => [ // 屏幕边界
        'x' => 0, // 左上角 X 坐标
        'y' => 0, // 左上角 Y 坐标
        'width' => 1920, // 宽度
        'height' => 1080, // 高度
    ],
    'workArea' => [ // 工作区域
        'x' => 0,
        'y' => 0,
        'width' => 1920,
        'height' => 1040,
    ],
    'scaleFactor' => 1.0, // 缩放因子
    'rotation' => 0, // 旋转角度
    'internal' => true, // 是否内置显示器
    'primary' => true, // 是否主显示器
]
```

### 鼠标位置对象

```php
[
    'x' => 100, // X 坐标
    'y' => 200, // Y 坐标
]
```

### 屏幕尺寸对象

```php
[
    'width' => 1920, // 宽度
    'height' => 1080, // 高度
]
```

## 屏幕截图选项

捕获屏幕截图时，可以指定以下选项：

- `path`：截图保存路径，默认为 `runtime/screenshots/YmdHis.png`
- `format`：截图格式，可选值为 `png`、`jpg`、`jpeg`，默认为 `png`
- `quality`：截图质量，范围为 0-100，默认为 100

## 屏幕录制选项

开始屏幕录制时，可以指定以下选项：

- `path`：录制文件保存路径，默认为 `runtime/recordings/YmdHis.webm`
- `audio`：是否录制音频，默认为 `false`
- `videoConstraints`：视频约束，默认为 `['mandatory' => ['chromeMediaSource' => 'desktop']]`

## 实际应用场景

### 屏幕捕获应用

```php
use Native\ThinkPHP\Facades\Screen;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\GlobalShortcut;

class ScreenCaptureController
{
    /**
     * 初始化屏幕捕获应用
     *
     * @return \think\Response
     */
    public function initialize()
    {
        // 注册全局快捷键
        GlobalShortcut::register('CommandOrControl+Shift+S', function () {
            $this->captureScreen();
        });
        
        GlobalShortcut::register('CommandOrControl+Shift+W', function () {
            $this->captureWindow();
        });
        
        GlobalShortcut::register('CommandOrControl+Shift+R', function () {
            if (Screen::isRecording()) {
                $this->stopRecording();
            } else {
                $this->startRecording();
            }
        });
        
        return json(['success' => true, 'message' => '屏幕捕获应用已初始化']);
    }
    
    /**
     * 捕获屏幕
     *
     * @return \think\Response
     */
    public function captureScreen()
    {
        // 捕获屏幕截图
        $screenshotPath = Screen::captureScreenshot([
            'path' => $this->getScreenshotsPath() . date('YmdHis') . '.png',
        ]);
        
        if ($screenshotPath) {
            Notification::send('屏幕捕获', '屏幕截图已保存');
            
            return json(['success' => true, 'path' => $screenshotPath]);
        } else {
            Notification::send('屏幕捕获', '屏幕截图捕获失败', ['type' => 'error']);
            
            return json(['success' => false, 'message' => '屏幕截图捕获失败']);
        }
    }
    
    /**
     * 捕获窗口
     *
     * @return \think\Response
     */
    public function captureWindow()
    {
        // 捕获窗口截图
        $screenshotPath = Screen::captureWindow(null, [
            'path' => $this->getScreenshotsPath() . date('YmdHis') . '_window.png',
        ]);
        
        if ($screenshotPath) {
            Notification::send('屏幕捕获', '窗口截图已保存');
            
            return json(['success' => true, 'path' => $screenshotPath]);
        } else {
            Notification::send('屏幕捕获', '窗口截图捕获失败', ['type' => 'error']);
            
            return json(['success' => false, 'message' => '窗口截图捕获失败']);
        }
    }
    
    /**
     * 开始录制
     *
     * @return \think\Response
     */
    public function startRecording()
    {
        // 开始屏幕录制
        $success = Screen::startRecording([
            'path' => $this->getRecordingsPath() . date('YmdHis') . '.webm',
            'audio' => true,
        ]);
        
        if ($success) {
            Notification::send('屏幕捕获', '屏幕录制已开始');
            
            return json(['success' => true, 'message' => '屏幕录制已开始']);
        } else {
            Notification::send('屏幕捕获', '屏幕录制开始失败', ['type' => 'error']);
            
            return json(['success' => false, 'message' => '屏幕录制开始失败']);
        }
    }
    
    /**
     * 停止录制
     *
     * @return \think\Response
     */
    public function stopRecording()
    {
        // 停止屏幕录制
        $recordingPath = Screen::stopRecording();
        
        if ($recordingPath) {
            Notification::send('屏幕捕获', '屏幕录制已停止');
            
            return json(['success' => true, 'path' => $recordingPath]);
        } else {
            Notification::send('屏幕捕获', '屏幕录制停止失败', ['type' => 'error']);
            
            return json(['success' => false, 'message' => '屏幕录制停止失败']);
        }
    }
    
    /**
     * 获取截图保存路径
     *
     * @return string
     */
    protected function getScreenshotsPath()
    {
        $path = config('native.screen.screenshots_path');
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        return rtrim($path, '/') . '/';
    }
    
    /**
     * 获取录制保存路径
     *
     * @return string
     */
    protected function getRecordingsPath()
    {
        $path = config('native.screen.recordings_path');
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        return rtrim($path, '/') . '/';
    }
}
```

### 屏幕信息应用

```php
use Native\ThinkPHP\Facades\Screen;

class ScreenInfoController
{
    /**
     * 显示屏幕信息
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取所有屏幕
        $displays = Screen::getAllDisplays();
        
        // 获取主屏幕
        $primaryDisplay = Screen::getPrimaryDisplay();
        
        // 获取当前屏幕
        $currentDisplay = Screen::getCurrentDisplay();
        
        // 获取鼠标位置
        $cursorPosition = Screen::getCursorPosition();
        
        return view('screen/index', [
            'displays' => $displays,
            'primaryDisplay' => $primaryDisplay,
            'currentDisplay' => $currentDisplay,
            'cursorPosition' => $cursorPosition,
        ]);
    }
    
    /**
     * 显示屏幕详细信息
     *
     * @param int $displayId 屏幕 ID
     * @return \think\Response
     */
    public function detail($displayId)
    {
        // 获取屏幕尺寸
        $size = Screen::getDisplaySize($displayId);
        
        // 获取屏幕工作区尺寸
        $workAreaSize = Screen::getDisplayWorkAreaSize($displayId);
        
        // 获取屏幕缩放因子
        $scaleFactor = Screen::getDisplayScaleFactor($displayId);
        
        // 获取屏幕亮度
        $brightness = Screen::getBrightness($displayId);
        
        // 获取屏幕方向
        $orientation = Screen::getOrientation($displayId);
        
        // 获取屏幕分辨率
        $resolution = Screen::getResolution($displayId);
        
        return view('screen/detail', [
            'displayId' => $displayId,
            'size' => $size,
            'workAreaSize' => $workAreaSize,
            'scaleFactor' => $scaleFactor,
            'brightness' => $brightness,
            'orientation' => $orientation,
            'resolution' => $resolution,
        ]);
    }
    
    /**
     * 设置屏幕亮度
     *
     * @param int $displayId 屏幕 ID
     * @param float $brightness 亮度值（0.0 - 1.0）
     * @return \think\Response
     */
    public function setBrightness($displayId, $brightness)
    {
        $brightness = max(0.0, min(1.0, (float) $brightness));
        
        $success = Screen::setBrightness($brightness, $displayId);
        
        return json(['success' => $success]);
    }
    
    /**
     * 设置屏幕方向
     *
     * @param int $displayId 屏幕 ID
     * @param string $orientation 方向
     * @return \think\Response
     */
    public function setOrientation($displayId, $orientation)
    {
        $success = Screen::setOrientation($orientation, $displayId);
        
        return json(['success' => $success]);
    }
    
    /**
     * 设置屏幕分辨率
     *
     * @param int $displayId 屏幕 ID
     * @param int $width 宽度
     * @param int $height 高度
     * @return \think\Response
     */
    public function setResolution($displayId, $width, $height)
    {
        $success = Screen::setResolution($width, $height, $displayId);
        
        return json(['success' => $success]);
    }
}
```

## 最佳实践

1. **错误处理**：始终检查屏幕操作的返回值，并妥善处理错误情况。

2. **文件管理**：为屏幕截图和录制文件设置合适的保存路径，并定期清理不需要的文件。

3. **性能优化**：避免频繁捕获屏幕截图或录制屏幕，以免影响系统性能。

4. **用户体验**：提供友好的用户界面，显示屏幕捕获和录制的状态和结果。

5. **权限检查**：在捕获屏幕截图或录制屏幕之前，确保应用程序有足够的权限。

6. **多屏幕支持**：考虑多屏幕环境，允许用户选择要捕获或录制的屏幕。

7. **格式选择**：根据需求选择合适的截图格式和录制格式，平衡质量和文件大小。

## 故障排除

### 屏幕截图捕获失败

- 确保应用程序有足够的权限捕获屏幕
- 检查截图保存路径是否存在并可写
- 尝试使用不同的截图格式或质量设置
- 检查系统资源是否充足

### 屏幕录制失败

- 确保应用程序有足够的权限录制屏幕
- 检查录制保存路径是否存在并可写
- 尝试禁用音频录制
- 检查系统资源是否充足
- 确保没有其他应用程序正在使用屏幕录制功能

### 屏幕属性设置失败

- 确保应用程序有足够的权限设置屏幕属性
- 检查设置的值是否在有效范围内
- 确保指定的屏幕 ID 存在
- 检查系统是否支持设置特定的屏幕属性
