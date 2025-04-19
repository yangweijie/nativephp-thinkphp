<?php

namespace Native\ThinkPHP\Commands;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\App;
use think\facade\Config;

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
    protected $description = '启动 NativePHP 应用';

    /**
     * 执行命令
     *
     * @param Input $input
     * @param Output $output
     * @return int|null
     */
    protected function execute(Input $input, Output $output)
    {
        $output->writeln('正在启动 NativePHP 应用...');

        // 获取配置
        $port = Config::get('native.dev_server.port', 8000);
        $hostname = Config::get('native.dev_server.hostname', 'localhost');

        // 启动 ThinkPHP 服务器
        $output->writeln("<info>启动 ThinkPHP 服务器: http://{$hostname}:{$port}</info>");
        $this->startThinkPHPServer($hostname, $port, $output);

        // 启动 Electron 应用
        $output->writeln('<info>启动 Electron 应用...</info>');
        $this->startElectronApp($hostname, $port, $output);

        $output->writeln('<info>NativePHP 应用已启动！</info>');

        return 0;
    }

    /**
     * 启动 ThinkPHP 服务器
     *
     * @param string $hostname
     * @param int $port
     * @param Output $output
     * @return void
     */
    protected function startThinkPHPServer($hostname, $port, Output $output)
    {
        // 使用 ThinkPHP 的 think run 命令
        $command = PHP_BINARY . ' think run --host=' . $hostname . ' --port=' . $port;

        // 在后台运行服务器
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            pclose(popen("start /B {$command}", 'r'));
        } else {
            // Linux/Mac
            exec("{$command} > /dev/null 2>&1 &");
        }

        // 等待服务器启动
        $output->writeln("<info>等待 ThinkPHP 服务器启动...</info>");
        sleep(2);

        // 检查服务器是否启动
        $tries = 0;
        $maxTries = 10;
        $serverStarted = false;

        while ($tries < $maxTries) {
            try {
                $ch = curl_init("http://{$hostname}:{$port}");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 1);
                curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode >= 200 && $httpCode < 500) {
                    $serverStarted = true;
                    break;
                }
            } catch (\Exception $e) {
                // 忽略异常
            }

            $tries++;
            sleep(1);
        }

        if ($serverStarted) {
            $output->writeln("<info>ThinkPHP 服务器已启动: http://{$hostname}:{$port}</info>");
        } else {
            $output->writeln("<error>ThinkPHP 服务器启动失败，请手动启动: php think run --host={$hostname} --port={$port}</error>");
        }
    }

    /**
     * 启动 Electron 应用
     *
     * @param string $hostname
     * @param int $port
     * @param Output $output
     * @return void
     */
    protected function startElectronApp($hostname, $port, Output $output)
    {
        // 这里将实现启动 Electron 应用的逻辑
        // 在实际实现中，需要使用 Electron 的 API

        // 示例：创建一个简单的 Electron 应用
        $electronAppPath = App::getRuntimePath() . 'native/';

        if (!is_dir($electronAppPath)) {
            mkdir($electronAppPath, 0755, true);
        }

        // 创建 package.json
        $packageJson = [
            'name' => Config::get('native.name', 'NativePHP'),
            'version' => Config::get('native.version', '1.0.0'),
            'description' => 'NativePHP Application',
            'main' => 'main.js',
            'scripts' => [
                'start' => 'electron .'
            ],
            'dependencies' => [
                'electron' => '^22.0.0',
                'electron-store' => '^8.1.0',
                'electron-updater' => '^5.3.0'
            ]
        ];

        file_put_contents(
            $electronAppPath . 'package.json',
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
            $mainJs = str_replace('title: \'\',', 'title: \'' . $this->getWindowConfig('title', Config::get('native.name', 'NativePHP')) . '\',', $mainJs);
            $mainJs = str_replace('http://localhost:8000', 'http://{$hostname}:{$port}', $mainJs);
        } else {
            // 使用基本的 main.js 模板
            $mainJs = <<<JS
const { app, BrowserWindow } = require('electron');
const path = require('path');

// 保持对窗口对象的全局引用，如果不这么做，当 JavaScript 对象被垃圾回收时，窗口会自动关闭
let mainWindow;

function createWindow() {
    // 创建浏览器窗口
    mainWindow = new BrowserWindow({
        width: {$this->getWindowConfig('width', 800)},
        height: {$this->getWindowConfig('height', 600)},
        minWidth: {$this->getWindowConfig('min_width', 400)},
        minHeight: {$this->getWindowConfig('min_height', 400)},
        maxWidth: {$this->getWindowConfig('max_width', 'null')},
        maxHeight: {$this->getWindowConfig('max_height', 'null')},
        resizable: {$this->getWindowConfig('resizable', 'true')},
        fullscreen: {$this->getWindowConfig('fullscreen', 'false')},
        title: '{$this->getWindowConfig('title', Config::get('native.name', 'NativePHP'))}',
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
            preload: path.join(__dirname, 'preload.js')
        }
    });

    // 加载应用的 URL
    mainWindow.loadURL('http://{$hostname}:{$port}');

    // 当窗口关闭时调用的方法
    mainWindow.on('closed', function() {
        // 取消引用窗口对象，如果你的应用支持多窗口，通常你会将窗口存储在一个数组中，
        // 在这个时候你应该删除相应的元素
        mainWindow = null;
    });
}

// 当 Electron 完成初始化并准备创建浏览器窗口时调用此方法
app.on('ready', createWindow);

// 当所有窗口关闭时退出应用
app.on('window-all-closed', function() {
    // 在 macOS 上，除非用户用 Cmd + Q 确定地退出，
    // 否则绝大部分应用及其菜单栏会保持激活
    if (process.platform !== 'darwin') {
        app.quit();
    }
});

app.on('activate', function() {
    // 在 macOS 上，当点击 dock 图标并且没有其他窗口打开时，
    // 通常在应用程序中重新创建一个窗口
    if (mainWindow === null) {
        createWindow();
    }
});
JS;
        }

        file_put_contents($electronAppPath . 'main.js', $mainJs);

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

        file_put_contents($electronAppPath . 'preload.js', $preloadJs);

        // 安装依赖并启动 Electron 应用
        $output->writeln('<info>安装 Electron 依赖...</info>');

        // 切换到 Electron 应用目录
        $currentDir = getcwd();
        chdir($electronAppPath);

        // 安装依赖
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            pclose(popen('npm install', 'r'));

            // 启动 Electron 应用
            pclose(popen('start /B npm start', 'r'));
        } else {
            // Linux/Mac
            exec('npm install > /dev/null 2>&1');

            // 启动 Electron 应用
            exec('npm start > /dev/null 2>&1 &');
        }

        // 切回原目录
        chdir($currentDir);
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
}
