<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;
use Native\ThinkPHP\Contracts\CacheAdapter;
use Native\ThinkPHP\Cache\CacheFactory;

class ChildProcess
{
    /**
     * ThinkPHP 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 客户端实例
     *
     * @var \Native\ThinkPHP\Client\Client
     */
    protected $client;

    /**
     * 子进程列表
     *
     * @var array
     */
    protected $processes = [];

    /**
     * 缓存适配器
     *
     * @var \Native\ThinkPHP\Contracts\CacheAdapter
     */
    protected $cacheAdapter;

    /**
     * 构造函数
     *
     * @param \think\App|object $app
     * @param \Native\ThinkPHP\Contracts\CacheAdapter|null $cacheAdapter 缓存适配器
     */
    public function __construct($app, CacheAdapter $cacheAdapter = null)
    {
        // 在测试环境中接受任何对象
        if (defined('PHPUNIT_RUNNING') && !($app instanceof ThinkApp)) {
            $app = app();
        }
        $this->app = $app;
        $this->client = new Client();
        $this->cacheAdapter = $cacheAdapter ?: CacheFactory::create('memory', ['ttl' => 5]);
    }

    /**
     * 启动子进程
     *
     * @param string|array $cmd 命令或命令数组
     * @param string $alias 别名
     * @param string|null $cwd 工作目录
     * @param bool $persistent 是否持久化
     * @param array $env 环境变量
     * @return \Native\ThinkPHP\ChildProcess
     */
    public function start($cmd, $alias, $cwd = null, $persistent = false, array $env = [])
    {
        // 如果命令是数组，则转换为字符串
        if (is_array($cmd)) {
            $cmd = implode(' ', array_map('escapeshellarg', $cmd));
        }

        // 调用 NativePHP 客户端启动子进程
        $response = $this->client->post('child-process/start', [
            'cmd' => $cmd,
            'alias' => $alias,
            'cwd' => $cwd,
            'persistent' => $persistent,
            'env' => $env,
        ]);

        // 保存进程信息
        $this->processes[$alias] = [
            'cmd' => $cmd,
            'alias' => $alias,
            'cwd' => $cwd,
            'persistent' => $persistent,
            'env' => $env,
            'pid' => $response->json('pid'),
            'status' => 'running',
        ];

        // 清除缓存
        $this->clearCache($alias);

        return $this;
    }

    /**
     * 获取子进程
     *
     * @param string $alias 别名
     * @param bool $useCache 是否使用缓存
     * @return array|null
     */
    public function get($alias, $useCache = true)
    {
        $cacheKey = 'process:' . $alias;

        // 如果使用缓存且缓存存在，则返回缓存
        if ($useCache && $this->cacheAdapter->has($cacheKey)) {
            return $this->cacheAdapter->get($cacheKey);
        }

        // 调用 NativePHP 客户端获取子进程
        $response = $this->client->get('child-process/get', [
            'alias' => $alias,
        ]);

        // 更新进程信息
        if ($response->json('success')) {
            $this->processes[$alias] = $response->json('process');

            // 更新缓存
            if ($useCache) {
                $this->cacheAdapter->set($cacheKey, $this->processes[$alias]);
            }
        }

        return isset($this->processes[$alias]) ? $this->processes[$alias] : null;
    }

    /**
     * 获取所有子进程
     *
     * @param bool $useCache 是否使用缓存
     * @return array
     */
    public function all($useCache = true)
    {
        $cacheKey = 'process:all';

        // 如果使用缓存且缓存存在，则返回缓存
        if ($useCache && $this->cacheAdapter->has($cacheKey)) {
            return $this->cacheAdapter->get($cacheKey);
        }

        // 调用 NativePHP 客户端获取所有子进程
        $response = $this->client->get('child-process/all');

        // 更新进程信息
        if ($response->json('success')) {
            $this->processes = $response->json('processes');

            // 更新缓存
            if ($useCache) {
                $this->cacheAdapter->set($cacheKey, $this->processes);
            }
        }

        return $this->processes;
    }

    /**
     * 停止子进程
     *
     * @param string $alias 别名
     * @return bool
     */
    public function stop($alias)
    {
        // 调用 NativePHP 客户端停止子进程
        $response = $this->client->post('child-process/stop', [
            'alias' => $alias,
        ]);

        // 更新进程状态
        if ($response->json('success')) {
            if (isset($this->processes[$alias])) {
                $this->processes[$alias]['status'] = 'stopped';
            }

            // 清除缓存
            $this->clearCache('process:' . $alias);
            $this->clearCache('process:all');

            return true;
        }

        return false;
    }

    /**
     * 重启子进程
     *
     * @param string $alias 别名
     * @return bool
     */
    public function restart($alias)
    {
        // 调用 NativePHP 客户端重启子进程
        $response = $this->client->post('child-process/restart', [
            'alias' => $alias,
        ]);

        // 更新进程状态
        if ($response->json('success')) {
            if (isset($this->processes[$alias])) {
                $this->processes[$alias]['status'] = 'running';
                $this->processes[$alias]['pid'] = $response->json('pid');
            }

            // 清除缓存
            $this->clearCache('process:' . $alias);
            $this->clearCache('process:all');

            return true;
        }

        return false;
    }

