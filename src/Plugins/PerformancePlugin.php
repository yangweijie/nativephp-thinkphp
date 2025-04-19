<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Utils\PerformanceOptimizer;

class PerformancePlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'performance';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '性能优化插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 性能优化器
     *
     * @var \Native\ThinkPHP\Utils\PerformanceOptimizer
     */
    protected $optimizer;

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 创建性能优化器
        $this->optimizer = new PerformanceOptimizer($app);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.end' => [$this, 'onAppEnd'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 优化应用程序
        $this->optimizer->optimize();
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录应用启动时间
        $startTime = microtime(true);
        \Native\ThinkPHP\Facades\Settings::set('app_start_time', $startTime);
    }

    /**
     * 应用结束事件处理
     *
     * @return void
     */
    public function onAppEnd(): void
    {
        // 记录应用结束时间
        $endTime = microtime(true);
        $startTime = \Native\ThinkPHP\Facades\Settings::get('app_start_time', $endTime);
        $duration = $endTime - $startTime;

        // 记录应用运行时间
        \Native\ThinkPHP\Facades\Logger::info('Application ended', [
            'duration' => round($duration, 4),
        ]);

        // 分析应用程序性能
        $report = $this->optimizer->getReport();

        // 记录性能报告
        \Native\ThinkPHP\Facades\Logger::info('Performance report', [
            'summary' => $report['summary'],
            'recommendations' => $report['recommendations'],
        ]);
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 清除性能优化设置
        \Native\ThinkPHP\Facades\Settings::set('performance', []);
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}