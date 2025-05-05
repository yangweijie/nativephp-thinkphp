<?php

namespace native\thinkphp\command;

use Illuminate\Console\Concerns\ConfiguresPrompts;
use Illuminate\Console\Concerns\HasParameters;
use Illuminate\Console\Concerns\InteractsWithSignals;
use Illuminate\Console\Concerns\PromptsForMissingInput;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolverInterface as Resolver;
use Illuminate\Database\Events\DatabaseRefreshed;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Support\Traits\Macroable;
use native\thinkphp\NativeService;
use Symfony\Component\Console\Command\Command as CommandAlias;
use Symfony\Component\Console\Input\InputOption;
use think\console\Command;
use yangweijie\thinkphpPackageTools\adapter\laravel\LaravelCommand;

class FreshCommand extends Command
{
    use LaravelCommand;
    
    use
        ConfirmableTrait,
        ConfiguresPrompts,
        HasParameters,
        InteractsWithSignals,
        Prohibitable,
        PromptsForMissingInput,
        Macroable;

    protected string $description = 'Drop all tables and re-run all migrations in the NativePHP development environment';

    /**
     * The migrator instance.
     *
     * @var Migrator
     */
    protected $migrator;

    public function __construct(Migrator $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
        $this->signature = 'native:migrate:fresh';
    }

    public function handle()
    {
        $nativeServiceProvider = new NativeService($this->app);

        $nativeServiceProvider->removeDatabase();

        $nativeServiceProvider->rewriteDatabase();

        if ($this->isProhibited() ||
            ! $this->confirmToProceed()) {
            return CommandAlias::FAILURE;
        }

        $database = $this->input->getOption('database');

        $this->migrator->usingConnection($database, function () use ($database) {
            if ($this->migrator->repositoryExists()) {
                $this->newLine();

                $this->components->task('Dropping all tables', fn () => $this->callSilent('db:wipe', array_filter([
                        '--database' => $database,
                        '--drop-views' => $this->option('drop-views'),
                        '--drop-types' => $this->option('drop-types'),
                        '--force' => true,
                    ])) == 0);
            }
        });

        $this->newLine();

        $this->call('migrate', array_filter([
            '--database' => $database,
            '--path' => $this->input->getOption('path'),
            '--realpath' => $this->input->getOption('realpath'),
            '--schema-path' => $this->input->getOption('schema-path'),
            '--force' => true,
            '--step' => $this->option('step'),
        ]));

        if ($this->app->bound(Dispatcher::class)) {
            $this->app[Dispatcher::class]->dispatch(
                new DatabaseRefreshed($database, $this->needsSeeding())
            );
        }

        if ($this->needsSeeding()) {
            $this->runSeeder($database);
        }

        return 0;
    }

    /**
     * Determine if the developer has requested database seeding.
     *
     * @return bool
     */
    protected function needsSeeding()
    {
        return $this->option('seed') || $this->option('seeder');
    }

    /**
     * Run the database seeder command.
     *
     * @param  string  $database
     * @return void
     */
    protected function runSeeder($database)
    {
        $this->call('db:seed', array_filter([
            '--database' => $database,
            '--class' => $this->option('seeder') ?: 'Database\\Seeders\\DatabaseSeeder',
            '--force' => true,
        ]));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use'],
            ['drop-views', null, InputOption::VALUE_NONE, 'Drop all tables and views'],
            ['drop-types', null, InputOption::VALUE_NONE, 'Drop all tables and types (Postgres only)'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production'],
            ['path', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'The path(s) to the migrations files to be executed'],
            ['realpath', null, InputOption::VALUE_NONE, 'Indicate any provided migration file paths are pre-resolved absolute paths'],
            ['schema-path', null, InputOption::VALUE_OPTIONAL, 'The path to a schema dump file'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run'],
            ['seeder', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder'],
            ['step', null, InputOption::VALUE_NONE, 'Force the migrations to be run so they can be rolled back individually'],
        ];
    }
}
