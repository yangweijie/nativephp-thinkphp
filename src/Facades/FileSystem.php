<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string|false read(string $path, string $encoding = 'utf8') 读取文件内容
 * @method static bool write(string $path, string $content) 写入内容到文件
 * @method static bool append(string $path, string $content) 追加内容到文件
 * @method static bool delete(string $path) 删除文件
 * @method static bool copy(string $source, string $destination) 复制文件
 * @method static bool move(string $source, string $destination) 移动文件
 * @method static bool exists(string $path) 检查文件是否存在
 * @method static int|false size(string $path) 获取文件大小
 * @method static int|false lastModified(string $path) 获取文件修改时间
 * @method static bool makeDirectory(string $path, int $mode = 0755, bool $recursive = false) 创建目录
 * @method static bool deleteDirectory(string $path, bool $recursive = false) 删除目录
 * 
 * @see \Native\ThinkPHP\FileSystem
 */
class FileSystem extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.filesystem';
    }
}
