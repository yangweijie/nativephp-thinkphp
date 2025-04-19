<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileService
{
    /**
     * 获取目录中的文件和子目录
     *
     * @param string $path
     * @return array
     */
    public function getFiles($path)
    {
        if (!is_dir($path)) {
            throw new \Exception('目录不存在或不是一个有效的目录');
        }

        $files = [];
        $items = scandir($path);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            $isDir = is_dir($fullPath);

            $files[] = [
                'name' => $item,
                'path' => $fullPath,
                'isDir' => $isDir,
                'size' => $isDir ? $this->getDirectorySize($fullPath) : FileSystem::size($fullPath),
                'formattedSize' => $isDir ? $this->formatSize($this->getDirectorySize($fullPath)) : $this->formatSize(FileSystem::size($fullPath)),
                'lastModified' => FileSystem::lastModified($fullPath),
                'formattedLastModified' => date('Y-m-d H:i:s', FileSystem::lastModified($fullPath)),
                'extension' => $isDir ? null : pathinfo($item, PATHINFO_EXTENSION),
                'type' => $isDir ? 'directory' : $this->getFileType($item),
                'icon' => $isDir ? 'folder' : $this->getFileIcon(pathinfo($item, PATHINFO_EXTENSION)),
            ];
        }

        // 按类型和名称排序：目录在前，文件在后
        usort($files, function ($a, $b) {
            if ($a['isDir'] && !$b['isDir']) {
                return -1;
            }

            if (!$a['isDir'] && $b['isDir']) {
                return 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return $files;
    }

    /**
     * 获取目录大小
     *
     * @param string $path
     * @return int
     */
    public function getDirectorySize($path)
    {
        $size = 0;

        if (!is_dir($path)) {
            return $size;
        }

        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * 格式化文件大小
     *
     * @param int $size
     * @return string
     */
    public function formatSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2) . ' ' . $units[$i];
    }

    /**
     * 获取文件类型
     *
     * @param string $filename
     * @return string
     */
    public function getFileType($filename)
    {
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // 检查是否为加密文件
        if ($extension === 'encrypted') {
            return 'encrypted';
        }

        // 如果有完整路径，检查文件内容
        if (file_exists($filename) && !is_dir($filename)) {
            // 检查文件头部
            $handle = @fopen($filename, 'rb');
            if ($handle) {
                $header = fread($handle, 9);
                fclose($handle);

                if ($header === "ENCRYPTED") {
                    return 'encrypted';
                }
            }
        }

        $types = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            'audio' => ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'],
            'document' => ['doc', 'docx', 'pdf', 'txt', 'rtf', 'odt', 'md'],
            'spreadsheet' => ['xls', 'xlsx', 'csv', 'ods'],
            'presentation' => ['ppt', 'pptx', 'odp'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
            'code' => ['php', 'js', 'html', 'css', 'json', 'xml', 'py', 'java', 'c', 'cpp', 'cs', 'go', 'rb'],
        ];

        foreach ($types as $type => $extensions) {
            if (in_array($extension, $extensions)) {
                return $type;
            }
        }

        return 'other';
    }

    /**
     * 获取文件图标
     *
     * @param string $extension
     * @return string
     */
    public function getFileIcon($extension)
    {
        $extension = strtolower($extension);

        // 加密文件图标
        if ($extension === 'encrypted') {
            return 'encrypted';
        }

        $icons = [
            'image' => ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp'],
            'video' => ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm'],
            'audio' => ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a'],
            'pdf' => ['pdf'],
            'word' => ['doc', 'docx', 'odt'],
            'excel' => ['xls', 'xlsx', 'csv', 'ods'],
            'powerpoint' => ['ppt', 'pptx', 'odp'],
            'archive' => ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz'],
            'code' => ['php', 'js', 'html', 'css', 'json', 'xml', 'py', 'java', 'c', 'cpp', 'cs', 'go', 'rb'],
            'text' => ['txt', 'md', 'rtf'],
            'encrypted' => ['encrypted'],
        ];

        foreach ($icons as $icon => $extensions) {
            if (in_array($extension, $extensions)) {
                return $icon;
            }
        }

        return 'file';
    }

    /**
     * 获取文件或目录属性
     *
     * @param string $path
     * @return array
     */
    public function getProperties($path)
    {
        $isDir = is_dir($path);
        $filename = basename($path);

        $properties = [
            'name' => $filename,
            'path' => $path,
            'isDir' => $isDir,
            'size' => $isDir ? $this->getDirectorySize($path) : FileSystem::size($path),
            'formattedSize' => $isDir ? $this->formatSize($this->getDirectorySize($path)) : $this->formatSize(FileSystem::size($path)),
            'lastModified' => FileSystem::lastModified($path),
            'formattedLastModified' => date('Y-m-d H:i:s', FileSystem::lastModified($path)),
            'created' => filectime($path),
            'formattedCreated' => date('Y-m-d H:i:s', filectime($path)),
            'accessed' => fileatime($path),
            'formattedAccessed' => date('Y-m-d H:i:s', fileatime($path)),
            'permissions' => substr(sprintf('%o', fileperms($path)), -4),
            'owner' => function_exists('posix_getpwuid') ? posix_getpwuid(fileowner($path))['name'] : fileowner($path),
            'group' => function_exists('posix_getgrgid') ? posix_getgrgid(filegroup($path))['name'] : filegroup($path),
        ];

        if (!$isDir) {
            $properties['extension'] = pathinfo($filename, PATHINFO_EXTENSION);
            $properties['type'] = $this->getFileType($filename);
            $properties['mimeType'] = mime_content_type($path);
        } else {
            $properties['itemCount'] = count(scandir($path)) - 2; // 减去 . 和 ..
        }

        return $properties;
    }

    /**
     * 复制目录
     *
     * @param string $source
     * @param string $destination
     * @return bool
     */
    public function copyDirectory($source, $destination)
    {
        if (!is_dir($source)) {
            return false;
        }

        if (!is_dir($destination)) {
            FileSystem::makeDirectory($destination, 0755, true);
        }

        $items = scandir($source);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $sourcePath = $source . DIRECTORY_SEPARATOR . $item;
            $destPath = $destination . DIRECTORY_SEPARATOR . $item;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                FileSystem::copy($sourcePath, $destPath);
            }
        }

        return true;
    }

    /**
     * 搜索文件
     *
     * @param string $path 搜索路径
     * @param string $keyword 搜索关键词
     * @param bool $recursive 是否递归搜索
     * @return array
     */
    public function searchFiles($path, $keyword, $recursive = false)
    {
        if (!is_dir($path)) {
            throw new \Exception('目录不存在或不是一个有效的目录');
        }

        $results = [];

        if ($recursive) {
            // 递归搜索
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                $name = $item->getFilename();
                $fullPath = $item->getPathname();

                // 如果文件名包含关键词，添加到结果中
                if (stripos($name, $keyword) !== false) {
                    $results[] = $this->getFileInfo($fullPath);
                    continue;
                }

                // 如果是文本文件，尝试搜索文件内容
                if ($item->isFile() && $this->isTextFile($fullPath)) {
                    try {
                        $content = FileSystem::read($fullPath);
                        if (stripos($content, $keyword) !== false) {
                            $results[] = $this->getFileInfo($fullPath);
                        }
                    } catch (\Exception $e) {
                        // 忽略读取错误
                    }
                }
            }
        } else {
            // 非递归搜索，只搜索当前目录
            $items = scandir($path);

            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }

                $fullPath = $path . DIRECTORY_SEPARATOR . $item;

                // 如果文件名包含关键词，添加到结果中
                if (stripos($item, $keyword) !== false) {
                    $results[] = $this->getFileInfo($fullPath);
                    continue;
                }

                // 如果是文本文件，尝试搜索文件内容
                if (is_file($fullPath) && $this->isTextFile($fullPath)) {
                    try {
                        $content = FileSystem::read($fullPath);
                        if (stripos($content, $keyword) !== false) {
                            $results[] = $this->getFileInfo($fullPath);
                        }
                    } catch (\Exception $e) {
                        // 忽略读取错误
                    }
                }
            }
        }

        // 按类型和名称排序：目录在前，文件在后
        usort($results, function ($a, $b) {
            if ($a['isDir'] && !$b['isDir']) {
                return -1;
            }

            if (!$a['isDir'] && $b['isDir']) {
                return 1;
            }

            return strcasecmp($a['name'], $b['name']);
        });

        return $results;
    }

    /**
     * 获取文件信息
     *
     * @param string $path 文件路径
     * @return array
     */
    protected function getFileInfo($path)
    {
        $isDir = is_dir($path);

        return [
            'name' => basename($path),
            'path' => $path,
            'isDir' => $isDir,
            'size' => $isDir ? $this->getDirectorySize($path) : FileSystem::size($path),
            'formattedSize' => $isDir ? $this->formatSize($this->getDirectorySize($path)) : $this->formatSize(FileSystem::size($path)),
            'lastModified' => FileSystem::lastModified($path),
            'formattedLastModified' => date('Y-m-d H:i:s', FileSystem::lastModified($path)),
            'extension' => $isDir ? null : pathinfo($path, PATHINFO_EXTENSION),
            'type' => $isDir ? 'directory' : $this->getFileType(basename($path)),
            'icon' => $isDir ? 'folder' : $this->getFileIcon(pathinfo($path, PATHINFO_EXTENSION)),
        ];
    }

    /**
     * 判断是否是文本文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    protected function isTextFile($path)
    {
        if (!is_file($path)) {
            return false;
        }

        // 检查文件大小，过大的文件不搜索内容
        $maxSize = 1024 * 1024; // 1MB
        if (FileSystem::size($path) > $maxSize) {
            return false;
        }

        // 检查文件扩展名
        $textExtensions = [
            'txt', 'md', 'html', 'htm', 'xml', 'json', 'csv', 'log',
            'php', 'js', 'css', 'py', 'java', 'c', 'cpp', 'h', 'cs', 'go', 'rb',
            'ini', 'conf', 'yml', 'yaml', 'toml', 'bat', 'sh', 'ps1'
        ];

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (in_array($extension, $textExtensions)) {
            return true;
        }

        // 尝试检测 MIME 类型
        try {
            $mimeType = mime_content_type($path);
            return strpos($mimeType, 'text/') === 0 ||
                   in_array($mimeType, ['application/json', 'application/xml', 'application/javascript']);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * 压缩文件或目录
     *
     * @param string|array $source 源文件或目录，可以是单个路径或路径数组
     * @param string $destination 目标压缩文件路径
     * @return bool
     * @throws \Exception
     */
    public function compress($source, $destination)
    {
        // 检查目标文件扩展名
        $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        if ($extension !== 'zip') {
            throw new \Exception('当前只支持压缩为 ZIP 格式');
        }

        // 创建新的 ZipArchive 对象
        $zip = new \ZipArchive();

        // 打开压缩文件
        $result = $zip->open($destination, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if ($result !== true) {
            throw new \Exception('无法创建压缩文件：' . $this->getZipErrorMessage($result));
        }

        // 处理单个文件或目录
        if (!is_array($source)) {
            $source = [$source];
        }

        foreach ($source as $item) {
            if (!file_exists($item)) {
                $zip->close();
                throw new \Exception('源文件或目录不存在：' . $item);
            }

            $this->addToZip($zip, $item, basename($item));
        }

        // 关闭压缩文件
        $zip->close();

        return true;
    }

    /**
     * 将文件或目录添加到 ZIP 文件
     *
     * @param \ZipArchive $zip ZIP 对象
     * @param string $source 源文件或目录
     * @param string $localName ZIP 文件中的路径
     * @return void
     */
    protected function addToZip(\ZipArchive $zip, $source, $localName)
    {
        if (is_dir($source)) {
            // 如果是目录，递归添加其中的文件
            $zip->addEmptyDir($localName);

            $items = new \DirectoryIterator($source);
            foreach ($items as $item) {
                if ($item->isDot()) {
                    continue;
                }

                $itemPath = $item->getPathname();
                $itemLocalName = $localName . '/' . $item->getFilename();

                $this->addToZip($zip, $itemPath, $itemLocalName);
            }
        } else {
            // 如果是文件，直接添加
            $zip->addFile($source, $localName);
        }
    }

    /**
     * 解压缩文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     * @throws \Exception
     */
    public function extract($source, $destination)
    {
        // 检查源文件是否存在
        if (!file_exists($source)) {
            throw new \Exception('源压缩文件不存在');
        }

        // 检查源文件扩展名
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        if ($extension !== 'zip') {
            throw new \Exception('当前只支持解压缩 ZIP 格式文件');
        }

        // 创建目标目录（如果不存在）
        if (!is_dir($destination)) {
            if (!FileSystem::makeDirectory($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        // 创建新的 ZipArchive 对象
        $zip = new \ZipArchive();

        // 打开压缩文件
        $result = $zip->open($source);
        if ($result !== true) {
            throw new \Exception('无法打开压缩文件：' . $this->getZipErrorMessage($result));
        }

        // 解压缩文件
        if (!$zip->extractTo($destination)) {
            $zip->close();
            throw new \Exception('解压缩失败');
        }

        // 关闭压缩文件
        $zip->close();

        return true;
    }

    /**
     * 获取 ZIP 错误消息
     *
     * @param int $code 错误代码
     * @return string
     */
    protected function getZipErrorMessage($code)
    {
        $errors = [
            \ZipArchive::ER_MULTIDISK => '多磁盘 ZIP 存档不支持',
            \ZipArchive::ER_RENAME => '重命名临时文件失败',
            \ZipArchive::ER_CLOSE => '关闭 ZIP 存档失败',
            \ZipArchive::ER_SEEK => '定位错误',
            \ZipArchive::ER_READ => '读取错误',
            \ZipArchive::ER_WRITE => '写入错误',
            \ZipArchive::ER_CRC => 'CRC 错误',
            \ZipArchive::ER_ZIPCLOSED => 'ZIP 存档已关闭',
            \ZipArchive::ER_NOENT => '文件不存在',
            \ZipArchive::ER_EXISTS => '文件已存在',
            \ZipArchive::ER_OPEN => '无法打开文件',
            \ZipArchive::ER_TMPOPEN => '无法创建临时文件',
            \ZipArchive::ER_ZLIB => 'Zlib 错误',
            \ZipArchive::ER_MEMORY => '内存分配失败',
            \ZipArchive::ER_CHANGED => '条目已更改',
            \ZipArchive::ER_COMPNOTSUPP => '不支持的压缩方法',
            \ZipArchive::ER_EOF => '意外的 EOF',
            \ZipArchive::ER_INVAL => '无效的参数',
            \ZipArchive::ER_NOZIP => '不是 ZIP 文件',
            \ZipArchive::ER_INTERNAL => '内部错误',
            \ZipArchive::ER_INCONS => 'ZIP 存档不一致',
            \ZipArchive::ER_REMOVE => '无法删除文件',
            \ZipArchive::ER_DELETED => '条目已删除',
        ];

        return isset($errors[$code]) ? $errors[$code] : '未知错误 (' . $code . ')';
    }
}
