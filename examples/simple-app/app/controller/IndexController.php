<?php
declare(strict_types=1);

namespace app\controller;

use NativePHP\Think\Facades\Native;
use think\Request;

class IndexController
{
    /**
     * 主页
     */
    public function index()
    {
        return view('index/index', [
            'title' => 'NativePHP-ThinkPHP 示例应用',
            'version' => Native::getConfig('app.version'),
        ]);
    }
    
    /**
     * 关于页面
     */
    public function about()
    {
        return view('index/about', [
            'title' => '关于',
            'version' => Native::getConfig('app.version'),
        ]);
    }
    
    /**
     * 发送通知
     */
    public function sendNotification(Request $request)
    {
        $title = $request->param('title', '通知');
        $message = $request->param('message', '这是一条测试通知');
        
        Native::notification()
            ->title($title)
            ->body($message)
            ->show();
        
        return json(['success' => true]);
    }
}
