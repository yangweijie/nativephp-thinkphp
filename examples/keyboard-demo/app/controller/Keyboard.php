<?php

namespace app\controller;

use app\BaseController;
use Native\ThinkPHP\Facades\Keyboard as KeyboardFacade;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Settings;
use Native\ThinkPHP\Facades\Window;
use think\facade\View;

class Keyboard extends BaseController
{
    /**
     * 显示主页
     *
     * @return \think\Response
     */
    public function index()
    {
        // 获取已注册的快捷键
        $shortcuts = KeyboardFacade::getRegisteredShortcuts();
        
        // 获取已注册的全局快捷键
        $globalShortcuts = KeyboardFacade::getRegisteredGlobalShortcuts();
        
        // 获取键盘布局
        $layout = KeyboardFacade::getLayout();
        
        return View::fetch('keyboard/index', [
            'shortcuts' => $shortcuts,
            'globalShortcuts' => $globalShortcuts,
            'layout' => $layout,
        ]);
    }
    
    /**
     * 注册快捷键
     *
     * @return \think\Response
     */
    public function register()
    {
        $accelerator = input('accelerator');
        $description = input('description');
        $isGlobal = input('is_global/b', false);
        
        if (empty($accelerator)) {
            return json(['success' => false, 'message' => '快捷键组合不能为空']);
        }
        
        // 检查快捷键是否已注册
        if ($isGlobal) {
            if (KeyboardFacade::isGlobalRegistered($accelerator)) {
                return json(['success' => false, 'message' => '该全局快捷键已注册']);
            }
        } else {
            if (KeyboardFacade::isRegistered($accelerator)) {
                return json(['success' => false, 'message' => '该快捷键已注册']);
            }
        }
        
        // 注册快捷键
        if ($isGlobal) {
            $id = KeyboardFacade::registerGlobal($accelerator, function () use ($accelerator, $description) {
                // 发送通知
                Notification::send('全局快捷键触发', "您按下了全局快捷键：{$accelerator}");
                
                // 记录快捷键触发
                $this->logShortcutTrigger($accelerator, $description, true);
            });
        } else {
            $id = KeyboardFacade::register($accelerator, function () use ($accelerator, $description) {
                // 发送通知
                Notification::send('快捷键触发', "您按下了快捷键：{$accelerator}");
                
                // 记录快捷键触发
                $this->logShortcutTrigger($accelerator, $description, false);
            });
        }
        
        if ($id) {
            // 保存快捷键信息
            $shortcuts = Settings::get('keyboard.shortcuts', []);
            $shortcuts[$id] = [
                'id' => $id,
                'accelerator' => $accelerator,
                'description' => $description,
                'is_global' => $isGlobal,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            Settings::set('keyboard.shortcuts', $shortcuts);
            
            return json(['success' => true, 'message' => ($isGlobal ? '全局' : '') . '快捷键注册成功', 'id' => $id]);
        } else {
            return json(['success' => false, 'message' => ($isGlobal ? '全局' : '') . '快捷键注册失败']);
        }
    }
    
    /**
     * 注销快捷键
     *
     * @return \think\Response
     */
    public function unregister()
    {
        $id = input('id');
        
        if (empty($id)) {
            return json(['success' => false, 'message' => '快捷键ID不能为空']);
        }
        
        // 获取快捷键信息
        $shortcuts = Settings::get('keyboard.shortcuts', []);
        if (!isset($shortcuts[$id])) {
            return json(['success' => false, 'message' => '快捷键不存在']);
        }
        
        $isGlobal = $shortcuts[$id]['is_global'] ?? false;
        
        // 注销快捷键
        $result = $isGlobal ? KeyboardFacade::unregisterGlobal($id) : KeyboardFacade::unregister($id);
        
        if ($result) {
            // 删除快捷键信息
            unset($shortcuts[$id]);
            Settings::set('keyboard.shortcuts', $shortcuts);
            
            return json(['success' => true, 'message' => ($isGlobal ? '全局' : '') . '快捷键注销成功']);
        } else {
            return json(['success' => false, 'message' => ($isGlobal ? '全局' : '') . '快捷键注销失败']);
        }
    }
    
    /**
     * 注销所有快捷键
     *
     * @return \think\Response
     */
    public function unregisterAll()
    {
        $type = input('type', 'all');
        
        if ($type === 'global') {
            // 注销所有全局快捷键
            KeyboardFacade::unregisterAllGlobal();
            
            // 删除所有全局快捷键信息
            $shortcuts = Settings::get('keyboard.shortcuts', []);
            foreach ($shortcuts as $id => $shortcut) {
                if ($shortcut['is_global'] ?? false) {
                    unset($shortcuts[$id]);
                }
            }
            Settings::set('keyboard.shortcuts', $shortcuts);
            
            return json(['success' => true, 'message' => '所有全局快捷键已注销']);
        } elseif ($type === 'local') {
            // 注销所有本地快捷键
            KeyboardFacade::unregisterAll();
            
            // 删除所有本地快捷键信息
            $shortcuts = Settings::get('keyboard.shortcuts', []);
            foreach ($shortcuts as $id => $shortcut) {
                if (!($shortcut['is_global'] ?? false)) {
                    unset($shortcuts[$id]);
                }
            }
            Settings::set('keyboard.shortcuts', $shortcuts);
            
            return json(['success' => true, 'message' => '所有本地快捷键已注销']);
        } else {
            // 注销所有快捷键
            KeyboardFacade::unregisterAll();
            KeyboardFacade::unregisterAllGlobal();
            
            // 删除所有快捷键信息
            Settings::set('keyboard.shortcuts', []);
            
            return json(['success' => true, 'message' => '所有快捷键已注销']);
        }
    }
    
    /**
     * 模拟按键
     *
     * @return \think\Response
     */
    public function sendKey()
    {
        $key = input('key');
        $modifiers = input('modifiers/a', []);
        
        if (empty($key)) {
            return json(['success' => false, 'message' => '按键不能为空']);
        }
        
        $result = KeyboardFacade::sendKey($key, $modifiers);
        
        if ($result) {
            return json(['success' => true, 'message' => '按键模拟成功']);
        } else {
            return json(['success' => false, 'message' => '按键模拟失败']);
        }
    }
    
    /**
     * 模拟按键序列
     *
     * @return \think\Response
     */
    public function sendText()
    {
        $text = input('text');
        
        if (empty($text)) {
            return json(['success' => false, 'message' => '文本不能为空']);
        }
        
        $result = KeyboardFacade::sendText($text);
        
        if ($result) {
            return json(['success' => true, 'message' => '文本输入成功']);
        } else {
            return json(['success' => false, 'message' => '文本输入失败']);
        }
    }
    
    /**
     * 显示快捷键历史记录
     *
     * @return \think\Response
     */
    public function history()
    {
        // 获取快捷键触发历史记录
        $history = Settings::get('keyboard.history', []);
        
        // 按时间倒序排序
        usort($history, function ($a, $b) {
            return strtotime($b['triggered_at']) - strtotime($a['triggered_at']);
        });
        
        return View::fetch('keyboard/history', [
            'history' => $history,
        ]);
    }
    
    /**
     * 清空快捷键历史记录
     *
     * @return \think\Response
     */
    public function clearHistory()
    {
        Settings::set('keyboard.history', []);
        
        return json(['success' => true, 'message' => '历史记录已清空']);
    }
    
    /**
     * 显示键盘监听器页面
     *
     * @return \think\Response
     */
    public function listener()
    {
        return View::fetch('keyboard/listener');
    }
    
    /**
     * 开始监听键盘事件
     *
     * @return \think\Response
     */
    public function startListener()
    {
        $event = input('event', 'keydown');
        
        // 检查是否已有监听器
        $listenerId = Settings::get('keyboard.listener.id');
        if ($listenerId) {
            // 先移除旧的监听器
            KeyboardFacade::off($listenerId);
        }
        
        // 注册新的监听器
        $id = KeyboardFacade::on($event, function ($keyEvent) {
            // 记录按键事件
            $this->logKeyEvent($keyEvent);
        });
        
        if ($id) {
            // 保存监听器信息
            Settings::set('keyboard.listener', [
                'id' => $id,
                'event' => $event,
                'started_at' => date('Y-m-d H:i:s'),
            ]);
            
            return json(['success' => true, 'message' => '键盘监听器已启动', 'id' => $id]);
        } else {
            return json(['success' => false, 'message' => '键盘监听器启动失败']);
        }
    }
    
    /**
     * 停止监听键盘事件
     *
     * @return \think\Response
     */
    public function stopListener()
    {
        // 获取监听器信息
        $listener = Settings::get('keyboard.listener');
        if (empty($listener) || empty($listener['id'])) {
            return json(['success' => false, 'message' => '没有正在运行的键盘监听器']);
        }
        
        // 移除监听器
        $result = KeyboardFacade::off($listener['id']);
        
        if ($result) {
            // 清除监听器信息
            Settings::forget('keyboard.listener');
            
            return json(['success' => true, 'message' => '键盘监听器已停止']);
        } else {
            return json(['success' => false, 'message' => '键盘监听器停止失败']);
        }
    }
    
    /**
     * 获取键盘事件记录
     *
     * @return \think\Response
     */
    public function getKeyEvents()
    {
        // 获取键盘事件记录
        $events = Settings::get('keyboard.events', []);
        
        // 按时间倒序排序
        usort($events, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });
        
        // 只返回最近的 100 条记录
        $events = array_slice($events, 0, 100);
        
        return json(['success' => true, 'events' => $events]);
    }
    
