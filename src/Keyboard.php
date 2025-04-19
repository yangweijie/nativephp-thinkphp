<?php

namespace Native\ThinkPHP;

use think\App as ThinkApp;
use Native\ThinkPHP\Client\Client;

class Keyboard
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
     * 已注册的快捷键
     *
     * @var array
     */
    protected $shortcuts = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(ThinkApp $app)
    {
        $this->app = $app;
        $this->client = new Client();
    }

    /**
     * 注册快捷键
     *
     * @param string $accelerator 快捷键组合，如 'CommandOrControl+Shift+K'
     * @param callable|string $callback 回调函数或路由
     * @return string 快捷键ID
     */
    public function register($accelerator, $callback)
    {
        $id = md5($accelerator . microtime(true));

        $response = $this->client->post('keyboard/register', [
            'accelerator' => $accelerator,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            $this->shortcuts[$id] = [
                'accelerator' => $accelerator,
                'callback' => $callback,
            ];
        }

        return $id;
    }

    /**
     * 注销快捷键
     *
     * @param string $id 快捷键ID
     * @return bool
     */
    public function unregister($id)
    {
        if (!isset($this->shortcuts[$id])) {
            return false;
        }

        $response = $this->client->post('keyboard/unregister', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->shortcuts[$id]);
            return true;
        }

        return false;
    }

    /**
     * 注销所有快捷键
     *
     * @return void
     */
    public function unregisterAll()
    {
        $this->client->post('keyboard/unregister-all');
        $this->shortcuts = [];
    }

    /**
     * 检查快捷键是否已注册
     *
     * @param string $accelerator 快捷键组合
     * @return bool
     */
    public function isRegistered($accelerator)
    {
        foreach ($this->shortcuts as $shortcut) {
            if ($shortcut['accelerator'] === $accelerator) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取所有已注册的快捷键
     *
     * @return array
     */
    public function getRegisteredShortcuts()
    {
        return $this->shortcuts;
    }

    /**
     * 模拟按键
     *
     * @param string $key 按键
     * @param array $modifiers 修饰键数组，如 ['shift', 'control']
     * @return bool
     */
    public function sendKey($key, array $modifiers = [])
    {
        $response = $this->client->post('keyboard/send-key', [
            'key' => $key,
            'modifiers' => $modifiers,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 模拟按键序列
     *
     * @param string $sequence 按键序列，如 'Hello, World!'
     * @return bool
     */
    public function sendText($sequence)
    {
        $response = $this->client->post('keyboard/send-text', [
            'text' => $sequence,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 获取键盘布局
     *
     * @return array
     */
    public function getLayout()
    {
        $response = $this->client->get('keyboard/layout');
        return $response->json('layout') ?? [];
    }

    /**
     * 设置键盘布局
     *
     * @param string $layout 键盘布局，如 'qwerty', 'dvorak'
     * @return bool
     */
    public function setLayout($layout)
    {
        $response = $this->client->post('keyboard/layout', [
            'layout' => $layout,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 注册全局快捷键
     *
     * @param string $accelerator 快捷键组合，如 'CommandOrControl+Shift+K'
     * @param callable|string $callback 回调函数或路由
     * @return string 快捷键ID
     */
    public function registerGlobal($accelerator, $callback)
    {
        $id = md5('global:' . $accelerator . microtime(true));

        $response = $this->client->post('keyboard/register-global', [
            'accelerator' => $accelerator,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            $this->shortcuts[$id] = [
                'accelerator' => $accelerator,
                'callback' => $callback,
                'global' => true,
            ];
        }

        return $id;
    }

    /**
     * 注销全局快捷键
     *
     * @param string $id 快捷键ID
     * @return bool
     */
    public function unregisterGlobal($id)
    {
        if (!isset($this->shortcuts[$id]) || empty($this->shortcuts[$id]['global'])) {
            return false;
        }

        $response = $this->client->post('keyboard/unregister-global', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->shortcuts[$id]);
            return true;
        }

        return false;
    }

    /**
     * 注销所有全局快捷键
     *
     * @return void
     */
    public function unregisterAllGlobal()
    {
        $this->client->post('keyboard/unregister-all-global');

        // 移除所有全局快捷键
        foreach ($this->shortcuts as $id => $shortcut) {
            if (!empty($shortcut['global'])) {
                unset($this->shortcuts[$id]);
            }
        }
    }

    /**
     * 检查快捷键是否已注册为全局快捷键
     *
     * @param string $accelerator 快捷键组合
     * @return bool
     */
    public function isGlobalRegistered($accelerator)
    {
        foreach ($this->shortcuts as $shortcut) {
            if ($shortcut['accelerator'] === $accelerator && !empty($shortcut['global'])) {
                return true;
            }
        }

        return false;
    }

    /**
     * 获取所有已注册的全局快捷键
     *
     * @return array
     */
    public function getRegisteredGlobalShortcuts()
    {
        $globalShortcuts = [];

        foreach ($this->shortcuts as $id => $shortcut) {
            if (!empty($shortcut['global'])) {
                $globalShortcuts[$id] = $shortcut;
            }
        }

        return $globalShortcuts;
    }

    /**
     * 模拟按下并松开按键
     *
     * @param string $key 按键
     * @param array $modifiers 修饰键数组，如 ['shift', 'control']
     * @return bool
     */
    public function tapKey($key, array $modifiers = [])
    {
        $response = $this->client->post('keyboard/tap-key', [
            'key' => $key,
            'modifiers' => $modifiers,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 模拟按下按键
     *
     * @param string $key 按键
     * @param array $modifiers 修饰键数组，如 ['shift', 'control']
     * @return bool
     */
    public function keyDown($key, array $modifiers = [])
    {
        $response = $this->client->post('keyboard/key-down', [
            'key' => $key,
            'modifiers' => $modifiers,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 模拟松开按键
     *
     * @param string $key 按键
     * @param array $modifiers 修饰键数组，如 ['shift', 'control']
     * @return bool
     */
    public function keyUp($key, array $modifiers = [])
    {
        $response = $this->client->post('keyboard/key-up', [
            'key' => $key,
            'modifiers' => $modifiers,
        ]);

        return (bool) $response->json('success');
    }

    /**
     * 监听键盘事件
     *
     * @param string $event 事件类型，如 'keydown', 'keyup', 'keypress'
     * @param callable $callback 回调函数
     * @return string 监听器ID
     */
    public function on($event, $callback)
    {
        $id = md5($event . microtime(true));

        $response = $this->client->post('keyboard/on', [
            'event' => $event,
            'id' => $id,
        ]);

        if ($response->json('success')) {
            $this->shortcuts[$id] = [
                'event' => $event,
                'callback' => $callback,
                'listener' => true,
            ];
        }

        return $id;
    }

    /**
     * 移除键盘事件监听器
     *
     * @param string $id 监听器ID
     * @return bool
     */
    public function off($id)
    {
        if (!isset($this->shortcuts[$id]) || empty($this->shortcuts[$id]['listener'])) {
            return false;
        }

        $response = $this->client->post('keyboard/off', [
            'id' => $id,
        ]);

        if ($response->json('success')) {
            unset($this->shortcuts[$id]);
            return true;
        }

        return false;
    }
}
