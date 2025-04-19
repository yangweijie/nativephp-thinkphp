<?php

namespace app\controller;

use app\BaseController;
use think\facade\View;
use think\facade\Request;
use Native\ThinkPHP\Facades\Broadcasting;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class BroadcastingController extends BaseController
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
        $channel = Request::param('channel');
        $event = Request::param('event');
        $data = Request::param('data', []);
        
        // 广播事件
        $success = Broadcasting::broadcast($channel, $event, $data);
        
        return json(['success' => $success]);
    }
    
    /**
     * 监听事件
     *
     * @return \think\Response
     */
    public function listen()
    {
        $channel = Request::param('channel');
        $event = Request::param('event');
        
        // 生成监听器ID
        $id = md5($channel . '.' . $event . '.' . microtime(true));
        
        // 监听事件
        Broadcasting::listen($channel, $event, function ($data) use ($channel, $event) {
            // 发送通知
            Notification::send('事件接收', "频道: {$channel}, 事件: {$event}");
            
            // 将数据传递给前端
            $this->sendEventToFrontend($channel, $event, $data);
        });
        
        return json(['success' => true, 'id' => $id]);
    }
    
    /**
     * 取消监听事件
     *
     * @return \think\Response
     */
    public function unlisten()
    {
        $id = Request::param('id');
        
        // 取消监听事件
        $success = Broadcasting::unlisten($id);
        
        return json(['success' => $success]);
    }
    
    /**
     * 获取频道列表
     *
     * @return \think\Response
     */
    public function channels()
    {
        // 获取频道列表
        $channels = Broadcasting::getChannels();
        
        return json(['channels' => $channels]);
    }
    
    /**
     * 获取事件列表
     *
     * @return \think\Response
     */
    public function events()
    {
        $channel = Request::param('channel');
        
        // 获取事件列表
        $events = Broadcasting::getEvents($channel);
        
        return json(['events' => $events]);
    }
    
    /**
     * 创建频道
     *
     * @return \think\Response
     */
    public function createChannel()
    {
        $channel = Request::param('channel');
        
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
        $channel = Request::param('channel');
        
        // 删除频道
        $success = Broadcasting::deleteChannel($channel);
        
        return json(['success' => $success]);
    }
    
    /**
     * 打开新窗口
     *
     * @return \think\Response
     */
    public function openWindow()
    {
        $type = Request::param('type', 'sender');
        $url = $type === 'sender' ? '/broadcasting/sender' : '/broadcasting/receiver';
        
        // 打开新窗口
        $windowId = Window::open($url, [
            'title' => $type === 'sender' ? '发送者' : '接收者',
            'width' => 800,
            'height' => 600,
            'resizable' => true,
        ]);
        
        return json(['success' => true, 'windowId' => $windowId]);
    }
    
    /**
     * 将事件数据发送到前端
     *
     * @param string $channel
     * @param string $event
     * @param array $data
     * @return void
     */
    protected function sendEventToFrontend($channel, $event, $data)
    {
        // 这里可以使用WebSocket或其他方式将数据发送到前端
        // 在实际应用中，你可能需要使用WebSocket或Server-Sent Events
        // 这里简化处理，通过广播到特定频道来实现
        Broadcasting::broadcast('frontend', 'event-received', [
            'channel' => $channel,
            'event' => $event,
            'data' => $data,
            'time' => date('Y-m-d H:i:s'),
        ]);
    }
}