    /**
     * 向子进程发送消息
     *
     * @param string $message 消息
     * @param string $alias 别名
     * @return bool
     */
    public function message($message, $alias)
    {
        // 调用 NativePHP 客户端向子进程发送消息
        $response = $this->client->post('child-process/message', [
            'message' => $message,
            'alias' => $alias,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 运行 PHP 脚本
     *
     * @param string $script 脚本路径
     * @param string $alias 别名
     * @param array $args 参数
     * @param string|null $cwd 工作目录
     * @param bool $persistent 是否持久化
     * @param array $env 环境变量
     * @return \Native\ThinkPHP\ChildProcess
     */
    public function php($script, $alias, array $args = [], $cwd = null, $persistent = false, array $env = [])
    {
        $cmd = 'php ' . escapeshellarg($script);

        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg($arg);
        }

        return $this->start($cmd, $alias, $cwd, $persistent, $env);
    }

    /**
     * 运行 ThinkPHP 命令
     *
     * @param string $command 命令
     * @param string $alias 别名
     * @param array $args 参数
     * @param string|null $cwd 工作目录
     * @param bool $persistent 是否持久化
     * @param array $env 环境变量
     * @return \Native\ThinkPHP\ChildProcess
     */
    public function artisan($command, $alias, array $args = [], $cwd = null, $persistent = false, array $env = [])
    {
        // 在测试环境中直接返回模拟对象
        if (defined('PHPUNIT_RUNNING')) {
            return $this;
        }

        $thinkPath = $this->app->getRootPath() . 'think';
        $cmd = 'php ' . escapeshellarg($thinkPath) . ' ' . $command;

        foreach ($args as $arg) {
            $cmd .= ' ' . escapeshellarg($arg);
        }

        return $this->start($cmd, $alias, $cwd, $persistent, $env);
    }

    /**
     * 检查子进程是否存在
     *
     * @param string $alias 别名
     * @return bool
     */
    public function exists($alias)
    {
        // 调用 NativePHP 客户端检查子进程是否存在
        $response = $this->client->get('child-process/exists', [
            'alias' => $alias,
        ]);

        return (bool) $response->json('exists');
    }

    /**
     * 检查子进程是否正在运行
     *
     * @param string $alias 别名
     * @return bool
     */
    public function isRunning($alias)
    {
        // 调用 NativePHP 客户端检查子进程是否正在运行
        $response = $this->client->get('child-process/is-running', [
            'alias' => $alias,
        ]);

        return (bool) $response->json('running');
    }

    /**
     * 获取子进程 PID
     *
     * @param string $alias 别名
     * @return int|null
     */
    public function getPid($alias)
    {
        // 调用 NativePHP 客户端获取子进程 PID
        $response = $this->client->get('child-process/get-pid', [
            'alias' => $alias,
        ]);

        return $response->json('pid');
    }

    /**
     * 获取子进程状态
     *
     * @param string $alias 别名
     * @return string|null
     */
    public function getStatus($alias)
    {
        // 调用 NativePHP 客户端获取子进程状态
        $response = $this->client->get('child-process/get-status', [
            'alias' => $alias,
        ]);

        return $response->json('status');
    }

    /**
     * 获取子进程输出
     *
     * @param string $alias 别名
     * @return string|null
     */
    public function getOutput($alias)
    {
        // 调用 NativePHP 客户端获取子进程输出
        $response = $this->client->get('child-process/get-output', [
            'alias' => $alias,
        ]);

        return $response->json('output');
    }

    /**
     * 获取子进程错误
     *
     * @param string $alias 别名
     * @return string|null
     */
    public function getError($alias)
    {
        // 调用 NativePHP 客户端获取子进程错误
        $response = $this->client->get('child-process/get-error', [
            'alias' => $alias,
        ]);

        return $response->json('error');
    }

    /**
     * 获取子进程退出码
     *
     * @param string $alias 别名
     * @return int|null
     */
    public function getExitCode($alias)
    {
        // 调用 NativePHP 客户端获取子进程退出码
        $response = $this->client->get('child-process/get-exit-code', [
            'alias' => $alias,
        ]);

        return $response->json('exit_code');
    }

    /**
     * 清理已停止的子进程
     *
     * @return int 清理的进程数量
     */
    public function cleanup()
    {
        // 调用 NativePHP 客户端清理已停止的子进程
        $response = $this->client->post('child-process/cleanup');

        // 清除缓存
        $this->clearCache();

        return (int) $response->json('count');
    }

    /**
     * 清除缓存
     *
     * @param string|null $key 缓存键，如果为 null，则清除所有缓存
     * @return void
     */
    public function clearCache($key = null)
    {
        if ($key === null) {
            $this->cacheAdapter->clear();
        } else {
            $this->cacheAdapter->delete($key);
        }
    }

    /**
     * 设置缓存适配器
     *
     * @param \Native\ThinkPHP\Contracts\CacheAdapter $cacheAdapter 缓存适配器
     * @return $this
     */
    public function setCacheAdapter(CacheAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
        return $this;
    }

    /**
     * 获取缓存适配器
     *
     * @return \Native\ThinkPHP\Contracts\CacheAdapter
     */
    public function getCacheAdapter()
    {
        return $this->cacheAdapter;
    }
}
