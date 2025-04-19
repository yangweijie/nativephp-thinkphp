<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Printer;
use Native\ThinkPHP\Facades\Logger;

class PrinterPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'printer';

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
    protected $description = '打印机插件';

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
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Printer plugin initialized');

        // 监听打印事件
        $this->app->event->listen('native.printer.print', function ($event) {
            $this->handlePrint($event);
        });

        $this->app->event->listen('native.printer.print_to_pdf', function ($event) {
            $this->handlePrintToPdf($event);
        });

        $this->app->event->listen('native.printer.print_preview', function ($event) {
            $this->handlePrintPreview($event);
        });
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Printer plugin started');

        // 创建临时目录
        $this->createTempDirectory();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Printer plugin quit');

        // 清理临时目录
        $this->cleanTempDirectory();
    }

    /**
     * 创建临时目录
     *
     * @return void
     */
    protected function createTempDirectory(): void
    {
        // 获取配置
        $config = config('native.printer', []);
        $tempPath = $config['temp_path'] ?? $this->app->getRuntimePath() . 'temp/print';

        // 创建临时目录
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        // 保存临时目录到配置
        /** @phpstan-ignore-next-line */
        config(['native.printer.temp_path' => $tempPath]);
    }

    /**
     * 清理临时目录
     *
     * @return void
     */
    protected function cleanTempDirectory(): void
    {
        // 获取配置
        $config = config('native.printer', []);
        $tempPath = $config['temp_path'] ?? $this->app->getRuntimePath() . 'temp/print';

        // 清理临时目录
        if (is_dir($tempPath)) {
            $files = glob($tempPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    /**
     * 处理打印事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePrint(array $event): void
    {
        // 记录打印事件
        $config = config('native.printer', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Print', [
                'type' => $event['type'] ?? 'html',
                'printer' => $event['printer'] ?? null,
            ]);
        }
    }

    /**
     * 处理打印到 PDF 事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePrintToPdf(array $event): void
    {
        // 记录打印到 PDF 事件
        $config = config('native.printer', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Print to PDF', [
                'output_path' => $event['output_path'] ?? null,
            ]);
        }
    }

    /**
     * 处理打印预览事件
     *
     * @param array $event
     * @return void
     */
    protected function handlePrintPreview(array $event): void
    {
        // 记录打印预览事件
        $config = config('native.printer', []);
        if (isset($config['log_operations']) && $config['log_operations']) {
            Logger::info('Print preview');
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('Printer plugin unloaded');

        // 清理临时目录
        $this->cleanTempDirectory();
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
