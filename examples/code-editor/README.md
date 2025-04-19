# 代码编辑器示例

这个示例展示了如何使用 NativePHP for ThinkPHP 创建一个轻量级的代码编辑器应用。

## 功能

- 文件打开、保存和编辑
- 语法高亮和代码格式化
- 项目管理和文件树
- 集成终端
- Git 集成
- 插件系统
- 主题和自定义设置
- 代码补全和提示
- 搜索和替换
- 多窗口和分屏编辑

## 文件结构

- `app/controller/` - 控制器目录
  - `Index.php` - 主控制器
  - `Editor.php` - 编辑器控制器
  - `Project.php` - 项目控制器
  - `Terminal.php` - 终端控制器
  - `Git.php` - Git 控制器
  - `Setting.php` - 设置控制器
  - `Plugin.php` - 插件控制器
- `app/model/` - 模型目录
  - `Project.php` - 项目模型
  - `File.php` - 文件模型
  - `Setting.php` - 设置模型
  - `Plugin.php` - 插件模型
- `app/service/` - 服务目录
  - `EditorService.php` - 编辑器服务
  - `ProjectService.php` - 项目服务
  - `TerminalService.php` - 终端服务
  - `GitService.php` - Git 服务
  - `SettingService.php` - 设置服务
  - `PluginService.php` - 插件服务
  - `SyntaxService.php` - 语法服务
- `view/` - 视图目录
  - `index/` - 主视图
  - `editor/` - 编辑器视图
  - `project/` - 项目视图
  - `terminal/` - 终端视图
  - `git/` - Git 视图
  - `setting/` - 设置视图
  - `plugin/` - 插件视图
- `public/static/` - 静态资源目录
  - `css/` - CSS 样式
  - `js/` - JavaScript 脚本
  - `lib/` - 第三方库
  - `themes/` - 主题文件
  - `plugins/` - 插件目录

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 技术实现

### 文件操作

使用 NativePHP 的 FileSystem 和 Dialog 类实现文件操作。

```php
// 在 EditorService.php 中
public function openFile()
{
    $file = Dialog::openFile([
        'title' => '打开文件',
        'filters' => [
            ['name' => '所有文件', 'extensions' => ['*']],
            ['name' => 'JavaScript', 'extensions' => ['js', 'jsx', 'ts', 'tsx']],
            ['name' => 'HTML', 'extensions' => ['html', 'htm']],
            ['name' => 'CSS', 'extensions' => ['css', 'scss', 'less']],
            ['name' => 'PHP', 'extensions' => ['php']],
        ],
    ]);
    
    if (!$file) {
        return null;
    }
    
    $content = FileSystem::read($file);
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    
    return [
        'path' => $file,
        'name' => basename($file),
        'content' => $content,
        'extension' => $extension,
        'language' => $this->getLanguageFromExtension($extension),
    ];
}

public function saveFile($path, $content)
{
    if (empty($path)) {
        $path = Dialog::saveFile([
            'title' => '保存文件',
            'filters' => [
                ['name' => '所有文件', 'extensions' => ['*']],
                ['name' => 'JavaScript', 'extensions' => ['js', 'jsx', 'ts', 'tsx']],
                ['name' => 'HTML', 'extensions' => ['html', 'htm']],
                ['name' => 'CSS', 'extensions' => ['css', 'scss', 'less']],
                ['name' => 'PHP', 'extensions' => ['php']],
            ],
        ]);
        
        if (!$path) {
            return false;
        }
    }
    
    FileSystem::write($path, $content);
    
    return [
        'path' => $path,
        'name' => basename($path),
    ];
}
```

### 项目管理

使用 NativePHP 的 FileSystem 和 Settings 类实现项目管理。

