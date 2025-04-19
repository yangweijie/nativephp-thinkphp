# App 类 API 参考

`App` 类提供了应用程序管理功能，包括获取应用信息、退出应用、重启应用等。

## 命名空间

```php
namespace Native\ThinkPHP;
```

## 使用方法

```php
use Native\ThinkPHP\Facades\App;
```

## 方法

### `name()`

获取应用名称。

**参数：** 无

**返回值：** `string` - 应用名称

**示例：**

```php
$name = App::name();
```

### `id()`

获取应用 ID。

**参数：** 无

**返回值：** `string` - 应用 ID

**示例：**

```php
$id = App::id();
```

### `version()`

获取应用版本。

**参数：** 无

**返回值：** `string` - 应用版本

**示例：**

```php
$version = App::version();
```

### `getRootPath()`

获取应用根路径。

**参数：** 无

**返回值：** `string` - 应用根路径

**示例：**

```php
$rootPath = App::getRootPath();
```

### `getAppPath()`

获取应用路径。

**参数：** 无

**返回值：** `string` - 应用路径

**示例：**

```php
$appPath = App::getAppPath();
```

### `getPublicPath()`

获取应用公共路径。

**参数：** 无

**返回值：** `string` - 应用公共路径

**示例：**

```php
$publicPath = App::getPublicPath();
```

### `getConfigPath()`

获取应用配置路径。

**参数：** 无

**返回值：** `string` - 应用配置路径

**示例：**

```php
$configPath = App::getConfigPath();
```

### `getRuntimePath()`

获取应用运行时路径。

**参数：** 无

**返回值：** `string` - 应用运行时路径

**示例：**

```php
$runtimePath = App::getRuntimePath();
```

### `getStoragePath()`

获取应用存储路径。

**参数：** 无

**返回值：** `string` - 应用存储路径

**示例：**

```php
$storagePath = App::getStoragePath();
```

### `getResourcesPath()`

获取应用资源路径。

**参数：** 无

**返回值：** `string` - 应用资源路径

**示例：**

```php
$resourcesPath = App::getResourcesPath();
```

### `getBuildPath()`

获取应用构建路径。

**参数：** 无

**返回值：** `string` - 应用构建路径

**示例：**

```php
$buildPath = App::getBuildPath();
```

### `quit()`

退出应用。

**参数：** 无

**返回值：** `void`

**示例：**

```php
App::quit();
```

### `restart()`

重启应用。

**参数：** 无

**返回值：** `void`

**示例：**

```php
App::restart();
```

### `hide()`

隐藏应用（隐藏所有窗口）。

**参数：** 无

**返回值：** `void`

**示例：**

```php
App::hide();
```

### `show()`

显示应用（显示所有窗口）。

**参数：** 无

**返回值：** `void`

**示例：**

```php
App::show();
```

### `focus()`

聚焦应用（聚焦主窗口）。

**参数：** 无

**返回值：** `void`

**示例：**

```php
App::focus();
```

### `isPackaged()`

检查应用是否已打包。

**参数：** 无

**返回值：** `bool` - 应用是否已打包

**示例：**

```php
$isPackaged = App::isPackaged();
```

### `isDevelopment()`

检查应用是否处于开发模式。

**参数：** 无

**返回值：** `bool` - 应用是否处于开发模式

**示例：**

```php
$isDevelopment = App::isDevelopment();
```

### `isProduction()`

检查应用是否处于生产模式。

**参数：** 无

**返回值：** `bool` - 应用是否处于生产模式

**示例：**

```php
$isProduction = App::isProduction();
```

### `getLocale()`

获取应用语言环境。

**参数：** 无

**返回值：** `string` - 应用语言环境

**示例：**

```php
$locale = App::getLocale();
```

### `setLocale($locale)`

设置应用语言环境。

**参数：**
- `$locale` (`string`) - 语言环境

**返回值：** `void`

**示例：**

```php
App::setLocale('zh-CN');
```

### `on($event, $callback)`

监听应用事件。

**参数：**
- `$event` (`string`) - 事件名称
- `$callback` (`callable`) - 回调函数

**返回值：** `void`

**示例：**

```php
App::on('before-quit', function () {
    // 应用退出前执行的代码
});
```

### `off($event, $callback = null)`

移除应用事件监听器。

**参数：**
- `$event` (`string`) - 事件名称
- `$callback` (`callable|null`) - 回调函数，如果为 null，则移除所有监听器

**返回值：** `void`

**示例：**

```php
App::off('before-quit');
```

### `emit($event, ...$args)`

触发应用事件。

**参数：**
- `$event` (`string`) - 事件名称
- `...$args` (`mixed`) - 事件参数

**返回值：** `array` - 事件处理结果

**示例：**

```php
App::emit('custom-event', $data);
```

## 事件

### `ready`

应用准备就绪时触发。

**示例：**

```php
App::on('ready', function () {
    // 应用准备就绪时执行的代码
});
```

### `before-quit`

应用退出前触发。

**示例：**

```php
App::on('before-quit', function () {
    // 应用退出前执行的代码
});
```

### `will-quit`

应用即将退出时触发。

**示例：**

```php
App::on('will-quit', function () {
    // 应用即将退出时执行的代码
});
```

### `quit`

应用退出时触发。

**示例：**

```php
App::on('quit', function () {
    // 应用退出时执行的代码
});
```

### `activate`

应用激活时触发。

**示例：**

```php
App::on('activate', function () {
    // 应用激活时执行的代码
});
```

### `deactivate`

应用停用时触发。

**示例：**

```php
App::on('deactivate', function () {
    // 应用停用时执行的代码
});
```

### `browser-window-created`

浏览器窗口创建时触发。

**示例：**

```php
App::on('browser-window-created', function ($window) {
    // 浏览器窗口创建时执行的代码
});
```

### `browser-window-focus`

浏览器窗口获得焦点时触发。

**示例：**

```php
App::on('browser-window-focus', function ($window) {
    // 浏览器窗口获得焦点时执行的代码
});
```

### `browser-window-blur`

浏览器窗口失去焦点时触发。

**示例：**

```php
App::on('browser-window-blur', function ($window) {
    // 浏览器窗口失去焦点时执行的代码
});
```
