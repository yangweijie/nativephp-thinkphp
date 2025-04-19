<?php

namespace Native\ThinkPHP;

use think\App;
use think\facade\Filesystem;

class Assets
{
    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 资源目录
     *
     * @var string
     */
    protected $assetsDirectory;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->assetsDirectory = $this->app->getRootPath() . 'resources/native/assets';

        // 确保资源目录存在
        if (!is_dir($this->assetsDirectory)) {
            mkdir($this->assetsDirectory, 0755, true);
        }
    }

    /**
     * 获取资源路径
     *
     * @param string $path
     * @return string
     */
    public function path(string $path = ''): string
    {
        $path = trim($path, '/');

        return $this->assetsDirectory . ($path ? '/' . $path : '');
    }

    /**
     * 获取资源 URL
     *
     * @param string $path
     * @return string
     */
    public function url(string $path = ''): string
    {
        $path = trim($path, '/');

        // 如果是开发环境，返回相对路径
        if (!$this->isRunningBundled()) {
            return '/resources/native/assets/' . $path;
        }

        // 如果是生产环境，返回 file:// 协议路径
        return 'file://' . $this->path($path);
    }

    /**
     * 检查资源是否存在
     *
     * @param string $path
     * @return bool
     */
    public function exists(string $path): bool
    {
        return file_exists($this->path($path));
    }

    /**
     * 获取资源内容
     *
     * @param string $path
     * @return string|null
     */
    public function get(string $path): ?string
    {
        if (!$this->exists($path)) {
            return null;
        }

        return file_get_contents($this->path($path));
    }

    /**
     * 保存资源
     *
     * @param string $path
     * @param string $contents
     * @return bool
     */
    public function put(string $path, string $contents): bool
    {
        $directory = dirname($this->path($path));

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return file_put_contents($this->path($path), $contents) !== false;
    }

    /**
     * 删除资源
     *
     * @param string $path
     * @return bool
     */
    public function delete(string $path): bool
    {
        if (!$this->exists($path)) {
            return false;
        }

        return unlink($this->path($path));
    }

    /**
     * 复制资源
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function copy(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $directory = dirname($this->path($to));

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return copy($this->path($from), $this->path($to));
    }

    /**
     * 移动资源
     *
     * @param string $from
     * @param string $to
     * @return bool
     */
    public function move(string $from, string $to): bool
    {
        if (!$this->exists($from)) {
            return false;
        }

        $directory = dirname($this->path($to));

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        return rename($this->path($from), $this->path($to));
    }

    /**
     * 获取目录中的所有文件
     *
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public function files(string $directory = '', bool $recursive = false): array
    {
        $directory = $this->path($directory);

        if (!is_dir($directory)) {
            return [];
        }

        $files = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_file($path)) {
                $files[] = str_replace($this->assetsDirectory . '/', '', $path);
            } elseif (is_dir($path) && $recursive) {
                $subFiles = $this->files(str_replace($this->assetsDirectory . '/', '', $path), true);
                $files = array_merge($files, $subFiles);
            }
        }

        return $files;
    }

    /**
     * 获取目录中的所有目录
     *
     * @param string $directory
     * @param bool $recursive
     * @return array
     */
    public function directories(string $directory = '', bool $recursive = false): array
    {
        $directory = $this->path($directory);

        if (!is_dir($directory)) {
            return [];
        }

        $directories = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_dir($path)) {
                $relativePath = str_replace($this->assetsDirectory . '/', '', $path);
                $directories[] = $relativePath;

                if ($recursive) {
                    $subDirectories = $this->directories($relativePath, true);
                    $directories = array_merge($directories, $subDirectories);
                }
            }
        }

        return $directories;
    }

    /**
     * 创建目录
     *
     * @param string $directory
     * @return bool
     */
    public function makeDirectory(string $directory): bool
    {
        $directory = $this->path($directory);

        if (is_dir($directory)) {
            return true;
        }

        return mkdir($directory, 0755, true);
    }

    /**
     * 删除目录
     *
     * @param string $directory
     * @return bool
     */
    public function deleteDirectory(string $directory): bool
    {
        $directory = $this->path($directory);

        if (!is_dir($directory)) {
            return false;
        }

        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;

            if (is_dir($path)) {
                $this->deleteDirectory(str_replace($this->assetsDirectory . '/', '', $path));
            } else {
                unlink($path);
            }
        }

        return rmdir($directory);
    }

    /**
     * 获取文件大小
     *
     * @param string $path
     * @return int|false
     */
    public function size(string $path)
    {
        if (!$this->exists($path)) {
            return false;
        }

        return filesize($this->path($path));
    }

    /**
     * 获取文件最后修改时间
     *
     * @param string $path
     * @return int|false
     */
    public function lastModified(string $path)
    {
        if (!$this->exists($path)) {
            return false;
        }

        return filemtime($this->path($path));
    }

    /**
     * 获取文件 MIME 类型
     *
     * @param string $path
     * @return string|false
     */
    public function mimeType(string $path)
    {
        if (!$this->exists($path)) {
            return false;
        }

        return mime_content_type($this->path($path));
    }

    /**
     * 检查应用是否以打包方式运行
     *
     * @return bool
     */
    protected function isRunningBundled(): bool
    {
        // 检查是否在 Electron 环境中运行
        return isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Electron') !== false;
    }
}
