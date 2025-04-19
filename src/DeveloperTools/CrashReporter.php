<?php

namespace Native\ThinkPHP\DeveloperTools;

use think\App;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Settings;

class CrashReporter
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 是否已注册
     *
     * @var bool
     */
    protected $registered = false;

    /**
     * 崩溃报告
     *
     * @var array
     */
    protected $reports = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->loadReports();
    }

    /**
     * 注册崩溃报告器
     *
     * @return void
     */
    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        // 注册异常处理器
        set_exception_handler([$this, 'handleException']);

        // 注册错误处理器
        set_error_handler([$this, 'handleError']);

        // 注册致命错误处理器
        register_shutdown_function([$this, 'handleFatalError']);

        $this->registered = true;
    }

    /**
     * 取消注册崩溃报告器
     *
     * @return void
     */
    public function unregister(): void
    {
        if (!$this->registered) {
            return;
        }

        // 恢复异常处理器
        restore_exception_handler();

        // 恢复错误处理器
        restore_error_handler();

        $this->registered = false;
    }

    /**
     * 处理异常
     *
     * @param \Throwable $exception
     * @return void
     */
    public function handleException(\Throwable $exception): void
    {
        // 创建崩溃报告
        $report = $this->createReport('exception', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
        ]);

        // 保存崩溃报告
        $this->saveReport($report);

        // 显示通知
        Notification::send('应用程序崩溃', '发生异常：' . $exception->getMessage());
    }

    /**
     * 处理错误
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return bool
     */
    public function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // 创建崩溃报告
        $report = $this->createReport('error', [
            'errno' => $errno,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
        ]);

        // 保存崩溃报告
        $this->saveReport($report);

        // 显示通知
        Notification::send('应用程序错误', '发生错误：' . $errstr);

        // 返回 false 表示继续执行 PHP 内部的错误处理器
        return false;
    }

    /**
     * 处理致命错误
     *
     * @return void
     */
    public function handleFatalError(): void
    {
        // 获取最后一个错误
        $error = error_get_last();

        // 检查是否是致命错误
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            // 创建崩溃报告
            $report = $this->createReport('fatal_error', [
                'errno' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
            ]);

            // 保存崩溃报告
            $this->saveReport($report);

            // 显示通知
            Notification::send('应用程序崩溃', '发生致命错误：' . $error['message']);
        }
    }

    /**
     * 创建崩溃报告
     *
     * @param string $type 报告类型
     * @param array $data 报告数据
     * @return array
     */
    protected function createReport(string $type, array $data): array
    {
        // 获取当前时间
        $time = time();

        // 获取应用信息
        $app = [
            'name' => $this->app->config->get('app.name'),
            'version' => $this->app->config->get('app.version'),
            'env' => $this->app->config->get('app.env'),
        ];

        // 获取系统信息
        $system = [
            'os' => PHP_OS,
            'php' => PHP_VERSION,
            'memory' => memory_get_usage(),
        ];

        // 创建报告
        $report = [
            'id' => uniqid('crash_'),
            'time' => $time,
            'type' => $type,
            'data' => $data,
            'app' => $app,
            'system' => $system,
        ];

        return $report;
    }

    /**
     * 保存崩溃报告
     *
     * @param array $report 崩溃报告
     * @return bool
     */
    protected function saveReport(array $report): bool
    {
        // 添加到报告列表
        $this->reports[$report['id']] = $report;

        // 保存报告到设置
        Settings::set('developer.crash_reports', $this->reports);

        // 保存报告到文件
        $reportDir = $this->app->getRuntimePath() . 'crash_reports';
        if (!is_dir($reportDir)) {
            mkdir($reportDir, 0755, true);
        }

        $reportFile = $reportDir . '/' . $report['id'] . '.json';
        FileSystem::write($reportFile, json_encode($report, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * 获取崩溃报告
     *
     * @param string $id 报告ID
     * @return array|null
     */
    public function getReport(string $id): ?array
    {
        return $this->reports[$id] ?? null;
    }

    /**
     * 获取所有崩溃报告
     *
     * @return array
     */
    public function getReports(): array
    {
        return $this->reports;
    }

    /**
     * 删除崩溃报告
     *
     * @param string $id 报告ID
     * @return bool
     */
    public function deleteReport(string $id): bool
    {
        if (!isset($this->reports[$id])) {
            return false;
        }

        // 从报告列表中删除
        unset($this->reports[$id]);

        // 更新设置
        Settings::set('developer.crash_reports', $this->reports);

        // 删除报告文件
        $reportFile = $this->app->getRuntimePath() . 'crash_reports/' . $id . '.json';
        if (file_exists($reportFile)) {
            unlink($reportFile);
        }

        return true;
    }

    /**
     * 清除所有崩溃报告
     *
     * @return bool
     */
    public function clearReports(): bool
    {
        // 清空报告列表
        $this->reports = [];

        // 更新设置
        Settings::set('developer.crash_reports', $this->reports);

        // 删除报告文件
        $reportDir = $this->app->getRuntimePath() . 'crash_reports';
        if (is_dir($reportDir)) {
            $files = glob($reportDir . '/*.json');
            foreach ($files as $file) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * 加载崩溃报告
     *
     * @return void
     */
    protected function loadReports(): void
    {
        // 从设置中加载报告
        $reports = Settings::get('developer.crash_reports', []);
        $this->reports = $reports;
    }
}