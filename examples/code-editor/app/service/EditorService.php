<?php

namespace app\service;

use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Notification;

class EditorService
{
    /**
     * 打开文件
     *
     * @return array|null
     */
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
        
        // 添加到最近文件列表
        $this->addToRecentFiles($file);
        
        return [
            'path' => $file,
            'name' => basename($file),
            'content' => $content,
            'extension' => $extension,
            'language' => $this->getLanguageFromExtension($extension),
        ];
    }
    
    /**
     * 保存文件
     *
     * @param string|null $path
     * @param string $content
     * @return array|false
     */
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
        
        // 添加到最近文件列表
        $this->addToRecentFiles($path);
        
        // 发送通知
        Notification::send('文件已保存', basename($path));
        
        return [
            'path' => $path,
            'name' => basename($path),
        ];
    }
    
    /**
     * 添加到最近文件列表
     *
     * @param string $path
     * @return void
     */
    protected function addToRecentFiles($path)
    {
        $recentFiles = Settings::get('files.recent', []);
        
        // 检查是否已存在
        foreach ($recentFiles as $key => $file) {
            if ($file['path'] === $path) {
                unset($recentFiles[$key]);
                break;
            }
        }
        
        // 添加到最前面
        array_unshift($recentFiles, [
            'path' => $path,
            'name' => basename($path),
            'extension' => pathinfo($path, PATHINFO_EXTENSION),
            'lastOpened' => date('Y-m-d H:i:s'),
        ]);
        
        // 限制最多保存 20 个
        $recentFiles = array_slice($recentFiles, 0, 20);
        
        Settings::set('files.recent', $recentFiles);
    }
    
    /**
     * 根据扩展名获取语言
     *
     * @param string $extension
     * @return string
     */
    public function getLanguageFromExtension($extension)
    {
        $languageMap = [
            'js' => 'javascript',
            'jsx' => 'javascript',
            'ts' => 'typescript',
            'tsx' => 'typescript',
            'html' => 'html',
            'htm' => 'html',
            'css' => 'css',
            'scss' => 'scss',
            'less' => 'less',
            'php' => 'php',
            'json' => 'json',
            'md' => 'markdown',
            'py' => 'python',
            'java' => 'java',
            'c' => 'c',
            'cpp' => 'cpp',
            'h' => 'cpp',
            'cs' => 'csharp',
            'go' => 'go',
            'rs' => 'rust',
            'rb' => 'ruby',
            'pl' => 'perl',
            'sh' => 'shell',
            'bat' => 'bat',
            'ps1' => 'powershell',
            'sql' => 'sql',
            'xml' => 'xml',
            'yaml' => 'yaml',
            'yml' => 'yaml',
            'ini' => 'ini',
            'conf' => 'ini',
            'txt' => 'plaintext',
        ];
        
        return $languageMap[strtolower($extension)] ?? 'plaintext';
    }
    
    /**
     * 格式化代码
     *
     * @param string $content
     * @param string $language
     * @return string
     */
    public function formatCode($content, $language)
    {
        // 这里可以集成各种格式化工具，如 Prettier、PHP-CS-Fixer 等
        // 简单起见，这里只返回原内容
        return $content;
    }
    
    /**
     * 获取代码补全
     *
     * @param string $content
     * @param string $language
     * @param int $line
     * @param int $column
     * @return array
     */
    public function getCompletions($content, $language, $line, $column)
    {
        // 这里可以集成各种语言服务器，如 TypeScript Language Server、PHP Language Server 等
        // 简单起见，这里只返回一些基本的补全
        $completions = [];
        
        switch ($language) {
            case 'javascript':
            case 'typescript':
                $completions = [
                    ['label' => 'console', 'kind' => 'function', 'detail' => 'console object'],
                    ['label' => 'console.log', 'kind' => 'function', 'detail' => 'Log to the console'],
                    ['label' => 'document', 'kind' => 'variable', 'detail' => 'document object'],
                    ['label' => 'window', 'kind' => 'variable', 'detail' => 'window object'],
                    ['label' => 'function', 'kind' => 'keyword', 'detail' => 'function declaration'],
                    ['label' => 'class', 'kind' => 'keyword', 'detail' => 'class declaration'],
                    ['label' => 'const', 'kind' => 'keyword', 'detail' => 'constant declaration'],
                    ['label' => 'let', 'kind' => 'keyword', 'detail' => 'variable declaration'],
                    ['label' => 'var', 'kind' => 'keyword', 'detail' => 'variable declaration'],
                ];
                break;
            case 'php':
                $completions = [
                    ['label' => 'echo', 'kind' => 'function', 'detail' => 'Output strings'],
                    ['label' => 'print', 'kind' => 'function', 'detail' => 'Output a string'],
                    ['label' => 'function', 'kind' => 'keyword', 'detail' => 'function declaration'],
                    ['label' => 'class', 'kind' => 'keyword', 'detail' => 'class declaration'],
                    ['label' => 'namespace', 'kind' => 'keyword', 'detail' => 'namespace declaration'],
                    ['label' => 'use', 'kind' => 'keyword', 'detail' => 'use declaration'],
                    ['label' => 'public', 'kind' => 'keyword', 'detail' => 'public visibility'],
                    ['label' => 'protected', 'kind' => 'keyword', 'detail' => 'protected visibility'],
                    ['label' => 'private', 'kind' => 'keyword', 'detail' => 'private visibility'],
                ];
                break;
            case 'html':
                $completions = [
                    ['label' => 'div', 'kind' => 'snippet', 'detail' => 'div element'],
                    ['label' => 'span', 'kind' => 'snippet', 'detail' => 'span element'],
                    ['label' => 'p', 'kind' => 'snippet', 'detail' => 'paragraph element'],
                    ['label' => 'a', 'kind' => 'snippet', 'detail' => 'anchor element'],
                    ['label' => 'img', 'kind' => 'snippet', 'detail' => 'image element'],
                    ['label' => 'ul', 'kind' => 'snippet', 'detail' => 'unordered list element'],
                    ['label' => 'ol', 'kind' => 'snippet', 'detail' => 'ordered list element'],
                    ['label' => 'li', 'kind' => 'snippet', 'detail' => 'list item element'],
                    ['label' => 'table', 'kind' => 'snippet', 'detail' => 'table element'],
                    ['label' => 'form', 'kind' => 'snippet', 'detail' => 'form element'],
                ];
                break;
            case 'css':
                $completions = [
                    ['label' => 'color', 'kind' => 'property', 'detail' => 'Sets the color of text'],
                    ['label' => 'background-color', 'kind' => 'property', 'detail' => 'Sets the background color'],
                    ['label' => 'font-size', 'kind' => 'property', 'detail' => 'Sets the font size'],
                    ['label' => 'font-weight', 'kind' => 'property', 'detail' => 'Sets the font weight'],
                    ['label' => 'margin', 'kind' => 'property', 'detail' => 'Sets the margin'],
                    ['label' => 'padding', 'kind' => 'property', 'detail' => 'Sets the padding'],
                    ['label' => 'display', 'kind' => 'property', 'detail' => 'Sets the display type'],
                    ['label' => 'position', 'kind' => 'property', 'detail' => 'Sets the position'],
                    ['label' => 'width', 'kind' => 'property', 'detail' => 'Sets the width'],
                    ['label' => 'height', 'kind' => 'property', 'detail' => 'Sets the height'],
                ];
                break;
        }
        
        return $completions;
    }
}
