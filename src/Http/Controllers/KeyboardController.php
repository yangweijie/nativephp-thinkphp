<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Keyboard;

class KeyboardController
{
    /**
     * 注册快捷键
     *
     * @param Request $request
     * @return Response
     */
    public function register(Request $request)
    {
        $accelerator = $request->param('accelerator');
        $id = $request->param('id');

        // 注册快捷键
        $success = Keyboard::register($accelerator, function() use ($id) {
            // 触发快捷键事件
            event('native.keyboard.shortcut', $id);
        });

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 注销快捷键
     *
     * @param Request $request
     * @return Response
     */
    public function unregister(Request $request)
    {
        $id = $request->param('id');

        // 注销快捷键
        $success = Keyboard::unregister($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注销所有快捷键
     *
     * @return Response
     */
    public function unregisterAll()
    {
        // 注销所有快捷键
        Keyboard::unregisterAll();

        return json([
            'success' => true,
        ]);
    }

    /**
     * 注册全局快捷键
     *
     * @param Request $request
     * @return Response
     */
    public function registerGlobal(Request $request)
    {
        $accelerator = $request->param('accelerator');
        $id = $request->param('id');

        // 注册全局快捷键
        $success = Keyboard::registerGlobal($accelerator, function() use ($id) {
            // 触发全局快捷键事件
            event('native.keyboard.global_shortcut', $id);
        });

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 注销全局快捷键
     *
     * @param Request $request
     * @return Response
     */
    public function unregisterGlobal(Request $request)
    {
        $id = $request->param('id');

        // 注销全局快捷键
        $success = Keyboard::unregisterGlobal($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注销所有全局快捷键
     *
     * @return Response
     */
    public function unregisterAllGlobal()
    {
        // 注销所有全局快捷键
        Keyboard::unregisterAllGlobal();

        return json([
            'success' => true,
        ]);
    }

    /**
     * 模拟按键
     *
     * @param Request $request
     * @return Response
     */
    public function sendKey(Request $request)
    {
        $key = $request->param('key');
        $modifiers = $request->param('modifiers', []);

        // 模拟按键
        $success = Keyboard::sendKey($key, $modifiers);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 模拟按键序列
     *
     * @param Request $request
     * @return Response
     */
    public function sendText(Request $request)
    {
        $text = $request->param('text');

        // 模拟按键序列
        $success = Keyboard::sendText($text);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 模拟按下并松开按键
     *
     * @param Request $request
     * @return Response
     */
    public function tapKey(Request $request)
    {
        $key = $request->param('key');
        $modifiers = $request->param('modifiers', []);

        // 模拟按下并松开按键
        $success = Keyboard::tapKey($key, $modifiers);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 模拟按下按键
     *
     * @param Request $request
     * @return Response
     */
    public function keyDown(Request $request)
    {
        $key = $request->param('key');
        $modifiers = $request->param('modifiers', []);

        // 模拟按下按键
        $success = Keyboard::keyDown($key, $modifiers);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 模拟松开按键
     *
     * @param Request $request
     * @return Response
     */
    public function keyUp(Request $request)
    {
        $key = $request->param('key');
        $modifiers = $request->param('modifiers', []);

        // 模拟松开按键
        $success = Keyboard::keyUp($key, $modifiers);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 监听键盘事件
     *
     * @param Request $request
     * @return Response
     */
    public function on(Request $request)
    {
        $event = $request->param('event');
        $id = $request->param('id');

        // 监听键盘事件
        $success = Keyboard::on($event, function($eventData) use ($id) {
            // 触发键盘事件
            event('native.keyboard.event.' . $id, $eventData);
        });

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 移除键盘事件监听器
     *
     * @param Request $request
     * @return Response
     */
    public function off(Request $request)
    {
        $id = $request->param('id');

        // 移除键盘事件监听器
        $success = Keyboard::off($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 获取键盘布局
     *
     * @return Response
     */
    public function getLayout()
    {
        // 获取键盘布局
        $layout = Keyboard::getLayout();

        return json([
            'layout' => $layout,
        ]);
    }

    /**
     * 设置键盘布局
     *
     * @param Request $request
     * @return Response
     */
    public function setLayout(Request $request)
    {
        $layout = $request->param('layout');

        // 设置键盘布局
        $success = Keyboard::setLayout($layout);

        return json([
            'success' => $success,
        ]);
    }
}
