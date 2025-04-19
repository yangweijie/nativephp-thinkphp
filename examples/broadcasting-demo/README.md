# 事件广播示例

这个示例展示了如何使用 NativePHP for ThinkPHP 的事件广播功能在不同窗口和组件之间进行通信。

## 功能

- 在多个窗口之间共享数据
- 实现实时通信
- 构建响应式用户界面
- 管理频道和事件

## 文件结构

- `app/controller/Broadcasting.php` - 广播控制器
- `app/controller/Window.php` - 窗口控制器
- `view/broadcasting/index.html` - 主页面
- `view/broadcasting/sender.html` - 发送者页面
- `view/broadcasting/receiver.html` - 接收者页面
- `public/static/js/app.js` - 前端 JavaScript 代码
- `public/static/css/app.css` - 前端 CSS 样式

## 使用方法

1. 启动应用：

```bash
php think native:serve
```

2. 构建应用：

```bash
php think native:build
```

## 实现说明

本示例使用 NativePHP for ThinkPHP 的以下功能：

- **Broadcasting**：用于在不同窗口和组件之间进行通信
- **Window**：用于创建和管理窗口
- **Notification**：用于发送通知

本示例实现了以下功能：

1. **多窗口通信**：在不同窗口之间发送和接收消息
2. **频道管理**：创建、删除和清空频道
3. **事件监听**：监听和处理事件
4. **实时数据更新**：实时更新用户界面

## 代码示例

### 控制器

```php
<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Broadcasting;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;

class Broadcasting extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        return View::fetch('broadcasting/index');
    }
    
    /**
     * 显示发送者页面
     *
     * @return \think\Response
     */
    public function sender()
    {
        return View::fetch('broadcasting/sender');
    }
    
    /**
     * 显示接收者页面
     *
     * @return \think\Response
     */
    public function receiver()
    {
        return View::fetch('broadcasting/receiver');
    }
    
    /**
     * 广播事件
     *
     * @return \think\Response
     */
    public function broadcast()
    {
        $channel = input('channel');
        $event = input('event');
        $data = input('data', []);
        
        // 广播事件
        $success = Broadcasting::broadcast($channel, $event, $data);
        
        return json(['success' => $success]);
    }
    
    /**
     * 创建频道
     *
     * @return \think\Response
     */
    public function createChannel()
    {
        $channel = input('channel');
        
        // 创建频道
        $success = Broadcasting::createChannel($channel);
        
        return json(['success' => $success]);
    }
    
    /**
     * 删除频道
     *
     * @return \think\Response
     */
    public function deleteChannel()
    {
        $channel = input('channel');
        
        // 删除频道
        $success = Broadcasting::deleteChannel($channel);
        
        return json(['success' => $success]);
    }
    
    /**
     * 获取所有频道
     *
     * @return \think\Response
     */
    public function getChannels()
    {
        // 获取所有频道
        $channels = Broadcasting::getChannels();
        
        return json(['channels' => $channels]);
    }
    
    /**
     * 打开发送者窗口
     *
     * @return \think\Response
     */
    public function openSenderWindow()
    {
        // 打开发送者窗口
        $windowId = Window::open('/broadcasting/sender', [
            'title' => '发送者',
            'width' => 600,
            'height' => 400,
        ]);
        
        return json(['success' => true, 'windowId' => $windowId]);
    }
    
    /**
     * 打开接收者窗口
     *
     * @return \think\Response
     */
    public function openReceiverWindow()
    {
        // 打开接收者窗口
        $windowId = Window::open('/broadcasting/receiver', [
            'title' => '接收者',
            'width' => 600,
            'height' => 400,
        ]);
        
        return json(['success' => true, 'windowId' => $windowId]);
    }
}
```

### 视图

