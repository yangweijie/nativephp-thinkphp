# NativePHP/ThinkPHP 跨平台增强

## 平台适配层

### 抽象平台 API
设计抽象的平台 API 层，隔离平台差异，提供统一的接口。

```php
// 平台接口
interface PlatformInterface
{
    public function getName();
    public function getVersion();
    public function getArch();
    public function getHomeDir();
    public function getTempDir();
    public function getAppDataDir();
    public function openFile($path);
    public function openUrl($url);
    public function showNotification($title, $body, $options = []);
}

// Windows 平台实现
class WindowsPlatform implements PlatformInterface
{
    public function getName()
    {
        return 'windows';
    }
    
    public function getVersion()
    {
        return php_uname('r');
    }
    
    public function getArch()
    {
        return php_uname('m');
    }
    
    public function getHomeDir()
    {
        return getenv('USERPROFILE');
    }
    
    public function getTempDir()
    {
        return getenv('TEMP');
    }
    
    public function getAppDataDir()
    {
        return getenv('APPDATA');
    }
    
    public function openFile($path)
    {
        return shell_exec("start \"\" \"$path\"");
    }
    
    public function openUrl($url)
    {
        return shell_exec("start \"\" \"$url\"");
    }
    
    public function showNotification($title, $body, $options = [])
    {
        // 使用 Windows 通知 API
        // ...
    }
}

// macOS 平台实现
class MacOSPlatform implements PlatformInterface
{
    public function getName()
    {
        return 'macos';
    }
    
    public function getVersion()
    {
        return php_uname('r');
    }
    
    public function getArch()
    {
        return php_uname('m');
    }
    
    public function getHomeDir()
    {
        return getenv('HOME');
    }
    
    public function getTempDir()
    {
        return sys_get_temp_dir();
    }
    
    public function getAppDataDir()
    {
        return getenv('HOME') . '/Library/Application Support';
    }
    
    public function openFile($path)
    {
        return shell_exec("open \"$path\"");
    }
    
    public function openUrl($url)
    {
        return shell_exec("open \"$url\"");
    }
    
    public function showNotification($title, $body, $options = [])
    {
        // 使用 macOS 通知 API
        // ...
    }
}

// Linux 平台实现
class LinuxPlatform implements PlatformInterface
{
    public function getName()
    {
        return 'linux';
    }
    
    public function getVersion()
    {
        return php_uname('r');
    }
    
    public function getArch()
    {
        return php_uname('m');
    }
    
    public function getHomeDir()
    {
        return getenv('HOME');
    }
    
    public function getTempDir()
    {
        return sys_get_temp_dir();
    }
    
    public function getAppDataDir()
    {
        return getenv('HOME') . '/.config';
    }
    
    public function openFile($path)
    {
        return shell_exec("xdg-open \"$path\"");
    }
    
    public function openUrl($url)
    {
        return shell_exec("xdg-open \"$url\"");
    }
    
    public function showNotification($title, $body, $options = [])
    {
        // 使用 Linux 通知 API
        // ...
    }
}

// 平台工厂
class PlatformFactory
{
    public static function create()
    {
        $os = strtolower(PHP_OS);
        
        if (strpos($os, 'win') !== false) {
            return new WindowsPlatform();
        } elseif (strpos($os, 'darwin') !== false) {
            return new MacOSPlatform();
        } elseif (strpos($os, 'linux') !== false) {
            return new LinuxPlatform();
        } else {
            throw new \Exception("Unsupported platform: $os");
        }
    }
}
```

### 平台特定功能
实现平台特定功能，提供更好的原生体验。

#### Windows 特定功能
- 任务栏进度条
- 跳转列表
- 开始菜单磁贴

```php
// Windows 特定功能
class WindowsFeatures
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function setTaskbarProgress($value, $mode = 'normal')
    {
        // 设置任务栏进度条
        // ...
    }
    
    public function setJumpList($categories)
    {
        // 设置跳转列表
        // ...
    }
    
    public function createShortcut($options)
    {
        // 创建快捷方式
        // ...
    }
}
```

#### macOS 特定功能
- Touch Bar 支持
- Dock 菜单
- 暗黑模式检测

```php
// macOS 特定功能
class MacOSFeatures
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function setTouchBar($items)
    {
        // 设置 Touch Bar
        // ...
    }
    
    public function setDockMenu($menu)
    {
        // 设置 Dock 菜单
        // ...
    }
    
    public function isDarkMode()
    {
        // 检测暗黑模式
        // ...
    }
}
```

