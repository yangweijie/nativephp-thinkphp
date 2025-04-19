<?php

namespace Native\ThinkPHP\Plugins;

use think\App;

class PluginManager
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 已加载的插件
     *
     * @var array
     */
    protected $plugins = [];

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
     */
    public function __construct(App $app)
    {
        $this->app = $app;
    }

    /**
     * 加载插件
     *
     * @param string $name 插件名称
     * @param array $config 插件配置
     * @return bool
     */
    public function load(string $name, array $config = []): bool
    {
        // 检查插件是否已加载
        if (isset($this->plugins[$name])) {
            return true;
        }

        // 检查插件是否存在
        $pluginClass = "\\Native\\ThinkPHP\\Plugins\\{$name}Plugin";
        if (!class_exists($pluginClass)) {
            // 尝试旧格式的插件路径
            $pluginClass = "\\Native\\ThinkPHP\\Plugins\\{$name}\\Plugin";
            if (!class_exists($pluginClass)) {
                return false;
            }
        }

        // 实例化插件
        $plugin = new $pluginClass($this->app, $config);

        // 初始化插件
        $plugin->init();

        // 注册插件
        $this->plugins[$name] = $plugin;

        // 注册插件钩子
        foreach ($plugin->getHooks() as $hook => $callback) {
            $this->registerHook($hook, $callback);
        }

        return true;
    }

    /**
     * 注册钩子
     *
     * @param string $hook 钩子名称
     * @param callable|array $callback 回调函数
     * @return void
     */
    public function registerHook(string $hook, $callback): void
    {
        // 确保回调是可调用的
        if (!is_callable($callback)) {
            // 如果回调不可调用，返回
            return;
        }
        if (!isset($this->hooks[$hook])) {
            $this->hooks[$hook] = [];
        }

        $this->hooks[$hook][] = $callback;
    }

    /**
     * 触发钩子
     *
     * @param string $hook 钩子名称
     * @param array $params 参数
     * @return array
     */
    public function triggerHook(string $hook, array $params = []): array
    {
        $results = [];

        if (isset($this->hooks[$hook])) {
            foreach ($this->hooks[$hook] as $callback) {
                $results[] = call_user_func_array($callback, $params);
            }
        }

        return $results;
    }

    /**
     * 获取已加载的插件
     *
     * @return array
     */
    public function getPlugins(): array
    {
        return $this->plugins;
    }

    /**
     * 获取插件
     *
     * @param string $name 插件名称
     * @return mixed|null
     */
    public function getPlugin(string $name)
    {
        return $this->plugins[$name] ?? null;
    }

    /**
     * 卸载插件
     *
     * @param string $name 插件名称
     * @return bool
     */
    public function unload(string $name): bool
    {
        if (!isset($this->plugins[$name])) {
            return false;
        }

        // 卸载插件
        $this->plugins[$name]->unload();

        // 移除插件钩子
        foreach ($this->hooks as $hook => $callbacks) {
            foreach ($callbacks as $index => $callback) {
                if (is_array($callback) && $callback[0] === $this->plugins[$name]) {
                    unset($this->hooks[$hook][$index]);
                }
            }
        }

        // 移除插件
        unset($this->plugins[$name]);

        return true;
    }
}