<?php

namespace native\thinkphp\command;

use Illuminate\Database\ConnectionResolverInterface as Resolver;
use native\thinkphp\support\command\SeedCommand as BaseSeedCommand;
use native\thinkphp\NativeService;

class SeedDatabaseCommand extends BaseSeedCommand
{

    public function __construct(Resolver $resolver){
        $this->signature = 'native:db:seed';
        parent::__construct($resolver);
    }

    protected string $description = 'Run the database seeders in the NativePHP development environment';

    public function handle(): int
    {
        (new NativeService($this->app))->rewriteDatabase();

        return parent::handle();
    }
}