#### 主页面 (index.html)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>事件广播示例</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <h1>事件广播示例</h1>
        
        <div class="card">
            <h2>频道管理</h2>
            <div class="form-group">
                <input type="text" id="channel-name" placeholder="频道名称">
                <button id="create-channel">创建频道</button>
                <button id="delete-channel">删除频道</button>
            </div>
            
            <h3>所有频道</h3>
            <ul id="channel-list"></ul>
        </div>
        
        <div class="card">
            <h2>窗口管理</h2>
            <button id="open-sender">打开发送者窗口</button>
            <button id="open-receiver">打开接收者窗口</button>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```

#### 发送者页面 (sender.html)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>发送者</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <h1>发送者</h1>
        
        <div class="card">
            <h2>发送消息</h2>
            <div class="form-group">
                <select id="channel-select">
                    <option value="">选择频道</option>
                </select>
            </div>
            
            <div class="form-group">
                <input type="text" id="event-name" placeholder="事件名称">
            </div>
            
            <div class="form-group">
                <textarea id="message-data" placeholder="消息内容 (JSON 格式)"></textarea>
            </div>
            
            <button id="send-message">发送消息</button>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```

#### 接收者页面 (receiver.html)

```html
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>接收者</title>
    <link rel="stylesheet" href="/static/css/app.css">
</head>
<body>
    <div class="container">
        <h1>接收者</h1>
        
        <div class="card">
            <h2>监听消息</h2>
            <div class="form-group">
                <select id="channel-select">
                    <option value="">选择频道</option>
                </select>
            </div>
            
            <div class="form-group">
                <input type="text" id="event-name" placeholder="事件名称">
                <button id="start-listening">开始监听</button>
                <button id="stop-listening">停止监听</button>
            </div>
            
            <h3>接收到的消息</h3>
            <div id="message-list"></div>
        </div>
    </div>
    
    <script src="/static/js/app.js"></script>
</body>
</html>
```

### JavaScript 代码

```javascript
// 获取频道列表
function getChannels() {
    fetch('/broadcasting/getChannels')
        .then(response => response.json())
        .then(data => {
            const channelList = document.getElementById('channel-list');
            const channelSelect = document.getElementById('channel-select');
            
            if (channelList) {
                channelList.innerHTML = '';
                
                data.channels.forEach(channel => {
                    const li = document.createElement('li');
                    li.textContent = channel;
                    channelList.appendChild(li);
                });
            }
            
            if (channelSelect) {
                // 保存当前选中的值
                const currentValue = channelSelect.value;
                
                // 清空选项
                channelSelect.innerHTML = '<option value="">选择频道</option>';
                
                // 添加新选项
                data.channels.forEach(channel => {
                    const option = document.createElement('option');
                    option.value = channel;
                    option.textContent = channel;
                    channelSelect.appendChild(option);
                });
                
                // 恢复选中的值
                if (currentValue) {
                    channelSelect.value = currentValue;
                }
            }
        });
}

// 创建频道
function createChannel(channelName) {
    fetch('/broadcasting/createChannel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ channel: channelName }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('频道创建成功');
                getChannels();
            } else {
                alert('频道创建失败');
            }
        });
}

// 删除频道
function deleteChannel(channelName) {
    fetch('/broadcasting/deleteChannel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ channel: channelName }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('频道删除成功');
                getChannels();
            } else {
                alert('频道删除失败');
            }
        });
}

// 广播事件
function broadcastEvent(channel, event, data) {
    fetch('/broadcasting/broadcast', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ channel, event, data }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('消息发送成功');
            } else {
                alert('消息发送失败');
            }
        });
}

// 打开发送者窗口
function openSenderWindow() {
    fetch('/broadcasting/openSenderWindow')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('打开发送者窗口失败');
            }
        });
}

// 打开接收者窗口
function openReceiverWindow() {
    fetch('/broadcasting/openReceiverWindow')
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('打开接收者窗口失败');
            }
        });
}

// 初始化页面
document.addEventListener('DOMContentLoaded', function() {
    // 获取频道列表
    getChannels();
    
    // 创建频道按钮
    const createChannelButton = document.getElementById('create-channel');
    if (createChannelButton) {
        createChannelButton.addEventListener('click', function() {
            const channelName = document.getElementById('channel-name').value;
            if (channelName) {
                createChannel(channelName);
            } else {
                alert('请输入频道名称');
            }
        });
    }
    
    // 删除频道按钮
    const deleteChannelButton = document.getElementById('delete-channel');
    if (deleteChannelButton) {
        deleteChannelButton.addEventListener('click', function() {
            const channelName = document.getElementById('channel-name').value;
            if (channelName) {
                deleteChannel(channelName);
            } else {
                alert('请输入频道名称');
            }
        });
    }
    
    // 打开发送者窗口按钮
    const openSenderButton = document.getElementById('open-sender');
    if (openSenderButton) {
        openSenderButton.addEventListener('click', function() {
            openSenderWindow();
        });
    }
    
    // 打开接收者窗口按钮
    const openReceiverButton = document.getElementById('open-receiver');
    if (openReceiverButton) {
        openReceiverButton.addEventListener('click', function() {
            openReceiverWindow();
        });
    }
    
    // 发送消息按钮
    const sendMessageButton = document.getElementById('send-message');
    if (sendMessageButton) {
        sendMessageButton.addEventListener('click', function() {
            const channel = document.getElementById('channel-select').value;
            const event = document.getElementById('event-name').value;
            const messageData = document.getElementById('message-data').value;
            
            if (!channel) {
                alert('请选择频道');
                return;
            }
            
            if (!event) {
                alert('请输入事件名称');
                return;
            }
            
            let data = {};
            try {
                data = JSON.parse(messageData || '{}');
            } catch (e) {
                alert('消息内容必须是有效的 JSON 格式');
                return;
            }
            
            broadcastEvent(channel, event, data);
        });
    }
    
    // 开始监听按钮
    const startListeningButton = document.getElementById('start-listening');
    if (startListeningButton) {
        startListeningButton.addEventListener('click', function() {
            const channel = document.getElementById('channel-select').value;
            const event = document.getElementById('event-name').value;
            
            if (!channel) {
                alert('请选择频道');
                return;
            }
            
            if (!event) {
                alert('请输入事件名称');
                return;
            }
            
            // 使用 EventSource 监听事件
            const eventSource = new EventSource(`/broadcasting/listen?channel=${channel}&event=${event}`);
            
            eventSource.addEventListener(event, function(e) {
                const data = JSON.parse(e.data);
                const messageList = document.getElementById('message-list');
                
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message';
                messageDiv.innerHTML = `
                    <div class="message-header">
                        <span class="message-event">${event}</span>
                        <span class="message-time">${new Date().toLocaleTimeString()}</span>
                    </div>
                    <div class="message-body">
                        <pre>${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
                
                messageList.appendChild(messageDiv);
            });
            
            eventSource.onerror = function() {
                alert('监听事件失败');
                eventSource.close();
            };
            
            // 保存 EventSource 实例
            window.eventSource = eventSource;
            
            alert('开始监听事件');
        });
    }
    
    // 停止监听按钮
    const stopListeningButton = document.getElementById('stop-listening');
    if (stopListeningButton) {
        stopListeningButton.addEventListener('click', function() {
            if (window.eventSource) {
                window.eventSource.close();
                window.eventSource = null;
                alert('停止监听事件');
            }
        });
    }
});
```

### CSS 样式

```css
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f5f5f5;
}