#### Linux 特定功能
- AppIndicator 支持
- DBus 集成
- 桌面环境检测

```php
// Linux 特定功能
class LinuxFeatures
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function setAppIndicator($options)
    {
        // 设置 AppIndicator
        // ...
    }
    
    public function getDesktopEnvironment()
    {
        // 获取桌面环境
        // ...
    }
    
    public function sendDBusSignal($service, $path, $interface, $signal, $args = [])
    {
        // 发送 DBus 信号
        // ...
    }
}
```

## 自适应 UI

### 响应式布局
实现响应式布局，适应不同屏幕尺寸和分辨率。

- 流式布局
- 网格系统
- 断点适配
- 自适应组件

### 平台风格适配
根据不同平台自动适配 UI 风格，提供原生体验。

```php
// UI 风格管理器
class UIStyleManager
{
    protected $app;
    protected $platform;
    
    public function __construct($app, $platform)
    {
        $this->app = $app;
        $this->platform = $platform;
    }
    
    public function getStyle()
    {
        $platformName = $this->platform->getName();
        $method = 'get' . ucfirst($platformName) . 'Style';
        
        if (method_exists($this, $method)) {
            return $this->$method();
        }
        
        return $this->getDefaultStyle();
    }
    
    protected function getWindowsStyle()
    {
        return [
            'fontFamily' => 'Segoe UI',
            'fontSize' => '14px',
            'primaryColor' => '#0078d7',
            'borderRadius' => '2px',
            'padding' => '8px',
        ];
    }
    
    protected function getMacosStyle()
    {
        return [
            'fontFamily' => 'San Francisco, Helvetica Neue',
            'fontSize' => '13px',
            'primaryColor' => '#007aff',
            'borderRadius' => '4px',
            'padding' => '8px',
        ];
    }
    
    protected function getLinuxStyle()
    {
        // 检测桌面环境
        $desktopEnv = getenv('XDG_CURRENT_DESKTOP');
        
        if (strpos($desktopEnv, 'GNOME') !== false) {
            return [
                'fontFamily' => 'Ubuntu, Cantarell',
                'fontSize' => '14px',
                'primaryColor' => '#3584e4',
                'borderRadius' => '8px',
                'padding' => '8px',
            ];
        } elseif (strpos($desktopEnv, 'KDE') !== false) {
            return [
                'fontFamily' => 'Noto Sans',
                'fontSize' => '14px',
                'primaryColor' => '#3daee9',
                'borderRadius' => '3px',
                'padding' => '6px',
            ];
        }
        
        return $this->getDefaultStyle();
    }
    
    protected function getDefaultStyle()
    {
        return [
            'fontFamily' => 'Arial, sans-serif',
            'fontSize' => '14px',
            'primaryColor' => '#2196f3',
            'borderRadius' => '4px',
            'padding' => '8px',
        ];
    }
}
```

### 主题系统
实现强大的主题系统，支持自定义主题和动态切换。

- 亮色/暗色主题
- 自定义主题
- 主题编辑器
- 主题市场

## 多平台构建

### 统一构建流程
实现统一的构建流程，简化多平台应用的构建和发布。

