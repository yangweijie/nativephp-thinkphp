<?php

namespace Native\ThinkPHP\DeveloperTools;

use think\App;
use Native\ThinkPHP\Facades\Settings;

class PerformanceMonitor
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 是否正在监控
     *
     * @var bool
     */
    protected $monitoring = false;

    /**
     * 监控数据
     *
     * @var array
     */
    protected $data = [];

    /**
     * 监控间隔（秒）
     *
     * @var int
     */
    protected $interval = 1;

    /**
     * 监控线程
     *
     * @var resource|null
     */
    protected $thread = null;

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
     * 开始监控
     *
     * @param int $interval 监控间隔（秒）
     * @return void
     */
    public function start(int $interval = 1): void
    {
        if ($this->monitoring) {
            return;
        }

        $this->interval = $interval;
        $this->monitoring = true;
        $this->data = [];

        // 启动监控线程
        $this->startMonitoringThread();
    }

    /**
     * 停止监控
     *
     * @return void
     */
    public function stop(): void
    {
        if (!$this->monitoring) {
            return;
        }

        $this->monitoring = false;

        // 停止监控线程
        $this->stopMonitoringThread();
    }

    /**
     * 获取监控数据
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * 清除监控数据
     *
     * @return void
     */
    public function clearData(): void
    {
        $this->data = [];
    }

    /**
     * 启动监控线程
     *
     * @return void
     */
    protected function startMonitoringThread(): void
    {
        // 使用定时器定期收集性能数据
        $this->thread = function () {
            while ($this->monitoring) {
                // 收集性能数据
                $this->collectPerformanceData();

                // 等待下一个监控周期
                sleep($this->interval);
            }
        };

        // 启动线程
        $this->thread();
    }

    /**
     * 停止监控线程
     *
     * @return void
     */
    protected function stopMonitoringThread(): void
    {
        $this->thread = null;
    }

    /**
     * 收集性能数据
     *
     * @return void
     */
    protected function collectPerformanceData(): void
    {
        // 获取当前时间
        $time = time();

        // 获取内存使用情况
        $memory = [
            'current' => memory_get_usage(),
            'peak' => memory_get_peak_usage(),
        ];

        // 获取 CPU 使用情况
        $cpu = $this->getCpuUsage();

        // 获取网络使用情况
        $network = $this->getNetworkUsage();

        // 获取磁盘使用情况
        $disk = $this->getDiskUsage();

        // 保存性能数据
        $this->data[] = [
            'time' => $time,
            'memory' => $memory,
            'cpu' => $cpu,
            'network' => $network,
            'disk' => $disk,
        ];

        // 限制数据量
        if (count($this->data) > 1000) {
            array_shift($this->data);
        }

        // 保存性能数据到设置
        Settings::set('developer.performance', $this->data);
    }

    /**
     * 获取 CPU 使用情况
     *
     * @return array
     */
    protected function getCpuUsage(): array
    {
        // 获取 CPU 使用情况
        $usage = 0;

        // 在 Windows 上使用 wmic
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'wmic cpu get load percentage';
            $output = shell_exec($cmd);
            if (preg_match('/(\d+)/', $output, $matches)) {
                $usage = (float) $matches[1];
            }
        }
        // 在 Linux/Unix 上使用 top
        else {
            $cmd = 'top -bn1 | grep "Cpu(s)" | sed "s/.*, *\([0-9.]*\)%* id.*/\1/" | awk \'{print 100 - $1}\'';
            $output = shell_exec($cmd);
            if (is_string($output)) {
                $usage = (float) trim($output);
            }
        }

        return [
            'usage' => $usage,
        ];
    }

    /**
     * 获取网络使用情况
     *
     * @return array
     */
    protected function getNetworkUsage(): array
    {
        // 获取网络使用情况
        $received = 0;
        $sent = 0;

        // 在 Windows 上使用 netstat
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'netstat -e';
            $output = shell_exec($cmd);
            if (preg_match('/Bytes\s+(\d+)\s+(\d+)/', $output, $matches)) {
                $received = (int) $matches[1];
                $sent = (int) $matches[2];
            }
        }
        // 在 Linux/Unix 上使用 ifconfig
        else {
            $cmd = 'ifconfig | grep -E "RX bytes|TX bytes"';
            $output = shell_exec($cmd);
            if (is_string($output)) {
                if (preg_match('/RX bytes:(\d+)/', $output, $matches)) {
                    $received = (int) $matches[1];
                }
                if (preg_match('/TX bytes:(\d+)/', $output, $matches)) {
                    $sent = (int) $matches[1];
                }
            }
        }

        return [
            'received' => $received,
            'sent' => $sent,
        ];
    }

    /**
     * 获取磁盘使用情况
     *
     * @return array
     */
    protected function getDiskUsage(): array
    {
        // 获取磁盘使用情况
        $total = disk_total_space('.');
        $free = disk_free_space('.');
        $used = $total - $free;
        $usage = ($used / $total) * 100;

        return [
            'total' => $total,
            'free' => $free,
            'used' => $used,
            'usage' => $usage,
        ];
    }
}