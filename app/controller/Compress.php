<?php

namespace app\controller;

use app\BaseController;
use app\service\CompressService;
use app\service\LogService;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\Notification;
use think\facade\View;
use Exception;

class Compress extends BaseController
{
    /**
     * 压缩服务
     *
     * @var \app\service\CompressService
     */
    protected $compressService;

    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;

    /**
     * 构造函数
     *
     * @param CompressService $compressService
     * @param LogService $logService
     */
    public function __construct(CompressService $compressService, LogService $logService)
    {
        $this->compressService = $compressService;
        $this->logService = $logService;
    }

    /**
     * 压缩文件/文件夹页面
     *
     * @return \think\Response
     */
    public function index()
    {
        $sources = input('sources', []);
        $currentPath = input('current_path', '');

        View::assign([
            'sources' => $sources,
            'currentPath' => $currentPath,
            'formats' => [
                'zip' => 'ZIP 格式 (.zip)',
                'tar' => 'TAR 格式 (.tar)',
                'gz' => 'GZIP 格式 (.gz)',
                'bz2' => 'BZIP2 格式 (.bz2)',
            ],
        ]);

        return view('compress/index');
    }

    /**
     * 执行压缩操作
     *
     * @return \think\Response
     */
    public function compress()
    {
        $sources = input('sources', []);
        $destination = input('destination', '');
        $format = input('format', 'zip');
        $password = input('password', '');
        $currentPath = input('current_path', '');

        // 验证输入
        if (empty($sources)) {
            return json(['success' => false, 'message' => '请选择要压缩的文件或文件夹']);
        }

        if (empty($destination)) {
            return json(['success' => false, 'message' => '请指定目标压缩文件路径']);
        }

        // 确保目标文件有正确的扩展名
        $extension = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        if ($extension !== $format) {
            $destination .= '.' . $format;
        }

        try {
            $this->logService->info('开始压缩文件', [
                'sources' => $sources,
                'destination' => $destination,
                'format' => $format,
                'has_password' => !empty($password)
            ]);

            // 执行压缩
            $result = $this->compressService->compress($sources, $destination, $format, $password);

            if ($result) {
                $this->logService->info('压缩文件成功', [
                    'destination' => $destination,
                    'format' => $format
                ]);

                Notification::send('压缩完成', '文件已成功压缩到: ' . $destination);
                
                // 如果有当前路径，返回到该路径
                if (!empty($currentPath)) {
                    return redirect('/file/index?path=' . urlencode($currentPath));
                } else {
                    return redirect('/file/index?path=' . urlencode(dirname($destination)));
                }
            } else {
                $this->logService->error('压缩文件失败', [
                    'sources' => $sources,
                    'destination' => $destination,
                    'format' => $format
                ]);

                return json(['success' => false, 'message' => '压缩操作失败']);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('压缩文件异常', [
                'sources' => $sources,
                'destination' => $destination,
                'format' => $format,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::send('压缩失败', $errorMessage);
            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 查看压缩文件内容
     *
     * @return \think\Response
     */
    public function view()
    {
        $path = input('path', '');
        $password = input('password', '');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '压缩文件不存在']);
        }

        try {
            $this->logService->info('查看压缩文件内容', [
                'path' => $path,
                'has_password' => !empty($password)
            ]);

            // 检测压缩文件类型
            $archiveType = $this->compressService->detectArchiveType($path);
            if (!$archiveType) {
                return json(['success' => false, 'message' => '不支持的压缩文件格式']);
            }

            // 获取压缩文件内容列表
            $contents = $this->compressService->listContents($path, $password);

            View::assign([
                'path' => $path,
                'archiveType' => $archiveType,
                'contents' => $contents,
                'hasPassword' => !empty($password),
                'filename' => basename($path),
            ]);

            return view('compress/view');
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('查看压缩文件内容失败', [
                'path' => $path,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            // 如果是密码错误，显示密码输入页面
            if (strpos($errorMessage, 'password') !== false) {
                View::assign([
                    'path' => $path,
                    'error' => $errorMessage,
                    'needPassword' => true,
                    'filename' => basename($path),
                ]);
                return view('compress/password');
            }

            Notification::send('查看压缩文件失败', $errorMessage);
            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 解压文件
     *
     * @return \think\Response
     */
    public function extract()
    {
        $path = input('path', '');
        $destination = input('destination', '');
        $password = input('password', '');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '压缩文件不存在']);
        }

        // 如果没有指定目标目录，使用默认目录（压缩文件所在目录）
        if (empty($destination)) {
            $destination = dirname($path) . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME);
        }

        try {
            $this->logService->info('开始解压文件', [
                'path' => $path,
                'destination' => $destination,
                'has_password' => !empty($password)
            ]);

            // 执行解压
            $result = $this->compressService->extract($path, $destination, $password);

            if ($result) {
                $this->logService->info('解压文件成功', [
                    'path' => $path,
                    'destination' => $destination
                ]);

                Notification::send('解压完成', '文件已成功解压到: ' . $destination);
                return redirect('/file/index?path=' . urlencode($destination));
            } else {
                $this->logService->error('解压文件失败', [
                    'path' => $path,
                    'destination' => $destination
                ]);

                return json(['success' => false, 'message' => '解压操作失败']);
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('解压文件异常', [
                'path' => $path,
                'destination' => $destination,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            // 如果是密码错误，显示密码输入页面
            if (strpos($errorMessage, 'password') !== false) {
                View::assign([
                    'path' => $path,
                    'error' => $errorMessage,
                    'needPassword' => true,
                    'filename' => basename($path),
                    'destination' => $destination,
                    'extractMode' => true,
                ]);
                return view('compress/password');
            }

            Notification::send('解压失败', $errorMessage);
            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 选择解压目标目录
     *
     * @return \think\Response
     */
    public function selectExtractDestination()
    {
        $path = input('path', '');
        $password = input('password', '');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '压缩文件不存在']);
        }

        try {
            $folder = Dialog::selectFolder([
                'title' => '选择解压目标文件夹',
                'defaultPath' => dirname($path),
            ]);

            if ($folder) {
                // 创建与压缩文件同名的子文件夹
                $subFolder = $folder . DIRECTORY_SEPARATOR . pathinfo($path, PATHINFO_FILENAME);
                
                return redirect('/compress/extract?path=' . urlencode($path) . 
                                '&destination=' . urlencode($subFolder) . 
                                '&password=' . urlencode($password));
            } else {
                return redirect('/compress/view?path=' . urlencode($path) . 
                                '&password=' . urlencode($password));
            }
        } catch (Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/compress/view?path=' . urlencode($path) . 
                            '&password=' . urlencode($password));
        }
    }

    /**
     * 验证密码
     *
     * @return \think\Response
     */
    public function validatePassword()
    {
        $path = input('path', '');
        $password = input('password', '');
        $action = input('action', 'view'); // view 或 extract
        $destination = input('destination', '');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '压缩文件不存在']);
        }

        if (empty($password)) {
            return json(['success' => false, 'message' => '请输入密码']);
        }

        if ($action === 'extract') {
            return redirect('/compress/extract?path=' . urlencode($path) . 
                            '&destination=' . urlencode($destination) . 
                            '&password=' . urlencode($password));
        } else {
            return redirect('/compress/view?path=' . urlencode($path) . 
                            '&password=' . urlencode($password));
        }
    }
}
