<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;

class StatisticsService
{
    /**
     * 文件服务
     *
     * @var \app\service\FileService
     */
    protected $fileService;
    
    /**
     * 构造函数
     *
     * @param \app\service\FileService $fileService
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    
    /**
     * 获取目录统计信息
     *
     * @param string $path 目录路径
     * @param bool $recursive 是否递归统计子目录
     * @param int $maxDepth 最大递归深度
     * @return array 统计信息
     */
    public function getDirectoryStatistics($path, $recursive = true, $maxDepth = 10)
    {
        if (!is_dir($path)) {
            throw new \Exception('路径不是一个有效的目录');
        }
        
        $stats = [
            'totalSize' => 0,
            'totalFiles' => 0,
            'totalDirectories' => 0,
            'fileTypes' => [],
            'largestFiles' => [],
            'newestFiles' => [],
            'oldestFiles' => [],
            'emptyDirectories' => [],
            'filesByExtension' => [],
            'sizeByExtension' => [],
            'filesByDate' => [
                'today' => 0,
                'yesterday' => 0,
                'thisWeek' => 0,
                'thisMonth' => 0,
                'thisYear' => 0,
                'older' => 0,
            ],
        ];
        
        // 扫描目录
        $this->scanDirectory($path, $stats, $recursive, $maxDepth);
        
        // 排序最大文件
        usort($stats['largestFiles'], function($a, $b) {
            return $b['size'] - $a['size'];
        });
        
        // 只保留前10个最大文件
        $stats['largestFiles'] = array_slice($stats['largestFiles'], 0, 10);
        
        // 排序最新文件
        usort($stats['newestFiles'], function($a, $b) {
            return $b['lastModified'] - $a['lastModified'];
        });
        
        // 只保留前10个最新文件
        $stats['newestFiles'] = array_slice($stats['newestFiles'], 0, 10);
        
        // 排序最旧文件
        usort($stats['oldestFiles'], function($a, $b) {
            return $a['lastModified'] - $b['lastModified'];
        });
        
        // 只保留前10个最旧文件
        $stats['oldestFiles'] = array_slice($stats['oldestFiles'], 0, 10);
        
        // 计算文件类型百分比
        $stats['fileTypePercentages'] = [];
        if ($stats['totalFiles'] > 0) {
            foreach ($stats['fileTypes'] as $type => $count) {
                $stats['fileTypePercentages'][$type] = round(($count / $stats['totalFiles']) * 100, 2);
            }
        }
        
        // 计算扩展名百分比
        $stats['extensionPercentages'] = [];
        if ($stats['totalFiles'] > 0) {
            foreach ($stats['filesByExtension'] as $ext => $count) {
                $stats['extensionPercentages'][$ext] = round(($count / $stats['totalFiles']) * 100, 2);
            }
        }
        
        // 计算扩展名大小百分比
        $stats['extensionSizePercentages'] = [];
        if ($stats['totalSize'] > 0) {
            foreach ($stats['sizeByExtension'] as $ext => $size) {
                $stats['extensionSizePercentages'][$ext] = round(($size / $stats['totalSize']) * 100, 2);
            }
        }
        
        // 格式化总大小
        $stats['totalSizeFormatted'] = $this->fileService->formatSize($stats['totalSize']);
        
        return $stats;
    }
    