```php
// 在 ProjectService.php 中
public function openProject()
{
    $directory = Dialog::openDirectory([
        'title' => '打开项目',
    ]);
    
    if (!$directory) {
        return null;
    }
    
    $project = [
        'path' => $directory,
        'name' => basename($directory),
        'files' => $this->scanDirectory($directory),
    ];
    
    // 保存到最近项目列表
    $recentProjects = Settings::get('projects.recent', []);
    
    // 检查是否已存在
    foreach ($recentProjects as $key => $recentProject) {
        if ($recentProject['path'] === $directory) {
            unset($recentProjects[$key]);
            break;
        }
    }
    
    // 添加到最前面
    array_unshift($recentProjects, [
        'path' => $directory,
        'name' => basename($directory),
        'lastOpened' => date('Y-m-d H:i:s'),
    ]);
    
    // 限制最多保存 10 个
    $recentProjects = array_slice($recentProjects, 0, 10);
    
    Settings::set('projects.recent', $recentProjects);
    
    return $project;
}

protected function scanDirectory($directory, $relativePath = '')
{
    $result = [];
    $files = FileSystem::files($directory);
    $directories = FileSystem::directories($directory);
    
    // 排除一些目录
    $excludeDirs = ['.git', 'node_modules', 'vendor'];
    
    foreach ($directories as $dir) {
        $dirName = basename($dir);
        
        if (in_array($dirName, $excludeDirs)) {
            continue;
        }
        
        $relPath = $relativePath ? $relativePath . '/' . $dirName : $dirName;
        
        $result[] = [
            'path' => $dir,
            'name' => $dirName,
            'type' => 'directory',
            'relativePath' => $relPath,
            'children' => $this->scanDirectory($dir, $relPath),
        ];
    }
    
    foreach ($files as $file) {
        $fileName = basename($file);
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $relPath = $relativePath ? $relativePath . '/' . $fileName : $fileName;
        
        $result[] = [
            'path' => $file,
            'name' => $fileName,
            'type' => 'file',
            'extension' => $extension,
            'relativePath' => $relPath,
            'language' => $this->getLanguageFromExtension($extension),
        ];
    }
    
    return $result;
}
```

### 集成终端

使用 NativePHP 的 Process 类实现集成终端。

```php
// 在 TerminalService.php 中
public function createTerminal($directory)
{
    $shell = $this->getDefaultShell();
    
    $process = Process::start($shell, [
        'cwd' => $directory,
        'env' => $this->getEnvironmentVariables(),
    ]);
    
    return [
        'id' => $process->id,
        'pid' => $process->pid,
    ];
}

public function sendCommand($id, $command)
{
    $process = Process::find($id);
    
    if (!$process) {
        return false;
    }
    
    $process->write($command . PHP_EOL);
    
    return true;
}

public function getOutput($id)
{
    $process = Process::find($id);
    
    if (!$process) {
        return null;
    }
    
    return $process->read();
}

public function terminateProcess($id)
{
    $process = Process::find($id);
    
    if (!$process) {
        return false;
    }
    
    $process->terminate();
    
    return true;
}

protected function getDefaultShell()
{
    if (PHP_OS_FAMILY === 'Windows') {
        return 'cmd.exe';
    }
    
    return '/bin/bash';
}

protected function getEnvironmentVariables()
{
    $env = [];
    
    // 复制当前环境变量
    foreach ($_ENV as $key => $value) {
        $env[$key] = $value;
    }
    
    // 添加一些自定义环境变量
    $env['TERM'] = 'xterm-256color';
    
    return $env;
}
```

### Git 集成

使用 NativePHP 的 Process 类实现 Git 集成。

