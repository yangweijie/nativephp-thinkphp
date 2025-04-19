<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Window as WindowFacade;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use think\facade\View;
use think\facade\Session;

class Window extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取所有窗口
        $windows = WindowFacade::all();
        
        // 获取当前窗口
        $current = WindowFacade::current();
        
        return View::fetch('window/index', [
            'windows' => $windows,
            'current' => $current,
        ]);
    }
    
    /**
     * 打开新窗口
     *
     * @return \think\Response
     */
    public function open()
    {
        $url = input('url', '/window/child');
        $title = input('title', '子窗口');
        $width = input('width/d', 800);
        $height = input('height/d', 600);
        $x = input('x/d', null);
        $y = input('y/d', null);
        $resizable = input('resizable/b', true);
        $minimizable = input('minimizable/b', true);
        $maximizable = input('maximizable/b', true);
        $closable = input('closable/b', true);
        $alwaysOnTop = input('always_on_top/b', false);
        $fullscreen = input('fullscreen/b', false);
        
        // 创建窗口选项
        $options = [
            'title' => $title,
            'width' => $width,
            'height' => $height,
            'resizable' => $resizable,
            'minimizable' => $minimizable,
            'maximizable' => $maximizable,
            'closable' => $closable,
            'alwaysOnTop' => $alwaysOnTop,
            'fullscreen' => $fullscreen,
        ];
        
        // 设置窗口位置
        if ($x !== null && $y !== null) {
            $options['x'] = $x;
            $options['y'] = $y;
        }
        
        // 打开新窗口
        $id = WindowFacade::open($url, $options);
        
        // 保存窗口信息
        $this->saveWindowInfo($id, $title, $url, $options);
        
        return json(['success' => true, 'message' => '窗口已打开', 'id' => $id]);
    }
    
    /**
     * 关闭窗口
     *
     * @return \think\Response
     */
    public function close()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 关闭窗口
        $result = WindowFacade::close($id);
        
        if ($result) {
            // 删除窗口信息
            $this->removeWindowInfo($id);
            
            return json(['success' => true, 'message' => '窗口已关闭']);
        } else {
            return json(['success' => false, 'message' => '窗口关闭失败']);
        }
    }
    
    /**
     * 关闭所有窗口
     *
     * @return \think\Response
     */
    public function closeAll()
    {
        // 关闭所有窗口
        $result = WindowFacade::closeAll();
        
        if ($result) {
            // 清空窗口信息
            Settings::set('windows', []);
            
            return json(['success' => true, 'message' => '所有窗口已关闭']);
        } else {
            return json(['success' => false, 'message' => '关闭所有窗口失败']);
        }
    }
    
    /**
     * 最小化窗口
     *
     * @return \think\Response
     */
    public function minimize()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 最小化窗口
        $result = WindowFacade::minimize($id);
        
        if ($result) {
            return json(['success' => true, 'message' => '窗口已最小化']);
        } else {
            return json(['success' => false, 'message' => '窗口最小化失败']);
        }
    }
    
    /**
     * 最大化窗口
     *
     * @return \think\Response
     */
    public function maximize()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 最大化窗口
        $result = WindowFacade::maximize($id);
        
        if ($result) {
            return json(['success' => true, 'message' => '窗口已最大化']);
        } else {
            return json(['success' => false, 'message' => '窗口最大化失败']);
        }
    }
    
    /**
     * 恢复窗口大小
     *
     * @return \think\Response
     */
    public function restore()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 恢复窗口大小
        $result = WindowFacade::restore($id);
        
        if ($result) {
            return json(['success' => true, 'message' => '窗口已恢复']);
        } else {
            return json(['success' => false, 'message' => '窗口恢复失败']);
        }
    }
    
    /**
     * 聚焦窗口
     *
     * @return \think\Response
     */
    public function focus()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 聚焦窗口
        $result = WindowFacade::focus($id);
        
        if ($result) {
            return json(['success' => true, 'message' => '窗口已聚焦']);
        } else {
            return json(['success' => false, 'message' => '窗口聚焦失败']);
        }
    }
    
    /**
     * 设置窗口标题
     *
     * @return \think\Response
     */
    public function setTitle()
    {
        $id = input('id');
        $title = input('title');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        if (empty($title)) {
            return json(['success' => false, 'message' => '窗口标题不能为空']);
        }
        
        // 设置窗口标题
        $result = WindowFacade::setTitle($title, $id);
        
        if ($result) {
            // 更新窗口信息
            $this->updateWindowInfo($id, ['title' => $title]);
            
            return json(['success' => true, 'message' => '窗口标题已设置']);
        } else {
            return json(['success' => false, 'message' => '设置窗口标题失败']);
        }
    }
    
    /**
     * 设置窗口大小
     *
     * @return \think\Response
     */
    public function setSize()
    {
        $id = input('id');
        $width = input('width/d');
        $height = input('height/d');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        if (empty($width) || empty($height)) {
            return json(['success' => false, 'message' => '窗口宽度和高度不能为空']);
        }
        
        // 设置窗口大小
        $result = WindowFacade::setSize($width, $height, $id);
        
        if ($result) {
            // 更新窗口信息
            $this->updateWindowInfo($id, ['width' => $width, 'height' => $height]);
            
            return json(['success' => true, 'message' => '窗口大小已设置']);
        } else {
            return json(['success' => false, 'message' => '设置窗口大小失败']);
        }
    }
    
    /**
     * 设置窗口位置
     *
     * @return \think\Response
     */
    public function setPosition()
    {
        $id = input('id');
        $x = input('x/d');
        $y = input('y/d');
        $animated = input('animated/b', false);
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        if ($x === null || $y === null) {
            return json(['success' => false, 'message' => '窗口位置不能为空']);
        }
        
        // 设置窗口位置
        $result = WindowFacade::setPosition($x, $y, $animated, $id);
        
        if ($result) {
            // 更新窗口信息
            $this->updateWindowInfo($id, ['x' => $x, 'y' => $y]);
            
            return json(['success' => true, 'message' => '窗口位置已设置']);
        } else {
            return json(['success' => false, 'message' => '设置窗口位置失败']);
        }
    }
    
    /**
     * 设置窗口是否总是置顶
     *
     * @return \think\Response
     */
    public function alwaysOnTop()
    {
        $id = input('id');
        $alwaysOnTop = input('always_on_top/b', true);
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 设置窗口是否总是置顶
        $result = WindowFacade::alwaysOnTop($alwaysOnTop, $id);
        
        if ($result) {
            // 更新窗口信息
            $this->updateWindowInfo($id, ['alwaysOnTop' => $alwaysOnTop]);
            
            return json(['success' => true, 'message' => '窗口置顶状态已设置']);
        } else {
            return json(['success' => false, 'message' => '设置窗口置顶状态失败']);
        }
    }
    
    /**
     * 显示子窗口页面
     *
     * @return \think\Response
     */
    public function child()
    {
        // 获取当前窗口
        $current = WindowFacade::current();
        
        // 获取所有窗口
        $windows = WindowFacade::all();
        
        // 获取窗口信息
        $windowInfo = $this->getWindowInfo($current['id'] ?? '');
        
        return View::fetch('window/child', [
            'current' => $current,
            'windows' => $windows,
            'windowInfo' => $windowInfo,
        ]);
    }
    
    /**
     * 发送消息到其他窗口
     *
     * @return \think\Response
     */
    public function sendMessage()
    {
        $targetId = input('target_id');
        $message = input('message');
        $sourceId = input('source_id');
        
        if (empty($targetId)) {
            return json(['success' => false, 'message' => '目标窗口ID不能为空']);
        }
        
        if (empty($message)) {
            return json(['success' => false, 'message' => '消息内容不能为空']);
        }
        
        // 保存消息
        $this->saveMessage($sourceId, $targetId, $message);
        
        return json(['success' => true, 'message' => '消息已发送']);
    }
    
    /**
     * 获取消息
     *
     * @return \think\Response
     */
    public function getMessages()
    {
        $windowId = input('window_id');
        
        if (empty($windowId)) {
            return json(['success' => false, 'message' => '窗口ID不能为空']);
        }
        
        // 获取消息
        $messages = $this->getWindowMessages($windowId);
        
        // 清除已读消息
        $this->clearWindowMessages($windowId);
        
        return json(['success' => true, 'messages' => $messages]);
    }
    
    /**
     * 保存窗口信息
     *
     * @param string $id 窗口ID
     * @param string $title 窗口标题
     * @param string $url 窗口URL
     * @param array $options 窗口选项
     * @return void
     */
    protected function saveWindowInfo($id, $title, $url, array $options)
    {
        // 获取所有窗口信息
        $windows = Settings::get('windows', []);
        
        // 添加新窗口信息
        $windows[$id] = [
            'id' => $id,
            'title' => $title,
            'url' => $url,
            'options' => $options,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        // 保存窗口信息
        Settings::set('windows', $windows);
    }
    
    /**
     * 更新窗口信息
     *
     * @param string $id 窗口ID
     * @param array $data 更新数据
     * @return void
     */
    protected function updateWindowInfo($id, array $data)
    {
        // 获取所有窗口信息
        $windows = Settings::get('windows', []);
        
        // 更新窗口信息
        if (isset($windows[$id])) {
            foreach ($data as $key => $value) {
                if (isset($windows[$id]['options'][$key])) {
                    $windows[$id]['options'][$key] = $value;
                } else {
                    $windows[$id][$key] = $value;
                }
            }
            
            // 保存窗口信息
            Settings::set('windows', $windows);
        }
    }
    
    /**
     * 删除窗口信息
     *
     * @param string $id 窗口ID
     * @return void
     */
    protected function removeWindowInfo($id)
    {
        // 获取所有窗口信息
        $windows = Settings::get('windows', []);
        
        // 删除窗口信息
        if (isset($windows[$id])) {
            unset($windows[$id]);
            
            // 保存窗口信息
            Settings::set('windows', $windows);
        }
    }
    
    /**
     * 获取窗口信息
     *
     * @param string $id 窗口ID
     * @return array|null
     */
    protected function getWindowInfo($id)
    {
        // 获取所有窗口信息
        $windows = Settings::get('windows', []);
        
        // 返回窗口信息
        return $windows[$id] ?? null;
    }
    
    /**
     * 保存消息
     *
     * @param string $sourceId 源窗口ID
     * @param string $targetId 目标窗口ID
     * @param string $message 消息内容
     * @return void
     */
    protected function saveMessage($sourceId, $targetId, $message)
    {
        // 获取所有消息
        $messages = Settings::get('messages', []);
        
        // 添加新消息
        $messages[] = [
            'id' => md5(uniqid('message', true)),
            'source_id' => $sourceId,
            'target_id' => $targetId,
            'message' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'read' => false,
        ];
        
        // 保存消息
        Settings::set('messages', $messages);
    }
    
    /**
     * 获取窗口消息
     *
     * @param string $windowId 窗口ID
     * @return array
     */
    protected function getWindowMessages($windowId)
    {
        // 获取所有消息
        $messages = Settings::get('messages', []);
        
        // 过滤出目标窗口的未读消息
        $windowMessages = [];
        foreach ($messages as $key => $message) {
            if ($message['target_id'] === $windowId && !$message['read']) {
                $windowMessages[] = $message;
                $messages[$key]['read'] = true;
            }
        }
        
        // 保存消息状态
        Settings::set('messages', $messages);
        
        return $windowMessages;
    }
    
    /**
     * 清除窗口消息
     *
     * @param string $windowId 窗口ID
     * @return void
     */
    protected function clearWindowMessages($windowId)
    {
        // 获取所有消息
        $messages = Settings::get('messages', []);
        
        // 过滤出非目标窗口的消息
        $filteredMessages = [];
        foreach ($messages as $message) {
            if ($message['target_id'] !== $windowId || !$message['read']) {
                $filteredMessages[] = $message;
            }
        }
        
        // 保存消息
        Settings::set('messages', $filteredMessages);
    }
}
