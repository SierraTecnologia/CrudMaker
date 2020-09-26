<?php

namespace SierraTecnologia\CrudMaker\Console;

use Config;
use Exception;
use Illuminate\Console\Command;
use SierraTecnologia\CrudMaker\Generators\CrudGenerator;
use SierraTecnologia\CrudMaker\Services\AppService;
use SierraTecnologia\CrudMaker\Services\ConfigService;
use SierraTecnologia\CrudMaker\Services\CrudService;
use SierraTecnologia\CrudMaker\Services\ValidatorService;

class CrudMaker extends Command
{
    /**
     * Column Types.
     *
     * @var array
     */
    public $columnTypes = [
        'bigIncrements',
        'increments',
        'bigInteger',
        'binary',
        'boolean',
        'char',
        'date',
        'dateTime',
        'decimal',
        'double',
        'enum',
        'float',
        'integer',
        'ipAddress',
        'json',
        'jsonb',
        'longText',
        'macAddress',
        'mediumInteger',
        'mediumText',
        'morphs',
        'smallInteger',
        'string',
        'text',
        'time',
        'tinyInteger',
        'timestamp',
        'uuid',
    ];

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:new {table}
        {--api : Creates an API Controller and Routes}
        {--apiOnly : Creates only the API Controller and Routes}
        {--ui= : Select one of bootstrap|semantic for the UI}
        {--withoutViews : Prevent the generating of views}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--withBaseService : Creates service as an extension of a BaseService class}
        {--withFacade : Creates a facade that can be bound in your app to access the CRUD service}
        {--migration : Generates a migration file}
        {--asPackage= : Generate the CRUD as a package by setting a directory}
        {--schema= : Basic schema support ie: id,increments,name:string,parent_id:integer}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a magical CRUD for a table with options for: Migration, API, UI, Schema and even Relationships';

    /**
     * The app service.
     *
     * @var AppService
     */
    protected $appService;

    /**
     * The Crud service.
     *
     * @var CrudService
     */
    protected $crudService;

    /**
     * The Crud generator.
     *
     * @var CrudGenerator
     */
    protected $crudGenerator;

    /**
     * The Config service.
     *
     * @var ConfigService
     */
    protected $configService;

    /**
     * The validator service.
     *
     * @var ValidatorService
     */
    protected $validator;

    /**
     * Generate a service provider for the new module.
     *
     * @param array $config
     *
     * @return void
     */
    public function createPackageServiceProvider($config): void
    {
        $this->crudService->generatePackageServiceProvider($config);
    }

    /**
     * Create a CRUD.
     *
     * @param array  $config
     * @param string $section
     * @param string $table
     * @param array  $splitTable
     *
     * @return void
     */
    public function createCRUD($config, $section, $table, $splitTable): void
    {
        $bar = $this->output->createProgressBar(7);

        try {
            $this->crudService->generateCore($config, $bar);
            $this->crudService->generateAppBased($config, $bar);

            $this->crudGenerator->createTests(
                $config,
                $this->option('serviceOnly'),
                $this->option('apiOnly'),
                $this->option('api')
            );
            $bar->advance();

            $this->crudGenerator->createFactory($config);
            $bar->advance();

            $this->crudService->generateAPI($config, $bar);
            $bar->advance();

            $this->crudService->generateDB($config, $bar, $section, $table, $splitTable, $this);
            $bar->finish();

            $this->crudReport($table);
        } catch (Exception $e) {
            throw new Exception('Unable to generate your CRUD: ('.$e->getFile().':'.$e->getLine().') '.$e->getMessage(), 1);
        }
    }

    /**
     * Generate a CRUD report.
     *
     * @param string $table
     *
     * @return void
     */
    private function crudReport($table): void
    {
        $this->line("\n");
        $this->line('Built model...');
        $this->line('Built request...');
        $this->line('Built service...');

        if (!$this->option('serviceOnly') && !$this->option('apiOnly')) {
            $this->line('Built controller...');
            if (!$this->option('withoutViews')) {
                $this->line('Built views...');
            }
            $this->line('Built routes...');
        }

        if ($this->option('withFacade')) {
            $this->line('Built facade...');
        }

        $this->line('Built tests...');
        $this->line('Built factory...');

        if ($this->option('api') || $this->option('apiOnly')) {
            $this->line('Built api...');
            $this->comment("\nAdd the following to your app/Providers/RouteServiceProvider.php: \n");
            $this->info("require base_path('routes/api.php'); \n");
        }

        if ($this->option('migration')) {
            $this->line('Built migration...');
            if ($this->option('schema')) {
                $this->line('Built schema...');
            }
        } else {
            $this->info("\nYou will want to create a migration in order to get the $table tests to work correctly.\n");
        }
    }
}
