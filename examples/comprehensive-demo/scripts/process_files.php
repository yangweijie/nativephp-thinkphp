<?php

/**
 * 文件处理脚本
 * 
 * 这个脚本用于处理指定目录中的文件，并将处理结果保存到目标目录。
 * 
 * 用法：php process_files.php <源目录> <目标目录> <文件类型>
 * 
 * 示例：php process_files.php runtime/temp runtime/processed txt
 */

// 检查命令行参数
if ($argc < 4) {
    echo "用法: php process_files.php <源目录> <目标目录> <文件类型>\n";
    exit(1);
}

// 获取命令行参数
$sourceDir = $argv[1];
$targetDir = $argv[2];
$fileType = $argv[3];

// 检查源目录是否存在
if (!is_dir($sourceDir)) {
    echo "错误: 源目录 '{$sourceDir}' 不存在\n";
    exit(1);
}

// 检查目标目录是否存在，如果不存在则创建
if (!is_dir($targetDir)) {
    if (!mkdir($targetDir, 0755, true)) {
        echo "错误: 无法创建目标目录 '{$targetDir}'\n";
        exit(1);
    }
}

// 获取文件列表
$files = glob("{$sourceDir}/*.{$fileType}");
$totalFiles = count($files);

if ($totalFiles === 0) {
    echo "没有找到 '{$fileType}' 类型的文件\n";
    exit(0);
}

echo "开始处理 {$totalFiles} 个文件...\n";

// 处理文件
$processedFiles = 0;

foreach ($files as $file) {
    // 获取文件名
    $fileName = basename($file);
    
    // 构建目标文件路径
    $targetFile = "{$targetDir}/processed_{$fileName}";
    
    // 读取文件内容
    $content = file_get_contents($file);
    
    // 处理文件内容（根据文件类型进行不同的处理）
    switch ($fileType) {
        case 'txt':
            // 对文本文件进行处理
            $processedContent = processTextFile($content);
            break;
            
        case 'csv':
            // 对CSV文件进行处理
            $processedContent = processCsvFile($content);
            break;
            
        case 'json':
            // 对JSON文件进行处理
            $processedContent = processJsonFile($content);
            break;
            
        default:
            // 默认处理
            $processedContent = $content;
            break;
    }
    
    // 写入处理后的内容到目标文件
    file_put_contents($targetFile, $processedContent);
    
    // 更新处理进度
    $processedFiles++;
    echo "Processed: {$processedFiles}/{$totalFiles}\n";
    
    // 模拟处理延迟
    sleep(1);
}

echo "文件处理完成: {$processedFiles}/{$totalFiles}\n";
exit(0);

/**
 * 处理文本文件
 *
 * @param string $content 文件内容
 * @return string 处理后的内容
 */
function processTextFile($content)
{
    // 添加处理时间戳
    $timestamp = date('Y-m-d H:i:s');
    $processedContent = "Processed at: {$timestamp}\n\n";
    
    // 转换为大写
    $processedContent .= strtoupper($content);
    
    // 添加处理摘要
    $processedContent .= "\n\nSummary:\n";
    $processedContent .= "Characters: " . strlen($content) . "\n";
    $processedContent .= "Words: " . str_word_count($content) . "\n";
    $processedContent .= "Lines: " . substr_count($content, "\n") + 1 . "\n";
    
    return $processedContent;
}

/**
 * 处理CSV文件
 *
 * @param string $content 文件内容
 * @return string 处理后的内容
 */
function processCsvFile($content)
{
    // 解析CSV内容
    $lines = explode("\n", $content);
    $processedLines = [];
    
    foreach ($lines as $line) {
        if (empty(trim($line))) {
            continue;
        }
        
        // 解析CSV行
        $values = str_getcsv($line);
        
        // 处理每个值
        foreach ($values as &$value) {
            // 如果是数字，则乘以2
            if (is_numeric($value)) {
                $value *= 2;
            }
            // 如果是文本，则转换为大写
            else {
                $value = strtoupper($value);
            }
        }
        
        // 重新组合CSV行
        $processedLines[] = implode(',', $values);
    }
    
    // 添加处理时间戳
    $timestamp = date('Y-m-d H:i:s');
    array_unshift($processedLines, "PROCESSED_AT,{$timestamp}");
    
    return implode("\n", $processedLines);
}

/**
 * 处理JSON文件
 *
 * @param string $content 文件内容
 * @return string 处理后的内容
 */
function processJsonFile($content)
{
    // 解析JSON内容
    $data = json_decode($content, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Error: Invalid JSON\n\n" . $content;
    }
    
    // 添加处理时间戳
    $data['processed_at'] = date('Y-m-d H:i:s');
    
    // 处理数据
    $data['processed'] = true;
    
    // 如果有items数组，处理每个项目
    if (isset($data['items']) && is_array($data['items'])) {
        foreach ($data['items'] as &$item) {
            // 如果项目有value属性，则乘以2
            if (isset($item['value']) && is_numeric($item['value'])) {
                $item['value'] *= 2;
            }
            
            // 添加处理标记
            $item['processed'] = true;
        }
    }
    
    // 转换回JSON
    return json_encode($data, JSON_PRETTY_PRINT);
}
