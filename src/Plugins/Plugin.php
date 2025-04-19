<?php

namespace Native\ThinkPHP\Plugins;

use think\App;

/**
 * 插件基类
 */
abstract class Plugin
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = '';

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
    protected $description = '';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = '';

    /**
     * 插件配置
     *
     * @var array
     */
    protected $config = [];

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
        $this->app = $app;
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    abstract public function init(): void;

    /**
     * 卸载插件
     *
     * @return void
     */
    abstract public function unload(): void;

    /**
     * 获取插件名称
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * 获取插件版本
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * 获取插件描述
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * 获取插件作者
     *
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * 获取插件配置
     *
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        $hooks = [];
        foreach ($this->hooks as $hook => $method) {
            if (is_string($method)) {
                $hooks[$hook] = [$this, $method];
            } else {
                $hooks[$hook] = $method;
            }
        }
        return $hooks;
    }
}
