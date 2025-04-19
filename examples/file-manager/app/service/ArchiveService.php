<?php

namespace app\service;

use Native\ThinkPHP\Facades\FileSystem;
use Native\ThinkPHP\Facades\Shell;
use ZipArchive;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ArchiveService
{
    /**
     * 支持的压缩格式
     *
     * @var array
     */
    protected $supportedFormats = [
        'zip' => [
            'extension' => 'zip',
            'mimeType' => 'application/zip',
            'description' => 'ZIP 压缩文件',
        ],
        'tar' => [
            'extension' => 'tar',
            'mimeType' => 'application/x-tar',
            'description' => 'TAR 归档文件',
        ],
        'gz' => [
            'extension' => 'gz',
            'mimeType' => 'application/gzip',
            'description' => 'GZIP 压缩文件',
        ],
        'tar.gz' => [
            'extension' => 'tar.gz',
            'mimeType' => 'application/x-gzip',
            'description' => 'TAR GZIP 压缩文件',
        ],
        'rar' => [
            'extension' => 'rar',
            'mimeType' => 'application/vnd.rar',
            'description' => 'RAR 压缩文件',
        ],
        '7z' => [
            'extension' => '7z',
            'mimeType' => 'application/x-7z-compressed',
            'description' => '7-Zip 压缩文件',
        ],
        'bz2' => [
            'extension' => 'bz2',
            'mimeType' => 'application/x-bzip2',
            'description' => 'BZIP2 压缩文件',
        ],
        'tar.bz2' => [
            'extension' => 'tar.bz2',
            'mimeType' => 'application/x-bzip2',
            'description' => 'TAR BZIP2 压缩文件',
        ],
        'xz' => [
            'extension' => 'xz',
            'mimeType' => 'application/x-xz',
            'description' => 'XZ 压缩文件',
        ],
        'tar.xz' => [
            'extension' => 'tar.xz',
            'mimeType' => 'application/x-xz',
            'description' => 'TAR XZ 压缩文件',
        ],
    ];

    /**
     * 获取支持的压缩格式
     *
     * @return array
     */
    public function getSupportedFormats()
    {
        return $this->supportedFormats;
    }

    /**
     * 检查文件是否为压缩文件
     *
     * @param string $path 文件路径
     * @return bool
     */
    public function isArchive($path)
    {
        if (!is_file($path)) {
            return false;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $filename = pathinfo($path, PATHINFO_FILENAME);

        // 检查复合扩展名格式
        if ($extension === 'gz') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return true;
            }
        } else if ($extension === 'bz2') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return true;
            }
        } else if ($extension === 'xz') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return true;
            }
        }

        // 检查单一扩展名格式
        return in_array($extension, array_column($this->supportedFormats, 'extension'));
    }

    /**
     * 获取压缩文件类型
     *
     * @param string $path 文件路径
     * @return string|null
     */
    public function getArchiveType($path)
    {
        if (!$this->isArchive($path)) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $filename = pathinfo($path, PATHINFO_FILENAME);

        // 检查复合扩展名格式
        if ($extension === 'gz') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return 'tar.gz';
            }
            return 'gz';
        } else if ($extension === 'bz2') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return 'tar.bz2';
            }
            return 'bz2';
        } else if ($extension === 'xz') {
            if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) === 'tar') {
                return 'tar.xz';
            }
            return 'xz';
        }

        return $extension;
    }

    /**
     * 创建 ZIP 压缩文件
     *
     * @param string $source 源文件或目录
     * @param string $destination 目标压缩文件
     * @param bool $includeDir 是否包含目录本身
     * @return bool
     */
    public function createZip($source, $destination, $includeDir = true)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('未安装 ZIP 扩展');
        }

        if (!file_exists($source)) {
            throw new \Exception('源文件或目录不存在');
        }

        $zip = new ZipArchive();
        if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \Exception('无法创建 ZIP 文件');
        }

        $source = str_replace('\\', '/', realpath($source));

        if (is_dir($source)) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($source),
                RecursiveIteratorIterator::SELF_FIRST
            );

            $sourceBasename = basename($source);

            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);

                // 跳过 . 和 ..
                if (in_array(substr($file, strrpos($file, '/') + 1), ['.', '..'])) {
                    continue;
                }

                $file = realpath($file);
                $file = str_replace('\\', '/', $file);

                if (is_dir($file)) {
                    if ($includeDir) {
                        $relativePath = $sourceBasename . '/' . substr($file, strlen($source) + 1);
                        $zip->addEmptyDir($relativePath);
                    } else {
                        $relativePath = substr($file, strlen($source) + 1);
                        if (!empty($relativePath)) {
                            $zip->addEmptyDir($relativePath);
                        }
                    }
                } else if (is_file($file)) {
                    if ($includeDir) {
                        $relativePath = $sourceBasename . '/' . substr($file, strlen($source) + 1);
                        $zip->addFile($file, $relativePath);
                    } else {
                        $relativePath = substr($file, strlen($source) + 1);
                        $zip->addFile($file, $relativePath);
                    }
                }
            }
        } else if (is_file($source)) {
            $zip->addFile($source, basename($source));
        }

        $zip->close();

        return file_exists($destination);
    }

    /**
     * 解压 ZIP 文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     */
    public function extractZip($source, $destination)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('未安装 ZIP 扩展');
        }

        if (!file_exists($source)) {
            throw new \Exception('源压缩文件不存在');
        }

        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        $zip = new ZipArchive();
        if ($zip->open($source) !== true) {
            throw new \Exception('无法打开 ZIP 文件');
        }

        $result = $zip->extractTo($destination);
        $zip->close();

        return $result;
    }

    /**
     * 创建 TAR 归档文件
     *
     * @param string $source 源文件或目录
     * @param string $destination 目标归档文件
     * @param bool $compress 是否压缩
     * @param string $compressType 压缩类型 (gzip, bzip2, xz)
     * @return bool
     */
    public function createTar($source, $destination, $compress = false, $compressType = 'gzip')
    {
        if (!file_exists($source)) {
            throw new \Exception('源文件或目录不存在');
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持创建 TAR 文件，请使用 ZIP 格式');
        }

        $compressFlag = '';
        if ($compress) {
            switch ($compressType) {
                case 'gzip':
                    $compressFlag = 'z';
                    break;
                case 'bzip2':
                    $compressFlag = 'j';
                    break;
                case 'xz':
                    $compressFlag = 'J';
                    break;
                default:
                    $compressFlag = 'z'; // 默认使用 gzip
            }
        }

        $command = 'tar -c' . $compressFlag . 'f ' . escapeshellarg($destination) . ' -C ' . escapeshellarg(dirname($source)) . ' ' . escapeshellarg(basename($source));

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('创建 TAR 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination);
    }

    /**
     * 解压 TAR 文件
     *
     * @param string $source 源归档文件
     * @param string $destination 目标目录
     * @param bool $isCompressed 是否为压缩的 TAR
     * @param string $compressType 压缩类型 (gzip, bzip2, xz)
     * @return bool
     */
    public function extractTar($source, $destination, $isCompressed = false, $compressType = 'gzip')
    {
        if (!file_exists($source)) {
            throw new \Exception('源归档文件不存在');
        }

        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持解压 TAR 文件，请使用 ZIP 格式');
        }

        $compressFlag = '';
        if ($isCompressed) {
            switch ($compressType) {
                case 'gzip':
                    $compressFlag = 'z';
                    break;
                case 'bzip2':
                    $compressFlag = 'j';
                    break;
                case 'xz':
                    $compressFlag = 'J';
                    break;
                default:
                    $compressFlag = 'z'; // 默认使用 gzip
            }
        }

        $command = 'tar -x' . $compressFlag . 'f ' . escapeshellarg($source) . ' -C ' . escapeshellarg($destination);

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('解压 TAR 文件失败: ' . implode("\n", $output));
        }

        return true;
    }

    /**
     * 压缩文件或目录
     *
     * @param string $source 源文件或目录
     * @param string $destination 目标压缩文件
     * @param string $format 压缩格式 (zip, tar, gz, tar.gz, bz2, tar.bz2, xz, tar.xz)
     * @param bool $includeDir 是否包含目录本身
     * @return bool
     */
    public function compress($source, $destination, $format = 'zip', $includeDir = true)
    {
        if (!in_array($format, array_keys($this->supportedFormats))) {
            throw new \Exception('不支持的压缩格式: ' . $format);
        }

        // 检查操作系统
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Windows 系统只支持 ZIP 格式
        if ($isWindows && $format !== 'zip') {
            throw new \Exception('Windows 系统只支持 ZIP 格式压缩');
        }

        switch ($format) {
            case 'zip':
                return $this->createZip($source, $destination, $includeDir);
            case 'tar':
                return $this->createTar($source, $destination, false);
            case 'gz':
                if (is_dir($source)) {
                    throw new \Exception('GZIP 格式不支持压缩目录，请使用 ZIP 或 TAR.GZ 格式');
                }
                return $this->createGzip($source, $destination);
            case 'tar.gz':
                return $this->createTar($source, $destination, true, 'gzip');
            case 'bz2':
                if (is_dir($source)) {
                    throw new \Exception('BZIP2 格式不支持压缩目录，请使用 ZIP 或 TAR.BZ2 格式');
                }
                return $this->createBzip2($source, $destination);
            case 'tar.bz2':
                return $this->createTar($source, $destination, true, 'bzip2');
            case 'xz':
                if (is_dir($source)) {
                    throw new \Exception('XZ 格式不支持压缩目录，请使用 ZIP 或 TAR.XZ 格式');
                }
                return $this->createXz($source, $destination);
            case 'tar.xz':
                return $this->createTar($source, $destination, true, 'xz');
            default:
                throw new \Exception('不支持的压缩格式: ' . $format);
        }
    }

    /**
     * 解压文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     */
    public function extract($source, $destination)
    {
        $type = $this->getArchiveType($source);

        if (!$type) {
            throw new \Exception('不支持的压缩文件格式');
        }

        // 检查操作系统
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        // Windows 系统只支持 ZIP 格式
        if ($isWindows && $type !== 'zip') {
            throw new \Exception('Windows 系统只支持解压 ZIP 格式文件');
        }

        switch ($type) {
            case 'zip':
                return $this->extractZip($source, $destination);
            case 'tar':
                return $this->extractTar($source, $destination, false);
            case 'gz':
                return $this->extractGzip($source, $destination);
            case 'tar.gz':
                return $this->extractTar($source, $destination, true, 'gzip');
            case 'bz2':
                return $this->extractBzip2($source, $destination);
            case 'tar.bz2':
                return $this->extractTar($source, $destination, true, 'bzip2');
            case 'xz':
                return $this->extractXz($source, $destination);
            case 'tar.xz':
                return $this->extractTar($source, $destination, true, 'xz');
            case '7z':
                throw new \Exception('暂不支持解压 7z 格式文件');
            case 'rar':
                throw new \Exception('暂不支持解压 RAR 格式文件');
            default:
                throw new \Exception('不支持的压缩文件格式: ' . $type);
        }
    }

    /**
     * 创建 GZIP 压缩文件
     *
     * @param string $source 源文件
     * @param string $destination 目标压缩文件
     * @return bool
     */
    public function createGzip($source, $destination)
    {
        if (!file_exists($source) || is_dir($source)) {
            throw new \Exception('源文件不存在或是一个目录');
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持创建 GZIP 文件，请使用 ZIP 格式');
        }

        $command = 'gzip -c ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('创建 GZIP 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination);
    }

    /**
     * 解压 GZIP 文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     */
    public function extractGzip($source, $destination)
    {
        if (!file_exists($source)) {
            throw new \Exception('源压缩文件不存在');
        }

        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持解压 GZIP 文件，请使用 ZIP 格式');
        }

        $filename = basename($source);
        if (substr($filename, -3) === '.gz') {
            $filename = substr($filename, 0, -3);
        }

        $command = 'gzip -dc ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination . '/' . $filename);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('解压 GZIP 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination . '/' . $filename);
    }

    /**
     * 创建 BZIP2 压缩文件
     *
     * @param string $source 源文件
     * @param string $destination 目标压缩文件
     * @return bool
     */
    public function createBzip2($source, $destination)
    {
        if (!file_exists($source) || is_dir($source)) {
            throw new \Exception('源文件不存在或是一个目录');
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持创建 BZIP2 文件，请使用 ZIP 格式');
        }

        $command = 'bzip2 -c ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('创建 BZIP2 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination);
    }

    /**
     * 解压 BZIP2 文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     */
    public function extractBzip2($source, $destination)
    {
        if (!file_exists($source)) {
            throw new \Exception('源压缩文件不存在');
        }

        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持解压 BZIP2 文件，请使用 ZIP 格式');
        }

        $filename = basename($source);
        if (substr($filename, -4) === '.bz2') {
            $filename = substr($filename, 0, -4);
        }

        $command = 'bzip2 -dc ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination . '/' . $filename);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('解压 BZIP2 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination . '/' . $filename);
    }

    /**
     * 创建 XZ 压缩文件
     *
     * @param string $source 源文件
     * @param string $destination 目标压缩文件
     * @return bool
     */
    public function createXz($source, $destination)
    {
        if (!file_exists($source) || is_dir($source)) {
            throw new \Exception('源文件不存在或是一个目录');
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持创建 XZ 文件，请使用 ZIP 格式');
        }

        $command = 'xz -c ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('创建 XZ 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination);
    }

    /**
     * 解压 XZ 文件
     *
     * @param string $source 源压缩文件
     * @param string $destination 目标目录
     * @return bool
     */
    public function extractXz($source, $destination)
    {
        if (!file_exists($source)) {
            throw new \Exception('源压缩文件不存在');
        }

        if (!is_dir($destination)) {
            if (!mkdir($destination, 0755, true)) {
                throw new \Exception('无法创建目标目录');
            }
        }

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持解压 XZ 文件，请使用 ZIP 格式');
        }

        $filename = basename($source);
        if (substr($filename, -3) === '.xz') {
            $filename = substr($filename, 0, -3);
        }

        $command = 'xz -dc ' . escapeshellarg($source) . ' > ' . escapeshellarg($destination . '/' . $filename);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('解压 XZ 文件失败: ' . implode("\n", $output));
        }

        return file_exists($destination . '/' . $filename);
    }

    /**
     * 列出压缩文件内容
     *
     * @param string $path 压缩文件路径
     * @return array
     */
    public function listContents($path)
    {
        $type = $this->getArchiveType($path);

        if (!$type) {
            throw new \Exception('不支持的压缩文件格式');
        }

        switch ($type) {
            case 'zip':
                return $this->listZipContents($path);
            case 'tar':
            case 'tar.gz':
                return $this->listTarContents($path, $type === 'tar.gz');
            case 'gz':
                return $this->listGzipContents($path);
            default:
                throw new \Exception('不支持的压缩文件格式: ' . $type);
        }
    }

    /**
     * 列出 ZIP 文件内容
     *
     * @param string $path ZIP 文件路径
     * @return array
     */
    protected function listZipContents($path)
    {
        if (!extension_loaded('zip')) {
            throw new \Exception('未安装 ZIP 扩展');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \Exception('无法打开 ZIP 文件');
        }

        $contents = [];
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'];
            $isDir = substr($name, -1) === '/';

            $contents[] = [
                'name' => $name,
                'size' => $stat['size'],
                'compressedSize' => $stat['comp_size'],
                'mtime' => $stat['mtime'],
                'isDir' => $isDir,
            ];
        }

        $zip->close();

        return $contents;
    }

    /**
     * 列出 TAR 文件内容
     *
     * @param string $path TAR 文件路径
     * @param bool $isGzipped 是否为 GZIP 压缩的 TAR
     * @return array
     */
    protected function listTarContents($path, $isGzipped = false)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持列出 TAR 文件内容，请使用 ZIP 格式');
        }

        $command = 'tar -tvf ' . escapeshellarg($path);

        if ($isGzipped) {
            $command = 'tar -tzvf ' . escapeshellarg($path);
        }

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('列出 TAR 文件内容失败: ' . implode("\n", $output));
        }

        $contents = [];
        foreach ($output as $line) {
            // 解析 tar 输出
            if (preg_match('/^([-d])([rwx-]{9})\s+\d+\s+(\S+)\s+(\S+)\s+(\d+)\s+(\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2})\s+(.+)$/', $line, $matches)) {
                $isDir = $matches[1] === 'd';
                $name = $matches[7];

                $contents[] = [
                    'name' => $name,
                    'size' => (int) $matches[5],
                    'mtime' => strtotime($matches[6]),
                    'isDir' => $isDir,
                ];
            }
        }

        return $contents;
    }

    /**
     * 列出 GZIP 文件内容
     *
     * @param string $path GZIP 文件路径
     * @return array
     */
    protected function listGzipContents($path)
    {
        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            throw new \Exception('Windows 系统暂不支持列出 GZIP 文件内容，请使用 ZIP 格式');
        }

        $command = 'gzip -l ' . escapeshellarg($path);
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            throw new \Exception('列出 GZIP 文件内容失败: ' . implode("\n", $output));
        }

        $contents = [];
        // GZIP 通常只包含一个文件
        $filename = basename($path);
        if (substr($filename, -3) === '.gz') {
            $filename = substr($filename, 0, -3);
        }

        // 解析 gzip 输出
        if (count($output) >= 2 && preg_match('/^\s*(\d+)\s+(\d+)\s+/', $output[1], $matches)) {
            $contents[] = [
                'name' => $filename,
                'size' => (int) $matches[2],
                'compressedSize' => (int) $matches[1],
                'mtime' => filemtime($path),
                'isDir' => false,
            ];
        }

        return $contents;
    }
}
