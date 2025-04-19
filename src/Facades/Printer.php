<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static array getPrinters() 获取所有打印机
 * @method static array|null getDefaultPrinter() 获取默认打印机
 * @method static bool printHtml(string $html, array $options = []) 打印 HTML
 * @method static bool printFile(string $filePath, array $options = []) 打印文件
 * @method static bool printToPdf(string $html, string $outputPath, array $options = []) 打印到 PDF
 * @method static bool showPrintPreview(string $html, array $options = []) 显示打印预览
 * 
 * @see \Native\ThinkPHP\Printer
 */
class Printer extends Facade
{
    /**
     * 获取当前Facade对应类名
     * 
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.printer';
    }
}
