<?php

namespace Native\ThinkPHP\Http\Controllers;

use think\Request;
use think\Response;
use Native\ThinkPHP\Facades\Process;

class ProcessController
{
    /**
     * 运行命令
     *
     * @param Request $request
     * @return Response
     */
    public function run(Request $request)
    {
        $command = $request->param('command');
        $options = $request->param('options', []);

        // 运行命令
        $processId = Process::run($command, $options);

        return json([
            'success' => $processId > 0,
            'process_id' => $processId,
        ]);
    }

    /**
     * 运行 PHP 脚本
     *
     * @param Request $request
     * @return Response
     */
    public function runPhp(Request $request)
    {
        $script = $request->param('script');
        $args = $request->param('args', []);
        $options = $request->param('options', []);

        // 运行 PHP 脚本
        $processId = Process::runPhp($script, $args, $options);

        return json([
            'success' => $processId > 0,
            'process_id' => $processId,
        ]);
    }

    /**
     * 运行 ThinkPHP 命令
     *
     * @param Request $request
     * @return Response
     */
    public function runThink(Request $request)
    {
        $command = $request->param('command');
        $args = $request->param('args', []);
        $options = $request->param('options', []);

        // 运行 ThinkPHP 命令
        $processId = Process::runThink($command, $args, $options);

        return json([
            'success' => $processId > 0,
            'process_id' => $processId,
        ]);
    }

    /**
     * 获取进程信息
     *
     * @param Request $request
     * @return Response
     */
    public function get(Request $request)
    {
        $processId = $request->param('process_id');

        // 获取进程信息
        $process = Process::get($processId);

        return json([
            'success' => $process !== null,
            'process' => $process,
        ]);
    }

    /**
     * 获取所有进程
     *
     * @return Response
     */
    public function all()
    {
        // 获取所有进程
        $processes = Process::all();

        return json([
            'processes' => $processes,
        ]);
    }

    /**
     * 获取进程输出
     *
     * @param Request $request
     * @return Response
     */
    public function getOutput(Request $request)
    {
        $processId = $request->param('process_id');

        // 获取进程输出
        $output = Process::getOutput($processId);

        return json([
            'output' => $output,
        ]);
    }

    /**
     * 获取进程错误
     *
     * @param Request $request
     * @return Response
     */
    public function getError(Request $request)
    {
        $processId = $request->param('process_id');

        // 获取进程错误
        $error = Process::getError($processId);

        return json([
            'error' => $error,
        ]);
    }

    /**
     * 获取进程退出码
     *
     * @param Request $request
     * @return Response
     */
    public function getExitCode(Request $request)
    {
        $processId = $request->param('process_id');

        // 获取进程退出码
        $exitCode = Process::getExitCode($processId);

        return json([
            'exit_code' => $exitCode,
        ]);
    }

    /**
     * 检查进程是否正在运行
     *
     * @param Request $request
     * @return Response
     */
    public function isRunning(Request $request)
    {
        $processId = $request->param('process_id');

        // 检查进程是否正在运行
        $isRunning = Process::isRunning($processId);

        return json([
            'is_running' => $isRunning,
        ]);
    }

    /**
     * 向进程发送输入
     *
     * @param Request $request
     * @return Response
     */
    public function write(Request $request)
    {
        $processId = $request->param('process_id');
        $input = $request->param('input');

        // 向进程发送输入
        $success = Process::write($processId, $input);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 向进程发送信号
     *
     * @param Request $request
     * @return Response
     */
    public function signal(Request $request)
    {
        $processId = $request->param('process_id');
        $signal = $request->param('signal');

        // 向进程发送信号
        $success = Process::signal($processId, $signal);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 终止进程
     *
     * @param Request $request
     * @return Response
     */
    public function kill(Request $request)
    {
        $processId = $request->param('process_id');
        $signal = $request->param('signal', 'SIGTERM');

        // 终止进程
        $success = Process::kill($processId, $signal);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 等待进程结束
     *
     * @param Request $request
     * @return Response
     */
    public function wait(Request $request)
    {
        $processId = $request->param('process_id');
        $timeout = $request->param('timeout', 0);

        // 等待进程结束
        $success = Process::wait($processId, $timeout);

        return json([
            'success' => $success,
        ]);
    }

    /**
     * 设置进程事件回调
     *
     * @param Request $request
     * @return Response
     */
    public function on(Request $request)
    {
        $processId = $request->param('process_id');
        $event = $request->param('event');
        $id = $request->param('id');

        // 设置进程事件回调
        $success = Process::on($processId, $event, function($eventData) use ($id) {
            // 触发事件
            event('native.process.' . $id, $eventData);
        });

        return json([
            'success' => $success,
            'id' => $id,
        ]);
    }

    /**
     * 清理已结束的进程
     *
     * @return Response
     */
    public function cleanup()
    {
        // 清理已结束的进程
        $count = Process::cleanup();

        return json([
            'count' => $count,
        ]);
    }

    /**
     * 获取进程信息
     *
     * @param Request $request
     * @return Response
     */
    public function getInfo(Request $request)
    {
        $processId = $request->param('process_id');

        // 获取进程信息
        $info = Process::getInfo($processId);

        return json([
            'success' => $info !== null,
            'info' => $info,
        ]);
    }

    /**
     * 获取所有进程
     *
     * @return Response
     */
    public function getProcesses()
    {
        // 获取所有进程
        $processes = Process::getProcesses();

        return json([
            'processes' => $processes,
        ]);
    }
}
