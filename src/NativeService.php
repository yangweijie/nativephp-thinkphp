<?php
namespace native\thinkphp;
use Illuminate\Support\Arr;
use native\thinkphp\ChildProcess as ChildProcessImplementation;
use native\thinkphp\client\Client;
use native\thinkphp\command\FreshCommand;
use native\thinkphp\command\LoadPHPConfigurationCommand;
use native\thinkphp\command\LoadStartupConfigurationCommand;
use native\thinkphp\command\MigrateCommand;
use native\thinkphp\command\MinifyApplicationCommand;
use native\thinkphp\command\SeedDatabaseCommand;
use native\thinkphp\contract\ChildProcess as ChildProcessContract;
use native\thinkphp\contract\GlobalShortcut as GlobalShortcutContract;
use native\thinkphp\contract\PowerMonitor as PowerMonitorContract;
use native\thinkphp\contract\WindowManager as WindowManagerContract;
use native\thinkphp\event\EventWatcher;
use native\thinkphp\exception\Handler;
use native\thinkphp\GlobalShortcut as GlobalShortcutImplementation;
use native\thinkphp\logging\LogWatcher;
use native\thinkphp\PowerMonitor as PowerMonitorImplementation;
use native\thinkphp\support\service\ConsoleSupportService;
use native\thinkphp\windows\WindowManager as WindowManagerImplementation;
use think\facade\Console;
use think\facade\Db;
use yangweijie\thinkphpPackageTools\Package;
use yangweijie\thinkphpPackageTools\PackageService;

class NativeService extends PackageService
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('nativephp-thinkphp')
            ->hasConfigFile('nativephp')
            ->hasConfigFile('nativephp-internal')
            ->hasRoute('api')
            ->hasCommands([
                 LoadStartupConfigurationCommand::class,
                 LoadPHPConfigurationCommand::class,
                // MigrateCommand::class,
                 MinifyApplicationCommand::class,
//                 SeedDatabaseCommand::class,
            ]);
    }

    public function registeringPackage()
    {
        // $this->app->bind('migrator', function(){
        //     return new ConsoleSupportService($this->app);
        // });
        // ThinkPHP：绑定单例
        // $this->app->bind(FreshCommand::class, function () {
        //     return new FreshCommand(app('migrator')); // 解析依赖的 migrator 服务
        // }, true); // 第三个参数 true 表示单例绑定

        // $this->app->bind(MigrateCommand::class, function (\think\App $app) {
        //     return new MigrateCommand($app['migrator'], $app['events']);
        // });

        // $this->app->bind(WindowManagerContract::class, function (Client $client) {
        //     return new WindowManagerImplementation($client);
        // });

        // $this->app->bind(ChildProcessContract::class, function (Client $client) {
        //     return new ChildProcessImplementation($client);
        // });

        // $this->app->bind(GlobalShortcutContract::class, function (Client $client) {
        //     return new GlobalShortcutImplementation($client);
        // });

        // $this->app->bind(PowerMonitorContract::class, function (Client $client) {
        //     return new PowerMonitorImplementation($client);
        // });

        if (config('nativephp-internal.running')) {
            $this->app->bind(['think\exception\Handle'=> Handler::class]);
            $this->configureApp();
        }

        $this->app->bind('ContextMenu', 'native\thinkphp\facade\ContextMenu');
        $this->app->bind('Dock', 'native\thinkphp\facade\Dock');
        $this->app->bind('Process', 'native\thinkphp\facade\Process');
        $this->app->bind('Window', 'native\thinkphp\facade\Window');
        $this->app->bind('Clipboard', 'native\thinkphp\facade\Clipboard');

    }

    public function boot(){
        if (config('nativephp-internal.running')) {
            $this->rewriteDatabase();
        }
        parent::boot();
    }

    protected function configureApp()
    {
        if ($this->app->isDebug()) {
            app(LogWatcher::class)->register();
        }

        app(EventWatcher::class)->register();

        $this->rewriteStoragePath();

        $this->configureDisks();

        config(['type' => 'file'], 'session');
        config(['default' => 'database']);
    }

    protected function rewriteStoragePath()
    {
        if ($this->app->isDebug()) {
            return;
        }

        $oldStoragePath = $this->app->getRuntimePath().DIRECTORY_SEPARATOR.'storage';

//        $this->app->useStoragePath(config('nativephp-internal.storage_path'));

        // Patch all config values that contain the old storage path
        $config = Arr::dot(config());

        foreach ($config as $key => $value) {
            if (is_string($value) && str_contains($value, $oldStoragePath)) {
                $newValue = str_replace($oldStoragePath, config('nativephp-internal.storage_path'), $value);
                config([$key => $newValue]);
            }
        }
    }

    public function rewriteDatabase()
    {
        $databasePath = config('nativephp-internal.database_path');

        if ($this->app->isDebug()) {
            $databasePath = database_path('nativephp.sqlite');

            if (! file_exists($databasePath)) {
                touch($databasePath);

                Console::call('native:migrate');
            }
        }

        config([
            'database.connections.nativephp' => [
                'driver' => 'sqlite',
                'url' => env('DATABASE_URL'),
                'database' => $databasePath,
                'prefix' => '',
                'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
            ],
        ]);

        config(['database.default' => 'nativephp']);

        if (file_exists($databasePath)) {
            DB::execute('PRAGMA journal_mode=WAL;');
            DB::execute('PRAGMA busy_timeout=5000;');
        }
    }

    public function removeDatabase()
    {
        $databasePath = config('nativephp-internal.database_path');

        if ($this->app->isDebug()) {
            $databasePath = database_path('nativephp.sqlite');
        }

        @unlink($databasePath);
        @unlink($databasePath.'-shm');
        @unlink($databasePath.'-wal');
    }

    protected function configureDisks(): void
    {
        $disks = [
            'NATIVEPHP_USER_HOME_PATH' => 'user_home',
            'NATIVEPHP_APP_DATA_PATH' => 'app_data',
            'NATIVEPHP_USER_DATA_PATH' => 'user_data',
            'NATIVEPHP_DESKTOP_PATH' => 'desktop',
            'NATIVEPHP_DOCUMENTS_PATH' => 'documents',
            'NATIVEPHP_DOWNLOADS_PATH' => 'downloads',
            'NATIVEPHP_MUSIC_PATH' => 'music',
            'NATIVEPHP_PICTURES_PATH' => 'pictures',
            'NATIVEPHP_VIDEOS_PATH' => 'videos',
            'NATIVEPHP_RECENT_PATH' => 'recent',
        ];

        foreach ($disks as $env => $disk) {
            if (! env($env)) {
                continue;
            }

            config([
                'filesystems.disks.'.$disk => [
                    'driver' => 'local',
                    'root' => env($env, ''),
                    'throw' => false,
                    'links' => 'skip',
                ],
            ]);
        }
    }
}