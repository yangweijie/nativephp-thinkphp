<?php

namespace NativePHP\Think\Controller;

use think\facade\Config;
use think\Response;
use NativePHP\Think\Facades\Native;

class ElectronController
{
    /**
     * 获取 NativePHP 配置
     */
    public function config(): Response
    {
        $config = Config::get('native');

        // 处理路径
        if (isset($config['app']['icon'])) {
            $config['app']['icon'] = $this->normalizePath($config['app']['icon']);
        }

        if (isset($config['tray']['icon'])) {
            $config['tray']['icon'] = $this->normalizePath($config['tray']['icon']);
        }

        return Response::create($config, 'json');
    }

    /**
     * 处理 IPC 消息
     */
    public function ipc(): Response
    {
        $channel = request()->param('channel');
        $payload = request()->param('payload', []);

        if (empty($channel)) {
            return Response::create(['error' => 'Channel is required'], 'json', 400);
        }

        // 处理 IPC 消息
        Native::ipc()->handleMessage($channel, $payload);

        return Response::create(['success' => true], 'json');
    }

    /**
     * 处理调用请求
     */
    public function invoke(): Response
    {
        $channel = request()->param('channel');
        $payload = request()->param('payload', []);

        if (empty($channel)) {
            return Response::create(['error' => 'Channel is required'], 'json', 400);
        }

        // 处理调用请求
        try {
            $result = $this->handleInvoke($channel, $payload);
            return Response::create(['success' => true, 'result' => $result], 'json');
        } catch (\Exception $e) {
            return Response::create(['error' => $e->getMessage()], 'json', 500);
        }
    }

    /**
     * 处理调用请求
     */
    protected function handleInvoke(string $channel, array $payload)
    {
        // 触发事件
        Native::events()->dispatch('ipc.invoke', [
            'channel' => $channel,
            'payload' => $payload
        ]);

        // 根据通道处理不同的调用
        switch ($channel) {
            case 'app.getConfig':
                return Config::get($payload['key'] ?? null, $payload['default'] ?? null);

            case 'app.getVersion':
                return Config::get('native.app.version');

            case 'app.getName':
                return Config::get('native.app.name');

            default:
                // 触发自定义事件，允许其他组件处理
                $result = null;
                $handled = false;

                Native::events()->dispatch('ipc.invoke.' . $channel, [
                    'payload' => $payload,
                    'result' => &$result,
                    'handled' => &$handled
                ]);

                if ($handled) {
                    return $result;
                }

                throw new \Exception("Unhandled invoke channel: {$channel}");
        }
    }

    /**
     * 规范化路径
     */
    protected function normalizePath(?string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // 如果是相对路径，转换为绝对路径
        if (!preg_match('/^(https?:\/\/|\/)/i', $path)) {
            $path = public_path($path);
        }

        return $path;
    }
}