```php
// 在 GitService.php 中
public function getStatus($directory)
{
    $process = Process::run('git status --porcelain', [
        'cwd' => $directory,
    ]);
    
    if ($process->exitCode !== 0) {
        return [
            'success' => false,
            'message' => $process->errorOutput,
        ];
    }
    
    $output = $process->output;
    $files = [];
    
    foreach (explode(PHP_EOL, $output) as $line) {
        if (empty($line)) {
            continue;
        }
        
        $status = substr($line, 0, 2);
        $file = substr($line, 3);
        
        $files[] = [
            'file' => $file,
            'status' => $this->parseGitStatus($status),
        ];
    }
    
    return [
        'success' => true,
        'files' => $files,
    ];
}

public function commit($directory, $message)
{
    $process = Process::run('git commit -m ' . escapeshellarg($message), [
        'cwd' => $directory,
    ]);
    
    return [
        'success' => $process->exitCode === 0,
        'output' => $process->output,
        'error' => $process->errorOutput,
    ];
}

public function push($directory)
{
    $process = Process::run('git push', [
        'cwd' => $directory,
    ]);
    
    return [
        'success' => $process->exitCode === 0,
        'output' => $process->output,
        'error' => $process->errorOutput,
    ];
}

public function pull($directory)
{
    $process = Process::run('git pull', [
        'cwd' => $directory,
    ]);
    
    return [
        'success' => $process->exitCode === 0,
        'output' => $process->output,
        'error' => $process->errorOutput,
    ];
}

protected function parseGitStatus($status)
{
    $statusMap = [
        'M' => 'modified',
        'A' => 'added',
        'D' => 'deleted',
        'R' => 'renamed',
        'C' => 'copied',
        'U' => 'updated',
        '?' => 'untracked',
        '!' => 'ignored',
    ];
    
    $index = $status[0];
    $workTree = $status[1];
    
    if ($index === ' ' && $workTree === 'M') {
        return 'modified';
    }
    
    if ($index === 'M' && $workTree === ' ') {
        return 'staged';
    }
    
    if ($index === 'M' && $workTree === 'M') {
        return 'modified-staged';
    }
    
    if ($index === 'A' && $workTree === ' ') {
        return 'added';
    }
    
    if ($index === 'D' && $workTree === ' ') {
        return 'deleted';
    }
    
    if ($index === 'R' && $workTree === ' ') {
        return 'renamed';
    }
    
    if ($index === '?' && $workTree === '?') {
        return 'untracked';
    }
    
    return 'unknown';
}
```

### 插件系统

使用 NativePHP 的 FileSystem 和 Settings 类实现插件系统。

```php
// 在 PluginService.php 中
public function getPlugins()
{
    $pluginsDir = public_path() . '/static/plugins';
    
    if (!is_dir($pluginsDir)) {
        FileSystem::makeDirectory($pluginsDir, 0755, true);
    }
    
    $plugins = [];
    $directories = FileSystem::directories($pluginsDir);
    
    foreach ($directories as $directory) {
        $manifestFile = $directory . '/manifest.json';
        
        if (!FileSystem::exists($manifestFile)) {
            continue;
        }
        
        $manifest = json_decode(FileSystem::read($manifestFile), true);
        
        if (!$manifest) {
            continue;
        }
        
        $plugins[] = [
            'id' => basename($directory),
            'name' => $manifest['name'] ?? basename($directory),
            'description' => $manifest['description'] ?? '',
            'version' => $manifest['version'] ?? '1.0.0',
            'author' => $manifest['author'] ?? '',
            'main' => $manifest['main'] ?? 'index.js',
            'enabled' => $this->isPluginEnabled(basename($directory)),
            'path' => $directory,
        ];
    }
    
    return $plugins;
}

public function installPlugin($file)
{
    $pluginsDir = public_path() . '/static/plugins';
    
    if (!is_dir($pluginsDir)) {
        FileSystem::makeDirectory($pluginsDir, 0755, true);
    }
    
    // 解压插件
    $zip = new \ZipArchive();
    
    if ($zip->open($file) !== true) {
        return [
            'success' => false,
            'message' => '无法打开插件文件',
        ];
    }
    
    // 检查是否有 manifest.json
    $manifestIndex = $zip->locateName('manifest.json', \ZipArchive::FL_NODIR);
    
    if ($manifestIndex === false) {
        $zip->close();
        
        return [
            'success' => false,
            'message' => '插件文件格式不正确，缺少 manifest.json',
        ];
    }
    
    // 读取 manifest.json
    $manifest = json_decode($zip->getFromIndex($manifestIndex), true);
    
    if (!$manifest || !isset($manifest['name'])) {
        $zip->close();
        
        return [
            'success' => false,
            'message' => 'manifest.json 格式不正确',
        ];
    }
    
    // 创建插件目录
    $pluginDir = $pluginsDir . '/' . $manifest['name'];
    
    if (is_dir($pluginDir)) {
        FileSystem::deleteDirectory($pluginDir);
    }
    
    FileSystem::makeDirectory($pluginDir, 0755, true);
    
    // 解压到插件目录
    $zip->extractTo($pluginDir);
    $zip->close();
    
    // 启用插件
    $this->enablePlugin($manifest['name']);
    
    return [
        'success' => true,
        'plugin' => [
            'id' => $manifest['name'],
            'name' => $manifest['name'],
            'description' => $manifest['description'] ?? '',
            'version' => $manifest['version'] ?? '1.0.0',
            'author' => $manifest['author'] ?? '',
            'main' => $manifest['main'] ?? 'index.js',
            'enabled' => true,
            'path' => $pluginDir,
        ],
    ];
}

public function enablePlugin($id)
{
    $enabledPlugins = Settings::get('plugins.enabled', []);
    
    if (!in_array($id, $enabledPlugins)) {
        $enabledPlugins[] = $id;
        Settings::set('plugins.enabled', $enabledPlugins);
    }
    
    return true;
}

public function disablePlugin($id)
{
    $enabledPlugins = Settings::get('plugins.enabled', []);
    
    if (in_array($id, $enabledPlugins)) {
        $enabledPlugins = array_filter($enabledPlugins, function ($pluginId) use ($id) {
            return $pluginId !== $id;
        });
        
        Settings::set('plugins.enabled', array_values($enabledPlugins));
    }
    
    return true;
}

public function isPluginEnabled($id)
{
    $enabledPlugins = Settings::get('plugins.enabled', []);
    
    return in_array($id, $enabledPlugins);
}
```

