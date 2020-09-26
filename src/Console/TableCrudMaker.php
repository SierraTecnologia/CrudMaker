<?php

namespace SierraTecnologia\CrudMaker\Console;

use Exception;
// use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use SierraTecnologia\CrudMaker\Services\TableService;

class TableCrudMaker extends Command
{
    // use DetectsApplicationNamespace;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'crudmaker:table {table}
        {--api : Creates an API Controller and Routes}
        {--ui= : Select one of bootstrap|semantic for the UI}
        {--serviceOnly : Does not generate a Controller or Routes}
        {--withFacade : Creates a facade that can be bound in your app to access the CRUD service}
        {--relationships= : Define the relationship ie: hasOne|App\Comment|comment,hasOne|App\Rating|rating or relation|class|column (without the _id)}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a magical CRUD from an existing table';
}