.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

h1 {
    color: #333;
    text-align: center;
    margin-bottom: 30px;
}

h2 {
    color: #555;
    margin-top: 0;
}

h3 {
    color: #777;
    margin-top: 20px;
}

.card {
    background-color: #fff;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    padding: 20px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 15px;
}

input[type="text"],
select,
textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 14px;
}

textarea {
    height: 100px;
    resize: vertical;
}

button {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    margin-right: 10px;
}

button:hover {
    background-color: #45a049;
}

ul {
    list-style-type: none;
    padding: 0;
}

li {
    padding: 8px 0;
    border-bottom: 1px solid #eee;
}

#message-list {
    max-height: 300px;
    overflow-y: auto;
}

.message {
    background-color: #f9f9f9;
    border-left: 3px solid #4CAF50;
    padding: 10px;
    margin-bottom: 10px;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
}

.message-event {
    font-weight: bold;
    color: #4CAF50;
}

.message-time {
    color: #999;
    font-size: 12px;
}

.message-body pre {
    margin: 0;
    white-space: pre-wrap;
    font-family: monospace;
    font-size: 12px;
}
```

## 总结

本示例展示了如何使用 NativePHP for ThinkPHP 的事件广播功能在不同窗口和组件之间进行通信。通过这个示例，你可以了解如何：

1. 创建和管理频道
2. 广播和监听事件
3. 在多个窗口之间共享数据
4. 构建响应式用户界面

这些功能可以用于构建更加复杂和交互性强的桌面应用程序。
