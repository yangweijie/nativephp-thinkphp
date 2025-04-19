<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\WebSocket\Client;

class WebSocketDemo extends BaseController
{
    /**
     * WebSocket 客户端
     *
     * @var Client
     */
    protected $client;

    /**
     * 显示首页
     *
     * @return \think\Response
     */
    public function index()
    {
        return view('websocket-demo/index');
    }

    /**
     * 连接到 WebSocket 服务器
     *
     * @return \think\Response
     */
    public function connect()
    {
        $url = request()->param('url');
        
        if (empty($url)) {
            return json(['success' => false, 'message' => 'URL 不能为空']);
        }
        
        try {
            // 创建 WebSocket 客户端
            $this->client = Http::websocket($url);
            
            // 注册事件处理器
            $this->client->on('open', function () {
                Notification::send('WebSocket', '连接已建立');
            });
            
            $this->client->on('message', function ($data) {
                Notification::send('WebSocket', '收到消息: ' . $data);
            });
            
            $this->client->on('close', function ($code, $reason) {
                Notification::send('WebSocket', '连接已关闭: ' . $reason);
            });
            
            $this->client->on('error', function ($error) {
                Notification::send('WebSocket', '错误: ' . $error);
            });
            
            // 连接到服务器
            $connected = $this->client->connect($url);
            
            if ($connected) {
                // 启动定时器，定期处理消息
                $this->startTicker();
                
                return json(['success' => true, 'message' => '连接成功']);
            } else {
                return json(['success' => false, 'message' => '连接失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => '连接失败: ' . $e->getMessage()]);
        }
    }
    
    /**
     * 断开连接
     *
     * @return \think\Response
     */
    public function disconnect()
    {
        if ($this->client && $this->client->isConnected()) {
            $this->client->close();
            return json(['success' => true, 'message' => '已断开连接']);
        }
        
        return json(['success' => false, 'message' => '未连接']);
    }
    
    /**
     * 发送消息
     *
     * @return \think\Response
     */
    public function send()
    {
        $message = request()->param('message');
        
        if (empty($message)) {
            return json(['success' => false, 'message' => '消息不能为空']);
        }
        
        if (!$this->client || !$this->client->isConnected()) {
            return json(['success' => false, 'message' => '未连接']);
        }
        
        $sent = $this->client->send($message);
        
        if ($sent) {
            return json(['success' => true, 'message' => '消息已发送']);
        } else {
            return json(['success' => false, 'message' => '发送失败']);
        }
    }
    
    /**
     * 启动定时器
     *
     * @return void
     */
    protected function startTicker()
    {
        // 在实际应用中，应该使用更好的方式来实现定时器
        // 这里简化处理，使用前端 JavaScript 定时器
    }
    
    /**
     * 处理一次消息循环
     *
     * @return \think\Response
     */
    public function tick()
    {
        if (!$this->client || !$this->client->isConnected()) {
            return json(['success' => false, 'message' => '未连接']);
        }
        
        $this->client->tick();
        
        return json(['success' => true]);
    }
}
