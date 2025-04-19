<?php

namespace Native\ThinkPHP\Utils;

use think\App as ThinkApp;
use Native\ThinkPHP\Facades\FileSystem;

/**
 * 性能优化器
 */
class PerformanceOptimizer
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 优化配置
     *
     * @var array
     */
    protected $config = [];

    /**
     * 优化报告
     *
     * @var array
     */
    protected $report = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(ThinkApp $app, array $config = [])
    {
        $this->app = $app;
        $this->config = array_merge([
            'cache_dir' => $app->getRuntimePath() . 'cache/',
            'log_dir' => $app->getRuntimePath() . 'log/',
            'temp_dir' => $app->getRuntimePath() . 'temp/',
            'max_cache_size' => 100 * 1024 * 1024, // 100MB
            'max_log_size' => 50 * 1024 * 1024, // 50MB
            'max_temp_size' => 200 * 1024 * 1024, // 200MB
            'max_cache_files' => 1000,
            'max_log_files' => 100,
            'max_temp_files' => 500,
            'log_retention_days' => 7,
        ], $config);

        // 初始化报告
        $this->report = [
            'start_time' => microtime(true),
            'optimizations' => [],
            'errors' => [],
        ];
    }

    /**
     * 执行优化
     *
     * @return bool
     */
    public function optimize()
    {
        try {
            // 清理缓存目录
            $this->cleanDirectory($this->config['cache_dir'], $this->config['max_cache_size'], $this->config['max_cache_files']);
            $this->report['optimizations'][] = [
                'type' => 'cache_cleanup',
                'message' => '缓存目录已清理',
            ];

            // 清理日志目录
            $this->cleanLogDirectory($this->config['log_dir'], $this->config['log_retention_days']);
            $this->report['optimizations'][] = [
                'type' => 'log_cleanup',
                'message' => '日志目录已清理',
            ];

            // 清理临时目录
            $this->cleanDirectory($this->config['temp_dir'], $this->config['max_temp_size'], $this->config['max_temp_files']);
            $this->report['optimizations'][] = [
                'type' => 'temp_cleanup',
                'message' => '临时目录已清理',
            ];

            // 记录完成时间
            $this->report['end_time'] = microtime(true);
            $this->report['duration'] = $this->report['end_time'] - $this->report['start_time'];

            return true;
        } catch (\Exception $e) {
            $this->report['errors'][] = [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ];

            return false;
        }
    }

    /**
     * 获取优化报告
     *
     * @return array
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * 清理目录
     *
     * @param string $directory
     * @param int $maxSize
     * @param int $maxFiles
     * @return void
     */
    protected function cleanDirectory($directory, $maxSize, $maxFiles)
    {
        if (!is_dir($directory)) {
            return;
        }

        // 获取目录大小和文件数量
        $totalSize = 0;
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size = $file->getSize();
                $totalSize += $size;
                $files[] = [
                    'path' => $file->getPathname(),
                    'size' => $size,
                    'time' => $file->getMTime(),
                ];
            }
        }

        // 如果文件数量超过最大值，按修改时间排序并删除最旧的文件
        if (count($files) > $maxFiles) {
            usort($files, function ($a, $b) {
                return $a['time'] - $b['time'];
            });

            $filesToDelete = array_slice($files, 0, count($files) - $maxFiles);
            foreach ($filesToDelete as $file) {
                unlink($file['path']);
                $totalSize -= $file['size'];
            }
        }

        // 如果目录大小超过最大值，按修改时间排序并删除最旧的文件
        if ($totalSize > $maxSize) {
            usort($files, function ($a, $b) {
                return $a['time'] - $b['time'];
            });

            $sizeToDelete = $totalSize - $maxSize;
            $deletedSize = 0;

            foreach ($files as $file) {
                if ($deletedSize >= $sizeToDelete) {
                    break;
                }

                unlink($file['path']);
                $deletedSize += $file['size'];
            }
        }
    }

    /**
     * 清理日志目录
     *
     * @param string $directory
     * @param int $retentionDays
     * @return void
     */
    protected function cleanLogDirectory($directory, $retentionDays)
    {
        if (!is_dir($directory)) {
            return;
        }

        // 获取当前时间戳
        $now = time();
        $maxAge = $retentionDays * 24 * 60 * 60;

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $fileAge = $now - $file->getMTime();
                if ($fileAge > $maxAge) {
                    unlink($file->getPathname());
                }
            }
        }
    }
}
