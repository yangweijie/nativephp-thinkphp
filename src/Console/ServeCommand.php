<?php

namespace Native\ThinkPHP\Console;

use think\App;
use think\facade\Config;
use think\facade\Env;

class ServeCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:serve';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '启动 NativePHP 开发服务器';

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
        $this->addOption('host', null, Option::VALUE_OPTIONAL, '主机地址', '127.0.0.1');
        $this->addOption('port', 'p', Option::VALUE_OPTIONAL, '端口', 8000);
        $this->addOption('dev', 'd', Option::VALUE_NONE, '开发模式');
    }

    /**
     * 处理命令
     *
     * @return int
     */
    protected function handle()
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $dev = $this->option('dev');

        $this->info('正在启动 NativePHP 开发服务器...');
        $this->info("主机: {$host}");
        $this->info("端口: {$port}");
        $this->info("模式: " . ($dev ? '开发模式' : '生产模式'));

        // 检查环境
        if (!$this->checkEnvironment()) {
            return 1;
        }

        // 准备开发环境
        $this->prepareDevEnvironment();

        // 启动 ThinkPHP 服务器
        $this->startThinkPHPServer($host, $port);

        // 启动 Electron 应用
        $this->startElectronApp($host, $port, $dev);

        return 0;
    }

    /**
     * 检查环境
     *
     * @return bool
     */
    protected function checkEnvironment()
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

        // 检查 electron 是否安装
        exec('npx electron --version', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->warn('electron 未安装，正在安装...');
            exec('npm install --save-dev electron', $output, $returnCode);
            if ($returnCode !== 0) {
                $this->error('electron 安装失败');
                return false;
            }
        }

        return true;
    }

    /**
     * 准备开发环境
     *
     * @return void
     */
    protected function prepareDevEnvironment()
    {
        $devDir = $this->app->getRuntimePath() . 'dev';

        if (!is_dir($devDir)) {
            mkdir($devDir, 0755, true);
        }

        // 创建 package.json
        $packageJson = [
            'name' => 'nativephp-dev',
            'version' => '1.0.0',
            'description' => 'NativePHP Development Environment',
            'main' => 'main.js',
            'scripts' => [
                'start' => 'electron .',
            ],
            'dependencies' => [
                'electron' => '^24.0.0',
            ],
        ];

        file_put_contents(
            $devDir . '/package.json',
            json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        // 创建 main.js
        $mainJs = <<<JS
const { app, BrowserWindow, Menu, Tray, ipcMain, dialog, shell } = require('electron');
const path = require('path');
const url = require('url');
const fs = require('fs');

// 保持对窗口对象的全局引用
let mainWindow;
let tray;

// 应用配置
const config = {
    name: '{$this->getAppName()}',
    width: 800,
    height: 600,
    minWidth: 800,
    minHeight: 600,
    icon: '{$this->getIconPath()}',
    trayIcon: '{$this->getTrayIconPath()}',
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
    mainWindow.loadURL('http://{$this->option('host')}:{$this->option('port')}');

    // 打开开发者工具
    mainWindow.webContents.openDevTools();

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

// IPC 通信
ipcMain.on('app:quit', () => {
    app.quit();
});

ipcMain.on('app:restart', () => {
    app.relaunch();
    app.exit();
});

ipcMain.on('app:focus', () => {
    if (mainWindow) {
        mainWindow.focus();
    }
});

ipcMain.on('app:hide', () => {
    if (mainWindow) {
        mainWindow.hide();
    }
});

ipcMain.on('app:show', () => {
    if (mainWindow) {
        mainWindow.show();
    }
});

ipcMain.on('app:minimize', () => {
    if (mainWindow) {
        mainWindow.minimize();
    }
});

ipcMain.on('app:maximize', () => {
    if (mainWindow) {
        mainWindow.maximize();
    }
});

ipcMain.on('app:restore', () => {
    if (mainWindow) {
        mainWindow.restore();
    }
});

ipcMain.on('dialog:open', (event, options) => {
    dialog.showOpenDialog(mainWindow, options).then(result => {
        event.reply('dialog:open-result', result);
    });
});

ipcMain.on('dialog:save', (event, options) => {
    dialog.showSaveDialog(mainWindow, options).then(result => {
        event.reply('dialog:save-result', result);
    });
});

ipcMain.on('dialog:message', (event, options) => {
    dialog.showMessageBox(mainWindow, options).then(result => {
        event.reply('dialog:message-result', result);
    });
});

ipcMain.on('shell:open-external', (event, url) => {
    shell.openExternal(url);
});
JS;

        file_put_contents($devDir . '/main.js', $mainJs);

        // 创建 preload.js
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
    // 应用操作
    app: {
        quit: () => ipcRenderer.send('app:quit'),
        restart: () => ipcRenderer.send('app:restart'),
        focus: () => ipcRenderer.send('app:focus'),
        hide: () => ipcRenderer.send('app:hide'),
        show: () => ipcRenderer.send('app:show'),
        minimize: () => ipcRenderer.send('app:minimize'),
        maximize: () => ipcRenderer.send('app:maximize'),
        restore: () => ipcRenderer.send('app:restore'),
    },
    // 对话框操作
    dialog: {
        open: (options) => {
            return new Promise((resolve) => {
                ipcRenderer.once('dialog:open-result', (event, result) => {
                    resolve(result);
                });
                ipcRenderer.send('dialog:open', options);
            });
        },
        save: (options) => {
            return new Promise((resolve) => {
                ipcRenderer.once('dialog:save-result', (event, result) => {
                    resolve(result);
                });
                ipcRenderer.send('dialog:save', options);
            });
        },
        message: (options) => {
            return new Promise((resolve) => {
                ipcRenderer.once('dialog:message-result', (event, result) => {
                    resolve(result);
                });
                ipcRenderer.send('dialog:message', options);
            });
        },
    },
    // Shell 操作
    shell: {
        openExternal: (url) => ipcRenderer.send('shell:open-external', url),
    },
});
JS;

        file_put_contents($devDir . '/preload.js', $preloadJs);

        // 安装依赖
        $this->info('正在安装开发依赖...');
        exec('cd ' . escapeshellarg($devDir) . ' && npm install', $output, $returnCode);
        if ($returnCode !== 0) {
            $this->error('依赖安装失败');
            return;
        }

        $this->info('开发环境准备完成');
    }

    /**
     * 启动 ThinkPHP 服务器
     *
     * @param string $host
     * @param int $port
     * @return void
     */
    protected function startThinkPHPServer($host, $port)
    {
        $this->info('正在启动 ThinkPHP 服务器...');

        // 使用 ThinkPHP 的 server 命令
        $command = 'php think server --host=' . escapeshellarg($host) . ' --port=' . escapeshellarg((string)$port);

        // 在后台运行服务器
        if (PHP_OS_FAMILY === 'Windows') {
            pclose(popen('start /B ' . $command, 'r'));
        } else {
            exec($command . ' > /dev/null 2>&1 &');
        }

        // 等待服务器启动
        $this->info('等待服务器启动...');
        sleep(2);
    }

    /**
     * 启动 Electron 应用
     *
     * @param string $host
     * @param int $port
     * @param bool $dev
     * @return void
     */
    protected function startElectronApp($host, $port, $dev)
    {
        $devDir = $this->app->getRuntimePath() . 'dev';

        $this->info('正在启动 Electron 应用...');

        // 启动 Electron 应用
        $command = 'cd ' . escapeshellarg($devDir) . ' && npx electron .';

        if ($dev) {
            // 在开发模式下，直接在前台运行 Electron
            passthru($command);
        } else {
            // 在生产模式下，在后台运行 Electron
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen('start ' . $command, 'r'));
            } else {
                exec($command . ' > /dev/null 2>&1 &');
            }
        }
    }

    /**
     * 获取应用名称
     *
     * @return string
     */
    protected function getAppName()
    {
        return Config::get('native.name', Env::get('NATIVEPHP_APP_NAME', 'NativePHP'));
    }

    /**
     * 获取图标路径
     *
     * @return string
     */
    protected function getIconPath()
    {
        $iconPath = $this->app->getRootPath() . 'resources/native/icons/icon.png';
        if (file_exists($iconPath)) {
            return $iconPath;
        }

        return '';
    }

    /**
     * 获取托盘图标路径
     *
     * @return string
     */
    protected function getTrayIconPath()
    {
        $trayIconPath = $this->app->getRootPath() . 'resources/native/tray-icons/tray-icon.png';
        if (file_exists($trayIconPath)) {
            return $trayIconPath;
        }

        return '';
    }
}