    /**
     * 扫描目录
     *
     * @param string $path 目录路径
     * @param array &$stats 统计信息
     * @param bool $recursive 是否递归
     * @param int $maxDepth 最大递归深度
     * @param int $currentDepth 当前递归深度
     * @return void
     */
    protected function scanDirectory($path, &$stats, $recursive = true, $maxDepth = 10, $currentDepth = 0)
    {
        if ($currentDepth > $maxDepth) {
            return;
        }
        
        $items = scandir($path);
        $hasFiles = false;
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            
            $fullPath = $path . DIRECTORY_SEPARATOR . $item;
            
            if (is_dir($fullPath)) {
                $stats['totalDirectories']++;
                
                if ($recursive) {
                    $dirHasFiles = $this->scanDirectory($fullPath, $stats, $recursive, $maxDepth, $currentDepth + 1);
                    
                    if (!$dirHasFiles) {
                        $stats['emptyDirectories'][] = [
                            'path' => $fullPath,
                            'name' => $item,
                        ];
                    }
                }
            } else {
                $hasFiles = true;
                $stats['totalFiles']++;
                
                // 获取文件大小
                $fileSize = FileSystem::size($fullPath);
                $stats['totalSize'] += $fileSize;
                
                // 获取文件扩展名
                $extension = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                $extension = $extension ?: 'unknown';
                
                // 统计文件扩展名
                if (!isset($stats['filesByExtension'][$extension])) {
                    $stats['filesByExtension'][$extension] = 0;
                }
                $stats['filesByExtension'][$extension]++;
                
                // 统计扩展名大小
                if (!isset($stats['sizeByExtension'][$extension])) {
                    $stats['sizeByExtension'][$extension] = 0;
                }
                $stats['sizeByExtension'][$extension] += $fileSize;
                
                // 确定文件类型
                $fileType = $this->getFileType($extension);
                
                // 统计文件类型
                if (!isset($stats['fileTypes'][$fileType])) {
                    $stats['fileTypes'][$fileType] = 0;
                }
                $stats['fileTypes'][$fileType]++;
                
                // 获取文件修改时间
                $lastModified = FileSystem::lastModified($fullPath);
                
                // 添加到最大文件列表
                $stats['largestFiles'][] = [
                    'path' => $fullPath,
                    'name' => $item,
                    'size' => $fileSize,
                    'sizeFormatted' => $this->fileService->formatSize($fileSize),
                    'extension' => $extension,
                    'type' => $fileType,
                    'lastModified' => $lastModified,
                    'lastModifiedFormatted' => date('Y-m-d H:i:s', $lastModified),
                ];
                
                // 添加到最新文件列表
                $stats['newestFiles'][] = [
                    'path' => $fullPath,
                    'name' => $item,
                    'size' => $fileSize,
                    'sizeFormatted' => $this->fileService->formatSize($fileSize),
                    'extension' => $extension,
                    'type' => $fileType,
                    'lastModified' => $lastModified,
                    'lastModifiedFormatted' => date('Y-m-d H:i:s', $lastModified),
                ];
                
                // 添加到最旧文件列表
                $stats['oldestFiles'][] = [
                    'path' => $fullPath,
                    'name' => $item,
                    'size' => $fileSize,
                    'sizeFormatted' => $this->fileService->formatSize($fileSize),
                    'extension' => $extension,
                    'type' => $fileType,
                    'lastModified' => $lastModified,
                    'lastModifiedFormatted' => date('Y-m-d H:i:s', $lastModified),
                ];
                
                // 按日期统计文件
                $today = strtotime('today');
                $yesterday = strtotime('yesterday');
                $thisWeekStart = strtotime('this week');
                $thisMonthStart = strtotime('first day of this month');
                $thisYearStart = strtotime('first day of January this year');
                
                if ($lastModified >= $today) {
                    $stats['filesByDate']['today']++;
                } elseif ($lastModified >= $yesterday) {
                    $stats['filesByDate']['yesterday']++;
                } elseif ($lastModified >= $thisWeekStart) {
                    $stats['filesByDate']['thisWeek']++;
                } elseif ($lastModified >= $thisMonthStart) {
                    $stats['filesByDate']['thisMonth']++;
                } elseif ($lastModified >= $thisYearStart) {
                    $stats['filesByDate']['thisYear']++;
                } else {
                    $stats['filesByDate']['older']++;
                }
            }
        }
        
        return $hasFiles;
    }
    
    /**
     * 获取文件类型
     *
     * @param string $extension 文件扩展名
     * @return string 文件类型
     */
    protected function getFileType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'svg', 'webp', 'ico', 'tif', 'tiff'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp'];
        $archiveExtensions = ['zip', 'rar', '7z', 'tar', 'gz', 'bz2', 'xz', 'iso'];
        $audioExtensions = ['mp3', 'wav', 'ogg', 'flac', 'aac', 'm4a', 'wma'];
        $videoExtensions = ['mp4', 'avi', 'mov', 'wmv', 'flv', 'mkv', 'webm', 'mpg', 'mpeg', '3gp'];
        $codeExtensions = ['php', 'js', 'css', 'html', 'htm', 'xml', 'json', 'py', 'java', 'c', 'cpp', 'cs', 'go', 'rb', 'pl', 'sh', 'bat', 'ps1'];
        $dataExtensions = ['csv', 'tsv', 'sql', 'db', 'sqlite', 'mdb', 'accdb'];
        
        if (in_array($extension, $imageExtensions)) {
            return 'image';
        } elseif (in_array($extension, $documentExtensions)) {
            return 'document';
        } elseif (in_array($extension, $archiveExtensions)) {
            return 'archive';
        } elseif (in_array($extension, $audioExtensions)) {
            return 'audio';
        } elseif (in_array($extension, $videoExtensions)) {
            return 'video';
        } elseif (in_array($extension, $codeExtensions)) {
            return 'code';
        } elseif (in_array($extension, $dataExtensions)) {
            return 'data';
        } else {
            return 'other';
        }
    }
    
    /**
     * 获取文件类型图标
     *
     * @param string $type 文件类型
     * @return string 图标类名
     */
    public function getFileTypeIcon($type)
    {
        $icons = [
            'image' => 'image-icon',
            'document' => 'document-icon',
            'archive' => 'archive-icon',
            'audio' => 'audio-icon',
            'video' => 'video-icon',
            'code' => 'code-icon',
            'data' => 'data-icon',
            'other' => 'file-icon',
        ];
        
        return $icons[$type] ?? 'file-icon';
    }
    
    /**
     * 获取文件类型颜色
     *
     * @param string $type 文件类型
     * @return string 颜色代码
     */
    public function getFileTypeColor($type)
    {
        $colors = [
            'image' => '#4CAF50',
            'document' => '#2196F3',
            'archive' => '#FF9800',
            'audio' => '#9C27B0',
            'video' => '#F44336',
            'code' => '#607D8B',
            'data' => '#00BCD4',
            'other' => '#9E9E9E',
        ];
        
        return $colors[$type] ?? '#9E9E9E';
    }
    
    /**
     * 获取文件类型名称
     *
     * @param string $type 文件类型
     * @return string 类型名称
     */
    public function getFileTypeName($type)
    {
        $names = [
            'image' => '图片',
            'document' => '文档',
            'archive' => '压缩包',
            'audio' => '音频',
            'video' => '视频',
            'code' => '代码',
            'data' => '数据',
            'other' => '其他',
        ];
        
        return $names[$type] ?? '其他';
    }
}
