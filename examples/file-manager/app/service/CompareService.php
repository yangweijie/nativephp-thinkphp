<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;

class CompareService
{
    /**
     * 比较两个文件的内容
     *
     * @param string $file1 第一个文件路径
     * @param string $file2 第二个文件路径
     * @return array 比较结果
     * @throws \Exception 如果文件不存在或无法读取
     */
    public function compareFiles($file1, $file2)
    {
        // 检查文件是否存在
        if (!FileSystem::exists($file1)) {
            throw new \Exception('第一个文件不存在: ' . $file1);
        }
        
        if (!FileSystem::exists($file2)) {
            throw new \Exception('第二个文件不存在: ' . $file2);
        }
        
        // 读取文件内容
        $content1 = FileSystem::read($file1);
        $content2 = FileSystem::read($file2);
        
        // 将内容分割为行
        $lines1 = explode("\n", $content1);
        $lines2 = explode("\n", $content2);
        
        // 计算差异
        $diff = $this->computeDiff($lines1, $lines2);
        
        // 获取文件信息
        $info1 = [
            'path' => $file1,
            'name' => basename($file1),
            'size' => FileSystem::size($file1),
            'lastModified' => FileSystem::lastModified($file1),
        ];
        
        $info2 = [
            'path' => $file2,
            'name' => basename($file2),
            'size' => FileSystem::size($file2),
            'lastModified' => FileSystem::lastModified($file2),
        ];
        
        return [
            'file1' => $info1,
            'file2' => $info2,
            'diff' => $diff,
            'stats' => [
                'added' => count(array_filter($diff, function($item) { return $item['type'] === 'added'; })),
                'removed' => count(array_filter($diff, function($item) { return $item['type'] === 'removed'; })),
                'changed' => count(array_filter($diff, function($item) { return $item['type'] === 'changed'; })),
            ],
        ];
    }
    
    /**
     * 计算两个文本数组的差异
     *
     * @param array $lines1 第一个文本的行数组
     * @param array $lines2 第二个文本的行数组
     * @return array 差异结果
     */
    protected function computeDiff($lines1, $lines2)
    {
        $result = [];
        $maxLines = max(count($lines1), count($lines2));
        
        // 使用最长公共子序列算法计算差异
        $lcs = $this->longestCommonSubsequence($lines1, $lines2);
        
        $i = 0; // 第一个文件的索引
        $j = 0; // 第二个文件的索引
        $k = 0; // LCS的索引
        
        while ($i < count($lines1) || $j < count($lines2)) {
            // 两个文件都到达了LCS的当前位置
            if ($i < count($lines1) && $j < count($lines2) && $lines1[$i] === $lines2[$j] && ($k >= count($lcs) || ($i === $lcs[$k][0] && $j === $lcs[$k][1]))) {
                $result[] = [
                    'type' => 'unchanged',
                    'line1' => $i + 1,
                    'line2' => $j + 1,
                    'content' => $lines1[$i],
                ];
                $i++;
                $j++;
                if ($k < count($lcs)) {
                    $k++;
                }
            }
            // 第一个文件中的行被删除
            elseif ($i < count($lines1) && ($k >= count($lcs) || $i < $lcs[$k][0])) {
                $result[] = [
                    'type' => 'removed',
                    'line1' => $i + 1,
                    'line2' => null,
                    'content' => $lines1[$i],
                ];
                $i++;
            }
            // 第二个文件中的行被添加
            elseif ($j < count($lines2) && ($k >= count($lcs) || $j < $lcs[$k][1])) {
                $result[] = [
                    'type' => 'added',
                    'line1' => null,
                    'line2' => $j + 1,
                    'content' => $lines2[$j],
                ];
                $j++;
            }
            // 两个文件中的行被修改
            else {
                // 查找下一个匹配点
                $nextMatch = ($k < count($lcs)) ? $lcs[$k] : [count($lines1), count($lines2)];
                
                // 收集修改的行
                $removed = [];
                $added = [];
                
                while ($i < $nextMatch[0]) {
                    $removed[] = [
                        'line' => $i + 1,
                        'content' => $lines1[$i],
                    ];
                    $i++;
                }
                
                while ($j < $nextMatch[1]) {
                    $added[] = [
                        'line' => $j + 1,
                        'content' => $lines2[$j],
                    ];
                    $j++;
                }
                
                // 如果有删除和添加，则标记为更改
                if (!empty($removed) && !empty($added)) {
                    $result[] = [
                        'type' => 'changed',
                        'removed' => $removed,
                        'added' => $added,
                    ];
                } else {
                    // 否则分别添加删除和添加
                    foreach ($removed as $item) {
                        $result[] = [
                            'type' => 'removed',
                            'line1' => $item['line'],
                            'line2' => null,
                            'content' => $item['content'],
                        ];
                    }
                    
                    foreach ($added as $item) {
                        $result[] = [
                            'type' => 'added',
                            'line1' => null,
                            'line2' => $item['line'],
                            'content' => $item['content'],
                        ];
                    }
                }
            }
        }
        
        return $result;
    }
    
    /**
     * 计算最长公共子序列
     *
     * @param array $a 第一个序列
     * @param array $b 第二个序列
     * @return array 最长公共子序列的索引对
     */
    protected function longestCommonSubsequence($a, $b)
    {
        $m = count($a);
        $n = count($b);
        
        // 创建DP表
        $dp = [];
        for ($i = 0; $i <= $m; $i++) {
            $dp[$i] = array_fill(0, $n + 1, 0);
        }
        
        // 填充DP表
        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                if ($a[$i - 1] === $b[$j - 1]) {
                    $dp[$i][$j] = $dp[$i - 1][$j - 1] + 1;
                } else {
                    $dp[$i][$j] = max($dp[$i - 1][$j], $dp[$i][$j - 1]);
                }
            }
        }
        
        // 回溯找出LCS
        $lcs = [];
        $i = $m;
        $j = $n;
        
        while ($i > 0 && $j > 0) {
            if ($a[$i - 1] === $b[$j - 1]) {
                $lcs[] = [$i - 1, $j - 1];
                $i--;
                $j--;
            } elseif ($dp[$i - 1][$j] >= $dp[$i][$j - 1]) {
                $i--;
            } else {
                $j--;
            }
        }
        
        // 反转LCS以获得正确的顺序
        return array_reverse($lcs);
    }
    
    /**
     * 检查文件是否可比较
     *
     * @param string $path 文件路径
     * @return bool 是否可比较
     */
    public function isComparable($path)
    {
        if (!FileSystem::exists($path) || is_dir($path)) {
            return false;
        }
        
        // 检查文件大小
        $maxSize = 5 * 1024 * 1024; // 5MB
        if (FileSystem::size($path) > $maxSize) {
            return false;
        }
        
        // 检查文件类型
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $textExtensions = [
            'txt', 'md', 'html', 'htm', 'xml', 'json', 'csv', 'log',
            'php', 'js', 'css', 'py', 'java', 'c', 'cpp', 'h', 'cs', 'go', 'rb',
            'ini', 'conf', 'yml', 'yaml', 'toml', 'bat', 'sh', 'ps1'
        ];
        
        if (in_array($extension, $textExtensions)) {
            return true;
        }
        
        // 尝试检测MIME类型
        try {
            $mimeType = mime_content_type($path);
            return strpos($mimeType, 'text/') === 0 || 
                   in_array($mimeType, ['application/json', 'application/xml', 'application/javascript']);
        } catch (\Exception $e) {
            return false;
        }
    }
}
