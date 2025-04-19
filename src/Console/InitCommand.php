<?php

namespace Native\ThinkPHP\Console;

use think\App;
use think\facade\Config;
use think\facade\Env;
use think\facade\Filesystem;

class InitCommand extends Command
{
    /**
     * 命令名称
     *
     * @var string
     */
    protected $name = 'native:init';

    /**
     * 命令描述
     *
     * @var string
     */
    protected $description = '初始化 NativePHP 应用程序';

    /**
     * 应用实例
     *
     * @var \think\App
     */
    protected $app;

    /**
     * 构造函数
     *
     * @param \think\App $app
     */
    public function __construct(App $app)
    {
        parent::__construct();
        $this->app = $app;
    }

    /**
     * 配置命令
     *
     * @return void
     */
    protected function configure()
    {
        parent::configure();
        $this->addOption('force', 'f', Option::VALUE_NONE, '强制覆盖已存在的文件');
    }

    /**
     * 处理命令
     *
     * @return int
     */
    protected function handle()
    {
        $this->info('正在初始化 NativePHP 应用程序...');

        // 创建配置文件
        $this->createConfigFile();

        // 创建资源目录
        $this->createResourceDirectories();

        // 创建图标文件
        $this->createIconFiles();

        // 创建环境变量
        $this->createEnvironmentVariables();

        $this->info('NativePHP 应用程序初始化完成！');

        return 0;
    }

    /**
     * 创建配置文件
     *
     * @return void
     */
    protected function createConfigFile()
    {
        $configPath = $this->app->getConfigPath() . 'native.php';

        if (file_exists($configPath) && !$this->option('force')) {
            $this->warn('配置文件已存在，跳过创建。使用 --force 选项覆盖。');
            return;
        }

        // 复制配置文件
        $stubPath = __DIR__ . '/../../config/native.php';
        if (file_exists($stubPath)) {
            file_put_contents($configPath, file_get_contents($stubPath));
            $this->info('配置文件创建成功：' . $configPath);
        } else {
            $this->error('配置文件模板不存在：' . $stubPath);
        }
    }

    /**
     * 创建资源目录
     *
     * @return void
     */
    protected function createResourceDirectories()
    {
        $resourcesPath = $this->app->getRootPath() . 'resources/native';

        if (!is_dir($resourcesPath)) {
            mkdir($resourcesPath, 0755, true);
            $this->info('资源目录创建成功：' . $resourcesPath);
        }

        // 创建图标目录
        $iconsPath = $resourcesPath . '/icons';
        if (!is_dir($iconsPath)) {
            mkdir($iconsPath, 0755, true);
            $this->info('图标目录创建成功：' . $iconsPath);
        }

        // 创建托盘图标目录
        $trayIconsPath = $resourcesPath . '/tray-icons';
        if (!is_dir($trayIconsPath)) {
            mkdir($trayIconsPath, 0755, true);
            $this->info('托盘图标目录创建成功：' . $trayIconsPath);
        }

        // 创建安装程序目录
        $installerPath = $resourcesPath . '/installer';
        if (!is_dir($installerPath)) {
            mkdir($installerPath, 0755, true);
            $this->info('安装程序目录创建成功：' . $installerPath);
        }
    }

    /**
     * 创建图标文件
     *
     * @return void
     */
    protected function createIconFiles()
    {
        $resourcesPath = $this->app->getRootPath() . 'resources/native';

        // 复制默认图标
        $defaultIconPath = __DIR__ . '/../../resources/icons/icon.png';
        $targetIconPath = $resourcesPath . '/icons/icon.png';

        if (!file_exists($targetIconPath) || $this->option('force')) {
            if (file_exists($defaultIconPath)) {
                copy($defaultIconPath, $targetIconPath);
                $this->info('默认图标创建成功：' . $targetIconPath);
            } else {
                $this->warn('默认图标不存在：' . $defaultIconPath);
            }
        }

        // 复制默认托盘图标
        $defaultTrayIconPath = __DIR__ . '/../../resources/tray-icons/tray-icon.png';
        $targetTrayIconPath = $resourcesPath . '/tray-icons/tray-icon.png';

        if (!file_exists($targetTrayIconPath) || $this->option('force')) {
            if (file_exists($defaultTrayIconPath)) {
                copy($defaultTrayIconPath, $targetTrayIconPath);
                $this->info('默认托盘图标创建成功：' . $targetTrayIconPath);
            } else {
                $this->warn('默认托盘图标不存在：' . $defaultTrayIconPath);
            }
        }

        // 复制默认安装程序图标
        $defaultInstallerIconPath = __DIR__ . '/../../resources/installer/installer-icon.png';
        $targetInstallerIconPath = $resourcesPath . '/installer/installer-icon.png';

        if (!file_exists($targetInstallerIconPath) || $this->option('force')) {
            if (file_exists($defaultInstallerIconPath)) {
                copy($defaultInstallerIconPath, $targetInstallerIconPath);
                $this->info('默认安装程序图标创建成功：' . $targetInstallerIconPath);
            } else {
                $this->warn('默认安装程序图标不存在：' . $defaultInstallerIconPath);
            }
        }
    }

    /**
     * 创建环境变量
     *
     * @return void
     */
    protected function createEnvironmentVariables()
    {
        $envPath = $this->app->getRootPath() . '.env';

        if (!file_exists($envPath)) {
            $this->warn('环境变量文件不存在：' . $envPath);
            return;
        }

        $envContent = file_get_contents($envPath);

        // 添加 NativePHP 环境变量
        $nativeEnvVars = [
            'NATIVEPHP_APP_NAME' => Config::get('app.name', 'NativePHP'),
            'NATIVEPHP_APP_VERSION' => '1.0.0',
            'NATIVEPHP_APP_AUTHOR' => 'NativePHP',
            'NATIVEPHP_UPDATE_SERVER_URL' => '',
        ];

        $newEnvContent = $envContent;
        $addedVars = false;

        foreach ($nativeEnvVars as $key => $value) {
            if (!preg_match('/^' . $key . '=/m', $envContent)) {
                $newEnvContent .= PHP_EOL . $key . '=' . $value;
                $addedVars = true;
            }
        }

        if ($addedVars) {
            file_put_contents($envPath, $newEnvContent);
            $this->info('环境变量添加成功');
        } else {
            $this->info('环境变量已存在，无需添加');
        }
    }
}
