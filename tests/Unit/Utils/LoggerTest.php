<?php

namespace Native\ThinkPHP\Tests\Unit\Utils;

use Native\ThinkPHP\Tests\TestCase;
use Native\ThinkPHP\Utils\Logger;
use Native\ThinkPHP\Facades\FileSystem;

class LoggerTest extends TestCase
{
    /**
     * 日志文件路径
     *
     * @var string
     */
    protected $logFile;

    /**
     * 设置测试环境
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->logFile = __DIR__ . '/../../runtime/logs/test.log';

        // 确保日志目录存在
        $dir = dirname($this->logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // 清空日志文件
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    /**
     * 测试创建日志实例
     *
     * @return void
     */
    public function testCreateLogger()
    {
        $logger = new Logger($this->app, $this->logFile);

        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertEquals($this->logFile, $logger->getLogFile());
    }

    /**
     * 测试写入日志
     *
     * @return void
     */
    public function testWriteLog()
    {
        $logger = new Logger($this->app, $this->logFile);

        $result = $logger->info('Test message');

        $this->assertTrue($result);
        $this->assertTrue(file_exists($this->logFile));

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[INFO] Test message', $content);
    }

    /**
     * 测试写入带上下文的日志
     *
     * @return void
     */
    public function testWriteLogWithContext()
    {
        $logger = new Logger($this->app, $this->logFile);

        $result = $logger->error('Error message', ['code' => 500, 'message' => 'Internal Server Error']);

        $this->assertTrue($result);

        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('[ERROR] Error message', $content);
        $this->assertStringContainsString('500', $content);
        $this->assertStringContainsString('Internal Server Error', $content);
    }

    /**
     * 测试设置日志级别
     *
     * @return void
     */
    public function testSetLogLevel()
    {
        $logger = new Logger($this->app, $this->logFile, 'error');

        // 这条日志不应该被写入，因为级别低于 error
        $logger->info('Info message');

        // 这条日志应该被写入，因为级别等于 error
        $logger->error('Error message');

        $content = file_get_contents($this->logFile);
        $this->assertStringNotContainsString('[INFO] Info message', $content);
        $this->assertStringContainsString('[ERROR] Error message', $content);
    }

    /**
     * 测试获取日志内容
     *
     * @return void
     */
    public function testGetLogContent()
    {
        $logger = new Logger($this->app, $this->logFile);

        $logger->info('Line 1');
        $logger->info('Line 2');
        $logger->info('Line 3');

        $content = $logger->get();

        $this->assertStringContainsString('Line 1', $content);
        $this->assertStringContainsString('Line 2', $content);
        $this->assertStringContainsString('Line 3', $content);

        // 测试获取指定行数
        $content = $logger->get(2);

        $this->assertStringNotContainsString('Line 1', $content);
        $this->assertStringContainsString('Line 2', $content);
        $this->assertStringContainsString('Line 3', $content);
    }

    /**
     * 测试清空日志
     *
     * @return void
     */
    public function testClearLog()
    {
        $logger = new Logger($this->app, $this->logFile);

        $logger->info('Test message');

        $this->assertNotEmpty(file_get_contents($this->logFile));

        $result = $logger->clear();

        $this->assertTrue($result);
        $this->assertEmpty(file_get_contents($this->logFile));
    }

    /**
     * 测试轮换日志文件
     *
     * @return void
     */
    public function testRotateLog()
    {
        $logger = new Logger($this->app, $this->logFile);

        // 写入一些日志
        for ($i = 0; $i < 10; $i++) {
            $logger->info('Test message ' . $i);
        }

        // 获取日志文件大小
        $size = $logger->size();

        // 轮换日志文件
        $result = $logger->rotate($size - 1, 3);

        $this->assertTrue($result);
        $this->assertEmpty(file_get_contents($this->logFile));

        // 检查是否创建了备份文件
        $backupFiles = glob(dirname($this->logFile) . '/test.log.*');
        $this->assertNotEmpty($backupFiles);
    }

    /**
     * 清理测试环境
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // 删除日志文件
        if ($this->logFile && file_exists($this->logFile)) {
            unlink($this->logFile);
        }

        // 删除备份文件
        if ($this->logFile) {
            $logDir = dirname($this->logFile);
            $backupFiles = glob($logDir . '/test.log.*');
            if (is_array($backupFiles)) {
                foreach ($backupFiles as $file) {
                    unlink($file);
                }
            }
        }

        parent::tearDown();
    }
}
