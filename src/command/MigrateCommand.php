<?php

namespace native\thinkphp\command;

use Illuminate\Contracts\Events\Dispatcher;
use native\thinkphp\support\command\MigrateCommand as BaseMigrateCommand;
use Illuminate\Database\Migrations\Migrator;
use native\thinkphp\NativeService;
use Throwable;


class MigrateCommand extends BaseMigrateCommand
{
    
    protected string $description = 'Run the database migrations in the NativePHP development environment';

    public function __construct(Migrator $migrator, Dispatcher $dispatcher)
    {
        $this->signature = 'native:'.$this->signature;

        parent::__construct($migrator, $dispatcher);
    }

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        (new NativeService($this->app))->rewriteDatabase();

        return parent::handle();
    }
}
