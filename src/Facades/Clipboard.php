<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string text() 读取剪贴板文本
 * @method static void setText(string $text) 写入文本到剪贴板
 * @method static string|null image() 读取剪贴板图片
 * @method static void setImage(string $path) 写入图片到剪贴板
 * @method static void clear() 清空剪贴板
 * @method static string html() 读取剪贴板 HTML
 * @method static void setHtml(string $html) 写入 HTML 到剪贴板
 * @method static bool has(string $format) 检查剪贴板是否包含指定格式的数据
 * @method static array formats() 获取剪贴板中可用的格式
 * @method static string rtf() 读取剪贴板 RTF
 * @method static void setRtf(string $rtf) 写入 RTF 到剪贴板
 * @method static array files() 读取剪贴板文件路径
 * @method static void setFiles(array $files) 写入文件路径到剪贴板
 * @method static string|null readFormat(string $format) 读取剪贴板自定义格式数据
 * @method static void writeFormat(string $format, string $data) 写入自定义格式数据到剪贴板
 * @method static string onChange(callable $callback) 监听剪贴板变化
 * @method static bool offChange(string $id) 移除剪贴板变化监听器
 *
 * @see \Native\ThinkPHP\Clipboard
 */
class Clipboard extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.clipboard';
    }
}
