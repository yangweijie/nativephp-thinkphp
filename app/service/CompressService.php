<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Notification;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Exception;

class CompressService
{
    /**
     * 压缩文件或目录
     *
     * @param string|array $source 源文件或目录路径
     * @param string $destination 目标压缩文件路径
     * @param string $format 压缩格式 (zip, tar, gz, bz2)
     * @param string $password 可选密码保护 (仅支持zip)
     * @return bool
     * @throws Exception
     */
    public function compress($source, string $destination, string $format = 'zip', string $password = '')
    {
        // 确保目标目录存在
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            FileSystem::makeDirectory($destDir, 0755, true);
        }

        // 根据格式选择压缩方法
        switch (strtolower($format)) {
            case 'zip':
                return $this->compressZip($source, $destination, $password);
            case 'tar':
                return $this->compressTar($source, $destination);
            case 'gz':
            case 'gzip':
                return $this->compressGzip($source, $destination);
            case 'bz2':
            case 'bzip2':
                return $this->compressBzip2($source, $destination);
            default:
                throw new Exception("不支持的压缩格式: {$format}");
        }
    }

    /**
     * 解压文件
     *
     * @param string $source 源压缩文件路径
     * @param string $destination 目标解压目录
     * @param string $password 可选密码 (仅支持zip)
     * @return bool
     * @throws Exception
     */
    public function extract(string $source, string $destination, string $password = '')
    {
        // 确保目标目录存在
        if (!is_dir($destination)) {
            FileSystem::makeDirectory($destination, 0755, true);
        }

        // 根据文件扩展名选择解压方法
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'zip':
                return $this->extractZip($source, $destination, $password);
            case 'tar':
                return $this->extractTar($source, $destination);
            case 'gz':
            case 'gzip':
                return $this->extractGzip($source, $destination);
            case 'bz2':
            case 'bzip2':
                return $this->extractBzip2($source, $destination);
            default:
                throw new Exception("不支持的压缩格式: {$extension}");
        }
    }

    /**
     * 获取压缩文件内容列表
     *
     * @param string $source 压缩文件路径
     * @param string $password 可选密码 (仅支持zip)
     * @return array
     * @throws Exception
     */
    public function listContents(string $source, string $password = '')
    {
        $extension = strtolower(pathinfo($source, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'zip':
                return $this->listZipContents($source, $password);
            case 'tar':
                return $this->listTarContents($source);
            case 'gz':
            case 'gzip':
            case 'bz2':
            case 'bzip2':
                // 这些格式通常只包含一个文件
                return [basename($source, '.' . $extension)];
            default:
                throw new Exception("不支持的压缩格式: {$extension}");
        }
    }

    /**
     * 使用ZIP格式压缩
     *
     * @param string|array $source
     * @param string $destination
     * @param string $password
     * @return bool
     * @throws Exception
     */
    protected function compressZip($source, string $destination, string $password = '')
    {
        $zip = new ZipArchive();
        
        $zipFlags = ZipArchive::CREATE;
        if (file_exists($destination)) {
            $zipFlags |= ZipArchive::OVERWRITE;
        }
        
        if ($zip->open($destination, $zipFlags) !== true) {
            throw new Exception("无法创建ZIP文件: {$destination}");
        }

        // 设置密码（如果提供）
        if (!empty($password)) {
            $zip->setPassword($password);
        }

        // 处理单个文件/目录或多个文件/目录
        $sources = is_array($source) ? $source : [$source];
        
        foreach ($sources as $src) {
            if (is_dir($src)) {
                $this->addDirToZip($zip, $src, basename($src));
            } else {
                $zip->addFile($src, basename($src));
                
                // 如果有密码，为文件设置加密
                if (!empty($password)) {
                    $zip->setEncryptionName(basename($src), ZipArchive::EM_AES_256);
                }
            }
        }

        $result = $zip->close();
        
        if (!$result) {
            throw new Exception("压缩文件失败: {$destination}");
        }
        
        return true;
    }

    /**
     * 递归添加目录到ZIP
     *
     * @param ZipArchive $zip
     * @param string $dir
     * @param string $localDir
     * @return void
     */
    protected function addDirToZip(ZipArchive $zip, string $dir, string $localDir)
    {
        $zip->addEmptyDir($localDir);
        
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            $filePath = $file->getRealPath();
            $relativePath = $localDir . '/' . substr($filePath, strlen($dir) + 1);
            
            if ($file->isDir()) {
                $zip->addEmptyDir($relativePath);
            } else {
                $zip->addFile($filePath, $relativePath);
                
                // 如果有密码，为文件设置加密
                if (method_exists($zip, 'setPassword') && $zip->getPassword()) {
                    $zip->setEncryptionName($relativePath, ZipArchive::EM_AES_256);
                }
            }
        }
    }

    /**
     * 解压ZIP文件
     *
     * @param string $source
     * @param string $destination
     * @param string $password
     * @return bool
     * @throws Exception
     */
    protected function extractZip(string $source, string $destination, string $password = '')
    {
        $zip = new ZipArchive();
        
        if ($zip->open($source) !== true) {
            throw new Exception("无法打开ZIP文件: {$source}");
        }
        
        // 设置密码（如果提供）
        if (!empty($password)) {
            $zip->setPassword($password);
        }
        
        $result = $zip->extractTo($destination);
        $zip->close();
        
        if (!$result) {
            throw new Exception("解压文件失败: {$source}");
        }
        
        return true;
    }

    /**
     * 列出ZIP文件内容
     *
     * @param string $source
     * @param string $password
     * @return array
     * @throws Exception
     */
    protected function listZipContents(string $source, string $password = '')
    {
        $zip = new ZipArchive();
        
        if ($zip->open($source) !== true) {
            throw new Exception("无法打开ZIP文件: {$source}");
        }
        
        // 设置密码（如果提供）
        if (!empty($password)) {
            $zip->setPassword($password);
        }
        
        $contents = [];
        
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            if ($stat) {
                $contents[] = [
                    'name' => $stat['name'],
                    'size' => $stat['size'],
                    'compressed_size' => $stat['comp_size'],
                    'modified' => date('Y-m-d H:i:s', $stat['mtime']),
                    'is_dir' => substr($stat['name'], -1) === '/',
                    'encrypted' => ($stat['encryption_method'] > 0)
                ];
            }
        }
        
        $zip->close();
        
        return $contents;
    }

    /**
     * 使用TAR格式压缩
     *
     * @param string|array $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function compressTar($source, string $destination)
    {
        // 检查是否安装了PharData扩展
        if (!class_exists('PharData')) {
            throw new Exception("需要PharData扩展来处理TAR文件");
        }

        try {
            $tar = new \PharData($destination);
            
            // 处理单个文件/目录或多个文件/目录
            $sources = is_array($source) ? $source : [$source];
            
            foreach ($sources as $src) {
                if (is_dir($src)) {
                    $tar->buildFromDirectory($src);
                } else {
                    $tar->addFile($src, basename($src));
                }
            }
            
            return true;
        } catch (\Exception $e) {
            throw new Exception("TAR压缩失败: " . $e->getMessage());
        }
    }

    /**
     * 解压TAR文件
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function extractTar(string $source, string $destination)
    {
        // 检查是否安装了PharData扩展
        if (!class_exists('PharData')) {
            throw new Exception("需要PharData扩展来处理TAR文件");
        }

        try {
            $tar = new \PharData($source);
            $tar->extractTo($destination, null, true); // 第三个参数为true表示覆盖现有文件
            
            return true;
        } catch (\Exception $e) {
            throw new Exception("TAR解压失败: " . $e->getMessage());
        }
    }

    /**
     * 列出TAR文件内容
     *
     * @param string $source
     * @return array
     * @throws Exception
     */
    protected function listTarContents(string $source)
    {
        // 检查是否安装了PharData扩展
        if (!class_exists('PharData')) {
            throw new Exception("需要PharData扩展来处理TAR文件");
        }

        try {
            $tar = new \PharData($source);
            $contents = [];
            
            foreach ($tar as $file) {
                $contents[] = [
                    'name' => $file->getFilename(),
                    'size' => $file->getSize(),
                    'compressed_size' => $file->getCompressedSize(),
                    'modified' => date('Y-m-d H:i:s', $file->getMTime()),
                    'is_dir' => $file->isDir(),
                    'encrypted' => false
                ];
            }
            
            return $contents;
        } catch (\Exception $e) {
            throw new Exception("无法读取TAR内容: " . $e->getMessage());
        }
    }

    /**
     * 使用GZIP格式压缩
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function compressGzip(string $source, string $destination)
    {
        // GZIP通常用于单个文件
        if (is_dir($source)) {
            throw new Exception("GZIP不支持直接压缩目录，请先创建TAR然后使用GZIP压缩");
        }

        $data = file_get_contents($source);
        if ($data === false) {
            throw new Exception("无法读取源文件: {$source}");
        }

        $compressed = gzencode($data, 9); // 9是最高压缩级别
        if ($compressed === false) {
            throw new Exception("GZIP压缩失败");
        }

        if (file_put_contents($destination, $compressed) === false) {
            throw new Exception("无法写入压缩文件: {$destination}");
        }

        return true;
    }

    /**
     * 解压GZIP文件
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function extractGzip(string $source, string $destination)
    {
        $data = file_get_contents($source);
        if ($data === false) {
            throw new Exception("无法读取源文件: {$source}");
        }

        $decompressed = gzdecode($data);
        if ($decompressed === false) {
            throw new Exception("GZIP解压失败");
        }

        // 如果目标是目录，使用原始文件名（去掉.gz扩展名）
        if (is_dir($destination)) {
            $filename = basename($source);
            if (substr($filename, -3) === '.gz') {
                $filename = substr($filename, 0, -3);
            }
            $destination = rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . $filename;
        }

        if (file_put_contents($destination, $decompressed) === false) {
            throw new Exception("无法写入解压文件: {$destination}");
        }

        return true;
    }

    /**
     * 使用BZIP2格式压缩
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function compressBzip2(string $source, string $destination)
    {
        // BZIP2通常用于单个文件
        if (is_dir($source)) {
            throw new Exception("BZIP2不支持直接压缩目录，请先创建TAR然后使用BZIP2压缩");
        }

        $data = file_get_contents($source);
        if ($data === false) {
            throw new Exception("无法读取源文件: {$source}");
        }

        $compressed = bzcompress($data, 9); // 9是最高压缩级别
        if ($compressed === false) {
            throw new Exception("BZIP2压缩失败");
        }

        if (file_put_contents($destination, $compressed) === false) {
            throw new Exception("无法写入压缩文件: {$destination}");
        }

        return true;
    }

    /**
     * 解压BZIP2文件
     *
     * @param string $source
     * @param string $destination
     * @return bool
     * @throws Exception
     */
    protected function extractBzip2(string $source, string $destination)
    {
        $data = file_get_contents($source);
        if ($data === false) {
            throw new Exception("无法读取源文件: {$source}");
        }

        $decompressed = bzdecompress($data);
        if ($decompressed === false) {
            throw new Exception("BZIP2解压失败");
        }

        // 如果目标是目录，使用原始文件名（去掉.bz2扩展名）
        if (is_dir($destination)) {
            $filename = basename($source);
            if (substr($filename, -4) === '.bz2') {
                $filename = substr($filename, 0, -4);
            }
            $destination = rtrim($destination, '/\\') . DIRECTORY_SEPARATOR . $filename;
        }

        if (file_put_contents($destination, $decompressed) === false) {
            throw new Exception("无法写入解压文件: {$destination}");
        }

        return true;
    }

    /**
     * 检测压缩文件类型
     *
     * @param string $filePath
     * @return string|null
     */
    public function detectArchiveType(string $filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'zip':
                return 'zip';
            case 'tar':
                return 'tar';
            case 'gz':
            case 'gzip':
                return 'gzip';
            case 'bz2':
            case 'bzip2':
                return 'bzip2';
            case 'rar':
                return 'rar';
            case '7z':
                return '7z';
            default:
                // 尝试通过文件头检测
                $handle = fopen($filePath, 'rb');
                if ($handle) {
                    $header = fread($handle, 4);
                    fclose($handle);
                    
                    if (substr($header, 0, 2) === 'PK') {
                        return 'zip';
                    } elseif (substr($header, 0, 2) === "\x1F\x8B") {
                        return 'gzip';
                    } elseif (substr($header, 0, 3) === 'BZh') {
                        return 'bzip2';
                    } elseif (substr($header, 0, 4) === 'Rar!') {
                        return 'rar';
                    } elseif (substr($header, 0, 4) === '7z\xBC\xAF') {
                        return '7z';
                    }
                }
                return null;
        }
    }
}
