<?php

namespace app\service;

use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Notification;

class ProjectService
{
    /**
     * 编辑器服务
     *
     * @var \app\service\EditorService
     */
    protected $editorService;
    
    /**
     * 构造函数
     *
     * @param \app\service\EditorService $editorService
     */
    public function __construct(EditorService $editorService)
    {
        $this->editorService = $editorService;
    }
    
    /**
     * 打开项目
     *
     * @return array|null
     */
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
        $this->addToRecentProjects($directory);
        
        // 发送通知
        Notification::send('项目已打开', basename($directory));
        
        return $project;
    }
    
    /**
     * 扫描目录
     *
     * @param string $directory
     * @param string $relativePath
     * @return array
     */
    public function scanDirectory($directory, $relativePath = '')
    {
        $result = [];
        $files = FileSystem::files($directory);
        $directories = FileSystem::directories($directory);
        
        // 排除一些目录
        $excludeDirs = ['.git', 'node_modules', 'vendor', '.idea', '.vscode'];
        
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
                'language' => $this->editorService->getLanguageFromExtension($extension),
            ];
        }
        
        return $result;
    }
    
    /**
     * 添加到最近项目列表
     *
     * @param string $path
     * @return void
     */
    protected function addToRecentProjects($path)
    {
        $recentProjects = Settings::get('projects.recent', []);
        
        // 检查是否已存在
        foreach ($recentProjects as $key => $project) {
            if ($project['path'] === $path) {
                unset($recentProjects[$key]);
                break;
            }
        }
        
        // 添加到最前面
        array_unshift($recentProjects, [
            'path' => $path,
            'name' => basename($path),
            'lastOpened' => date('Y-m-d H:i:s'),
        ]);
        
        // 限制最多保存 10 个
        $recentProjects = array_slice($recentProjects, 0, 10);
        
        Settings::set('projects.recent', $recentProjects);
    }
    
    /**
     * 创建项目
     *
     * @param string $name
     * @param string $path
     * @param string $template
     * @return array|false
     */
    public function createProject($name, $path, $template = 'empty')
    {
        if (empty($name) || empty($path)) {
            return false;
        }
        
        $projectPath = $path . '/' . $name;
        
        if (is_dir($projectPath)) {
            return false;
        }
        
        FileSystem::makeDirectory($projectPath, 0755, true);
        
        // 根据模板创建项目
        switch ($template) {
            case 'html':
                $this->createHtmlProject($projectPath);
                break;
            case 'node':
                $this->createNodeProject($projectPath);
                break;
            case 'php':
                $this->createPhpProject($projectPath);
                break;
            default:
                $this->createEmptyProject($projectPath);
                break;
        }
        
        // 添加到最近项目列表
        $this->addToRecentProjects($projectPath);
        
        // 发送通知
        Notification::send('项目已创建', $name);
        
        return [
            'path' => $projectPath,
            'name' => $name,
            'files' => $this->scanDirectory($projectPath),
        ];
    }
    
    /**
     * 创建空项目
     *
     * @param string $path
     * @return void
     */
    protected function createEmptyProject($path)
    {
        FileSystem::write($path . '/README.md', '# ' . basename($path) . "\n\nThis is a new project.");
    }
    
    /**
     * 创建 HTML 项目
     *
     * @param string $path
     * @return void
     */
    protected function createHtmlProject($path)
    {
        FileSystem::write($path . '/index.html', '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . basename($path) . '</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>' . basename($path) . '</h1>
    <p>Welcome to your new project!</p>
    
    <script src="js/main.js"></script>
</body>
</html>');
        
        FileSystem::makeDirectory($path . '/css', 0755, true);
        FileSystem::write($path . '/css/style.css', 'body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 20px;
    line-height: 1.6;
}

h1 {
    color: #333;
}');
        
        FileSystem::makeDirectory($path . '/js', 0755, true);
        FileSystem::write($path . '/js/main.js', 'document.addEventListener("DOMContentLoaded", function() {
    console.log("Document ready!");
});');
        
        FileSystem::write($path . '/README.md', '# ' . basename($path) . "\n\nThis is a new HTML project.");
    }
    
    /**
     * 创建 Node.js 项目
     *
     * @param string $path
     * @return void
     */
    protected function createNodeProject($path)
    {
        FileSystem::write($path . '/package.json', '{
  "name": "' . basename($path) . '",
  "version": "1.0.0",
  "description": "A new Node.js project",
  "main": "index.js",
  "scripts": {
    "start": "node index.js",
    "test": "echo \\"Error: no test specified\\" && exit 1"
  },
  "keywords": [],
  "author": "",
  "license": "ISC"
}');
        
        FileSystem::write($path . '/index.js', 'console.log("Hello, ' . basename($path) . '!");

// Start your application here');
        
        FileSystem::makeDirectory($path . '/src', 0755, true);
        FileSystem::write($path . '/src/app.js', '// Application logic goes here

function greet(name) {
    return `Hello, ${name}!`;
}

module.exports = {
    greet
};');
        
        FileSystem::write($path . '/.gitignore', 'node_modules/
npm-debug.log
.DS_Store');
        
        FileSystem::write($path . '/README.md', '# ' . basename($path) . "\n\nThis is a new Node.js project.\n\n## Installation\n\n```bash\nnpm install\n```\n\n## Usage\n\n```bash\nnpm start\n```");
    }
    
    /**
     * 创建 PHP 项目
     *
     * @param string $path
     * @return void
     */
    protected function createPhpProject($path)
    {
        FileSystem::write($path . '/index.php', '<?php

require_once __DIR__ . \'/vendor/autoload.php\';

use App\\App;

$app = new App();
$app->run();');
        
        FileSystem::makeDirectory($path . '/src/App', 0755, true);
        FileSystem::write($path . '/src/App/App.php', '<?php

namespace App;

class App
{
    public function run()
    {
        echo "Hello, ' . basename($path) . '!";
    }
}');
        
        FileSystem::write($path . '/composer.json', '{
    "name": "app/' . strtolower(basename($path)) . '",
    "description": "A new PHP project",
    "type": "project",
    "autoload": {
        "psr-4": {
            "App\\\\": "src/App/"
        }
    },
    "require": {
        "php": ">=7.4"
    }
}');
        
        FileSystem::write($path . '/.gitignore', 'vendor/
composer.lock
.DS_Store');
        
        FileSystem::write($path . '/README.md', '# ' . basename($path) . "\n\nThis is a new PHP project.\n\n## Installation\n\n```bash\ncomposer install\n```\n\n## Usage\n\n```bash\nphp -S localhost:8000\n```");
    }
}
