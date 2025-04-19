<?php

namespace Native\ThinkPHP\Testing;

use think\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;

class TestHelper
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 断言条件为真
     *
     * @param bool $condition 条件
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertTrue(bool $condition, string $message = '断言失败：条件应为真'): void
    {
        if (!$condition) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言条件为假
     *
     * @param bool $condition 条件
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertFalse(bool $condition, string $message = '断言失败：条件应为假'): void
    {
        if ($condition) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言相等
     *
     * @param mixed $expected 期望值
     * @param mixed $actual 实际值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertEquals($expected, $actual, string $message = '断言失败：值应相等'): void
    {
        if ($expected !== $actual) {
            throw new \Exception($message . "，期望：{$expected}，实际：{$actual}");
        }
    }

    /**
     * 断言不相等
     *
     * @param mixed $expected 期望值
     * @param mixed $actual 实际值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertNotEquals($expected, $actual, string $message = '断言失败：值应不相等'): void
    {
        if ($expected === $actual) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言为空
     *
     * @param mixed $value 值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertEmpty($value, string $message = '断言失败：值应为空'): void
    {
        if (!empty($value)) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言不为空
     *
     * @param mixed $value 值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertNotEmpty($value, string $message = '断言失败：值应不为空'): void
    {
        if (empty($value)) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言为 null
     *
     * @param mixed $value 值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertNull($value, string $message = '断言失败：值应为 null'): void
    {
        if ($value !== null) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言不为 null
     *
     * @param mixed $value 值
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertNotNull($value, string $message = '断言失败：值应不为 null'): void
    {
        if ($value === null) {
            throw new \Exception($message);
        }
    }

    /**
     * 断言包含
     *
     * @param mixed $needle 查找值
     * @param array|string $haystack 被查找的数组或字符串
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertContains($needle, $haystack, string $message = '断言失败：应包含指定值'): void
    {
        if (is_array($haystack)) {
            if (!in_array($needle, $haystack)) {
                throw new \Exception($message);
            }
        } elseif (is_string($haystack)) {
            if (strpos($haystack, $needle) === false) {
                throw new \Exception($message);
            }
        } else {
            throw new \Exception('断言失败：被查找的值应为数组或字符串');
        }
    }

    /**
     * 断言不包含
     *
     * @param mixed $needle 查找值
     * @param array|string $haystack 被查找的数组或字符串
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertNotContains($needle, $haystack, string $message = '断言失败：应不包含指定值'): void
    {
        if (is_array($haystack)) {
            if (in_array($needle, $haystack)) {
                throw new \Exception($message);
            }
        } elseif (is_string($haystack)) {
            if (strpos($haystack, $needle) !== false) {
                throw new \Exception($message);
            }
        } else {
            throw new \Exception('断言失败：被查找的值应为数组或字符串');
        }
    }

    /**
     * 断言文件存在
     *
     * @param string $filename 文件路径
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertFileExists(string $filename, string $message = '断言失败：文件应存在'): void
    {
        if (!file_exists($filename)) {
            throw new \Exception($message . "：{$filename}");
        }
    }

    /**
     * 断言文件不存在
     *
     * @param string $filename 文件路径
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertFileNotExists(string $filename, string $message = '断言失败：文件应不存在'): void
    {
        if (file_exists($filename)) {
            throw new \Exception($message . "：{$filename}");
        }
    }

    /**
     * 断言目录存在
     *
     * @param string $directory 目录路径
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertDirectoryExists(string $directory, string $message = '断言失败：目录应存在'): void
    {
        if (!is_dir($directory)) {
            throw new \Exception($message . "：{$directory}");
        }
    }

    /**
     * 断言目录不存在
     *
     * @param string $directory 目录路径
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertDirectoryNotExists(string $directory, string $message = '断言失败：目录应不存在'): void
    {
        if (is_dir($directory)) {
            throw new \Exception($message . "：{$directory}");
        }
    }

    /**
     * 断言窗口存在
     *
     * @param string $id 窗口ID
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertWindowExists(string $id, string $message = '断言失败：窗口应存在'): void
    {
        if (!Window::exists($id)) {
            throw new \Exception($message . "：{$id}");
        }
    }

    /**
     * 断言窗口不存在
     *
     * @param string $id 窗口ID
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function assertWindowNotExists(string $id, string $message = '断言失败：窗口应不存在'): void
    {
        if (Window::exists($id)) {
            throw new \Exception($message . "：{$id}");
        }
    }

    /**
     * 等待条件为真
     *
     * @param callable $condition 条件函数
     * @param int $timeout 超时时间（秒）
     * @param int $interval 检查间隔（毫秒）
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function waitUntil(callable $condition, int $timeout = 10, int $interval = 100, string $message = '等待超时'): void
    {
        $startTime = microtime(true);
        $endTime = $startTime + $timeout;

        while (microtime(true) < $endTime) {
            if ($condition()) {
                return;
            }

            usleep($interval * 1000);
        }

        throw new \Exception($message);
    }

    /**
     * 等待窗口出现
     *
     * @param string $id 窗口ID
     * @param int $timeout 超时时间（秒）
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function waitForWindow(string $id, int $timeout = 10, string $message = '等待窗口超时'): void
    {
        $this->waitUntil(function () use ($id) {
            return Window::exists($id);
        }, $timeout, 100, $message . "：{$id}");
    }

    /**
     * 等待窗口消失
     *
     * @param string $id 窗口ID
     * @param int $timeout 超时时间（秒）
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function waitForWindowClose(string $id, int $timeout = 10, string $message = '等待窗口关闭超时'): void
    {
        $this->waitUntil(function () use ($id) {
            return !Window::exists($id);
        }, $timeout, 100, $message . "：{$id}");
    }

    /**
     * 等待文件出现
     *
     * @param string $filename 文件路径
     * @param int $timeout 超时时间（秒）
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function waitForFile(string $filename, int $timeout = 10, string $message = '等待文件超时'): void
    {
        $this->waitUntil(function () use ($filename) {
            return file_exists($filename);
        }, $timeout, 100, $message . "：{$filename}");
    }

    /**
     * 等待文件消失
     *
     * @param string $filename 文件路径
     * @param int $timeout 超时时间（秒）
     * @param string $message 错误消息
     * @return void
     * @throws \Exception
     */
    public function waitForFileRemove(string $filename, int $timeout = 10, string $message = '等待文件删除超时'): void
    {
        $this->waitUntil(function () use ($filename) {
            return !file_exists($filename);
        }, $timeout, 100, $message . "：{$filename}");
    }
}