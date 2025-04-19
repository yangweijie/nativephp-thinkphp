<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Dock;

class DockController
{
    /**
     * 设置 Dock 图标
     *
     * @param Request $request
     * @return Response
     */
    public function setIcon(Request $request)
    {
        $path = $request->param('path');

        // 设置 Dock 图标
        $success = Dock::setIcon($path);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置 Dock 徽章文本
     *
     * @param Request $request
     * @return Response
     */
    public function setBadge(Request $request)
    {
        $text = $request->param('text');

        // 设置 Dock 徽章文本
        $success = Dock::setBadge($text);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置 Dock 徽章计数
     *
     * @param Request $request
     * @return Response
     */
    public function setBadgeCount(Request $request)
    {
        $count = $request->param('count');

        // 设置 Dock 徽章计数
        $success = Dock::setBadgeCount($count);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 获取 Dock 徽章计数
     *
     * @return Response
     */
    public function getBadgeCount()
    {
        // 获取 Dock 徽章计数
        $count = Dock::getBadgeCount();

        return json([
            'count' => $count,
        ]);
    }

    /**
     * 清除 Dock 徽章
     *
     * @return Response
     */
    public function clearBadge()
    {
        // 清除 Dock 徽章
        $success = Dock::clearBadge();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置 Dock 菜单
     *
     * @param Request $request
     * @return Response
     */
    public function setMenu(Request $request)
    {
        $items = $request->param('items');

        // 设置 Dock 菜单
        $success = Dock::setMenu($items);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 显示 Dock 图标
     *
     * @return Response
     */
    public function show()
    {
        // 显示 Dock 图标
        $success = Dock::show();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 隐藏 Dock 图标
     *
     * @return Response
     */
    public function hide()
    {
        // 隐藏 Dock 图标
        $success = Dock::hide();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 检查 Dock 图标是否可见
     *
     * @return Response
     */
    public function isVisible()
    {
        // 检查 Dock 图标是否可见
        $visible = Dock::isVisible();

        return json([
            'visible' => $visible,
        ]);
    }

    /**
     * 弹跳 Dock 图标
     *
     * @param Request $request
     * @return Response
     */
    public function bounce(Request $request)
    {
        $type = $request->param('type', 'informational');

        // 弹跳 Dock 图标
        $success = Dock::bounce($type);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 取消弹跳 Dock 图标
     *
     * @param Request $request
     * @return Response
     */
    public function cancelBounce(Request $request)
    {
        $id = $request->param('id');

        // 取消弹跳 Dock 图标
        $success = Dock::cancelBounce($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置下载进度条
     *
     * @param Request $request
     * @return Response
     */
    public function setDownloadProgress(Request $request)
    {
        $progress = $request->param('progress');

        // 设置下载进度条
        $success = Dock::setDownloadProgress($progress);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 清除下载进度条
     *
     * @return Response
     */
    public function clearDownloadProgress()
    {
        // 清除下载进度条
        $success = Dock::clearDownloadProgress();

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置 Dock 图标的工具提示
     *
     * @param Request $request
     * @return Response
     */
    public function setToolTip(Request $request)
    {
        $tooltip = $request->param('tooltip');

        // 设置 Dock 图标的工具提示
        $success = Dock::setToolTip($tooltip);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注册 Dock 菜单点击事件
     *
     * @param Request $request
     * @return Response
     */
    public function onMenuClick(Request $request)
    {
        $id = $request->param('id');

        // 注册 Dock 菜单点击事件
        $success = true;

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 移除 Dock 菜单点击事件监听器
     *
     * @param Request $request
     * @return Response
     */
    public function offMenuClick(Request $request)
    {
        $id = $request->param('id');

        // 移除 Dock 菜单点击事件监听器
        $success = Dock::offMenuClick($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注册 Dock 图标点击事件
     *
     * @param Request $request
     * @return Response
     */
    public function onClick(Request $request)
    {
        $id = $request->param('id');

        // 注册 Dock 图标点击事件
        $success = true;

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 移除 Dock 图标点击事件监听器
     *
     * @param Request $request
     * @return Response
     */
    public function offClick(Request $request)
    {
        $id = $request->param('id');

        // 移除 Dock 图标点击事件监听器
        $success = Dock::offClick($id);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置 Dock 图标闪烁
     *
     * @param Request $request
     * @return Response
     */
    public function setFlash(Request $request)
    {
        $flash = $request->param('flash', true);

        // 设置 Dock 图标闪烁
        $success = Dock::setFlash($flash);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 创建自定义 Dock 菜单
     *
     * @param Request $request
     * @return Response
     */
    public function createMenu(Request $request)
    {
        $template = $request->param('template');

        // 创建自定义 Dock 菜单
        $success = Dock::createMenu($template);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 获取 Dock 图标大小
     *
     * @return Response
     */
    public function getIconSize()
    {
        // 获取 Dock 图标大小
        $size = Dock::getIconSize();

        return json([
            'size' => $size,
        ]);
    }
}
