<?php

namespace app\controller;

use app\BaseController;
use app\service\AuthService;
use app\service\ChatService;
use app\service\FileService;
use app\service\CallService;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Menu;
use Native\ThinkPHP\Facades\ContextMenu;

class Chat extends BaseController
{
    protected $authService;
    protected $chatService;
    protected $fileService;
    protected $callService;
    
    public function __construct(
        AuthService $authService,
        ChatService $chatService,
        FileService $fileService,
        CallService $callService
    ) {
        $this->authService = $authService;
        $this->chatService = $chatService;
        $this->fileService = $fileService;
        $this->callService = $callService;
    }
    
    public function index()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取最近会话
        $conversations = $this->chatService->getRecentConversations();
        
        return view('chat/index', [
            'conversations' => $conversations,
        ]);
    }
    
    public function new()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取联系人列表
        $contacts = $this->chatService->getContacts();
        
        return view('chat/new', [
            'contacts' => $contacts,
        ]);
    }
    
    public function createConversation()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $contactId = input('contact_id');
        
        if (empty($contactId)) {
            return json(['success' => false, 'message' => '请选择联系人']);
        }
        
        $result = $this->chatService->createConversation($contactId);
        
        if ($result['success']) {
            // 创建会话成功，打开会话窗口
            Window::open('/chat/conversation/' . $result['conversation_id'], [
                'title' => $result['conversation_name'],
                'width' => 800,
                'height' => 600,
            ]);
            
            return json(['success' => true, 'conversation_id' => $result['conversation_id']]);
        } else {
            // 创建会话失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
    
    public function newGroup()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取联系人列表
        $contacts = $this->chatService->getContacts();
        
        return view('chat/new-group', [
            'contacts' => $contacts,
        ]);
    }
    
    public function createGroup()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $name = input('name');
        $members = input('members/a');
        
        if (empty($name)) {
            return json(['success' => false, 'message' => '请输入群组名称']);
        }
        
        if (empty($members)) {
            return json(['success' => false, 'message' => '请选择群组成员']);
        }
        
        $result = $this->chatService->createGroup($name, $members);
        
        if ($result['success']) {
            // 创建群组成功，打开群组窗口
            Window::open('/chat/conversation/' . $result['conversation_id'], [
                'title' => $result['conversation_name'],
                'width' => 800,
                'height' => 600,
            ]);
            
            return json(['success' => true, 'conversation_id' => $result['conversation_id']]);
        } else {
            // 创建群组失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
    
    public function conversation($id)
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取会话信息
        $conversation = $this->chatService->getConversation($id);
        
        if (!$conversation) {
            return redirect('/chat');
        }
        
        // 获取会话消息
        $messages = $this->chatService->getMessages($id);
        
        // 创建上下文菜单
        $this->createContextMenu();
        
        return view('chat/conversation', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }
    
    protected function createContextMenu()
    {
        $menu = Menu::create()
            ->add('复制', ['click' => 'copyMessage'])
            ->add('删除', ['click' => 'deleteMessage'])
            ->add('转发', ['click' => 'forwardMessage'])
            ->add('回复', ['click' => 'replyMessage']);
        
        ContextMenu::register($menu);
    }
    
    public function sendMessage()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $conversationId = input('conversation_id');
        $content = input('content');
        $type = input('type', 'text');
        
        if (empty($conversationId) || empty($content)) {
            return json(['success' => false, 'message' => '会话ID和消息内容不能为空']);
        }
        
        $result = $this->chatService->sendMessage($conversationId, $content, $type);
        
        return json($result);
    }
    
    public function sendFile()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $conversationId = input('conversation_id');
        
        if (empty($conversationId)) {
            return json(['success' => false, 'message' => '会话ID不能为空']);
        }
        
        $result = $this->fileService->sendFile($conversationId);
        
        return json($result);
    }
    
    public function startCall()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $conversationId = input('conversation_id');
        $type = input('type', 'audio');
        
        if (empty($conversationId)) {
            return json(['success' => false, 'message' => '会话ID不能为空']);
        }
        
        $result = $this->callService->startCall($conversationId, $type);
        
        return json($result);
    }
    
    public function deleteMessage()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $messageId = input('message_id');
        
        if (empty($messageId)) {
            return json(['success' => false, 'message' => '消息ID不能为空']);
        }
        
        // 确认删除
        $confirm = Dialog::confirm('确认删除', '确定要删除这条消息吗？', ['buttons' => ['确定', '取消']]);
        
        if ($confirm === 0) {
            $result = $this->chatService->deleteMessage($messageId);
            
            return json($result);
        } else {
            return json(['success' => false, 'message' => '已取消删除']);
        }
    }
    
    public function forwardMessage()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $messageId = input('message_id');
        
        if (empty($messageId)) {
            return json(['success' => false, 'message' => '消息ID不能为空']);
        }
        
        // 打开转发窗口
        Window::open('/chat/forward/' . $messageId, [
            'title' => '转发消息',
            'width' => 400,
            'height' => 500,
        ]);
        
        return json(['success' => true]);
    }
    
    public function forward($messageId)
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return redirect('/auth/login');
        }
        
        // 获取消息信息
        $message = $this->chatService->getMessage($messageId);
        
        if (!$message) {
            return redirect('/chat');
        }
        
        // 获取最近会话
        $conversations = $this->chatService->getRecentConversations();
        
        return view('chat/forward', [
            'message' => $message,
            'conversations' => $conversations,
        ]);
    }
    
    public function doForward()
    {
        // 检查用户是否已登录
        if (!$this->authService->isLoggedIn()) {
            return json(['success' => false, 'message' => '请先登录']);
        }
        
        $messageId = input('message_id');
        $conversationIds = input('conversation_ids/a');
        
        if (empty($messageId) || empty($conversationIds)) {
            return json(['success' => false, 'message' => '消息ID和会话ID不能为空']);
        }
        
        $result = $this->chatService->forwardMessage($messageId, $conversationIds);
        
        if ($result['success']) {
            // 转发成功
            Notification::send('转发成功', '消息已成功转发');
            
            return json(['success' => true]);
        } else {
            // 转发失败
            return json(['success' => false, 'message' => $result['message']]);
        }
    }
}
