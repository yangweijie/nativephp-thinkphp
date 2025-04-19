<?php

namespace Native\ThinkPHP\Facades;

/**
 * FileSystem 门面类模拟
 */
class FileSystem
{
    /**
     * 写入文件
     *
     * @param string $path 文件路径
     * @param string $content 文件内容
     * @return bool
     */
    public static function write($path, $content)
    {
        return file_put_contents($path, $content) !== false;
    }

    /**
     * 读取文件
     *
     * @param string $path 文件路径
     * @return string
     */
    public static function read($path)
    {
        return file_exists($path) ? file_get_contents($path) : '';
    }

    /**
     * 追加内容到文件
     *
     * @param string $path 文件路径
     * @param string $content 追加内容
     * @return bool
     */
    public static function append($path, $content)
    {
        return file_put_contents($path, $content, FILE_APPEND) !== false;
    }

    /**
     * 删除文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public static function delete($path)
    {
        return file_exists($path) ? unlink($path) : true;
    }

    /**
     * 复制文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return bool
     */
    public static function copy($source, $destination)
    {
        return copy($source, $destination);
    }

    /**
     * 移动文件
     *
     * @param string $source 源文件路径
     * @param string $destination 目标文件路径
     * @return bool
     */
    public static function move($source, $destination)
    {
        return rename($source, $destination);
    }

    /**
     * 检查文件是否存在
     *
     * @param string $path 文件路径
     * @return bool
     */
    public static function exists($path)
    {
        return file_exists($path);
    }

    /**
     * 获取文件大小
     *
     * @param string $path 文件路径
     * @return int
     */
    public static function size($path)
    {
        return file_exists($path) ? filesize($path) : 0;
    }

    /**
     * 创建目录
     *
     * @param string $path 目录路径
     * @param int $mode 权限模式
     * @param bool $recursive 是否递归创建
     * @return bool
     */
    public static function makeDirectory($path, $mode = 0755, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }
}