    /**
     * 清空键盘事件记录
     *
     * @return \think\Response
     */
    public function clearKeyEvents()
    {
        Settings::set('keyboard.events', []);
        
        return json(['success' => true, 'message' => '键盘事件记录已清空']);
    }
    
    /**
     * 记录快捷键触发
     *
     * @param string $accelerator 快捷键组合
     * @param string $description 描述
     * @param bool $isGlobal 是否全局快捷键
     * @return void
     */
    protected function logShortcutTrigger($accelerator, $description, $isGlobal)
    {
        // 获取历史记录
        $history = Settings::get('keyboard.history', []);
        
        // 添加新记录
        $history[] = [
            'accelerator' => $accelerator,
            'description' => $description,
            'is_global' => $isGlobal,
            'triggered_at' => date('Y-m-d H:i:s'),
        ];
        
        // 限制记录数量
        if (count($history) > 100) {
            $history = array_slice($history, -100);
        }
        
        // 保存历史记录
        Settings::set('keyboard.history', $history);
    }
    
    /**
     * 记录按键事件
     *
     * @param array $keyEvent 按键事件
     * @return void
     */
    protected function logKeyEvent($keyEvent)
    {
        // 获取事件记录
        $events = Settings::get('keyboard.events', []);
        
        // 添加新记录
        $events[] = array_merge($keyEvent, [
            'time' => date('Y-m-d H:i:s'),
        ]);
        
        // 限制记录数量
        if (count($events) > 1000) {
            $events = array_slice($events, -1000);
        }
        
        // 保存事件记录
        Settings::set('keyboard.events', $events);
    }
}
