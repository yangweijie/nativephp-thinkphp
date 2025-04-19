<?php

namespace app\service;

use Native\ThinkPHP\Facades\Http;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Window;

class ChatService
{
    /**
     * API 基础 URL
     *
     * @var string
     */
    protected $baseUrl = 'https://chat.example.com/api';
    
    /**
     * WebSocket 连接
     *
     * @var mixed
     */
    protected $socket;
    
    /**
     * 通知服务
     *
     * @var \app\service\NotificationService
     */
    protected $notificationService;
    
    /**
     * 构造函数
     *
     * @param \app\service\NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }
    
    /**
     * 连接聊天服务器
     *
     * @return bool
     */
    public function connect()
    {
        if (!Settings::get('auth.token')) {
            return false;
        }
        
        $token = Settings::get('auth.token');
        $url = "wss://chat.example.com/ws?token={$token}";
        
        try {
            $this->socket = Http::websocket($url);
            
            $this->socket->on('message', function ($data) {
                $this->handleMessage($data);
            });
            
            $this->socket->on('close', function () {
                $this->reconnect();
            });
            
            $this->socket->connect();
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * 断开聊天服务器连接
     *
     * @return bool
     */
    public function disconnect()
    {
        if ($this->socket) {
            $this->socket->close();
            $this->socket = null;
            
            return true;
        }
        
        return false;
    }
    
    /**
     * 重新连接聊天服务器
     *
     * @return bool
     */
    public function reconnect()
    {
        $this->disconnect();
        
        // 延迟 3 秒后重新连接
        sleep(3);
        
        return $this->connect();
    }
    
    /**
     * 处理收到的消息
     *
     * @param array $data
     * @return void
     */
    protected function handleMessage($data)
    {
        if (!is_array($data)) {
            $data = json_decode($data, true);
        }
        
        if (!isset($data['type'])) {
            return;
        }
        
        switch ($data['type']) {
            case 'message':
                $this->handleNewMessage($data['data']);
                break;
            case 'typing':
                $this->handleTyping($data['data']);
                break;
            case 'read':
                $this->handleRead($data['data']);
                break;
            case 'call':
                $this->handleCall($data['data']);
                break;
            case 'online':
                $this->handleOnline($data['data']);
                break;
            case 'offline':
                $this->handleOffline($data['data']);
                break;
        }
    }
    
    /**
     * 处理新消息
     *
     * @param array $data
     * @return void
     */
    protected function handleNewMessage($data)
    {
        // 通知新消息
        $this->notificationService->notifyNewMessage($data);
        
        // 更新会话列表
        $this->updateConversationList($data['conversation_id']);
        
        // 如果当前窗口是该会话，则更新消息列表
        $currentWindow = Window::current();
        if ($currentWindow && $currentWindow->url === '/chat/conversation/' . $data['conversation_id']) {
            $currentWindow->webContents->executeJavaScript("updateMessages({$data['id']})");
        }
    }
    
    /**
     * 处理正在输入
     *
     * @param array $data
     * @return void
     */
    protected function handleTyping($data)
    {
        // 如果当前窗口是该会话，则显示正在输入
        $currentWindow = Window::current();
        if ($currentWindow && $currentWindow->url === '/chat/conversation/' . $data['conversation_id']) {
            $currentWindow->webContents->executeJavaScript("showTyping('{$data['user_name']}')");
        }
    }
    
    /**
     * 处理已读消息
     *
     * @param array $data
     * @return void
     */
    protected function handleRead($data)
    {
        // 如果当前窗口是该会话，则更新已读状态
        $currentWindow = Window::current();
        if ($currentWindow && $currentWindow->url === '/chat/conversation/' . $data['conversation_id']) {
            $currentWindow->webContents->executeJavaScript("updateReadStatus({$data['message_id']})");
        }
    }
    
    /**
     * 处理通话请求
     *
     * @param array $data
     * @return void
     */
    protected function handleCall($data)
    {
        // 通知通话请求
        $this->notificationService->notifyCall($data);
    }
    
    /**
     * 处理联系人上线
     *
     * @param array $data
     * @return void
     */
    protected function handleOnline($data)
    {
        // 更新联系人状态
        $windows = Window::all();
        foreach ($windows as $window) {
            $window->webContents->executeJavaScript("updateContactStatus({$data['user_id']}, 'online')");
        }
    }
    
    /**
     * 处理联系人下线
     *
     * @param array $data
     * @return void
     */
    protected function handleOffline($data)
    {
        // 更新联系人状态
        $windows = Window::all();
        foreach ($windows as $window) {
            $window->webContents->executeJavaScript("updateContactStatus({$data['user_id']}, 'offline')");
        }
    }
    
    /**
     * 获取联系人列表
     *
     * @return array
     */
    public function getContacts()
    {
        if (!Settings::get('auth.token')) {
            return [];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->get($this->baseUrl . '/contacts');
        
        if ($response->successful()) {
            return $response->json('data', []);
        }
        
        return [];
    }
    
    /**
     * 获取最近会话
     *
     * @return array
     */
    public function getRecentConversations()
    {
        if (!Settings::get('auth.token')) {
            return [];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->get($this->baseUrl . '/conversations');
        
        if ($response->successful()) {
            return $response->json('data', []);
        }
        
        return [];
    }
    
    /**
     * 获取会话信息
     *
     * @param int $id
     * @return array|null
     */
    public function getConversation($id)
    {
        if (!Settings::get('auth.token')) {
            return null;
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->get($this->baseUrl . '/conversations/' . $id);
        
        if ($response->successful()) {
            return $response->json('data');
        }
        
        return null;
    }
    
    /**
     * 创建会话
     *
     * @param int $contactId
     * @return array
     */
    public function createConversation($contactId)
    {
        if (!Settings::get('auth.token')) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/conversations', [
                'contact_id' => $contactId,
            ]);
        
        if ($response->successful()) {
            $data = $response->json('data');
            
            return [
                'success' => true,
                'conversation_id' => $data['id'],
                'conversation_name' => $data['name'],
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '创建会话失败'),
            ];
        }
    }
    
    /**
     * 创建群组
     *
     * @param string $name
     * @param array $members
     * @return array
     */
    public function createGroup($name, $members)
    {
        if (!Settings::get('auth.token')) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/groups', [
                'name' => $name,
                'members' => $members,
            ]);
        
        if ($response->successful()) {
            $data = $response->json('data');
            
            return [
                'success' => true,
                'conversation_id' => $data['conversation_id'],
                'conversation_name' => $name,
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '创建群组失败'),
            ];
        }
    }
    
    /**
     * 获取会话消息
     *
     * @param int $conversationId
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getMessages($conversationId, $page = 1, $perPage = 20)
    {
        if (!Settings::get('auth.token')) {
            return [];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->get($this->baseUrl . '/conversations/' . $conversationId . '/messages', [
                'page' => $page,
                'per_page' => $perPage,
            ]);
        
        if ($response->successful()) {
            return $response->json('data', []);
        }
        
        return [];
    }
    
    /**
     * 获取消息信息
     *
     * @param int $id
     * @return array|null
     */
    public function getMessage($id)
    {
        if (!Settings::get('auth.token')) {
            return null;
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->get($this->baseUrl . '/messages/' . $id);
        
        if ($response->successful()) {
            return $response->json('data');
        }
        
        return null;
    }
    
    /**
     * 发送消息
     *
     * @param int $conversationId
     * @param string $content
     * @param string $type
     * @return array
     */
    public function sendMessage($conversationId, $content, $type = 'text')
    {
        if (!Settings::get('auth.token')) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/messages', [
                'conversation_id' => $conversationId,
                'content' => $content,
                'type' => $type,
            ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
                'message' => $response->json('data'),
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '发送消息失败'),
            ];
        }
    }
    
    /**
     * 删除消息
     *
     * @param int $id
     * @return array
     */
    public function deleteMessage($id)
    {
        if (!Settings::get('auth.token')) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->delete($this->baseUrl . '/messages/' . $id);
        
        if ($response->successful()) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '删除消息失败'),
            ];
        }
    }
    
    /**
     * 转发消息
     *
     * @param int $id
     * @param array $conversationIds
     * @return array
     */
    public function forwardMessage($id, $conversationIds)
    {
        if (!Settings::get('auth.token')) {
            return [
                'success' => false,
                'message' => '请先登录',
            ];
        }
        
        $response = Http::withToken(Settings::get('auth.token'))
            ->post($this->baseUrl . '/messages/' . $id . '/forward', [
                'conversation_ids' => $conversationIds,
            ]);
        
        if ($response->successful()) {
            return [
                'success' => true,
            ];
        } else {
            return [
                'success' => false,
                'message' => $response->json('message', '转发消息失败'),
            ];
        }
    }
    
    /**
     * 更新会话列表
     *
     * @param int $conversationId
     * @return void
     */
    protected function updateConversationList($conversationId)
    {
        $windows = Window::all();
        foreach ($windows as $window) {
            if ($window->url === '/chat' || $window->url === '/') {
                $window->webContents->executeJavaScript("updateConversationList({$conversationId})");
            }
        }
    }
}
