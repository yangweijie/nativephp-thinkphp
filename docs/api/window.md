# Window 类 API 参考

`Window` 类提供了窗口管理功能，包括打开窗口、关闭窗口、设置窗口大小和位置等。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\Window;
```

## 方法

### `open($url, $options = [])`

打开新窗口。

**参数：**
- `$url` (`string`) - 窗口 URL
- `$options` (`array`) - 窗口选项
  - `title` (`string`) - 窗口标题
  - `width` (`int`) - 窗口宽度
  - `height` (`int`) - 窗口高度
  - `minWidth` (`int`) - 窗口最小宽度
  - `minHeight` (`int`) - 窗口最小高度
  - `maxWidth` (`int`) - 窗口最大宽度
  - `maxHeight` (`int`) - 窗口最大高度
  - `resizable` (`bool`) - 窗口是否可调整大小
  - `movable` (`bool`) - 窗口是否可移动
  - `minimizable` (`bool`) - 窗口是否可最小化
  - `maximizable` (`bool`) - 窗口是否可最大化
  - `closable` (`bool`) - 窗口是否可关闭
  - `focusable` (`bool`) - 窗口是否可聚焦
  - `alwaysOnTop` (`bool`) - 窗口是否总是置顶
  - `fullscreen` (`bool`) - 窗口是否全屏
  - `kiosk` (`bool`) - 窗口是否为 kiosk 模式
  - `center` (`bool`) - 窗口是否居中
  - `x` (`int`) - 窗口 x 坐标
  - `y` (`int`) - 窗口 y 坐标
  - `backgroundColor` (`string`) - 窗口背景颜色
  - `transparent` (`bool`) - 窗口是否透明
  - `frame` (`bool`) - 窗口是否有边框
  - `show` (`bool`) - 窗口是否显示
  - `paintWhenInitiallyHidden` (`bool`) - 窗口初始隐藏时是否绘制
  - `webPreferences` (`array`) - Web 首选项
    - `devTools` (`bool`) - 是否启用开发者工具
    - `nodeIntegration` (`bool`) - 是否启用 Node.js 集成
    - `contextIsolation` (`bool`) - 是否启用上下文隔离
    - `sandbox` (`bool`) - 是否启用沙箱

**返回值：** `int` - 窗口 ID

**示例：**

```php
$windowId = Window::open('/path/to/page', [
    'title' => '窗口标题',
    'width' => 800,
    'height' => 600,
    'resizable' => true,
]);
```

### `close()`

关闭当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功关闭窗口

**示例：**

```php
Window::close();
```

### `minimize()`

最小化当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功最小化窗口

**示例：**

```php
Window::minimize();
```

### `maximize()`

最大化当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功最大化窗口

**示例：**

```php
Window::maximize();
```

### `restore()`

恢复当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功恢复窗口

**示例：**

```php
Window::restore();
```

### `setSize($width, $height)`

设置当前窗口大小。

**参数：**
- `$width` (`int`) - 窗口宽度
- `$height` (`int`) - 窗口高度

**返回值：** `bool` - 是否成功设置窗口大小

**示例：**

```php
Window::setSize(800, 600);
```

### `getSize()`

获取当前窗口大小。

**参数：** 无

**返回值：** `array` - 窗口大小，包含 `width` 和 `height` 键

**示例：**

```php
$size = Window::getSize();
$width = $size['width'];
$height = $size['height'];
```

### `setPosition($x, $y)`

设置当前窗口位置。

**参数：**
- `$x` (`int`) - 窗口 x 坐标
- `$y` (`int`) - 窗口 y 坐标

**返回值：** `bool` - 是否成功设置窗口位置

**示例：**

```php
Window::setPosition(100, 100);
```

### `getPosition()`

获取当前窗口位置。

**参数：** 无

**返回值：** `array` - 窗口位置，包含 `x` 和 `y` 键

**示例：**

```php
$position = Window::getPosition();
$x = $position['x'];
$y = $position['y'];
```

### `setTitle($title)`

设置当前窗口标题。

**参数：**
- `$title` (`string`) - 窗口标题

**返回值：** `bool` - 是否成功设置窗口标题

**示例：**

```php
Window::setTitle('新标题');
```

### `getTitle()`

获取当前窗口标题。

**参数：** 无

**返回值：** `string` - 窗口标题

**示例：**

```php
$title = Window::getTitle();
```

### `setResizable($resizable)`

设置当前窗口是否可调整大小。

**参数：**
- `$resizable` (`bool`) - 是否可调整大小

**返回值：** `bool` - 是否成功设置窗口可调整大小

**示例：**

```php
Window::setResizable(true);
```

### `isResizable()`

检查当前窗口是否可调整大小。

**参数：** 无

**返回值：** `bool` - 窗口是否可调整大小

**示例：**

```php
$isResizable = Window::isResizable();
```

### `setMovable($movable)`

设置当前窗口是否可移动。

**参数：**
- `$movable` (`bool`) - 是否可移动

**返回值：** `bool` - 是否成功设置窗口可移动

**示例：**

```php
Window::setMovable(true);
```

### `isMovable()`

检查当前窗口是否可移动。

**参数：** 无

**返回值：** `bool` - 窗口是否可移动

**示例：**

```php
$isMovable = Window::isMovable();
```

### `setMinimizable($minimizable)`

设置当前窗口是否可最小化。

**参数：**
- `$minimizable` (`bool`) - 是否可最小化

**返回值：** `bool` - 是否成功设置窗口可最小化

**示例：**

```php
Window::setMinimizable(true);
```

### `isMinimizable()`

检查当前窗口是否可最小化。

**参数：** 无

**返回值：** `bool` - 窗口是否可最小化

**示例：**

```php
$isMinimizable = Window::isMinimizable();
```

### `setMaximizable($maximizable)`

设置当前窗口是否可最大化。

**参数：**
- `$maximizable` (`bool`) - 是否可最大化

**返回值：** `bool` - 是否成功设置窗口可最大化

**示例：**

```php
Window::setMaximizable(true);
```

### `isMaximizable()`

检查当前窗口是否可最大化。

**参数：** 无

**返回值：** `bool` - 窗口是否可最大化

**示例：**

```php
$isMaximizable = Window::isMaximizable();
```

### `setClosable($closable)`

设置当前窗口是否可关闭。

**参数：**
- `$closable` (`bool`) - 是否可关闭

**返回值：** `bool` - 是否成功设置窗口可关闭

**示例：**

```php
Window::setClosable(true);
```

### `isClosable()`

检查当前窗口是否可关闭。

**参数：** 无

**返回值：** `bool` - 窗口是否可关闭

**示例：**

```php
$isClosable = Window::isClosable();
```

### `setAlwaysOnTop($alwaysOnTop)`

设置当前窗口是否总是置顶。

**参数：**
- `$alwaysOnTop` (`bool`) - 是否总是置顶

**返回值：** `bool` - 是否成功设置窗口总是置顶

**示例：**

```php
Window::setAlwaysOnTop(true);
```

### `isAlwaysOnTop()`

检查当前窗口是否总是置顶。

**参数：** 无

**返回值：** `bool` - 窗口是否总是置顶

**示例：**

```php
$isAlwaysOnTop = Window::isAlwaysOnTop();
```

### `setFullScreen($fullScreen)`

设置当前窗口是否全屏。

**参数：**
- `$fullScreen` (`bool`) - 是否全屏

**返回值：** `bool` - 是否成功设置窗口全屏

**示例：**

```php
Window::setFullScreen(true);
```

### `isFullScreen()`

检查当前窗口是否全屏。

**参数：** 无

**返回值：** `bool` - 窗口是否全屏

**示例：**

```php
$isFullScreen = Window::isFullScreen();
```

### `setKiosk($kiosk)`

设置当前窗口是否为 kiosk 模式。

**参数：**
- `$kiosk` (`bool`) - 是否为 kiosk 模式

**返回值：** `bool` - 是否成功设置窗口为 kiosk 模式

**示例：**

```php
Window::setKiosk(true);
```

### `isKiosk()`

检查当前窗口是否为 kiosk 模式。

**参数：** 无

**返回值：** `bool` - 窗口是否为 kiosk 模式

**示例：**

```php
$isKiosk = Window::isKiosk();
```

### `setBackgroundColor($color)`

设置当前窗口背景颜色。

**参数：**
- `$color` (`string`) - 背景颜色

**返回值：** `bool` - 是否成功设置窗口背景颜色

**示例：**

```php
Window::setBackgroundColor('#ffffff');
```

### `setTransparent($transparent)`

设置当前窗口是否透明。

**参数：**
- `$transparent` (`bool`) - 是否透明

**返回值：** `bool` - 是否成功设置窗口透明

**示例：**

```php
Window::setTransparent(true);
```

### `isTransparent()`

检查当前窗口是否透明。

**参数：** 无

**返回值：** `bool` - 窗口是否透明

**示例：**

```php
$isTransparent = Window::isTransparent();
```

### `setVisible($visible)`

设置当前窗口是否可见。

**参数：**
- `$visible` (`bool`) - 是否可见

**返回值：** `bool` - 是否成功设置窗口可见

**示例：**

```php
Window::setVisible(true);
```

### `isVisible()`

检查当前窗口是否可见。

**参数：** 无

**返回值：** `bool` - 窗口是否可见

**示例：**

```php
$isVisible = Window::isVisible();
```

### `setFocusable($focusable)`

设置当前窗口是否可聚焦。

**参数：**
- `$focusable` (`bool`) - 是否可聚焦

**返回值：** `bool` - 是否成功设置窗口可聚焦

**示例：**

```php
Window::setFocusable(true);
```

### `isFocusable()`

检查当前窗口是否可聚焦。

**参数：** 无

**返回值：** `bool` - 窗口是否可聚焦

**示例：**

```php
$isFocusable = Window::isFocusable();
```

### `focus()`

聚焦当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功聚焦窗口

**示例：**

```php
Window::focus();
```

### `isFocused()`

检查当前窗口是否已聚焦。

**参数：** 无

**返回值：** `bool` - 窗口是否已聚焦

**示例：**

```php
$isFocused = Window::isFocused();
```

### `reload()`

重新加载当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功重新加载窗口

**示例：**

```php
Window::reload();
```

### `forceReload()`

强制重新加载当前窗口。

**参数：** 无

**返回值：** `bool` - 是否成功强制重新加载窗口

**示例：**

```php
Window::forceReload();
```

### `openDevTools()`

打开开发者工具。

**参数：** 无

**返回值：** `bool` - 是否成功打开开发者工具

**示例：**

```php
Window::openDevTools();
```

### `closeDevTools()`

关闭开发者工具。

**参数：** 无

**返回值：** `bool` - 是否成功关闭开发者工具

**示例：**

```php
Window::closeDevTools();
```

### `toggleDevTools()`

切换开发者工具。

**参数：** 无

**返回值：** `bool` - 是否成功切换开发者工具

**示例：**

```php
Window::toggleDevTools();
```

### `isDevToolsOpen()`

检查开发者工具是否已打开。

**参数：** 无

**返回值：** `bool` - 开发者工具是否已打开

**示例：**

```php
$isDevToolsOpen = Window::isDevToolsOpen();
```

### `on($event, $callback)`

监听窗口事件。

**参数：**
- `$event` (`string`) - 事件名称
- `$callback` (`callable`) - 回调函数

**返回值：** `void`

**示例：**

```php
Window::on('close', function () {
    // 窗口关闭时执行的代码
});
```

### `off($event, $callback = null)`

移除窗口事件监听器。

**参数：**
- `$event` (`string`) - 事件名称
- `$callback` (`callable|null`) - 回调函数，如果为 null，则移除所有监听器

**返回值：** `void`

**示例：**

```php
Window::off('close');
```

## 事件

### `close`

窗口关闭时触发。

**示例：**

```php
Window::on('close', function () {
    // 窗口关闭时执行的代码
});
```

### `minimize`

窗口最小化时触发。

**示例：**

```php
Window::on('minimize', function () {
    // 窗口最小化时执行的代码
});
```

### `maximize`

窗口最大化时触发。

**示例：**

```php
Window::on('maximize', function () {
    // 窗口最大化时执行的代码
});
```

### `restore`

窗口恢复时触发。

**示例：**

```php
Window::on('restore', function () {
    // 窗口恢复时执行的代码
});
```

### `focus`

窗口获得焦点时触发。

**示例：**

```php
Window::on('focus', function () {
    // 窗口获得焦点时执行的代码
});
```

### `blur`

窗口失去焦点时触发。

**示例：**

```php
Window::on('blur', function () {
    // 窗口失去焦点时执行的代码
});
```

### `move`

窗口移动时触发。

**示例：**

```php
Window::on('move', function ($x, $y) {
    // 窗口移动时执行的代码
});
```

### `resize`

窗口调整大小时触发。

**示例：**

```php
Window::on('resize', function ($width, $height) {
    // 窗口调整大小时执行的代码
});
```

### `enter-full-screen`

窗口进入全屏时触发。

**示例：**

```php
Window::on('enter-full-screen', function () {
    // 窗口进入全屏时执行的代码
});
```

### `leave-full-screen`

窗口离开全屏时触发。

**示例：**

```php
Window::on('leave-full-screen', function () {
    // 窗口离开全屏时执行的代码
});
```
