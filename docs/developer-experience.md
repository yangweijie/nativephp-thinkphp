# NativePHP/ThinkPHP 开发者体验提升

## 开发工具增强

### 命令行工具
增强命令行工具，提供更多功能和更好的用户体验。

```php
// 命令行工具基类
abstract class NativeCommand extends \think\console\Command
{
    protected $app;
    
    public function __construct($app)
    {
        parent::__construct();
        $this->app = $app;
    }
    
    protected function logo()
    {
        $this->output->writeln('');
        $this->output->writeln('  <fg=green>NativePHP</> for <fg=blue>ThinkPHP</>');
        $this->output->writeln('  <fg=yellow>v' . $this->app->config->get('native.version', '1.0.0') . '</>');
        $this->output->writeln('');
    }
    
    protected function success($message)
    {
        $this->output->writeln('  <fg=green>✓</> ' . $message);
    }
    
    protected function error($message)
    {
        $this->output->writeln('  <fg=red>✗</> ' . $message);
    }
    
    protected function info($message)
    {
        $this->output->writeln('  <fg=blue>ℹ</> ' . $message);
    }
    
    protected function warning($message)
    {
        $this->output->writeln('  <fg=yellow>⚠</> ' . $message);
    }
    
    protected function task($title, $callback)
    {
        $this->output->write('  <fg=blue>▶</> ' . $title . ' ... ');
        
        try {
            $result = $callback();
            $this->output->writeln('<fg=green>完成</>');
            return $result;
        } catch (\Exception $e) {
            $this->output->writeln('<fg=red>失败</>');
            $this->output->writeln('    <fg=red>' . $e->getMessage() . '</>');
            return false;
        }
    }
}

// 新建应用命令
class NewCommand extends NativeCommand
{
    protected $name = 'native:new';
    protected $description = '创建新的 NativePHP 应用';
    
    protected function configure()
    {
        $this->addArgument('name', \think\console\Input\Argument::REQUIRED, '应用名称');
        $this->addOption('path', null, \think\console\Input\Option::VALUE_OPTIONAL, '应用路径', './');
    }
    
    protected function execute(\think\console\Input $input, \think\console\Output $output)
    {
        $this->logo();
        
        $name = $input->getArgument('name');
        $path = $input->getOption('path');
        $appPath = rtrim($path, '/') . '/' . $name;
        
        $this->info('创建新的 NativePHP 应用: ' . $name);
        
        // 检查目录是否存在
        if (is_dir($appPath)) {
            $this->error('目录已存在: ' . $appPath);
            return 1;
        }
        
        // 创建应用目录
        $this->task('创建应用目录', function () use ($appPath) {
            mkdir($appPath, 0755, true);
            return true;
        });
        
        // 创建应用结构
        $this->task('创建应用结构', function () use ($appPath) {
            mkdir($appPath . '/app', 0755, true);
            mkdir($appPath . '/config', 0755, true);
            mkdir($appPath . '/public', 0755, true);
            mkdir($appPath . '/runtime', 0755, true);
            mkdir($appPath . '/view', 0755, true);
            return true;
        });
        
        // 创建配置文件
        $this->task('创建配置文件', function () use ($appPath, $name) {
            $config = [
                'name' => $name,
                'version' => '1.0.0',
                'app_id' => 'com.nativephp.' . strtolower($name),
                'window' => [
                    'width' => 800,
                    'height' => 600,
                    'title' => $name,
                ],
            ];
            
            file_put_contents(
                $appPath . '/config/native.php',
                '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($config, true) . ';'
            );
            
            return true;
        });
        
        // 创建示例控制器
        $this->task('创建示例控制器', function () use ($appPath) {
            // ...
            return true;
        });
        
        // 创建示例视图
        $this->task('创建示例视图', function () use ($appPath) {
            // ...
            return true;
        });
        
        // 创建 composer.json
        $this->task('创建 composer.json', function () use ($appPath, $name) {
            $composer = [
                'name' => strtolower($name),
                'description' => 'A NativePHP application',
                'type' => 'project',
                'require' => [
                    'php' => '^8.1',
                    'topthink/framework' => '^8.1',
                    'nativephp/thinkphp' => '^1.0',
                ],
                'autoload' => [
                    'psr-4' => [
                        'app\\' => 'app/',
                    ],
                ],
                'scripts' => [
                    'post-install-cmd' => [
                        'php think native:init',
                    ],
                ],
                'config' => [
                    'sort-packages' => true,
                ],
            ];
            
            file_put_contents(
                $appPath . '/composer.json',
                json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            
            return true;
        });
        
        // 安装依赖
        $this->task('安装依赖', function () use ($appPath) {
            chdir($appPath);
            system('composer install');
            return true;
        });
        
        $this->success('NativePHP 应用创建成功!');
        $this->info('应用目录: ' . $appPath);
        $this->info('使用以下命令启动应用:');
        $this->output->writeln('');
        $this->output->writeln('  <fg=yellow>cd ' . $appPath . '</>');
        $this->output->writeln('  <fg=yellow>php think native:serve</>');
        $this->output->writeln('');
        
        return 0;
    }
}

// 开发服务器命令
class ServeCommand extends NativeCommand
{
    protected $name = 'native:serve';
    protected $description = '启动 NativePHP 开发服务器';
    
    protected function configure()
    {
        $this->addOption('port', 'p', \think\console\Input\Option::VALUE_OPTIONAL, '端口号', 8000);
        $this->addOption('host', null, \think\console\Input\Option::VALUE_OPTIONAL, '主机名', 'localhost');
        $this->addOption('dev-tools', 'd', \think\console\Input\Option::VALUE_NONE, '启用开发者工具');
    }
    
    protected function execute(\think\console\Input $input, \think\console\Output $output)
    {
        $this->logo();
        
        $port = $input->getOption('port');
        $host = $input->getOption('host');
        $devTools = $input->getOption('dev-tools');
        
        $this->info('启动 NativePHP 开发服务器');
        $this->info('地址: http://' . $host . ':' . $port);
        
        // 设置环境变量
        putenv('NATIVEPHP_DEV_SERVER_PORT=' . $port);
        putenv('NATIVEPHP_DEV_SERVER_HOSTNAME=' . $host);
        putenv('NATIVEPHP_DEVELOPER_SHOW_DEVTOOLS=' . ($devTools ? 'true' : 'false'));
        
        // 启动 PHP 服务器
        $phpServer = new \Symfony\Component\Process\Process([
            PHP_BINARY,
            'think',
            'run',
            '--port=' . $port,
            '--host=' . $host,
        ]);
        
        $phpServer->setTimeout(null);
        $phpServer->start();
        
        $this->success('PHP 服务器已启动');
        
        // 启动 Electron
        $electronPath = $this->findElectron();
        if (!$electronPath) {
            $this->error('未找到 Electron，请先安装 Electron');
            $this->info('npm install -g electron');
            return 1;
        }
        
        $this->info('使用 Electron: ' . $electronPath);
        
        // 创建临时 Electron 应用
        $tempDir = sys_get_temp_dir() . '/nativephp-' . uniqid();
        mkdir($tempDir, 0755, true);
        
        $this->task('创建临时 Electron 应用', function () use ($tempDir, $port, $host, $devTools) {
            // 创建 package.json
            $packageJson = [
                'name' => 'nativephp-dev',
                'version' => '1.0.0',
                'main' => 'main.js',
            ];
            
            file_put_contents(
                $tempDir . '/package.json',
                json_encode($packageJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            );
            
            // 创建 main.js
            $mainJs = <<<JS
const { app, BrowserWindow } = require('electron');

let mainWindow;

function createWindow() {
    mainWindow = new BrowserWindow({
        width: 800,
        height: 600,
        webPreferences: {
            nodeIntegration: false,
            contextIsolation: true,
        }
    });

    mainWindow.loadURL('http://{$host}:{$port}');
    
    if ({$devTools}) {
        mainWindow.webContents.openDevTools();
    }

    mainWindow.on('closed', function() {
        mainWindow = null;
    });
}

app.on('ready', createWindow);

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
JS;
            
            file_put_contents($tempDir . '/main.js', $mainJs);
            
            return true;
        });
        
        // 启动 Electron
        $electron = new \Symfony\Component\Process\Process([
            $electronPath,
            $tempDir,
        ]);
        
        $electron->setTimeout(null);
        $electron->start();
        
        $this->success('Electron 已启动');
        
        // 等待进程结束
        $this->info('按 Ctrl+C 停止服务器');
        
        while ($electron->isRunning() && $phpServer->isRunning()) {
            usleep(1000000); // 1 秒
        }
        
        // 清理
        $this->task('清理临时文件', function () use ($tempDir) {
            $this->removeDirectory($tempDir);
            return true;
        });
        
        if ($phpServer->isRunning()) {
            $phpServer->stop();
        }
        
        if ($electron->isRunning()) {
            $electron->stop();
        }
        
        $this->success('服务器已停止');
        
        return 0;
    }
    
    protected function findElectron()
    {
        $paths = [
            'electron',
            'node_modules/.bin/electron',
            'node_modules/electron/cli.js',
        ];
        
        foreach ($paths as $path) {
            $process = new \Symfony\Component\Process\Process(['which', $path]);
            $process->run();
            
            if ($process->isSuccessful()) {
                return trim($process->getOutput());
            }
        }
        
        return null;
    }
    
    protected function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object == '.' || $object == '..') {
                continue;
            }
            
            $path = $dir . '/' . $object;
            
            if (is_dir($path)) {
                $this->removeDirectory($path);
            } else {
                unlink($path);
            }
        }
        
        rmdir($dir);
    }
}
```

