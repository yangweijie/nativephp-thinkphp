<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Logger;

class DialogPlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'dialog';

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
    protected $description = '对话框插件';

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
            'app.error' => [$this, 'onAppError'],
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
        Logger::info('Dialog plugin initialized');

        // 监听对话框事件
        $this->app->event->listen('native.dialog.show', function ($event) {
            $this->handleDialogShow($event);
        });

        $this->app->event->listen('native.dialog.result', function ($event) {
            $this->handleDialogResult($event);
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
        Logger::info('Dialog plugin started');

        // 检查是否需要显示启动对话框
        $config = config('native.dialog', []);
        if (isset($config['show_startup_dialog']) && $config['show_startup_dialog']) {
            $this->showStartupDialog();
        }
    }

    /**
     * 应用错误事件处理
     *
     * @param \Exception $exception
     * @return void
     */
    public function onAppError($exception): void
    {
        // 检查是否需要显示错误对话框
        $config = config('native.dialog', []);
        if (isset($config['show_error_dialog']) && $config['show_error_dialog']) {
            $this->showErrorDialog($exception);
        }
    }

    /**
     * 显示启动对话框
     *
     * @return void
     */
    protected function showStartupDialog(): void
    {
        $config = config('native.dialog', []);
        $startupDialog = $config['startup_dialog'] ?? [];

        if (empty($startupDialog)) {
            return;
        }

        $type = $startupDialog['type'] ?? 'info';
        $message = $startupDialog['message'] ?? '应用已启动';
        $title = $startupDialog['title'] ?? config('native.name', 'NativePHP');
        $options = $startupDialog['options'] ?? [];

        // 合并选项
        $options = array_merge(['title' => $title], $options);

        // 显示对话框
        switch ($type) {
            case 'error':
                Dialog::error($message, $options);
                break;
            case 'warning':
                Dialog::warning($message, $options);
                break;
            case 'question':
                Dialog::question($message, $options);
                break;
            case 'confirm':
                Dialog::confirm($message, $options);
                break;
            case 'prompt':
                Dialog::prompt($message, $options);
                break;
            case 'info':
            default:
                Dialog::info($message, $options);
                break;
        }
    }

    /**
     * 显示错误对话框
     *
     * @param \Exception $exception
     * @return void
     */
    protected function showErrorDialog($exception): void
    {
        $config = config('native.dialog', []);
        $errorDialog = $config['error_dialog'] ?? [];

        $title = $errorDialog['title'] ?? '应用错误';
        $options = $errorDialog['options'] ?? [];

        // 合并选项
        $options = array_merge(['title' => $title], $options);

        // 显示错误对话框
        Dialog::error($exception->getMessage(), $options);
    }

    /**
     * 处理对话框显示事件
     *
     * @param array $event
     * @return void
     */
    protected function handleDialogShow(array $event): void
    {
        // 记录对话框显示
        Logger::info('Dialog shown', [
            'type' => $event['type'] ?? 'unknown',
            'title' => $event['title'] ?? null,
        ]);
    }

    /**
     * 处理对话框结果事件
     *
     * @param array $event
     * @return void
     */
    protected function handleDialogResult(array $event): void
    {
        // 记录对话框结果
        Logger::info('Dialog result', [
            'type' => $event['type'] ?? 'unknown',
            'result' => $event['result'] ?? null,
        ]);
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('Dialog plugin unloaded');
    }


}
