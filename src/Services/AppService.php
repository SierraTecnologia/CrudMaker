<?php

namespace SierraTecnologia\CrudMaker\Services;

use RuntimeException;

class AppService
{
    public function getAppNamespace()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        foreach (((array) data_get($composer, 'autoload.psr-4')) as $namespace => $path) {
            foreach ((array) $path as $pathChoice) {
                // dd('AppService', realpath(app()->path()), realpath(base_path().'/'.$pathChoice));
                if (realpath(app()->path()) == realpath(base_path().'/'.$pathChoice)) {
                    return $namespace;
                }
            }
        }

        throw new RuntimeException('Unable to detect application namespace.');
    }
}
