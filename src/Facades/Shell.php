<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static void showInFolder(string $path) 在文件夹中显示文件
 * @method static string openFile(string $path) 打开文件
 * @method static void trashFile(string $path) 将文件移动到回收站
 * @method static void openExternal(string $url) 使用外部程序打开 URL
 * 
 * @see \Native\ThinkPHP\Shell
 */
class Shell extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.shell';
    }
}