### 开发者工具
集成开发者工具，提供调试和性能分析功能。

- 应用检查器
- 网络监控
- 性能分析
- 日志查看器

## 开发流程优化

### 热重载
实现热重载功能，提高开发效率。

```php
// 热重载服务
class HotReloadService
{
    protected $app;
    protected $watcher;
    protected $server;
    protected $clients = [];
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function start()
    {
        // 启动文件监视器
        $this->startWatcher();
        
        // 启动 WebSocket 服务器
        $this->startServer();
    }
    
    protected function startWatcher()
    {
        $paths = [
            $this->app->getAppPath(),
            $this->app->getConfigPath(),
            $this->app->getRootPath() . 'view',
            $this->app->getRootPath() . 'public',
        ];
        
        $this->watcher = new \Symfony\Component\Finder\Finder();
        $this->watcher->files()
            ->in($paths)
            ->name('*.php')
            ->name('*.html')
            ->name('*.js')
            ->name('*.css');
        
        $lastRun = time();
        $hashes = [];
        
        // 定期检查文件变化
        while (true) {
            $changed = false;
            
            foreach ($this->watcher as $file) {
                $path = $file->getRealPath();
                $hash = md5_file($path);
                
                if (!isset($hashes[$path]) || $hashes[$path] !== $hash) {
                    $hashes[$path] = $hash;
                    $changed = true;
                }
            }
            
            if ($changed && time() - $lastRun > 1) {
                $this->notifyClients();
                $lastRun = time();
            }
            
            usleep(1000000); // 1 秒
        }
    }
    
    protected function startServer()
    {
        $this->server = new \Ratchet\Server\IoServer(
            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    new class($this) implements \Ratchet\MessageComponentInterface {
                        protected $service;
                        
                        public function __construct($service)
                        {
                            $this->service = $service;
                        }
                        
                        public function onOpen(\Ratchet\ConnectionInterface $conn)
                        {
                            $this->service->addClient($conn);
                        }
                        
                        public function onClose(\Ratchet\ConnectionInterface $conn)
                        {
                            $this->service->removeClient($conn);
                        }
                        
                        public function onError(\Ratchet\ConnectionInterface $conn, \Exception $e)
                        {
                            $conn->close();
                        }
                        
                        public function onMessage(\Ratchet\ConnectionInterface $from, $msg)
                        {
                            // 不需要处理客户端消息
                        }
                    }
                )
            ),
            8080
        );
        
        $this->server->run();
    }
    
    public function addClient(\Ratchet\ConnectionInterface $client)
    {
        $this->clients[] = $client;
    }
    
    public function removeClient(\Ratchet\ConnectionInterface $client)
    {
        $index = array_search($client, $this->clients);
        if ($index !== false) {
            unset($this->clients[$index]);
        }
    }
    
    protected function notifyClients()
    {
        foreach ($this->clients as $client) {
            $client->send(json_encode(['type' => 'reload']));
        }
    }
}

// 热重载中间件
class HotReloadMiddleware
{
    public function handle($request, \Closure $next)
    {
        $response = $next($request);
        
        // 只处理 HTML 响应
        if (strpos($response->getHeader('Content-Type'), 'text/html') !== false) {
            $content = $response->getContent();
            
            // 注入热重载脚本
            $script = <<<HTML
<script>
(function() {
    var socket = new WebSocket('ws://localhost:8080');
    socket.onmessage = function(event) {
        var data = JSON.parse(event.data);
        if (data.type === 'reload') {
            window.location.reload();
        }
    };
    socket.onclose = function() {
        console.log('Hot reload connection closed. Reconnecting...');
        setTimeout(function() {
            window.location.reload();
        }, 2000);
    };
})();
</script>
HTML;
            
            $content = str_replace('</body>', $script . '</body>', $content);
            $response->content($content);
        }
        
        return $response;
    }
}
```

