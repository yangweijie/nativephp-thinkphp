<?php

namespace app\controller;

use app\BaseController;
use app\service\ArchiveService;
use app\service\LogService;
use think\facade\View;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\FileSystem;

class Archive extends BaseController
{
    /**
     * 压缩服务
     *
     * @var \app\service\ArchiveService
     */
    protected $archiveService;

    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;

    /**
     * 构造函数
     *
     * @param \app\service\ArchiveService $archiveService
     * @param \app\service\LogService $logService
     */
    public function __construct(ArchiveService $archiveService, LogService $logService)
    {
        $this->archiveService = $archiveService;
        $this->logService = $logService;
    }

    /**
     * 压缩文件或目录
     *
     * @return \think\Response
     */
    public function compress()
    {
        $source = input('source');
        $format = input('format', 'zip');
        $includeDir = input('include_dir', true);

        if (empty($source)) {
            return json(['success' => false, 'message' => '源路径不能为空']);
        }

        if (!FileSystem::exists($source)) {
            return json(['success' => false, 'message' => '源文件或目录不存在']);
        }

        try {
            // 选择保存位置
            $defaultName = basename($source) . '.' . $format;
            $defaultPath = dirname($source) . '/' . $defaultName;
            
            $destination = Dialog::saveFile([
                'title' => '保存压缩文件',
                'defaultPath' => $defaultPath,
                'filters' => [
                    ['name' => '压缩文件', 'extensions' => [$format]],
                    ['name' => '所有文件', 'extensions' => ['*']],
                ],
            ]);

            if (!$destination) {
                return json(['success' => false, 'message' => '未选择保存位置']);
            }

            $this->logService->info('压缩文件', [
                'source' => $source,
                'destination' => $destination,
                'format' => $format,
                'includeDir' => $includeDir
            ]);

            // 执行压缩
            $result = $this->archiveService->compress($source, $destination, $format, $includeDir);

            if ($result) {
                Notification::send('压缩成功', '文件已成功压缩到: ' . $destination);
                return json(['success' => true, 'path' => $destination]);
            } else {
                return json(['success' => false, 'message' => '压缩失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('压缩文件失败', [
                'source' => $source,
                'format' => $format,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 解压文件
     *
     * @return \think\Response
     */
    public function extract()
    {
        $source = input('source');

        if (empty($source)) {
            return json(['success' => false, 'message' => '源文件不能为空']);
        }

        if (!FileSystem::exists($source) || !$this->archiveService->isArchive($source)) {
            return json(['success' => false, 'message' => '源文件不存在或不是有效的压缩文件']);
        }

        try {
            // 选择解压位置
            $defaultPath = dirname($source);
            
            $destination = Dialog::selectFolder([
                'title' => '选择解压位置',
                'defaultPath' => $defaultPath,
            ]);

            if (!$destination) {
                return json(['success' => false, 'message' => '未选择解压位置']);
            }

            $this->logService->info('解压文件', [
                'source' => $source,
                'destination' => $destination
            ]);

            // 执行解压
            $result = $this->archiveService->extract($source, $destination);

            if ($result) {
                Notification::send('解压成功', '文件已成功解压到: ' . $destination);
                return json(['success' => true, 'path' => $destination]);
            } else {
                return json(['success' => false, 'message' => '解压失败']);
            }
        } catch (\Exception $e) {
            $this->logService->error('解压文件失败', [
                'source' => $source,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 查看压缩文件内容
     *
     * @return \think\Response
     */
    public function view()
    {
        $path = input('path');

        if (empty($path)) {
            return redirect('/file/index');
        }

        if (!FileSystem::exists($path) || !$this->archiveService->isArchive($path)) {
            Notification::send('错误', '文件不存在或不是有效的压缩文件');
            return redirect('/file/index');
        }

        try {
            $this->logService->info('查看压缩文件', [
                'path' => $path
            ]);

            $contents = $this->archiveService->listContents($path);
            $type = $this->archiveService->getArchiveType($path);

            View::assign([
                'path' => $path,
                'contents' => $contents,
                'type' => $type,
                'filename' => basename($path),
            ]);

            return view('archive/view');
        } catch (\Exception $e) {
            $this->logService->error('查看压缩文件失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }

    /**
     * 获取支持的压缩格式
     *
     * @return \think\Response
     */
    public function formats()
    {
        try {
            $formats = $this->archiveService->getSupportedFormats();
            return json(['success' => true, 'formats' => $formats]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 检查文件是否为压缩文件
     *
     * @return \think\Response
     */
    public function check()
    {
        $path = input('path');

        if (empty($path)) {
            return json(['success' => false, 'message' => '路径不能为空']);
        }

        try {
            $isArchive = $this->archiveService->isArchive($path);
            $type = $isArchive ? $this->archiveService->getArchiveType($path) : null;

            return json([
                'success' => true,
                'isArchive' => $isArchive,
                'type' => $type
            ]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
