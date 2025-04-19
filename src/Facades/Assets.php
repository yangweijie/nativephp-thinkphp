<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string path(string $path = '') 获取资源路径
 * @method static string url(string $path = '') 获取资源 URL
 * @method static bool exists(string $path) 检查资源是否存在
 * @method static string|null get(string $path) 获取资源内容
 * @method static bool put(string $path, string $contents) 保存资源
 * @method static bool delete(string $path) 删除资源
 * @method static bool copy(string $from, string $to) 复制资源
 * @method static bool move(string $from, string $to) 移动资源
 * @method static array files(string $directory = '', bool $recursive = false) 获取目录中的所有文件
 * @method static array directories(string $directory = '', bool $recursive = false) 获取目录中的所有目录
 * @method static bool makeDirectory(string $directory) 创建目录
 * @method static bool deleteDirectory(string $directory) 删除目录
 * @method static int|false size(string $path) 获取文件大小
 * @method static int|false lastModified(string $path) 获取文件最后修改时间
 * @method static string|false mimeType(string $path) 获取文件 MIME 类型
 *
 * @see \Native\ThinkPHP\Assets
 */
class Assets extends Facade
{
    /**
     * 获取当前 Facade 对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.assets';
    }
}
