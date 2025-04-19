<?php

namespace Native\ThinkPHP\Plugins;

use think\App;
use Native\ThinkPHP\Plugins\Plugin;
use Native\ThinkPHP\Facades\Database;
use Native\ThinkPHP\Facades\Logger;

class DatabasePlugin extends Plugin
{
    /**
     * 插件名称
     *
     * @var string
     */
    protected $name = 'database';

    /**
     * 插件版本
     *
     * @var string
     */
    protected $version = '1.0.0';

    /**
     * 插件描述
     *
     * @var string
     */
    protected $description = '数据库管理插件';

    /**
     * 插件作者
     *
     * @var string
     */
    protected $author = 'NativePHP';

    /**
     * 插件钩子
     *
     * @var array
     */
    protected $hooks = [];

    /**
     * 构造函数
     *
     * @param \think\App $app
     * @param array $config
     */
    public function __construct(App $app, array $config = [])
    {
        parent::__construct($app, $config);

        // 注册钩子
        $this->hooks = [
            'app.start' => [$this, 'onAppStart'],
            'app.quit' => [$this, 'onAppQuit'],
        ];
    }

    /**
     * 初始化插件
     *
     * @return void
     */
    public function init(): void
    {
        // 记录插件启动
        Logger::info('Database plugin initialized');
    }

    /**
     * 应用启动事件处理
     *
     * @return void
     */
    public function onAppStart(): void
    {
        // 记录插件启动
        Logger::info('Database plugin started');

        // 初始化数据库
        $this->initDatabase();
    }

    /**
     * 应用退出事件处理
     *
     * @return void
     */
    public function onAppQuit(): void
    {
        // 记录插件卸载
        Logger::info('Database plugin quit');
    }

    /**
     * 初始化数据库
     *
     * @return void
     */
    protected function initDatabase(): void
    {
        // 获取配置
        $config = config('native.database', []);

        // 如果配置了自动备份，则备份数据库
        if (isset($config['auto_backup']) && $config['auto_backup']) {
            $this->backupDatabase();
        }

        // 如果配置了自动优化，则优化数据库
        if (isset($config['auto_optimize']) && $config['auto_optimize']) {
            $this->optimizeDatabase();
        }

        // 如果配置了自动迁移，则执行迁移
        if (isset($config['auto_migrate']) && $config['auto_migrate']) {
            $this->migrateDatabase();
        }
    }

    /**
     * 备份数据库
     *
     * @return void
     */
    protected function backupDatabase(): void
    {
        // 获取配置
        $config = config('native.database', []);

        // 获取备份路径
        $backupPath = $config['backup_path'] ?? $this->app->getRuntimePath() . 'database/backups';

        // 确保备份目录存在
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        // 生成备份文件名
        $backupFile = $backupPath . '/backup-' . date('YmdHis') . '.sqlite';

        // 备份数据库
        $success = Database::backup($backupFile);

        // 记录备份结果
        if ($success) {
            Logger::info('Database backup successful', [
                'path' => $backupFile,
            ]);
        } else {
            Logger::error('Database backup failed');
        }

        // 清理旧备份
        $this->cleanupBackups($backupPath, $config['backup_keep'] ?? 5);
    }

    /**
     * 清理旧备份
     *
     * @param string $backupPath
     * @param int $keep
     * @return void
     */
    protected function cleanupBackups(string $backupPath, int $keep): void
    {
        // 获取所有备份文件
        $files = glob($backupPath . '/backup-*.sqlite');

        // 如果备份文件数量小于等于保留数量，则不需要清理
        if (count($files) <= $keep) {
            return;
        }

        // 按文件修改时间排序
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // 删除多余的备份文件
        $filesToDelete = array_slice($files, $keep);
        foreach ($filesToDelete as $file) {
            unlink($file);
            Logger::info('Deleted old database backup', [
                'path' => $file,
            ]);
        }
    }

    /**
     * 优化数据库
     *
     * @return void
     */
    protected function optimizeDatabase(): void
    {
        // 优化数据库
        /** @phpstan-ignore-next-line */
        $success = Database::optimize();

        // 记录优化结果
        if ($success) {
            Logger::info('Database optimization successful');
        } else {
            Logger::error('Database optimization failed');
        }
    }

    /**
     * 迁移数据库
     *
     * @return void
     */
    protected function migrateDatabase(): void
    {
        // 执行迁移
        try {
            // 使用 ThinkPHP 的迁移命令
            $output = [];
            $returnVar = 0;
            exec('php think migrate:run', $output, $returnVar);

            // 记录迁移结果
            if ($returnVar === 0) {
                Logger::info('Database migration successful', [
                    'output' => implode(PHP_EOL, $output),
                ]);
            } else {
                Logger::error('Database migration failed', [
                    'output' => implode(PHP_EOL, $output),
                ]);
            }
        } catch (\Exception $e) {
            Logger::error('Database migration error', [
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * 卸载插件
     *
     * @return void
     */
    public function unload(): void
    {
        // 记录插件卸载
        Logger::info('Database plugin unloaded');
    }

    /**
     * 获取插件钩子
     *
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }
}