### 自动化测试
提供自动化测试工具，简化测试流程。

- 单元测试
- 集成测试
- UI 测试
- 端到端测试

## 调试工具

### 日志增强
增强日志功能，提供更详细的调试信息。

```php
// 日志管理器
class LogManager
{
    protected $app;
    protected $handlers = [];
    
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function channel($name = 'default')
    {
        if (!isset($this->handlers[$name])) {
            $this->handlers[$name] = $this->createHandler($name);
        }
        
        return $this->handlers[$name];
    }
    
    protected function createHandler($name)
    {
        $config = $this->app->config->get('log.' . $name, []);
        $type = $config['type'] ?? 'file';
        
        switch ($type) {
            case 'file':
                return new FileLogHandler($this->app, $name, $config);
            case 'database':
                return new DatabaseLogHandler($this->app, $name, $config);
            case 'syslog':
                return new SyslogLogHandler($this->app, $name, $config);
            default:
                throw new \Exception("Unsupported log handler type: $type");
        }
    }
}

// 文件日志处理器
class FileLogHandler
{
    protected $app;
    protected $name;
    protected $config;
    protected $file;
    
    public function __construct($app, $name, $config)
    {
        $this->app = $app;
        $this->name = $name;
        $this->config = $config;
        
        $path = $config['path'] ?? $app->getRuntimePath() . 'logs/';
        $file = $path . $name . '.log';
        
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        
        $this->file = $file;
    }
    
    public function log($level, $message, $context = [])
    {
        $entry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'level' => $level,
            'message' => $this->interpolate($message, $context),
            'context' => $context,
        ];
        
        $line = json_encode($entry) . PHP_EOL;
        file_put_contents($this->file, $line, FILE_APPEND);
        
        return $this;
    }
    
    public function debug($message, $context = [])
    {
        return $this->log('debug', $message, $context);
    }
    
    public function info($message, $context = [])
    {
        return $this->log('info', $message, $context);
    }
    
    public function warning($message, $context = [])
    {
        return $this->log('warning', $message, $context);
    }
    
    public function error($message, $context = [])
    {
        return $this->log('error', $message, $context);
    }
    
    protected function interpolate($message, $context)
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if (is_string($val) || is_numeric($val)) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        
        return strtr($message, $replace);
    }
}
```

