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
        if (!isset($update['url'])) {
            throw new \InvalidArgumentException('更新包 URL 不存在');
        }
        
        $content = file_get_contents($update['url']);
        if ($content === false) {
            throw new \RuntimeException('下载更新包失败');
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), 'update_');
        if (file_put_contents($tempFile, $content) === false) {
            throw new \RuntimeException('保存更新包失败');
        }
        
        return $tempFile;
    }
    
    /**
     * 安装更新
     */
    public function installUpdate(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException('更新包文件不存在');
        }
        
        // 触发更新安装事件
        $this->app->make('native')->bridge()->emit('updater:installing', [
            'path' => $filePath
        ]);
        
        // 具体的更新安装逻辑将由 Electron 端处理
    }
    
    /**
     * 解析版本号
     */
    protected function parseVersion(string $version): array
    {
        $parts = explode('.', $version);
        return [
            'major' => (int) ($parts[0] ?? 0),
            'minor' => (int) ($parts[1] ?? 0),
            'patch' => (int) ($parts[2] ?? 0)
        ];
    }
    
    /**
     * 比较版本号
     */
    protected function compareVersions(array $v1, array $v2): int
    {
        if ($v1['major'] != $v2['major']) {
            return $v1['major'] > $v2['major'] ? 1 : -1;
        }
        if ($v1['minor'] != $v2['minor']) {
            return $v1['minor'] > $v2['minor'] ? 1 : -1;
        }
        if ($v1['patch'] != $v2['patch']) {
            return $v1['patch'] > $v2['patch'] ? 1 : -1;
        }
        return 0;
    }
}