### 主题和设置

使用 NativePHP 的 Settings 类实现主题和设置。

```php
// 在 SettingService.php 中
public function getSettings()
{
    $defaultSettings = [
        'editor' => [
            'theme' => 'vs-dark',
            'fontSize' => 14,
            'fontFamily' => 'Consolas, "Courier New", monospace',
            'tabSize' => 4,
            'insertSpaces' => true,
            'wordWrap' => 'off',
            'autoSave' => false,
            'formatOnSave' => false,
            'minimap' => true,
            'lineNumbers' => true,
        ],
        'terminal' => [
            'fontSize' => 14,
            'fontFamily' => 'Consolas, "Courier New", monospace',
            'cursorStyle' => 'block',
            'cursorBlink' => true,
        ],
        'ui' => [
            'theme' => 'dark',
            'sidebarPosition' => 'left',
            'sidebarWidth' => 250,
            'showStatusBar' => true,
            'showActivityBar' => true,
        ],
    ];
    
    $settings = Settings::get('editor.settings', []);
    
    return array_merge($defaultSettings, $settings);
}

public function updateSettings($settings)
{
    Settings::set('editor.settings', $settings);
    
    return true;
}

public function getThemes()
{
    $themesDir = public_path() . '/static/themes';
    
    if (!is_dir($themesDir)) {
        FileSystem::makeDirectory($themesDir, 0755, true);
    }
    
    $themes = [
        [
            'id' => 'light',
            'name' => '浅色主题',
            'type' => 'builtin',
        ],
        [
            'id' => 'dark',
            'name' => '深色主题',
            'type' => 'builtin',
        ],
    ];
    
    $files = FileSystem::files($themesDir);
    
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) !== 'json') {
            continue;
        }
        
        $theme = json_decode(FileSystem::read($file), true);
        
        if (!$theme || !isset($theme['name'])) {
            continue;
        }
        
        $themes[] = [
            'id' => pathinfo($file, PATHINFO_FILENAME),
            'name' => $theme['name'],
            'type' => 'custom',
            'path' => $file,
        ];
    }
    
    return $themes;
}

public function setTheme($themeId)
{
    $settings = $this->getSettings();
    $settings['ui']['theme'] = $themeId;
    
    $this->updateSettings($settings);
    
    return true;
}
```

## 使用的 NativePHP 功能

- **Window**: 用于创建和管理多个窗口
- **Dialog**: 用于文件选择和保存对话框
- **FileSystem**: 用于文件读写和目录操作
- **Process**: 用于运行终端命令和 Git 操作
- **Settings**: 用于存储用户设置和项目信息
- **Menu**: 用于创建应用菜单和上下文菜单
- **GlobalShortcut**: 用于注册全局快捷键
- **Notification**: 用于发送通知
- **Clipboard**: 用于复制和粘贴代码
