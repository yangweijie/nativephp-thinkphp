<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\App;
use Native\ThinkPHP\Facades\Window;
use Native\ThinkPHP\Facades\Notification;

class NativeController
{
    /**
     * 获取应用信息
     *
     * @return Response
     */
    public function info()
    {
        return json([
            'version' => config('native.version', '1.0.0'),
            'name' => config('native.name', 'NativePHP App'),
            'environment' => $this->getEnvironment(),
        ]);
    }

    /**
     * 获取环境信息
     *
     * @return string
     */
    protected function getEnvironment()
    {
        if (App::isRunningBundled()) {
            return 'desktop';
        }

        return 'development';
    }

    /**
     * 发送通知
     *
     * @param Request $request
     * @return Response
     */
    public function sendNotification(Request $request)
    {
        $title = $request->param('title', '');
        $body = $request->param('body', '');
        $options = $request->param('options', []);

        Notification::send($title, $body, $options);

        return json([
            'success' => true,
            'reference' => null, // Notification::send() 返回 void，所以不能使用其返回值
        ]);
    }

    /**
     * 打开窗口
     *
     * @param Request $request
     * @return Response
     */
    public function openWindow(Request $request)
    {
        $url = $request->param('url', '');
        $options = $request->param('options', []);

        $windowId = Window::open($url, $options);

        return json([
            'success' => true,
            'id' => $windowId,
        ]);
    }

    /**
     * 关闭窗口
     *
     * @param Request $request
     * @return Response
     */
    public function closeWindow(Request $request)
    {
        $id = $request->param('id');

        Window::close($id);

        return json([
            'success' => true,
        ]);
    }

    /**
     * 重启应用
     *
     * @return Response
     */
    public function restart()
    {
        App::restart();

        return json([
            'success' => true,
        ]);
    }

    /**
     * 退出应用
     *
     * @return Response
     */
    public function quit()
    {
        App::quit();

        return json([
            'success' => true,
        ]);
    }
}
