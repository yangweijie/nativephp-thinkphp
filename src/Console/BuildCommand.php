<?php

namespace Native\ThinkPHP\Console;

use think\App;
use think\facade\Config;
use think\facade\Env;

class BuildCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:build';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '构建 NativePHP 应用程序';

    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    /**
     * 配置命令
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->addOption('platform', 'p', Option::VALUE_OPTIONAL, '构建平台 (windows, macos, linux)', null);
        $this->addOption('arch', 'a', Option::VALUE_OPTIONAL, '构建架构 (x64, arm64)', null);
        $this->addOption('release', 'r', Option::VALUE_NONE, '构建发布版本');
    }

    /**
     * 处理命令
     *
     * @return int
     */
    protected function handle()
    {
        $this->info('正在构建 NativePHP 应用程序...');

        // 获取构建平台
        $platform = $this->option('platform');
        if (!$platform) {
            $platform = $this->detectPlatform();
        }

        // 获取构建架构
        $arch = $this->option('arch');
        if (!$arch) {
            $arch = $this->detectArch();
        }

        // 是否构建发布版本
        $release = $this->option('release');

        $this->info("构建平台: {$platform}");
        $this->info("构建架构: {$arch}");
        $this->info("构建模式: " . ($release ? '发布版本' : '开发版本'));

        // 检查构建环境
        if (!$this->checkBuildEnvironment($platform)) {
            return 1;
        }

        // 准备构建目录
        $buildDir = $this->prepareBuildDirectory($platform, $arch, $release);

        // 构建应用程序
        $this->buildApplication($platform, $arch, $release, $buildDir);

        $this->info('NativePHP 应用程序构建完成！');

        return 0;
    }

    /**
     * 检测当前平台
     *
     * @return string
     */
    protected function detectPlatform()
    {
        $platform = PHP_OS_FAMILY;

        switch ($platform) {
            case 'Windows':
                return 'windows';
            case 'Darwin':
                return 'macos';
            case 'Linux':
                return 'linux';
            default:
                return 'unknown';
        }
    }

    /**
     * 检测当前架构
     *
     * @return string
     */
    protected function detectArch()
    {
        $arch = php_uname('m');

        if (in_array($arch, ['x86_64', 'amd64'])) {
            return 'x64';
        } elseif (in_array($arch, ['arm64', 'aarch64'])) {
            return 'arm64';
        } else {
            return 'x64'; // 默认为 x64
        }
    }

    /**
     * 检查构建环境
     *
     * @param string $platform
     * @return bool
     */
    protected function checkBuildEnvironment($platform)
    {
        // 检查 Node.js 是否安装
        exec('node --version', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->error('Node.js 未安装，请先安装 Node.js');
            return false;
        }

        // 检查 npm 是否安装
        exec('npm --version', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->error('npm 未安装，请先安装 npm');
            return false;
        }

        // 检查 electron-builder 是否安装
        exec('npx electron-builder --version', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->warn('electron-builder 未安装，正在安装...');
            exec('npm install --save-dev electron-builder', $output, $returnCode);
            if ($returnCode !== 0) {
                $this->error('electron-builder 安装失败');
                return false;
            }
        }

        // 检查平台特定依赖
        switch ($platform) {
            case 'windows':
                // Windows 平台无需额外检查
                break;
            case 'macos':
                // 检查 macOS 平台依赖
                if (!$this->checkMacOSDependencies()) {
                    return false;
                }
                break;
            case 'linux':
                // 检查 Linux 平台依赖
                if (!$this->checkLinuxDependencies()) {
                    return false;
                }
                break;
            default:
                $this->error("不支持的平台: {$platform}");
                return false;
        }

        return true;
    }

    /**
     * 检查 macOS 平台依赖
     *
     * @return bool
     */
    protected function checkMacOSDependencies()
    {
        // 检查 Xcode 是否安装
        exec('xcode-select -p', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->error('Xcode 命令行工具未安装，请先安装 Xcode 命令行工具');
            return false;
        }

        return true;
    }

    /**
     * 检查 Linux 平台依赖
     *
     * @return bool
     */
    protected function checkLinuxDependencies()
    {
        // 检查常见的 Linux 依赖
        $dependencies = [
            'fakeroot',
            'dpkg',
            'rpm',
        ];

        $missingDependencies = [];

        foreach ($dependencies as $dependency) {
            exec("which {$dependency}", $output, $returnCode);
            if ($returnCode !== 0) {
                $missingDependencies[] = $dependency;
            }
        }

        if (!empty($missingDependencies)) {
            $this->error('缺少以下依赖: ' . implode(', ', $missingDependencies));
            $this->info('请使用包管理器安装这些依赖');
            return false;
        }

        return true;
    }

    /**
     * 准备构建目录
     *
     * @param string $platform
     * @param string $arch
     * @param bool $release
     * @return string
     */
    protected function prepareBuildDirectory($platform, $arch, $release)
    {
        $buildDir = $this->app->getRuntimePath() . 'build';

        if (!is_dir($buildDir)) {
            mkdir($buildDir, 0755, true);
        }

        // 清理构建目录
        $this->info('正在清理构建目录...');
        $files = glob($buildDir . '/*');
        foreach ($files as $file) {
            if (is_dir($file)) {
                $this->removeDirectory($file);
            } else {
                unlink($file);
            }
        }

        // 创建平台特定构建目录
        $platformBuildDir = $buildDir . '/' . $platform . '-' . $arch;
        if (!is_dir($platformBuildDir)) {
            mkdir($platformBuildDir, 0755, true);
        }

        return $platformBuildDir;
    }

    /**
     * 递归删除目录
     *
     * @param string $dir
     * @return bool
     */
    protected function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    /**
     * 构建应用程序
     *
     * @param string $platform
     * @param string $arch
     * @param bool $release
     * @param string $buildDir
     * @return void
     */
    protected function buildApplication($platform, $arch, $release, $buildDir)
    {
        // 获取应用信息
        $appName = Config::get('native.name', Env::get('NATIVEPHP_APP_NAME', 'NativePHP'));
        $appVersion = Env::get('NATIVEPHP_APP_VERSION', '1.0.0');
        $appAuthor = Env::get('NATIVEPHP_APP_AUTHOR', 'NativePHP');

        // 创建 package.json
        $this->createPackageJson($buildDir, $appName, $appVersion, $appAuthor);

        // 创建 electron 主文件
        $this->createElectronMainFile($buildDir);

        // 复制资源文件
        $this->copyResourceFiles($buildDir);

        // 创建 electron-builder 配置
        $this->createElectronBuilderConfig($buildDir, $platform, $arch, $release);

        // 执行构建命令
        $this->executeBuildCommand($buildDir, $platform, $arch, $release);
    }

    /**
     * 创建 package.json 文件
     *
     * @param string $buildDir
     * @param string $appName
     * @param string $appVersion
     * @param string $appAuthor
     * @return void
     */
    protected function createPackageJson($buildDir, $appName, $appVersion, $appAuthor)
    {
        $packageJson = [
            'name' => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $appName)),
            'version' => $appVersion,
            'description' => $appName,
            'author' => $appAuthor,
            'main' => 'main.js',
            'scripts' => [
                'start' => 'electron .',
                'build' => 'electron-builder',
            ],
            'dependencies' => [
                'electron' => '^24.0.0',
            ],
            'devDependencies' => [
                'electron-builder' => '^24.0.0',
            ],
        ];

        file_put_contents(
            $buildDir . '/package.json',
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('package.json 文件创建成功');
    }

    /**
     * 创建 Electron 主文件
     *
     * @param string $buildDir
     * @return void
     */
    protected function createElectronMainFile($buildDir)
    {
        $mainJs = <<<'JS'
const { app, BrowserWindow, Menu, Tray, ipcMain, dialog, shell } = require('electron');
const path = require('path');
const url = require('url');
const fs = require('fs');

// 保持对窗口对象的全局引用，如果不这么做，当 JavaScript 对象被垃圾回收时，窗口会自动关闭
let mainWindow;
let tray;

// 应用配置
const config = {
    name: app.getName(),
    version: app.getVersion(),
    appPath: app.getAppPath(),
    userDataPath: app.getPath('userData'),
    tempPath: app.getPath('temp'),
    homePath: app.getPath('home'),
    width: 800,
    height: 600,
    minWidth: 800,
    minHeight: 600,
    icon: path.join(__dirname, 'resources', 'icons', 'icon.png'),
    trayIcon: path.join(__dirname, 'resources', 'tray-icons', 'tray-icon.png'),
    backgroundColor: '#ffffff',
    webPreferences: {
        nodeIntegration: false,
        contextIsolation: true,
        webSecurity: true,
        preload: path.join(__dirname, 'preload.js'),
    },
};

// 创建主窗口
function createWindow() {
    mainWindow = new BrowserWindow({
        width: config.width,
        height: config.height,
        minWidth: config.minWidth,
        minHeight: config.minHeight,
        icon: config.icon,
        backgroundColor: config.backgroundColor,
        webPreferences: config.webPreferences,
    });

    // 加载应用
    mainWindow.loadURL('http://localhost:8000');

    // 打开开发者工具
    // mainWindow.webContents.openDevTools();

    // 当窗口关闭时触发
    mainWindow.on('closed', function () {
        mainWindow = null;
    });

    // 创建系统托盘
    createTray();
}

// 创建系统托盘
function createTray() {
    tray = new Tray(config.trayIcon);
    const contextMenu = Menu.buildFromTemplate([
        { label: '显示应用', click: () => mainWindow.show() },
        { type: 'separator' },
        { label: '退出', click: () => app.quit() },
    ]);
    tray.setToolTip(config.name);
    tray.setContextMenu(contextMenu);

    tray.on('click', () => {
        if (mainWindow.isVisible()) {
            mainWindow.hide();
        } else {
            mainWindow.show();
        }
    });
}

// 当 Electron 完成初始化并准备创建浏览器窗口时调用此方法
app.whenReady().then(createWindow);

// 所有窗口关闭时退出应用
app.on('window-all-closed', function () {
    // 在 macOS 上，除非用户用 Cmd + Q 确定地退出，否则绝大部分应用及其菜单栏会保持激活
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', function () {
    // 在 macOS 上，当点击 dock 图标并且没有其他窗口打开时，通常在应用程序中重新创建一个窗口
    if (mainWindow === null) {
        createWindow();
    }
});
JS;

        file_put_contents($buildDir . '/main.js', $mainJs);

        // 创建 preload.js 文件
        $preloadJs = <<<'JS'
const { contextBridge, ipcRenderer } = require('electron');

// 暴露 API 给渲染进程
contextBridge.exposeInMainWorld('electron', {
    // 发送消息到主进程
    send: (channel, data) => {
        ipcRenderer.send(channel, data);
    },
    // 接收来自主进程的消息
    receive: (channel, func) => {
        ipcRenderer.on(channel, (event, ...args) => func(...args));
    },
});
JS;

        file_put_contents($buildDir . '/preload.js', $preloadJs);

        $this->info('Electron 主文件创建成功');
    }

    /**
     * 复制资源文件
     *
     * @param string $buildDir
     * @return void
     */
    protected function copyResourceFiles($buildDir)
    {
        $resourcesDir = $this->app->getRootPath() . 'resources/native';
        $targetResourcesDir = $buildDir . '/resources';

        if (!is_dir($targetResourcesDir)) {
            mkdir($targetResourcesDir, 0755, true);
        }

        // 复制图标
        $this->copyDirectory($resourcesDir . '/icons', $targetResourcesDir . '/icons');

        // 复制托盘图标
        $this->copyDirectory($resourcesDir . '/tray-icons', $targetResourcesDir . '/tray-icons');

        // 复制安装程序图标
        $this->copyDirectory($resourcesDir . '/installer', $targetResourcesDir . '/installer');

        $this->info('资源文件复制成功');
    }

    /**
     * 复制目录
     *
     * @param string $source
     * @param string $destination
     * @return void
     */
    protected function copyDirectory($source, $destination)
    {
        if (!is_dir($source)) {
            return;
        }

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $destinationPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destinationPath);
            } else {
                copy($sourcePath, $destinationPath);
            }
        }
        closedir($dir);
    }

    /**
     * 创建 electron-builder 配置
     *
     * @param string $buildDir
     * @param string $platform
     * @param string $arch
     * @param bool $release
     * @return void
     */
    protected function createElectronBuilderConfig($buildDir, $platform, $arch, $release)
    {
        $appName = Config::get('native.name', Env::get('NATIVEPHP_APP_NAME', 'NativePHP'));
        $appId = 'com.' . strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $appName));

        $electronBuilderConfig = [
            'appId' => $appId,
            'productName' => $appName,
            'directories' => [
                'output' => 'dist',
            ],
            'files' => [
                '**/*',
            ],
            'asar' => true,
            'win' => [
                'target' => [
                    'target' => 'nsis',
                    'arch' => [$arch],
                ],
                'icon' => 'resources/icons/icon.ico',
            ],
            'mac' => [
                'target' => [
                    'target' => 'dmg',
                    'arch' => [$arch],
                ],
                'icon' => 'resources/icons/icon.icns',
                'category' => 'public.app-category.utilities',
            ],
            'linux' => [
                'target' => [
                    'target' => 'AppImage',
                    'arch' => [$arch],
                ],
                'icon' => 'resources/icons/icon.png',
                'category' => 'Utility',
            ],
            'nsis' => [
                'oneClick' => false,
                'allowToChangeInstallationDirectory' => true,
                'installerIcon' => 'resources/installer/installer-icon.ico',
                'uninstallerIcon' => 'resources/installer/installer-icon.ico',
                'installerHeaderIcon' => 'resources/installer/installer-icon.ico',
                'createDesktopShortcut' => true,
                'createStartMenuShortcut' => true,
            ],
            'dmg' => [
                'icon' => 'resources/installer/installer-icon.icns',
                'background' => 'resources/installer/background.png',
                'window' => [
                    'width' => 540,
                    'height' => 380,
                ],
            ],
            'publish' => $release ? [
                'provider' => 'generic',
                'url' => Env::get('NATIVEPHP_UPDATE_SERVER_URL', ''),
            ] : null,
        ];

        file_put_contents(
            $buildDir . '/electron-builder.json',
            json_encode($electronBuilderConfig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        $this->info('electron-builder 配置创建成功');
    }

    /**
     * 执行构建命令
     *
     * @param string $buildDir
     * @param string $platform
     * @param string $arch
     * @param bool $release
     * @return void
     */
    protected function executeBuildCommand($buildDir, $platform, $arch, $release)
    {
        $this->info('正在构建应用程序...');

        $command = 'cd ' . escapeshellarg($buildDir) . ' && ';
        $command .= 'npm install && ';

        switch ($platform) {
            case 'windows':
                $command .= 'npx electron-builder --win';
                break;
            case 'macos':
                $command .= 'npx electron-builder --mac';
                break;
            case 'linux':
                $command .= 'npx electron-builder --linux';
                break;
        }

        $command .= ' --' . $arch;

        if ($release) {
            $command .= ' -p always';
        }

        $this->info('执行命令: ' . $command);

        // 执行构建命令
        $process = proc_open($command, [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes);

        if (is_resource($process)) {
            while (!feof($pipes[1])) {
                $output = fgets($pipes[1]);
                if ($output) {
                    $this->info(trim($output));
                }
            }

            while (!feof($pipes[2])) {
                $error = fgets($pipes[2]);
                if ($error) {
                    $this->error(trim($error));
                }
            }

            fclose($pipes[1]);
            fclose($pipes[2]);

            $exitCode = proc_close($process);

            if ($exitCode === 0) {
                $this->info('应用程序构建成功！');
                $this->info('构建输出目录: ' . $buildDir . '/dist');
            } else {
                $this->error('应用程序构建失败，退出代码: ' . $exitCode);
            }
        } else {
            $this->error('无法启动构建进程');
        }
    }
}
