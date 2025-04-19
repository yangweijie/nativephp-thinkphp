<?php

namespace Native\ThinkPHP\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use think\App;
use think\Container;

// 定义测试环境标记
if (!defined('PHPUNIT_RUNNING')) {
    define('PHPUNIT_RUNNING', true);
}

abstract class TestCase extends BaseTestCase
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 设置测试环境
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // 创建应用实例
        $this->app = new App();

        // 绑定应用实例到容器
        Container::setInstance($this->app);

        // 加载配置
        $this->loadConfig();

        // 注册服务提供者
        $this->registerServiceProvider();
    }

    /**
     * 加载配置
     *
     * @return void
     */
    protected function loadConfig()
    {
        // 加载测试配置
        $config = require __DIR__ . '/config/native.php';
        $this->app->config->set($config, 'native');
    }

    /**
     * 注册服务提供者
     *
     * @return void
     */
    protected function registerServiceProvider()
    {
        $provider = new \Native\ThinkPHP\NativeServiceProvider($this->app);
        $provider->register();
        $provider->boot();
    }

    /**
     * 清理测试环境
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->app = null;
        Container::setInstance(null);

        parent::tearDown();
    }
}
