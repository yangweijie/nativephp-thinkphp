<?php

namespace native\thinkphp\command;


use think\console\Command;

class LoadStartupConfigurationCommand extends Command
{
    protected $signature = 'native:config';

    public function handle()
    {
        echo json_encode(config('nativephp'));
    }
}
