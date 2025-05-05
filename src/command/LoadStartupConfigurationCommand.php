<?php

namespace native\thinkphp\command;


use think\console\Command;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;

class LoadStartupConfigurationCommand extends Command
{
    use LaravelCommand;

    public function __construct()
    {
        $this->signature = 'native:config';
        parent::__construct();
    }

    public function handle()
    {
        echo json_encode(config('nativephp'));
    }
}
