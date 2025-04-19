<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\PushNotification;

class PushNotificationController
{
    /**
     * 发送推送通知
     *
     * @param Request $request
     * @return Response
     */
    public function send(Request $request)
    {
        $tokens = $request->param('tokens');
        $title = $request->param('title');
        $body = $request->param('body');
        $data = $request->param('data', []);
        $options = $request->param('options', []);

        // 发送推送通知
        $reference = PushNotification::send($tokens, $title, $body, $data, $options);

        return json([
            'success' => $reference !== false,
            'reference' => $reference,
        ]);
    }

    /**
     * 获取推送状态
     *
     * @param Request $request
     * @return Response
     */
    public function getStatus(Request $request)
    {
        $reference = $request->param('reference');

        // 获取推送状态
        $status = PushNotification::getStatus($reference);

        return json($status);
    }

    /**
     * 取消推送
     *
     * @param Request $request
     * @return Response
     */
    public function cancel(Request $request)
    {
        $reference = $request->param('reference');

        // 取消推送
        $success = PushNotification::cancel($reference);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注册设备
     *
     * @param Request $request
     * @return Response
     */
    public function registerDevice(Request $request)
    {
        $token = $request->param('token');
        $data = $request->param('data', []);
        $provider = $request->param('provider');
        $config = $request->param('config', []);

        // 设置推送服务提供商
        if ($provider) {
            PushNotification::setProvider($provider);
        }

        // 设置推送服务配置
        if (!empty($config)) {
            PushNotification::setConfig($config);
        }

        // 注册设备
        $success = PushNotification::registerDevice($token, $data);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 注销设备
     *
     * @param Request $request
     * @return Response
     */
    public function unregisterDevice(Request $request)
    {
        $token = $request->param('token');

        // 注销设备
        $success = PushNotification::unregisterDevice($token);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 获取设备信息
     *
     * @param Request $request
     * @return Response
     */
    public function getDeviceInfo(Request $request)
    {
        $token = $request->param('token');

        // 获取设备信息
        $device = PushNotification::getDeviceInfo($token);

        return json([
            'success' => $device !== null,
            'device' => $device,
        ]);
    }

    /**
     * 获取推送历史
     *
     * @param Request $request
     * @return Response
     */
    public function getHistory(Request $request)
    {
        $limit = $request->param('limit', 10);
        $offset = $request->param('offset', 0);

        // 获取推送历史
        $history = PushNotification::getHistory($limit, $offset);

        return json([
            'history' => $history,
        ]);
    }

    /**
     * 获取推送统计
     *
     * @param Request $request
     * @return Response
     */
    public function getStatistics(Request $request)
    {
        $startDate = $request->param('start_date');
        $endDate = $request->param('end_date');

        // 获取推送统计
        $statistics = PushNotification::getStatistics($startDate, $endDate);

        return json([
            'statistics' => $statistics,
        ]);
    }

    /**
     * 设置推送服务提供商
     *
     * @param Request $request
     * @return Response
     */
    public function setProvider(Request $request)
    {
        $provider = $request->param('provider');

        // 设置推送服务提供商
        PushNotification::setProvider($provider);

        return json([
            'success' => true,
            'provider' => $provider,
        ]);
    }

    /**
     * 获取推送服务提供商
     *
     * @return Response
     */
    public function getProvider()
    {
        // 获取推送服务提供商
        $provider = PushNotification::getProvider();

        return json([
            'provider' => $provider,
        ]);
    }

    /**
     * 设置推送服务配置
     *
     * @param Request $request
     * @return Response
     */
    public function setConfig(Request $request)
    {
        $config = $request->param('config', []);

        // 设置推送服务配置
        PushNotification::setConfig($config);

        return json([
            'success' => true,
        ]);
    }

    /**
     * 获取推送服务配置
     *
     * @return Response
     */
    public function getConfig()
    {
        // 获取推送服务配置
        $config = PushNotification::getConfig();

        return json([
            'config' => $config,
        ]);
    }
}
