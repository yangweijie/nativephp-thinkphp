<?php

namespace app\controller;

use app\BaseController;
use app\service\FileService;
use app\service\LogService;
use app\service\FavoriteService;
use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Dialog;
use Native\ThinkPHP\Facades\System;
use Native\ThinkPHP\Facades\Notification;
use Native\ThinkPHP\Facades\Shell;
use think\facade\View;

class File extends BaseController
{
    /**
     * 文件服务
     *
     * @var \app\service\FileService
     */
    protected $fileService;

    /**
     * 日志服务
     *
     * @var \app\service\LogService
     */
    protected $logService;

    /**
     * 收藏夹服务
     *
     * @var \app\service\FavoriteService
     */
    protected $favoriteService;

    /**
     * 构造函数
     *
     * @param \app\service\FileService $fileService
     * @param \app\service\LogService $logService
     * @param \app\service\FavoriteService $favoriteService
     */
    public function __construct(FileService $fileService, LogService $logService, FavoriteService $favoriteService)
    {
        $this->fileService = $fileService;
        $this->logService = $logService;
        $this->favoriteService = $favoriteService;
    }

    /**
     * 文件列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $path = input('path', System::getHomePath());
        $filterType = input('filter_type', '');
        $filterValue = input('filter_value', '');
        $sortBy = input('sort_by', 'name');
        $sortOrder = input('sort_order', 'asc');

        try {
            $this->logService->info('访问目录', [
                'path' => $path,
                'filter_type' => $filterType,
                'filter_value' => $filterValue,
                'sort_by' => $sortBy,
                'sort_order' => $sortOrder
            ]);

            $files = $this->fileService->getFiles($path);

            // 应用过滤器
            if (!empty($filterType) && !empty($filterValue)) {
                $files = $this->applyFilter($files, $filterType, $filterValue);
            }

            // 应用排序
            $files = $this->applySort($files, $sortBy, $sortOrder);

            // 检查当前路径是否已收藏
            $isFavorite = $this->favoriteService->isFavorite($path);

            View::assign([
                'path' => $path,
                'files' => $files,
                'parentPath' => dirname($path),
                'hasErrors' => false,
                'errorMessage' => '',
                'filterType' => $filterType,
                'filterValue' => $filterValue,
                'sortBy' => $sortBy,
                'sortOrder' => $sortOrder,
                'isFavorite' => $isFavorite,
            ]);

            return view('file/index');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('访问目录失败', [
                'path' => $path,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::send('错误', $errorMessage);

            // 尝试恢复到上级目录
            $parentPath = dirname($path);
            if ($parentPath !== $path && is_dir($parentPath)) {
                return redirect('/file/index?path=' . urlencode($parentPath));
            }

            // 如果无法恢复到上级目录，则返回到主目录
            return redirect('/file/index?path=' . urlencode(System::getHomePath()));
        }
    }

    /**
     * 应用过滤器
     *
     * @param array $files 文件列表
     * @param string $filterType 过滤器类型
     * @param string $filterValue 过滤器值
     * @return array
     */
    protected function applyFilter($files, $filterType, $filterValue)
    {
        $result = [];

        foreach ($files as $file) {
            $match = false;

            switch ($filterType) {
                case 'name':
                    $match = stripos($file['name'], $filterValue) !== false;
                    break;

                case 'type':
                    if ($file['isDir'] && $filterValue === 'directory') {
                        $match = true;
                    } elseif (!$file['isDir'] && $filterValue === $file['type']) {
                        $match = true;
                    } elseif (!$file['isDir'] && $filterValue === 'extension' && !empty($file['extension'])) {
                        $match = strtolower($file['extension']) === strtolower($filterValue);
                    }
                    break;

                case 'size':
                    if ($filterValue === 'empty' && $file['size'] === 0) {
                        $match = true;
                    } elseif ($filterValue === 'small' && $file['size'] < 1024 * 100) { // < 100KB
                        $match = true;
                    } elseif ($filterValue === 'medium' && $file['size'] >= 1024 * 100 && $file['size'] < 1024 * 1024) { // 100KB - 1MB
                        $match = true;
                    } elseif ($filterValue === 'large' && $file['size'] >= 1024 * 1024) { // > 1MB
                        $match = true;
                    }
                    break;

                case 'date':
                    $now = time();
                    $fileTime = $file['lastModified'];

                    if ($filterValue === 'today' && date('Y-m-d', $fileTime) === date('Y-m-d', $now)) {
                        $match = true;
                    } elseif ($filterValue === 'yesterday' && date('Y-m-d', $fileTime) === date('Y-m-d', strtotime('-1 day'))) {
                        $match = true;
                    } elseif ($filterValue === 'week' && $fileTime >= strtotime('-1 week')) {
                        $match = true;
                    } elseif ($filterValue === 'month' && $fileTime >= strtotime('-1 month')) {
                        $match = true;
                    } elseif ($filterValue === 'year' && $fileTime >= strtotime('-1 year')) {
                        $match = true;
                    }
                    break;

                default:
                    $match = true;
                    break;
            }

            if ($match) {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * 应用排序
     *
     * @param array $files 文件列表
     * @param string $sortBy 排序字段
     * @param string $sortOrder 排序方向
     * @return array
     */
    protected function applySort($files, $sortBy, $sortOrder)
    {
        $sortFields = ['name', 'size', 'lastModified', 'type'];

        if (!in_array($sortBy, $sortFields)) {
            $sortBy = 'name';
        }

        $sortOrder = strtolower($sortOrder) === 'desc' ? SORT_DESC : SORT_ASC;

        // 首先按类型排序（目录在前，文件在后）
        $isDir = array_column($files, 'isDir');
        $sortField = array_column($files, $sortBy);

        // 如果按名称排序，使用不区分大小写的排序
        if ($sortBy === 'name') {
            $sortField = array_map('strtolower', $sortField);
        }

        array_multisort($isDir, SORT_DESC, $sortField, $sortOrder, $files);

        return $files;
    }

    /**
     * 查看文件
     *
     * @return \think\Response
     */
    public function view()
    {
        $path = input('path');

        if (empty($path)) {
            Notification::send('错误', '文件路径不能为空');
            $this->logService->warning('查看文件失败', ['error' => '文件路径不能为空']);
            return redirect('/file/index');
        }

        if (!FileSystem::exists($path)) {
            Notification::send('错误', '文件不存在');
            $this->logService->warning('查看文件失败', ['path' => $path, 'error' => '文件不存在']);

            // 尝试恢复到文件所在目录
            $parentPath = dirname($path);
            if (is_dir($parentPath)) {
                return redirect('/file/index?path=' . urlencode($parentPath));
            }

            return redirect('/file/index');
        }

        try {
            $this->logService->info('查看文件', ['path' => $path]);
            $content = FileSystem::read($path);
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $fileSize = FileSystem::size($path);

            // 大文件处理
            $maxSize = 1024 * 1024; // 1MB
            $isTruncated = false;

            if ($fileSize > $maxSize) {
                $content = substr($content, 0, $maxSize);
                $isTruncated = true;
            }

            $viewData = [
                'path' => $path,
                'content' => $content,
                'extension' => $extension,
                'filename' => basename($path),
                'fileSize' => $this->fileService->formatSize($fileSize),
                'isTruncated' => $isTruncated,
                'maxSize' => $this->fileService->formatSize($maxSize),
            ];

            // 获取图片信息
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']) && $fileSize <= $maxSize) {
                try {
                    $imageInfo = getimagesize($path);
                    if ($imageInfo) {
                        $viewData['imageInfo'] = [
                            'width' => $imageInfo[0],
                            'height' => $imageInfo[1],
                            'type' => $imageInfo[2],
                            'mime' => $imageInfo['mime'],
                        ];
                    }
                } catch (\Exception $e) {
                    // 忽略图片信息获取错误
                    $this->logService->warning('获取图片信息失败', [
                        'path' => $path,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            View::assign($viewData);

            return view('file/view');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('查看文件失败', [
                'path' => $path,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::send('错误', $errorMessage);

            // 尝试恢复到文件所在目录
            $parentPath = dirname($path);
            if (is_dir($parentPath)) {
                return redirect('/file/index?path=' . urlencode($parentPath));
            }

            return redirect('/file/index');
        }
    }

    /**
     * 编辑文件
     *
     * @return \think\Response
     */
    public function edit()
    {
        $path = input('path');

        if (empty($path) || !FileSystem::exists($path)) {
            Notification::send('错误', '文件不存在');
            return redirect('/file/index');
        }

        try {
            $content = FileSystem::read($path);

            View::assign([
                'path' => $path,
                'content' => $content,
                'filename' => basename($path),
            ]);

            return view('file/edit');
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }

    /**
     * 保存文件
     *
     * @return \think\Response
     */
    public function save()
    {
        $path = input('path');
        $content = input('content');

        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }

        try {
            $result = FileSystem::write($path, $content);

            if ($result) {
                Notification::send('保存成功', '文件已成功保存');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '保存失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 创建文件或目录
     *
     * @return \think\Response
     */
    public function create()
    {
        $path = input('path');
        $name = input('name');
        $type = input('type', 'file'); // file 或 directory

        if (empty($path) || empty($name)) {
            return json(['success' => false, 'message' => '路径和名称不能为空']);
        }

        try {
            $fullPath = rtrim($path, '/\\') . DIRECTORY_SEPARATOR . $name;

            if (FileSystem::exists($fullPath)) {
                return json(['success' => false, 'message' => '文件或目录已存在']);
            }

            if ($type === 'directory') {
                $result = FileSystem::makeDirectory($fullPath);
            } else {
                $result = FileSystem::write($fullPath, '');
            }

            if ($result) {
                Notification::send('创建成功', ($type === 'directory' ? '目录' : '文件') . '已创建');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '创建失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 删除文件或目录
     *
     * @return \think\Response
     */
    public function delete()
    {
        $path = input('path');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '文件或目录不存在']);
        }

        try {
            if (is_dir($path)) {
                $result = FileSystem::deleteDirectory($path);
            } else {
                $result = FileSystem::delete($path);
            }

            if ($result) {
                Notification::send('删除成功', '文件或目录已删除');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '删除失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 重命名文件或目录
     *
     * @return \think\Response
     */
    public function rename()
    {
        $path = input('path');
        $newName = input('newName');

        if (empty($path) || empty($newName) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '参数错误或文件不存在']);
        }

        try {
            $directory = dirname($path);
            $newPath = $directory . DIRECTORY_SEPARATOR . $newName;

            if (FileSystem::exists($newPath)) {
                return json(['success' => false, 'message' => '目标文件或目录已存在']);
            }

            $result = FileSystem::move($path, $newPath);

            if ($result) {
                Notification::send('重命名成功', '文件或目录已重命名');
                return json(['success' => true, 'newPath' => $newPath]);
            } else {
                return json(['success' => false, 'message' => '重命名失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 复制文件或目录
     *
     * @return \think\Response
     */
    public function copy()
    {
        $source = input('source');
        $destination = input('destination');

        if (empty($source) || empty($destination) || !FileSystem::exists($source)) {
            return json(['success' => false, 'message' => '参数错误或源文件不存在']);
        }

        try {
            if (is_dir($source)) {
                $result = $this->fileService->copyDirectory($source, $destination);
            } else {
                $result = FileSystem::copy($source, $destination);
            }

            if ($result) {
                Notification::send('复制成功', '文件或目录已复制');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '复制失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 移动文件或目录
     *
     * @return \think\Response
     */
    public function move()
    {
        $source = input('source');
        $destination = input('destination');

        if (empty($source) || empty($destination) || !FileSystem::exists($source)) {
            return json(['success' => false, 'message' => '参数错误或源文件不存在']);
        }

        try {
            $result = FileSystem::move($source, $destination);

            if ($result) {
                Notification::send('移动成功', '文件或目录已移动');
                return json(['success' => true]);
            } else {
                return json(['success' => false, 'message' => '移动失败']);
            }
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 获取文件属性
     *
     * @return \think\Response
     */
    public function properties()
    {
        $path = input('path');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '文件或目录不存在']);
        }

        try {
            $properties = $this->fileService->getProperties($path);

            return json(['success' => true, 'properties' => $properties]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 打开文件或目录
     *
     * @return \think\Response
     */
    public function open()
    {
        $path = input('path');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '文件或目录不存在']);
        }

        try {
            Shell::openFile($path);

            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 在文件夹中显示
     *
     * @return \think\Response
     */
    public function showInFolder()
    {
        $path = input('path');

        if (empty($path) || !FileSystem::exists($path)) {
            return json(['success' => false, 'message' => '文件或目录不存在']);
        }

        try {
            Shell::showInFolder($path);

            return json(['success' => true]);
        } catch (\Exception $e) {
            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 选择文件夹
     *
     * @return \think\Response
     */
    public function selectFolder()
    {
        try {
            $folder = Dialog::selectFolder([
                'title' => '选择文件夹',
            ]);

            if ($folder) {
                return redirect('/file/index?path=' . $folder);
            } else {
                return redirect('/file/index');
            }
        } catch (\Exception $e) {
            Notification::send('错误', $e->getMessage());
            return redirect('/file/index');
        }
    }

    /**
     * 搜索文件
     *
     * @return \think\Response
     */
    public function search()
    {
        $path = input('path', System::getHomePath());
        $keyword = input('keyword', '');
        $recursive = input('recursive', false);

        if (empty($keyword)) {
            return redirect('/file/index?path=' . urlencode($path));
        }

        try {
            $this->logService->info('搜索文件', [
                'path' => $path,
                'keyword' => $keyword,
                'recursive' => $recursive
            ]);

            $files = $this->fileService->searchFiles($path, $keyword, $recursive);

            View::assign([
                'path' => $path,
                'files' => $files,
                'keyword' => $keyword,
                'recursive' => $recursive,
                'parentPath' => dirname($path),
                'isSearchResult' => true,
            ]);

            return view('file/search');
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('搜索文件失败', [
                'path' => $path,
                'keyword' => $keyword,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            Notification::send('错误', $errorMessage);
            return redirect('/file/index?path=' . urlencode($path));
        }
    }

    /**
     * 压缩文件或目录
     *
     * @return \think\Response
     */
    public function compress()
    {
        $path = input('path');
        $items = input('items', []);
        $destination = input('destination');

        if (empty($path) || empty($destination)) {
            return json(['success' => false, 'message' => '参数错误']);
        }

        try {
            // 如果没有指定要压缩的项目，则压缩整个目录
            if (empty($items)) {
                $source = $path;
            } else {
                // 将相对路径转换为绝对路径
                $source = [];
                foreach ($items as $item) {
                    $itemPath = $path . DIRECTORY_SEPARATOR . $item;
                    if (file_exists($itemPath)) {
                        $source[] = $itemPath;
                    }
                }

                if (empty($source)) {
                    return json(['success' => false, 'message' => '没有有效的文件或目录要压缩']);
                }
            }

            // 确保目标文件有 .zip 扩展名
            if (strtolower(pathinfo($destination, PATHINFO_EXTENSION)) !== 'zip') {
                $destination .= '.zip';
            }

            $this->logService->info('压缩文件', [
                'source' => $source,
                'destination' => $destination
            ]);

            $result = $this->fileService->compress($source, $destination);

            if ($result) {
                Notification::send('压缩成功', '文件已成功压缩到 ' . basename($destination));
                return json(['success' => true, 'destination' => $destination]);
            } else {
                return json(['success' => false, 'message' => '压缩失败']);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('压缩文件失败', [
                'source' => $source ?? $path,
                'destination' => $destination,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 解压缩文件
     *
     * @return \think\Response
     */
    public function extract()
    {
        $source = input('source');
        $destination = input('destination');

        if (empty($source) || empty($destination)) {
            return json(['success' => false, 'message' => '参数错误']);
        }

        try {
            $this->logService->info('解压缩文件', [
                'source' => $source,
                'destination' => $destination
            ]);

            $result = $this->fileService->extract($source, $destination);

            if ($result) {
                Notification::send('解压缩成功', '文件已成功解压缩到 ' . basename($destination));
                return json(['success' => true, 'destination' => $destination]);
            } else {
                return json(['success' => false, 'message' => '解压缩失败']);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('解压缩文件失败', [
                'source' => $source,
                'destination' => $destination,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 上传文件
     *
     * @return \think\Response
     */
    public function upload()
    {
        $path = input('path');
        $files = request()->file();

        if (empty($path) || empty($files)) {
            return json(['success' => false, 'message' => '参数错误']);
        }

        try {
            $this->logService->info('上传文件', [
                'path' => $path,
                'files' => count($files)
            ]);

            $successCount = 0;
            $failCount = 0;
            $fileList = [];

            foreach ($files as $file) {
                $originalName = $file->getOriginalName();
                $targetPath = $path . DIRECTORY_SEPARATOR . $originalName;

                // 检查文件是否已存在
                if (FileSystem::exists($targetPath)) {
                    // 自动重命名
                    $pathInfo = pathinfo($originalName);
                    $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
                    $filename = $pathInfo['filename'];
                    $counter = 1;

                    while (FileSystem::exists($path . DIRECTORY_SEPARATOR . $filename . '_' . $counter . $extension)) {
                        $counter++;
                    }

                    $targetPath = $path . DIRECTORY_SEPARATOR . $filename . '_' . $counter . $extension;
                }

                try {
                    // 移动上传的文件
                    $file->move(dirname($targetPath), basename($targetPath));
                    $successCount++;
                    $fileList[] = basename($targetPath);
                } catch (\Exception $e) {
                    $failCount++;
                    $this->logService->error('上传文件失败', [
                        'file' => $originalName,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            if ($successCount > 0) {
                $message = $successCount . ' 个文件上传成功';
                if ($failCount > 0) {
                    $message .= ', ' . $failCount . ' 个文件上传失败';
                }

                Notification::send('上传成功', $message);
                return json(['success' => true, 'message' => $message, 'files' => $fileList]);
            } else {
                return json(['success' => false, 'message' => '所有文件上传失败']);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('上传文件失败', [
                'path' => $path,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 上传文件内容（拖放上传）
     *
     * @return \think\Response
     */
    public function uploadContent()
    {
        $path = input('path');
        $filename = input('filename');
        $content = input('content');

        if (empty($path) || empty($filename) || $content === null) {
            return json(['success' => false, 'message' => '参数错误']);
        }

        try {
            $this->logService->info('拖放上传文件', [
                'path' => $path,
                'filename' => $filename
            ]);

            $targetPath = $path . DIRECTORY_SEPARATOR . $filename;

            // 检查文件是否已存在
            if (FileSystem::exists($targetPath)) {
                // 自动重命名
                $pathInfo = pathinfo($filename);
                $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
                $filenameBase = $pathInfo['filename'];
                $counter = 1;

                while (FileSystem::exists($path . DIRECTORY_SEPARATOR . $filenameBase . '_' . $counter . $extension)) {
                    $counter++;
                }

                $targetPath = $path . DIRECTORY_SEPARATOR . $filenameBase . '_' . $counter . $extension;
                $filename = $filenameBase . '_' . $counter . $extension;
            }

            // 将Base64解码为二进制数据
            $binaryContent = base64_decode(preg_replace('#^data:.*?;base64,#', '', $content));

            if ($binaryContent === false) {
                return json(['success' => false, 'message' => '无效的文件内容']);
            }

            // 写入文件
            $result = FileSystem::write($targetPath, $binaryContent);

            if ($result) {
                Notification::send('上传成功', '文件 ' . $filename . ' 上传成功');
                return json(['success' => true, 'filename' => $filename, 'path' => $targetPath]);
            } else {
                return json(['success' => false, 'message' => '文件上传失败']);
            }
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $this->logService->error('拖放上传文件失败', [
                'path' => $path,
                'filename' => $filename,
                'error' => $errorMessage,
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $errorMessage]);
        }
    }

    /**
     * 选择文件
     *
     * @return \think\Response
     */
    public function selectFile()
    {
        try {
            $file = Dialog::selectFile([
                'title' => '选择文件',
                'filters' => [
                    ['name' => '所有文件', 'extensions' => ['*']],
                    ['name' => '文本文件', 'extensions' => ['txt', 'md', 'html', 'htm', 'xml', 'json', 'csv', 'log']],
                    ['name' => '代码文件', 'extensions' => ['php', 'js', 'css', 'py', 'java', 'c', 'cpp', 'h', 'cs', 'go', 'rb']],
                    ['name' => '配置文件', 'extensions' => ['ini', 'conf', 'yml', 'yaml', 'toml']],
                ],
            ]);

            if ($file) {
                $this->logService->info('选择文件', ['path' => $file]);
                return json(['success' => true, 'path' => $file]);
            } else {
                return json(['success' => false, 'message' => '未选择文件']);
            }
        } catch (\Exception $e) {
            $this->logService->error('选择文件失败', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * 获取文件预览
     *
     * @return \think\Response
     */
    public function preview()
    {
        $path = input('path');

        if (empty($path)) {
            return json(['success' => false, 'message' => '文件路径不能为空']);
        }

        try {
            if (!FileSystem::exists($path) || is_dir($path)) {
                return json(['success' => false, 'message' => '文件不存在或不是一个有效的文件']);
            }

            // 检查文件大小
            $maxSize = 100 * 1024; // 100KB
            $fileSize = FileSystem::size($path);

            if ($fileSize > $maxSize) {
                $content = FileSystem::read($path, 0, $maxSize) . '\n\n... (文件过大，只显示前 100KB)';
            } else {
                $content = FileSystem::read($path);
            }

            // 检查是否是二进制文件
            if (!mb_detect_encoding($content, 'UTF-8', true)) {
                return json(['success' => false, 'message' => '无法预览二进制文件']);
            }

            // 转义内容以安全显示
            $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');

            return json([
                'success' => true,
                'name' => basename($path),
                'path' => $path,
                'size' => $this->fileService->formatSize($fileSize),
                'lastModified' => date('Y-m-d H:i:s', FileSystem::lastModified($path)),
                'content' => $content,
            ]);
        } catch (\Exception $e) {
            $this->logService->error('获取文件预览失败', [
                'path' => $path,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
