<?php

namespace Native\ThinkPHP\Facades;

use think\Facade;

/**
 * @method static void assertTrue(bool $condition, string $message = '断言失败：条件应为真') 断言条件为真
 * @method static void assertFalse(bool $condition, string $message = '断言失败：条件应为假') 断言条件为假
 * @method static void assertEquals($expected, $actual, string $message = '断言失败：值应相等') 断言相等
 * @method static void assertNotEquals($expected, $actual, string $message = '断言失败：值应不相等') 断言不相等
 * @method static void assertEmpty($value, string $message = '断言失败：值应为空') 断言为空
 * @method static void assertNotEmpty($value, string $message = '断言失败：值应不为空') 断言不为空
 * @method static void assertNull($value, string $message = '断言失败：值应为 null') 断言为 null
 * @method static void assertNotNull($value, string $message = '断言失败：值应不为 null') 断言不为 null
 * @method static void assertContains($needle, $haystack, string $message = '断言失败：应包含指定值') 断言包含
 * @method static void assertNotContains($needle, $haystack, string $message = '断言失败：应不包含指定值') 断言不包含
 * @method static void assertFileExists(string $filename, string $message = '断言失败：文件应存在') 断言文件存在
 * @method static void assertFileNotExists(string $filename, string $message = '断言失败：文件应不存在') 断言文件不存在
 * @method static void assertDirectoryExists(string $directory, string $message = '断言失败：目录应存在') 断言目录存在
 * @method static void assertDirectoryNotExists(string $directory, string $message = '断言失败：目录应不存在') 断言目录不存在
 * @method static void assertWindowExists(string $id, string $message = '断言失败：窗口应存在') 断言窗口存在
 * @method static void assertWindowNotExists(string $id, string $message = '断言失败：窗口应不存在') 断言窗口不存在
 * @method static void waitUntil(callable $condition, int $timeout = 10, int $interval = 100, string $message = '等待超时') 等待条件为真
 * @method static void waitForWindow(string $id, int $timeout = 10, string $message = '等待窗口超时') 等待窗口出现
 * @method static void waitForWindowClose(string $id, int $timeout = 10, string $message = '等待窗口关闭超时') 等待窗口消失
 * @method static void waitForFile(string $filename, int $timeout = 10, string $message = '等待文件超时') 等待文件出现
 * @method static void waitForFileRemove(string $filename, int $timeout = 10, string $message = '等待文件删除超时') 等待文件消失
 *
 * @see \Native\ThinkPHP\Testing\TestHelper
 */
class TestHelper extends Facade
{
    /**
     * 获取当前Facade对应类名
     *
     * @return string
     */
    protected static function getFacadeClass()
    {
        return 'native.test_helper';
    }
}