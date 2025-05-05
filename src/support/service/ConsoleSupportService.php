<?php

namespace native\thinkphp\support\service;

use Illuminate\Contracts\Support\DeferrableProvider;
use think\migration\command\Migrate;
use think\Service;

class ConsoleSupportService extends AggregateServiceProvider implements DeferrableProvider
{
    /**
     * The provider class names.
     *
     * @var string[]
     */
    protected $providers = [
        Service::class,
        Migrate::class,
        ComposerService::class,
    ];
}