```php
// 构建配置
class BuildConfig
{
    protected $app;
    protected $config;
    
    public function __construct($app, $config = [])
    {
        $this->app = $app;
        $this->config = $config;
    }
    
    public function getAppName()
    {
        return $this->config['name'] ?? $this->app->config->get('native.name', 'NativePHP App');
    }
    
    public function getAppVersion()
    {
        return $this->config['version'] ?? $this->app->config->get('native.version', '1.0.0');
    }
    
    public function getAppId()
    {
        return $this->config['app_id'] ?? $this->app->config->get('native.app_id', 'com.nativephp.app');
    }
    
    public function getIcon($platform = null)
    {
        if ($platform) {
            return $this->config['icons'][$platform] ?? $this->getDefaultIcon($platform);
        }
        
        return $this->config['icons'] ?? [
            'win' => $this->getDefaultIcon('win'),
            'mac' => $this->getDefaultIcon('mac'),
            'linux' => $this->getDefaultIcon('linux'),
        ];
    }
    
    protected function getDefaultIcon($platform)
    {
        $publicPath = $this->app->getRootPath() . 'public/';
        
        switch ($platform) {
            case 'win':
                return $publicPath . 'favicon.ico';
            case 'mac':
                return $publicPath . 'favicon.icns';
            case 'linux':
                return $publicPath . 'favicon.png';
            default:
                return $publicPath . 'favicon.png';
        }
    }
    
    public function getOutputPath()
    {
        return $this->config['output_path'] ?? $this->app->getRuntimePath() . 'native/build/dist/';
    }
    
    public function getPlatforms()
    {
        return $this->config['platforms'] ?? ['win', 'mac', 'linux'];
    }
    
    public function getElectronVersion()
    {
        return $this->config['electron_version'] ?? '22.0.0';
    }
    
    public function getPackageJson()
    {
        $packageJson = [
            'name' => $this->getAppName(),
            'version' => $this->getAppVersion(),
            'description' => $this->config['description'] ?? 'NativePHP Application',
            'main' => 'main.js',
            'scripts' => [
                'start' => 'electron .',
                'build' => 'electron-builder',
                'build:win' => 'electron-builder --win',
                'build:mac' => 'electron-builder --mac',
                'build:linux' => 'electron-builder --linux'
            ],
            'build' => [
                'appId' => $this->getAppId(),
                'productName' => $this->getAppName(),
                'directories' => [
                    'output' => 'dist'
                ],
                'files' => [
                    'main.js',
                    'preload.js',
                    'php/**/*',
                    'public/**/*'
                ],
                'win' => [
                    'target' => [
                        'nsis'
                    ],
                    'icon' => $this->getIcon('win')
                ],
                'mac' => [
                    'target' => [
                        'dmg'
                    ],
                    'icon' => $this->getIcon('mac')
                ],
                'linux' => [
                    'target' => [
                        'AppImage'
                    ],
                    'icon' => $this->getIcon('linux')
                ]
            ],
            'dependencies' => [
                'electron-serve' => '^1.1.0',
                'electron-store' => '^8.1.0',
                'electron-updater' => '^5.3.0'
            ],
            'devDependencies' => [
                'electron' => '^' . $this->getElectronVersion(),
                'electron-builder' => '^23.6.0'
            ]
        ];
        
        // 合并自定义配置
        if (isset($this->config['package_json'])) {
            $packageJson = array_merge_recursive($packageJson, $this->config['package_json']);
        }
        
        return $packageJson;
    }
}

// 构建管理器
class BuildManager
{
    protected $app;
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function build($config = [])
    {
        $buildConfig = new BuildConfig($this->app, $config);
        
        // 创建构建目录
        $buildPath = $this->app->getRuntimePath() . 'native/build/';
        if (!is_dir($buildPath)) {
            mkdir($buildPath, 0755, true);
        }
        
        // 创建 Electron 应用
        $this->createElectronApp($buildPath, $buildConfig);
        
        // 复制 PHP 应用
        $this->copyPHPApp($buildPath);
        
        // 构建应用
        $this->buildApp($buildPath, $buildConfig);
        
        return $buildConfig->getOutputPath();
    }
    
    protected function createElectronApp($buildPath, $buildConfig)
    {
        // 创建 package.json
        file_put_contents(
            $buildPath . 'package.json',
            json_encode($buildConfig->getPackageJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
        
        // 创建 main.js
        // ...
        
        // 创建 preload.js
        // ...
    }
    
    protected function copyPHPApp($buildPath)
    {
        // 复制 PHP 应用文件
        // ...
    }
    
    protected function buildApp($buildPath, $buildConfig)
    {
        // 切换到构建目录
        $currentDir = getcwd();
        chdir($buildPath);
        
        // 安装依赖
        system('npm install');
        
        // 构建应用
        foreach ($buildConfig->getPlatforms() as $platform) {
            system('npm run build:' . $platform);
        }
        
        // 切回原目录
        chdir($currentDir);
    }
}
```

### 平台特定打包
针对不同平台提供特定的打包配置和优化。

#### Windows 打包
- NSIS 安装程序
- MSI 安装包
- 便携版

#### macOS 打包
- DMG 镜像
- PKG 安装包
- App Store 发布

#### Linux 打包
- AppImage
- Snap 包
- Flatpak 包
- DEB/RPM 包

## 多平台测试

### 自动化测试
实现自动化测试，确保应用在各平台上的一致性。

- 单元测试
- 集成测试
- UI 测试
- 端到端测试

### 持续集成
建立持续集成流程，自动构建和测试多平台应用。

- GitHub Actions
- Jenkins
- Travis CI
- CircleCI
