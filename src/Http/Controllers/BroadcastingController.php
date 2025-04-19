<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Broadcasting;

class BroadcastingController
{
    /**
     * 广播事件
     *
     * @param Request $request
     * @return Response
     */
    public function broadcast(Request $request)
    {
        $channel = $request->param('channel');
        $event = $request->param('event');
        $data = $request->param('data', []);
        
        // 广播事件
        $success = Broadcasting::broadcast($channel, $event, $data);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 监听事件
     *
     * @param Request $request
     * @return Response
     */
    public function listen(Request $request)
    {
        $channel = $request->param('channel');
        $event = $request->param('event');
        $id = $request->param('id');
        
        // 监听事件
        $success = Broadcasting::listen($channel, $event, function($eventData) use ($id) {
            // 触发事件
            event('native.broadcasting.' . $id, $eventData);
        });
        
        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }
    
    /**
     * 取消监听事件
     *
     * @param Request $request
     * @return Response
     */
    public function unlisten(Request $request)
    {
        $id = $request->param('id');
        
        // 取消监听事件
        $success = Broadcasting::unlisten($id);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 获取所有频道
     *
     * @return Response
     */
    public function getChannels()
    {
        // 获取所有频道
        $channels = Broadcasting::getChannels();
        
        return json([
            'channels' => $channels,
        ]);
    }
    
    /**
     * 获取频道中的事件
     *
     * @param Request $request
     * @return Response
     */
    public function getEvents(Request $request)
    {
        $channel = $request->param('channel');
        
        // 获取频道中的事件
        $events = Broadcasting::getEvents($channel);
        
        return json([
            'events' => $events,
        ]);
    }
    
    /**
     * 创建频道
     *
     * @param Request $request
     * @return Response
     */
    public function createChannel(Request $request)
    {
        $channel = $request->param('channel');
        
        // 创建频道
        $success = Broadcasting::createChannel($channel);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 删除频道
     *
     * @param Request $request
     * @return Response
     */
    public function deleteChannel(Request $request)
    {
        $channel = $request->param('channel');
        
        // 删除频道
        $success = Broadcasting::deleteChannel($channel);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 清空频道
     *
     * @param Request $request
     * @return Response
     */
    public function clearChannel(Request $request)
    {
        $channel = $request->param('channel');
        
        // 清空频道
        $success = Broadcasting::clearChannel($channel);
        
        return json([
            'success' => $success,
        ]);
    }
    
    /**
     * 检查频道是否存在
     *
     * @param Request $request
     * @return Response
     */
    public function channelExists(Request $request)
    {
        $channel = $request->param('channel');
        
        // 检查频道是否存在
        $exists = Broadcasting::channelExists($channel);
        
        return json([
            'exists' => $exists,
        ]);
    }
    
    /**
     * 获取频道中的监听器数量
     *
     * @param Request $request
     * @return Response
     */
    public function getListenerCount(Request $request)
    {
        $channel = $request->param('channel');
        
        // 获取频道中的监听器数量
        $count = Broadcasting::getListenerCount($channel);
        
        return json([
            'count' => $count,
        ]);
    }
}
