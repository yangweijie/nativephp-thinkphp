<?php

namespace native\thinkphp\support\service;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\Composer;
use Illuminate\Support\ServiceProvider;

class ComposerService extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        app()->bind('composer', function ($app) {
            return new Composer($app['files'], $app->basePath());
        }, true); // 第三个参数 true 表示单例绑定
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['composer'];
    }
}