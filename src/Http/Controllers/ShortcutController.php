<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Shortcut;

class ShortcutController
{
    /**
     * 创建桌面快捷方式
     *
     * @param Request $request
     * @return Response
     */
    public function createDesktop(Request $request)
    {
        $options = $request->param();

        // 创建桌面快捷方式
        $success = Shortcut::createDesktopShortcut($options);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 创建开始菜单快捷方式
     *
     * @param Request $request
     * @return Response
     */
    public function createStartMenu(Request $request)
    {
        $options = $request->param();

        // 创建开始菜单快捷方式
        $success = Shortcut::createStartMenuShortcut($options);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 创建自定义快捷方式
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request)
    {
        $path = $request->param('path');
        $options = $request->except(['path']);

        // 创建自定义快捷方式
        $success = Shortcut::createShortcut($path, $options);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 检查桌面快捷方式是否存在
     *
     * @return Response
     */
    public function existsOnDesktop()
    {
        // 检查桌面快捷方式是否存在
        $exists = Shortcut::existsOnDesktop();

        return json([
            'exists' => $exists,
        ]);
    }

    /**
     * 检查开始菜单快捷方式是否存在
     *
     * @return Response
     */
    public function existsInStartMenu()
    {
        // 检查开始菜单快捷方式是否存在
        $exists = Shortcut::existsInStartMenu();

        return json([
            'exists' => $exists,
        ]);
    }

    /**
     * 检查快捷方式是否存在
     *
     * @param Request $request
     * @return Response
     */
    public function exists(Request $request)
    {
        $path = $request->param('path');

        // 检查快捷方式是否存在
        $exists = Shortcut::exists($path);

        return json([
            'exists' => $exists,
        ]);
    }

    /**
     * 删除桌面快捷方式
     *
     * @return Response
     */
    public function removeFromDesktop()
    {
        // 删除桌面快捷方式
        $success = Shortcut::removeFromDesktop();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 删除开始菜单快捷方式
     *
     * @return Response
     */
    public function removeFromStartMenu()
    {
        // 删除开始菜单快捷方式
        $success = Shortcut::removeFromStartMenu();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 删除快捷方式
     *
     * @param Request $request
     * @return Response
     */
    public function remove(Request $request)
    {
        $path = $request->param('path');

        // 删除快捷方式
        $success = Shortcut::remove($path);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置开机自启动
     *
     * @param Request $request
     * @return Response
     */
    public function setLoginItemSettings(Request $request)
    {
        $enabled = $request->param('enabled', true);
        $options = $request->except(['enabled']);

        // 设置开机自启动
        $success = Shortcut::setLoginItemSettings($enabled, $options);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 获取开机自启动设置
     *
     * @param Request $request
     * @return Response
     */
    public function getLoginItemSettings(Request $request)
    {
        $options = $request->param();

        // 获取开机自启动设置
        $settings = Shortcut::getLoginItemSettings($options);

        return json([
            'settings' => $settings,
        ]);
    }

    /**
     * 获取桌面路径
     *
     * @return Response
     */
    public function getDesktopPath()
    {
        // 获取桌面路径
        $path = Shortcut::getDesktopPath();

        return json([
            'path' => $path,
        ]);
    }

    /**
     * 获取开始菜单路径
     *
     * @return Response
     */
    public function getStartMenuPath()
    {
        // 获取开始菜单路径
        $path = Shortcut::getStartMenuPath();

        return json([
            'path' => $path,
        ]);
    }

    /**
     * 获取应用程序路径
     *
     * @return Response
     */
    public function getApplicationPath()
    {
        // 获取应用程序路径
        $path = Shortcut::getApplicationPath();

        return json([
            'path' => $path,
        ]);
    }

    /**
     * 获取应用程序名称
     *
     * @return Response
     */
    public function getApplicationName()
    {
        // 获取应用程序名称
        $name = Shortcut::getApplicationName();

        return json([
            'name' => $name,
        ]);
    }
}