### 错误处理
增强错误处理，提供更友好的错误信息和调试工具。

- 错误捕获和报告
- 异常处理
- 崩溃报告
- 错误日志

## 文档工具

### 自动文档生成
实现自动文档生成，简化文档维护。

```php
// 文档生成器
class DocumentationGenerator
{
    protected $app;
    protected $config;
    
    public function __construct($app, $config = [])
    {
        $this->app = $app;
        $this->config = $config;
    }
    
    public function generate()
    {
        $outputPath = $this->config['output_path'] ?? $this->app->getRootPath() . 'docs/';
        
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0755, true);
        }
        
        // 生成 API 文档
        $this->generateApiDocs($outputPath);
        
        // 生成配置文档
        $this->generateConfigDocs($outputPath);
        
        // 生成命令行文档
        $this->generateCommandDocs($outputPath);
        
        // 生成 README
        $this->generateReadme($outputPath);
        
        return $outputPath;
    }
    
    protected function generateApiDocs($outputPath)
    {
        $apiPath = $outputPath . 'api/';
        
        if (!is_dir($apiPath)) {
            mkdir($apiPath, 0755, true);
        }
        
        // 获取所有 Facade 类
        $facadesPath = $this->app->getRootPath() . 'vendor/nativephp/thinkphp/src/Facades/';
        $facades = glob($facadesPath . '*.php');
        
        foreach ($facades as $facade) {
            $className = basename($facade, '.php');
            $this->generateFacadeDoc($className, $apiPath);
        }
        
        // 生成 API 索引
        $this->generateApiIndex($apiPath);
    }
    
    protected function generateFacadeDoc($className, $apiPath)
    {
        $facadeClass = "\\Native\\ThinkPHP\\Facades\\{$className}";
        $reflector = new \ReflectionClass($facadeClass);
        
        // 获取实际类
        $facadeAccessor = $reflector->getMethod('getFacadeAccessor')->invoke(null);
        $actualClass = $this->app->make($facadeAccessor);
        $actualReflector = new \ReflectionClass($actualClass);
        
        $doc = "# {$className} API\n\n";
        
        // 类描述
        $classDoc = $actualReflector->getDocComment();
        if ($classDoc) {
            $doc .= $this->parseDocComment($classDoc) . "\n\n";
        }
        
        // 方法列表
        $doc .= "## 方法\n\n";
        
        $methods = $actualReflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach ($methods as $method) {
            if ($method->isConstructor() || $method->isDestructor()) {
                continue;
            }
            
            $doc .= "### " . $method->getName() . "(";
            
            // 参数
            $parameters = $method->getParameters();
            $paramDocs = [];
            
            foreach ($parameters as $parameter) {
                $paramDoc = '';
                
                if ($parameter->hasType()) {
                    $paramDoc .= $parameter->getType() . ' ';
                }
                
                $paramDoc .= '$' . $parameter->getName();
                
                if ($parameter->isDefaultValueAvailable()) {
                    $defaultValue = $parameter->getDefaultValue();
                    if (is_string($defaultValue)) {
                        $defaultValue = "'{$defaultValue}'";
                    } elseif (is_array($defaultValue)) {
                        $defaultValue = '[]';
                    } elseif (is_null($defaultValue)) {
                        $defaultValue = 'null';
                    } elseif (is_bool($defaultValue)) {
                        $defaultValue = $defaultValue ? 'true' : 'false';
                    }
                    
                    $paramDoc .= ' = ' . $defaultValue;
                }
                
                $paramDocs[] = $paramDoc;
            }
            
            $doc .= implode(', ', $paramDocs) . ")\n\n";
            
            // 方法描述
            $methodDoc = $method->getDocComment();
            if ($methodDoc) {
                $doc .= $this->parseDocComment($methodDoc) . "\n\n";
            }
            
            // 返回值
            if ($method->hasReturnType()) {
                $doc .= "**返回值**: " . $method->getReturnType() . "\n\n";
            }
            
            // 示例
            $doc .= "**示例**:\n\n```php\n";
            $doc .= "use Native\\ThinkPHP\\Facades\\{$className};\n\n";
            $doc .= "\${$className}::" . $method->getName() . "(";
            
            $exampleParams = [];
            foreach ($parameters as $parameter) {
                if ($parameter->isDefaultValueAvailable()) {
                    continue;
                }
                
                if ($parameter->hasType()) {
                    $type = $parameter->getType();
                    if ($type == 'string') {
                        $exampleParams[] = "'example'";
                    } elseif ($type == 'int' || $type == 'float') {
                        $exampleParams[] = "1";
                    } elseif ($type == 'bool') {
                        $exampleParams[] = "true";
                    } elseif ($type == 'array') {
                        $exampleParams[] = "[]";
                    } else {
                        $exampleParams[] = "null";
                    }
                } else {
                    $exampleParams[] = "null";
                }
            }
            
            $doc .= implode(', ', $exampleParams) . ");\n";
            $doc .= "```\n\n";
        }
        
        file_put_contents($apiPath . $className . '.md', $doc);
    }
    
    protected function parseDocComment($comment)
    {
        $comment = preg_replace('#^\s*/\*+\s*#', '', $comment);
        $comment = preg_replace('#\s*\*+/\s*$#', '', $comment);
        $comment = preg_replace('#^\s*\*\s?#m', '', $comment);
        
        return trim($comment);
    }
    
    protected function generateApiIndex($apiPath)
    {
        $index = "# API 文档\n\n";
        $index .= "## Facades\n\n";
        
        $files = glob($apiPath . '*.md');
        sort($files);
        
        foreach ($files as $file) {
            $name = basename($file, '.md');
            $index .= "- [{$name}]({$name}.md)\n";
        }
        
        file_put_contents($apiPath . 'README.md', $index);
    }
    
    protected function generateConfigDocs($outputPath)
    {
        // ...
    }
    
    protected function generateCommandDocs($outputPath)
    {
        // ...
    }
    
    protected function generateReadme($outputPath)
    {
        // ...
    }
}
```

### 示例代码生成
提供示例代码生成工具，帮助开发者快速上手。

- 代码片段生成
- 示例应用生成
- 模板代码生成
