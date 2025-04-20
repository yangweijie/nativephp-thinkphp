<?php

namespace NativePHP\Think;

use think\App;

class UpdateManager
{
    protected array $config;
    protected array $currentVersion;
    
    public function __construct(protected App $app)
    {
        $this->config = $this->app->config->get('native.updater', []);
        $this->currentVersion = $this->parseVersion($this->app->config->get('native.app.version', '1.0.0'));
    }
    
    /**
     * 检查更新
     */
    public function checkForUpdates(): ?array
    {
        if (!$this->config['enabled']) {
            return null;
        }
        
        if (!$this->config['server']) {
            throw new \RuntimeException('未配置更新服务器地址');
        }
        
        try {
            $response = file_get_contents($this->config['server'] . '/updates.json');
            $updates = json_decode($response, true);
            
            if (!$updates) {
                return null;
            }
            
            // 过滤当前通道的更新
            $updates = array_filter($updates['versions'] ?? [], function($update) {
                return $update['channel'] === $this->config['channel'];
            });
            
            if (empty($updates)) {
                return null;
            }
            
            // 获取最新版本
            $latestUpdate = array_reduce($updates, function($latest, $update) {
                $version = $this->parseVersion($update['version']);
                if (!$latest || $this->compareVersions($version, $this->parseVersion($latest['version'])) > 0) {
                    return $update;
                }
                return $latest;
            });
            
            // 检查是否需要更新
            if ($this->compareVersions($this->currentVersion, $this->parseVersion($latestUpdate['version'])) >= 0) {
                return null;
            }
            
            return $latestUpdate;
            
        } catch (\Exception $e) {
            throw new \RuntimeException('检查更新失败: ' . $e->getMessage());
        }
    }
    
    /**
     * 下载更新
     */
    public function downloadUpdate(array $update): string
    {
        $downloadDir = $this->config['download_dir'];
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }
        
        $fileName = basename($update['url']);
        $filePath = $downloadDir . '/' . $fileName;
        
        // 验证签名
        if (!empty($this->config['pubkey'])) {
            if (empty($update['signature'])) {
                throw new \RuntimeException('更新包缺少签名');
            }
            
            if (!$this->verifySignature($update['url'], $update['signature'], $this->config['pubkey'])) {
                throw new \RuntimeException('更新包签名验证失败');
            }
        }
        
        // 下载文件
        $content = file_get_contents($update['url']);
        if ($content === false) {
            throw new \RuntimeException('下载更新失败');
        }
        
        file_put_contents($filePath, $content);
        
        return $filePath;
    }
    
    /**
     * 安装更新
     */
    public function installUpdate(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('更新文件不存在');
        }
        
        // 触发安装事件
        $this->app->native->bridge()->emit('updater:install', [
            'path' => $filePath,
            'mode' => $this->config['install_mode']
        ]);
    }
    
    /**
     * 解析版本号
     */
    protected function parseVersion(string $version): array
    {
        $parts = explode('.', $version);
        return [
            'major' => (int)($parts[0] ?? 0),
            'minor' => (int)($parts[1] ?? 0),
            'patch' => (int)($parts[2] ?? 0)
        ];
    }
    
    /**
     * 比较版本号
     * 返回: 1 如果 v1 > v2, -1 如果 v1 < v2, 0 如果相等
     */
    protected function compareVersions(array $v1, array $v2): int
    {
        foreach (['major', 'minor', 'patch'] as $part) {
            if ($v1[$part] > $v2[$part]) {
                return 1;
            }
            if ($v1[$part] < $v2[$part]) {
                return -1;
            }
        }
        return 0;
    }
    
    /**
     * 验证签名
     */
    protected function verifySignature(string $url, string $signature, string $pubkey): bool
    {
        $data = file_get_contents($url);
        if ($data === false) {
            return false;
        }
        
        $publicKey = openssl_pkey_get_public($pubkey);
        if (!$publicKey) {
            throw new \RuntimeException('无效的公钥');
        }
        
        $signature = base64_decode($signature);
        $verify = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA256);
        
        openssl_free_key($publicKey);
        
        return $verify === 1;
    }
}