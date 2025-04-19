<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use think\facade\Config;

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
    protected $description = '构建 NativePHP 桌面应用';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int|null
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('正在构建 NativePHP 桌面应用...');

        // 创建构建目录
        $buildPath = App::getRuntimePath() . 'native/build/';
        if (!is_dir($buildPath)) {
            mkdir($buildPath, 0755, true);
        }

        // 获取应用信息
        $appName = Config::get('native.name', 'NativePHP');
        $appVersion = Config::get('native.version', '1.0.0');
        $appId = Config::get('native.app_id', 'com.nativephp.app');

        // 创建 Electron 应用
        $this->createElectronApp($buildPath, $appName, $appVersion, $appId, $output);

        // 复制 PHP 应用
        $this->copyPHPApp($buildPath, $output);

        // 构建应用
        $this->buildApp($buildPath, $output);

        $output->writeln('<info>NativePHP 桌面应用构建完成！</info>');
        $output->writeln('<info>构建输出目录：' . $buildPath . 'dist/</info>');

        return 0;
    }

    /**
     * 创建 Electron 应用
     *
     * @param string $buildPath
     * @param string $appName
     * @param string $appVersion
     * @param string $appId
     * @param Output $output
     * @return void
     */
    protected function createElectronApp($buildPath, $appName, $appVersion, $appId, Output $output)
    {
        $output->writeln('创建 Electron 应用...');

        // 创建 package.json
        $packageJson = [
            'name' => $appName,
            'version' => $appVersion,
            'description' => 'NativePHP Application',
            'main' => 'main.js',
            'scripts' => [
                'start' => 'electron .',
                'build' => 'electron-builder',
                'build:win' => 'electron-builder --win',
                'build:mac' => 'electron-builder --mac',
                'build:linux' => 'electron-builder --linux'
            ],
            'build' => [
                'appId' => $appId,
                'productName' => $appName,
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
                    'icon' => 'public/favicon.ico'
                ],
                'mac' => [
                    'target' => [
                        'dmg'
                    ],
                    'icon' => 'public/favicon.icns'
                ],
                'linux' => [
                    'target' => [
                        'AppImage'
                    ],
                    'icon' => 'public/favicon.png'
                ]
            ],
            'dependencies' => [
                'electron-serve' => '^1.1.0',
                'electron-store' => '^8.1.0',
                'electron-updater' => '^5.3.0'
            ],
            'devDependencies' => [
                'electron' => '^22.0.0',
                'electron-builder' => '^23.6.0'
            ]
        ];

        file_put_contents(
            $buildPath . 'package.json',
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // 创建 main.js
        $mainJsPath = __DIR__ . '/../templates/main.js';
        if (file_exists($mainJsPath)) {
            // 使用增强版的 main.js 模板
            $mainJs = file_get_contents($mainJsPath);

            // 替换配置变量
            $mainJs = str_replace('width: 800', 'width: ' . $this->getWindowConfig('width', 800), $mainJs);
            $mainJs = str_replace('height: 600', 'height: ' . $this->getWindowConfig('height', 600), $mainJs);
            $mainJs = str_replace('minWidth: 400', 'minWidth: ' . $this->getWindowConfig('min_width', 400), $mainJs);
            $mainJs = str_replace('minHeight: 400', 'minHeight: ' . $this->getWindowConfig('min_height', 400), $mainJs);
            $mainJs = str_replace('maxWidth: null', 'maxWidth: ' . $this->getWindowConfig('max_width', 'null'), $mainJs);
            $mainJs = str_replace('maxHeight: null', 'maxHeight: ' . $this->getWindowConfig('max_height', 'null'), $mainJs);
            $mainJs = str_replace('resizable: true', 'resizable: ' . $this->getWindowConfig('resizable', 'true'), $mainJs);
            $mainJs = str_replace('fullscreen: false', 'fullscreen: ' . $this->getWindowConfig('fullscreen', 'false'), $mainJs);
            $mainJs = str_replace('title: \'\'', 'title: \'' . $appName . '\'', $mainJs);
            $mainJs = str_replace('http://localhost:8000', 'app.isPackaged ? loadURL() : \'http://localhost:8000\'', $mainJs);
        } else {
            // 使用基本的 main.js 模板
            $mainJs = <<<JS
const { app, BrowserWindow, Menu, Tray, dialog, clipboard, globalShortcut } = require('electron');
const path = require('path');
const serve = require('electron-serve');
const Store = require('electron-store');

// 配置存储
const store = new Store();

// 静态文件服务
const loadURL = serve({ directory: 'public' });

// 保持对窗口对象的全局引用
let mainWindow;
let tray = null;

// 创建主窗口
function createWindow() {
    mainWindow = new BrowserWindow({
        width: {$this->getWindowConfig('width', 800)},
        height: {$this->getWindowConfig('height', 600)},
        minWidth: {$this->getWindowConfig('min_width', 400)},
        minHeight: {$this->getWindowConfig('min_height', 400)},
        maxWidth: {$this->getWindowConfig('max_width', 'null')},
        maxHeight: {$this->getWindowConfig('max_height', 'null')},
        resizable: {$this->getWindowConfig('resizable', 'true')},
        fullscreen: {$this->getWindowConfig('fullscreen', 'false')},
        title: '{$appName}',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // 加载应用
    loadURL(mainWindow);

    // 开发环境下打开开发者工具
    if (process.env.NODE_ENV === 'development') {
        mainWindow.webContents.openDevTools();
    }

    // 当窗口关闭时调用的方法
    mainWindow.on('closed', function() {
        mainWindow = null;
    });

    // 创建应用菜单
    createMenu();

    // 创建系统托盘
    createTray();
}

// 创建应用菜单
function createMenu() {
    const template = [
        {
            label: '文件',
            submenu: [
                { label: '退出', role: 'quit' }
            ]
        },
        {
            label: '编辑',
            submenu: [
                { label: '撤销', role: 'undo' },
                { label: '重做', role: 'redo' },
                { type: 'separator' },
                { label: '剪切', role: 'cut' },
                { label: '复制', role: 'copy' },
                { label: '粘贴', role: 'paste' },
                { label: '删除', role: 'delete' },
                { label: '全选', role: 'selectAll' }
            ]
        },
        {
            label: '视图',
            submenu: [
                { label: '重新加载', role: 'reload' },
                { label: '强制重新加载', role: 'forceReload' },
                { type: 'separator' },
                { label: '实际大小', role: 'resetZoom' },
                { label: '放大', role: 'zoomIn' },
                { label: '缩小', role: 'zoomOut' },
                { type: 'separator' },
                { label: '全屏', role: 'togglefullscreen' }
            ]
        },
        {
            label: '窗口',
            submenu: [
                { label: '最小化', role: 'minimize' },
                { label: '关闭', role: 'close' }
            ]
        },
        {
            label: '帮助',
            submenu: [
                {
                    label: '关于',
                    click: () => {
                        dialog.showMessageBox(mainWindow, {
                            type: 'info',
                            title: '关于',
                            message: '{$appName}',
                            detail: '版本: {$appVersion}\\n基于 NativePHP 构建'
                        });
                    }
                }
            ]
        }
    ];

    const menu = Menu.buildFromTemplate(template);
    Menu.setApplicationMenu(menu);
}

// 创建系统托盘
function createTray() {
    tray = new Tray(path.join(__dirname, 'public/favicon.ico'));
    tray.setToolTip('{$appName}');

    const contextMenu = Menu.buildFromTemplate([
        { label: '显示', click: () => mainWindow.show() },
        { label: '退出', click: () => app.quit() }
    ]);

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
app.on('ready', createWindow);

// 当所有窗口关闭时退出应用
app.on('window-all-closed', function() {
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', function() {
    if (mainWindow === null) {
        createWindow();
    }
});

// 注册全局快捷键
app.on('ready', () => {
    // 示例：注册 Ctrl+Shift+A 快捷键
    globalShortcut.register('CommandOrControl+Shift+A', () => {
        if (mainWindow) {
            if (mainWindow.isVisible()) {
                mainWindow.hide();
            } else {
                mainWindow.show();
            }
        }
    });
});

// 应用退出前注销所有快捷键
app.on('will-quit', () => {
    globalShortcut.unregisterAll();
});
JS;
        }

        file_put_contents($buildPath . 'main.js', $mainJs);

        // 创建 preload.js
        $preloadJsPath = __DIR__ . '/../templates/preload.js';
        if (file_exists($preloadJsPath)) {
            // 使用增强版的 preload.js 模板
            $preloadJs = file_get_contents($preloadJsPath);
        } else {
            // 使用基本的 preload.js 模板
            $preloadJs = <<<JS
// preload.js
window.addEventListener('DOMContentLoaded', () => {
    const replaceText = (selector, text) => {
        const element = document.getElementById(selector);
        if (element) element.innerText = text;
    };

    for (const dependency of ['chrome', 'node', 'electron']) {
        replaceText(`\${dependency}-version`, process.versions[dependency]);
    }
});
JS;
        }

        file_put_contents($buildPath . 'preload.js', $preloadJs);

        $output->writeln('<info>Electron 应用创建成功！</info>');
    }

    /**
     * 复制 PHP 应用
     *
     * @param string $buildPath
     * @param Output $output
     * @return void
     */
    protected function copyPHPApp($buildPath, Output $output)
    {
        $output->writeln('复制 PHP 应用...');

        // 创建 PHP 目录
        $phpPath = $buildPath . 'php/';
        if (!is_dir($phpPath)) {
            mkdir($phpPath, 0755, true);
        }

        // 创建 public 目录
        $publicPath = $buildPath . 'public/';
        if (!is_dir($publicPath)) {
            mkdir($publicPath, 0755, true);
        }

        // 复制 PHP 应用文件
        $rootPath = App::getRootPath();
        $appPath = App::getAppPath();
        $configPath = $rootPath . 'config/';
        $extendPath = $rootPath . 'extend/';
        $vendorPath = $rootPath . 'vendor/';
        $routePath = $rootPath . 'route/';
        $viewPath = $rootPath . 'view/';

        // 复制应用目录
        $this->copyDirectory($appPath, $phpPath . 'app/');

        // 复制配置目录
        $this->copyDirectory($configPath, $phpPath . 'config/');

        // 复制扩展目录
        if (is_dir($extendPath)) {
            $this->copyDirectory($extendPath, $phpPath . 'extend/');
        }

        // 复制依赖目录
        $this->copyDirectory($vendorPath, $phpPath . 'vendor/');

        // 复制路由目录
        if (is_dir($routePath)) {
            $this->copyDirectory($routePath, $phpPath . 'route/');
        }

        // 复制视图目录
        if (is_dir($viewPath)) {
            $this->copyDirectory($viewPath, $phpPath . 'view/');
        }

        // 复制入口文件
        copy($rootPath . 'think', $phpPath . 'think');

        // 复制 public 目录
        $this->copyDirectory($rootPath . 'public', $publicPath);

        // 创建启动脚本
        $this->createStartupScript($phpPath, $output);

        $output->writeln('<info>PHP 应用复制成功！</info>');
    }

    /**
     * 创建启动脚本
     *
     * @param string $phpPath
     * @param Output $output
     * @return void
     */
    protected function createStartupScript($phpPath, Output $output)
    {
        $output->writeln('创建启动脚本...');

        // 创建 Windows 启动脚本
        $windowsScript = '@echo off
cd %~dp0
php think run --port=8000
';
        file_put_contents($phpPath . 'start.bat', $windowsScript);

        // 创建 Linux/Mac 启动脚本
        $unixScript = "#!/bin/bash\ncd \"\$(dirname \"\$0\")\"\nphp think run --port=8000\n";
        file_put_contents($phpPath . 'start.sh', $unixScript);
        chmod($phpPath . 'start.sh', 0755);

        $output->writeln('<info>启动脚本创建成功！</info>');
    }

    /**
     * 构建应用
     *
     * @param string $buildPath
     * @param Output $output
     * @return void
     */
    protected function buildApp($buildPath, Output $output)
    {
        $output->writeln('构建应用...');

        // 切换到构建目录
        $currentDir = getcwd();
        chdir($buildPath);

        // 安装依赖
        $output->writeln('安装依赖...');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            system('npm install');
        } else {
            // Linux/Mac
            system('npm install');
        }

        // 构建应用
        $output->writeln('构建应用...');
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            system('npm run build:win');
        } else if (strtoupper(substr(PHP_OS, 0, 3)) === 'DAR') {
            // Mac
            system('npm run build:mac');
        } else {
            // Linux
            system('npm run build:linux');
        }

        // 切回原目录
        chdir($currentDir);

        $output->writeln('<info>应用构建成功！</info>');
    }

    /**
     * 获取窗口配置
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getWindowConfig($key, $default)
    {
        $value = Config::get('native.window.' . $key, $default);

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        return $value;
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
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $dir = opendir($source);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcFile = $source . '/' . $file;
                $destFile = $destination . '/' . $file;
                if (is_dir($srcFile)) {
                    $this->copyDirectory($srcFile, $destFile);
                } else {
                    copy($srcFile, $destFile);
                }
            }
        }
        closedir($dir);
    }
}
