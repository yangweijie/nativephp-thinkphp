<?php
namespace native\thinkphp;
use native\thinkphp\event\EventWatcher;
use native\thinkphp\logging\LogWatcher;
use think\facade\Console;
use think\facade\Db;
use think\helper\Arr;
use think\Service;

class NativeService extends Service
{
    protected function configureApp()
    {
        if (config('app.debug')) {
            app(LogWatcher::class)->register();
        }

        app(EventWatcher::class)->register();

        $this->rewriteStoragePath();

        $this->configureDisks();

        config(['session.driver' => 'file']);
        config(['queue.default' => 'database']);
    }

    protected function rewriteStoragePath()
    {
        if (config('app.debug')) {
            return;
        }

        $oldStoragePath = $this->app->storagePath();

        $this->app->useStoragePath(config('nativephp-internal.storage_path'));

        // Patch all config values that contain the old storage path
        $config = Arr::dot(config()->all());

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

        if (config('app.debug')) {
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

        if (config('app.debug')) {
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