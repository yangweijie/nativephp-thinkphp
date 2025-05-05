<?php

namespace native\thinkphp\command;


use native\thinkphp\contract\ProvidesPhpIni;
use think\console\Command;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;

class LoadPHPConfigurationCommand extends Command
{
    use LaravelCommand;
    
    public function __construct(){
        $this->signature = 'native:php-ini';
        parent::__construct();
    }

    public function handle(): void
    {
        /** @var ProvidesPhpIni $provider */
        $provider = app(config('nativephp.provider'));
        $phpIni = [];
        if (method_exists($provider, 'phpIni')) {
            $phpIni = $provider->phpIni();
        }
        echo json_encode($phpIni);
    }
}
