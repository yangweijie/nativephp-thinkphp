<?php

namespace NativePHP\Think;

use think\App;

class UpdateManager
{
    protected $app;
    protected $backupPath;
    protected $downloadPath;
    protected $currentVersion;
    protected $rollbackVersions = [];

    public function __construct(App $app)
    {
        $this->app = $app;
        $this->backupPath = runtime_path('backups');
        $this->downloadPath = runtime_path('updates');
        $this->currentVersion = config('native.app.version');
        $this->initDirectories();
    }

    protected function initDirectories(): void
    {
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
        if (!is_dir($this->downloadPath)) {
            mkdir($this->downloadPath, 0755, true);
        }
    }

    public function checkForUpdates(): ?array
    {
        $server = config('native.updater.server');
        $channel = config('native.updater.channel');
        
        if (!$server) {
            return null;
        }

        $response = file_get_contents(sprintf(
            '%s/updates.json?version=%s&channel=%s',
            rtrim($server, '/'),
            urlencode($this->currentVersion),
            urlencode($channel)
        ));

        if (!$response) {
            return null;
        }

        return json_decode($response, true);
    }

    public function downloadUpdate(string $url, string $version): string
    {
        $hash = md5($url);
        $path = $this->downloadPath . '/' . $hash . '.zip';

        if (!copy($url, $path)) {
            throw new \RuntimeException('下载更新包失败');
        }

        // 验证签名
        if (!$this->verifyUpdate($path)) {
            unlink($path);
            throw new \RuntimeException('更新包签名验证失败');
        }

        return $path;
    }

    protected function verifyUpdate(string $path): bool
    {
        $pubkey = config('native.updater.pubkey');
        if (!$pubkey) {
            return true;
        }

        // 获取签名文件
        $signature = file_get_contents($path . '.sig');
        if (!$signature) {
            return false;
        }

        return verify_update_signature($path, $signature, $pubkey);
    }

    public function installUpdate(string $updatePath, string $version): bool
    {
        // 备份当前版本
        $this->backupCurrentVersion();

        try {
            // 解压更新包
            $zip = new \ZipArchive;
            if ($zip->open($updatePath) !== true) {
                throw new \RuntimeException('无法打开更新包');
            }

            $zip->extractTo($this->app->getRootPath());
            $zip->close();

            // 更新版本号
            $this->updateVersion($version);

            // 清理下载的更新包
            unlink($updatePath);
            if (file_exists($updatePath . '.sig')) {
                unlink($updatePath . '.sig');
            }

            return true;
        } catch (\Exception $e) {
            // 发生错误时回滚
            $this->rollback();
            throw $e;
        }
    }

    protected function backupCurrentVersion(): void
    {
        $backupFile = $this->backupPath . '/' . $this->currentVersion . '_' . time() . '.zip';
        
        $zip = new \ZipArchive;
        if ($zip->open($backupFile, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('无法创建备份文件');
        }

        $this->addToZip($zip, $this->app->getRootPath());
        $zip->close();

        // 保存到回滚版本列表
        $this->rollbackVersions[] = [
            'version' => $this->currentVersion,
            'path' => $backupFile,
            'time' => time()
        ];

        // 只保留最近的3个版本
        if (count($this->rollbackVersions) > 3) {
            $old = array_shift($this->rollbackVersions);
            if (file_exists($old['path'])) {
                unlink($old['path']);
            }
        }
    }

    protected function addToZip(\ZipArchive $zip, string $path, string $relativePath = ''): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $zip->addFile(
                    $file->getRealPath(),
                    $relativePath . substr($file->getRealPath(), strlen($path))
                );
            }
        }
    }

    public function rollback(): bool
    {
        if (empty($this->rollbackVersions)) {
            return false;
        }

        $lastBackup = array_pop($this->rollbackVersions);
        
        // 解压备份
        $zip = new \ZipArchive;
        if ($zip->open($lastBackup['path']) !== true) {
            return false;
        }

        $zip->extractTo($this->app->getRootPath());
        $zip->close();

        // 恢复版本号
        $this->updateVersion($lastBackup['version']);

        return true;
    }

    protected function updateVersion(string $version): void
    {
        $this->currentVersion = $version;
        
        // 更新环境文件中的版本号
        $envFile = $this->app->getRootPath() . '.env';
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            $content = preg_replace(
                '/APP_VERSION=.*/',
                'APP_VERSION=' . $version,
                $content
            );
            file_put_contents($envFile, $content);
        }
    }

    public function getRollbackVersions(): array
    {
        return $this->rollbackVersions;
    }

    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }
}