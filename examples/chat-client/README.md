# 桌面聊天客户端示例

这个示例展示了如何使用 NativePHP for ThinkPHP 创建一个功能完整的桌面聊天客户端应用。

## 功能

- 用户注册和登录
- 好友列表和群组管理
- 实时消息发送和接收
- 消息通知和提醒
- 文件传输和共享
- 语音和视频通话
- 消息历史记录和搜索
- 自定义主题和设置
- 多窗口支持

## 文件结构

- `app/controller/` - 控制器目录
  - `Index.php` - 主控制器
  - `Auth.php` - 认证控制器
  - `Chat.php` - 聊天控制器
  - `Contact.php` - 联系人控制器
  - `File.php` - 文件控制器
  - `Call.php` - 通话控制器
  - `Setting.php` - 设置控制器
- `app/model/` - 模型目录
  - `User.php` - 用户模型
  - `Message.php` - 消息模型
  - `Contact.php` - 联系人模型
  - `Group.php` - 群组模型
  - `File.php` - 文件模型
  - `Call.php` - 通话模型
- `app/service/` - 服务目录
  - `AuthService.php` - 认证服务
  - `ChatService.php` - 聊天服务
  - `ContactService.php` - 联系人服务
  - `FileService.php` - 文件服务
  - `CallService.php` - 通话服务
  - `NotificationService.php` - 通知服务
- `view/` - 视图目录
  - `index/` - 主视图
  - `auth/` - 认证视图
  - `chat/` - 聊天视图
  - `contact/` - 联系人视图
  - `file/` - 文件视图
  - `call/` - 通话视图
  - `setting/` - 设置视图
- `public/static/` - 静态资源目录
  - `css/` - CSS 样式
  - `js/` - JavaScript 脚本
  - `img/` - 图片资源
  - `sound/` - 音频资源

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 技术实现

### 实时通信

本示例使用 WebSocket 实现实时通信，通过 NativePHP 的 Http 类与服务器建立连接。

```php
// 在 ChatService.php 中
public function connect()
{
    $token = Settings::get('auth.token');
    $url = "wss://chat.example.com/ws?token={$token}";
    
    $this->socket = Http::websocket($url);
    
    $this->socket->on('message', function ($data) {
        $this->handleMessage($data);
    });
    
    $this->socket->on('close', function () {
        $this->reconnect();
    });
    
    $this->socket->connect();
}
```

### 消息通知

使用 NativePHP 的 Notification 类实现消息通知。

```php
// 在 NotificationService.php 中
public function notifyNewMessage($message)
{
    $sender = $message->sender->name;
    $content = $message->content;
    
    Notification::title('新消息')
        ->body("{$sender}: {$content}")
        ->onClick(function () use ($message) {
            Window::open('/chat/conversation/' . $message->conversation_id);
        })
        ->show();
}
```

### 文件传输

使用 NativePHP 的 FileSystem 和 Dialog 类实现文件传输。

```php
// 在 FileService.php 中
public function sendFile($conversationId)
{
    $file = Dialog::openFile([
        'title' => '选择要发送的文件',
        'filters' => [
            ['name' => '所有文件', 'extensions' => ['*']],
        ],
    ]);
    
    if (!$file) {
        return false;
    }
    
    $fileName = basename($file);
    $fileSize = FileSystem::size($file);
    $fileContent = FileSystem::read($file);
    
    // 上传文件到服务器
    $response = Http::post('https://chat.example.com/api/files/upload', [
        'conversation_id' => $conversationId,
        'name' => $fileName,
        'size' => $fileSize,
        'content' => base64_encode($fileContent),
    ]);
    
    return $response->json();
}
```

### 语音和视频通话

使用 NativePHP 的 Window 和 GlobalShortcut 类实现语音和视频通话。

```php
// 在 CallService.php 中
public function startCall($contactId, $type = 'audio')
{
    // 创建通话窗口
    $window = Window::open('/call/' . $type . '/' . $contactId, [
        'title' => $type === 'audio' ? '语音通话' : '视频通话',
        'width' => $type === 'audio' ? 400 : 800,
        'height' => $type === 'audio' ? 300 : 600,
        'alwaysOnTop' => true,
    ]);
    
    // 注册快捷键
    GlobalShortcut::register('CommandOrControl+E', function () use ($window) {
        $window->close();
    });
    
    // 发送通话请求到服务器
    Http::post('https://chat.example.com/api/calls/start', [
        'contact_id' => $contactId,
        'type' => $type,
    ]);
    
    return $window;
}
```

### 多窗口支持

使用 NativePHP 的 Window 类实现多窗口支持。

```php
// 在 ChatController.php 中
public function openConversation($id)
{
    $conversation = Conversation::find($id);
    
    if (!$conversation) {
        return json(['success' => false, 'message' => '会话不存在']);
    }
    
    $window = Window::open('/chat/conversation/' . $id, [
        'title' => $conversation->name,
        'width' => 800,
        'height' => 600,
        'minWidth' => 400,
        'minHeight' => 300,
    ]);
    
    return json(['success' => true, 'window_id' => $window->id]);
}
```

### 自定义主题和设置

使用 NativePHP 的 Settings 类实现自定义主题和设置。

```php
// 在 SettingService.php 中
public function setTheme($theme)
{
    Settings::set('app.theme', $theme);
    
    // 应用主题到所有窗口
    $windows = Window::all();
    foreach ($windows as $window) {
        $window->webContents->executeJavaScript("document.documentElement.setAttribute('data-theme', '{$theme}')");
    }
    
    return true;
}
```

## 使用的 NativePHP 功能

- **Window**: 用于创建和管理多个窗口
- **Notification**: 用于发送消息通知
- **Dialog**: 用于文件选择和确认对话框
- **GlobalShortcut**: 用于注册全局快捷键
- **Http**: 用于与服务器通信和 WebSocket 连接
- **FileSystem**: 用于文件读写和传输
- **Settings**: 用于存储用户设置和主题
- **Menu**: 用于创建应用菜单和上下文菜单
- **Tray**: 用于创建系统托盘图标和菜单
- **Clipboard**: 用于复制和粘贴文本和图片
