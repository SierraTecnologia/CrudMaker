<?php

namespace SierraTecnologia\CrudMaker\Services;

use SierraTecnologia\CrudMaker\Generators\CrudGenerator;
use SierraTecnologia\CrudMaker\Generators\DatabaseGenerator;

class CrudService
{
    protected CrudGenerator $crudGenerator;
    protected DatabaseGenerator $dbGenerator;

    /**
     * Generate core elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateCore($config, $bar): void
    {
        $this->crudGenerator->createModel($config);
        $this->crudGenerator->createService($config);

        if (strtolower($config['framework']) === 'laravel') {
            $this->crudGenerator->createRequest($config);
        }

        $bar->advance();
    }

    /**
     * Generate app based elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateAppBased($config, $bar): void
    {
        if (!$config['options-serviceOnly'] && !$config['options-apiOnly']) {
            $this->crudGenerator->createController($config);
            $this->crudGenerator->createViews($config);
            $this->crudGenerator->createRoutes($config);

            if ($config['options-withFacade']) {
                $this->crudGenerator->createFacade($config);
            }
        }
        $bar->advance();
    }

    /**
     * Generate db elements.
     *
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     * @param string                                        $section
     * @param string                                        $table
     * @param array                                         $splitTable
     * @param \SierraTecnologia\CrudMaker\Console\CrudMaker $command
     *
     * @return void
     */
    public function generateDB(array $config, $bar, $section, $table, $splitTable, $command): void
    {
        if ($config['options-migration']) {
            $this->dbGenerator->createMigration(
                $config,
                $section,
                $table,
                $splitTable,
                $command
            );
            if ($config['options-schema']) {
                $this->dbGenerator->createSchema(
                    $config,
                    $section,
                    $table,
                    $splitTable,
                    $config['options-schema']
                );
            }
        }
        $bar->advance();
    }

    /**
     * Generate api elements.
     *
     * @param array                                         $config
     * @param \Symfony\Component\Console\Helper\ProgressBar $bar
     *
     * @return void
     */
    public function generateAPI($config, $bar): void
    {
        if ($config['options-api'] || $config['options-apiOnly']) {
            $this->crudGenerator->createApi($config);
        }
        $bar->advance();
    }

    /**
     * Generates a service provider.
     *
     * @param array $config
     *
     * @return void
     */
    public function generatePackageServiceProvider($config): void
    {
        $this->crudGenerator->generatePackageServiceProvider($config);
    }

    /**
     * Corrects the namespace for the view.
     *
     * @param array $config
     *
     * @return void
     */
    public function correctViewNamespace($config): void
    {
        $controllerFile = $config['_path_controller_'].'/'.$config['_ucCamel_casePlural_'].'Controller.php';

        $controller = file_get_contents($controllerFile);

        $controller = str_replace("view('".$config['_sectionPrefix_'].$config['_lower_casePlural_'].".", "view('".$config['_sectionPrefix_'].$config['_lower_casePlural_']."::", $controller);

        file_put_contents($controllerFile, $controller);
    }
}
