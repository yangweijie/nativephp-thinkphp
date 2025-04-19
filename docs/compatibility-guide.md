# NativePHP/ThinkPHP 兼容性指南

## 跨平台兼容性

### 操作系统差异处理
NativePHP/ThinkPHP 需要在 Windows、macOS 和 Linux 上提供一致的体验，同时处理各平台的特殊性。

#### 文件路径处理
不同操作系统使用不同的路径分隔符，需要统一处理。

```php
// 示例实现
class Path
{
    public static function normalize($path)
    {
        return str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $path);
    }
    
    public static function join(...$parts)
    {
        return implode(DIRECTORY_SEPARATOR, $parts);
    }
}
```

#### 平台特定功能检测
在使用平台特定功能前进行检测，提供优雅的降级方案。

```php
// 示例实现
class PlatformDetector
{
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }
    
    public static function isMac()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'DAR';
    }
    
    public static function isLinux()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'LIN';
    }
    
    public static function hasFeature($feature)
    {
        // 检测特定功能是否可用
        switch ($feature) {
            case 'touch-bar':
                return self::isMac();
            case 'windows-jump-list':
                return self::isWindows();
            // 其他特性检测
            default:
                return false;
        }
    }
}
```

### 界面适配
确保应用界面在不同平台上都能正确显示和工作。

1. 使用响应式设计
2. 适配不同平台的 UI 风格
3. 处理高 DPI 显示器

## PHP 版本兼容性

### PHP 8.1+ 兼容性
确保与 PHP 8.1 及更高版本的完全兼容。

#### 使用新特性的兼容性包装
为新的 PHP 特性提供兼容性包装，使代码在不同版本上都能工作。

```php
// 示例实现
class PHPFeatures
{
    public static function hasEnums()
    {
        return version_compare(PHP_VERSION, '8.1.0', '>=');
    }
    
    public static function hasReadonlyProperties()
    {
        return version_compare(PHP_VERSION, '8.1.0', '>=');
    }
    
    public static function hasFibers()
    {
        return version_compare(PHP_VERSION, '8.1.0', '>=');
    }
}
```

#### 类型系统兼容性
处理 PHP 8.1+ 中更严格的类型系统。

1. 使用联合类型 (PHP 8.0+)
2. 使用 Intersection Types (PHP 8.1+)
3. 使用 Readonly Properties (PHP 8.1+)

### 扩展兼容性
确保与常用 PHP 扩展的兼容性。

1. 检测必要扩展是否可用
2. 提供扩展不可用时的替代方案
3. 明确记录扩展依赖

## ThinkPHP 版本兼容性

### ThinkPHP 8.1 适配
确保与 ThinkPHP 8.1 的完全兼容。

1. 使用 ThinkPHP 8.1 的新特性
2. 适配 ThinkPHP 8.1 的 API 变更
3. 处理废弃的功能和方法

### 向后兼容性
提供对旧版 ThinkPHP 的兼容性支持。

```php
// 示例实现
class ThinkPHPCompat
{
    public static function getVersion()
    {
        return defined('THINK_VERSION') ? THINK_VERSION : '8.1.0';
    }
    
    public static function isVersion8()
    {
        return version_compare(self::getVersion(), '8.0.0', '>=');
    }
    
    public static function isVersion6()
    {
        return version_compare(self::getVersion(), '6.0.0', '>=') && 
               version_compare(self::getVersion(), '7.0.0', '<');
    }
    
    public static function adaptMethod($object, $method, $args = [])
    {
        // 处理不同版本 API 差异
        if (self::isVersion8() && $method == 'oldMethod') {
            return call_user_func_array([$object, 'newMethod'], $args);
        }
        
        return call_user_func_array([$object, $method], $args);
    }
}
```

## 浏览器兼容性

### Electron 版本兼容性
确保与不同版本的 Electron 兼容。

1. 使用 Electron 的稳定 API
2. 处理 Electron 版本间的 API 变更
3. 提供版本检测和适配机制

### Web 标准兼容性
确保应用遵循 Web 标准，提高兼容性。

1. 使用标准 HTML、CSS 和 JavaScript
2. 避免使用实验性或非标准 API
3. 提供 polyfill 支持旧版浏览器
