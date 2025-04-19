<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static string|array|null openFile(array $options = []) 显示打开文件对话框
 * @method static string|null saveFile(array $options = []) 显示保存文件对话框
 * @method static string|null selectFolder(array $options = []) 显示选择文件夹对话框
 * @method static int message(string $message, array $options = []) 显示消息框
 * @method static int error(string $message, array $options = []) 显示错误消息框
 * @method static int info(string $message, array $options = []) 显示信息消息框
 * @method static int warning(string $message, array $options = []) 显示警告消息框
 * @method static int question(string $message, array $options = []) 显示问题消息框
 * @method static bool confirm(string $message, array $options = []) 显示确认消息框
 * @method static string|null prompt(string $message, array $options = []) 显示输入框
 * @method static array|null certificate(array $options = []) 显示证书选择对话框
 * @method static string|null color(array $options = []) 显示颜色选择对话框
 * @method static array|null font(array $options = []) 显示字体选择对话框
 * @method static mixed showWithConfig(\Native\ThinkPHP\DTOs\DialogConfig $config) 使用配置对象显示对话框
 *
 * @see \Native\ThinkPHP\Dialog
 */
class Dialog extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.dialog';
    }
}
