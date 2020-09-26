<?php

namespace SierraTecnologia\CrudMaker\Services;

use Illuminate\Filesystem\Filesystem;

class FileService
{
    /**
     * @param true $recursive
     *
     * @return void
     */
    public function mkdir(string $path, int $mode, bool $recursive): void
    {
        if (! is_dir($path)) {
            mkdir($path, $mode, $recursive);
        }
    }

    public function get(string $file): string
    {
        $filesystem = new Filesystem();
        $templateSource = config('crudmaker.template_source');
        $orginalFileSource = __DIR__.'/../Templates/Laravel/';

        if (is_null($templateSource)) {
            $templateSource = base_path('resources/crudmaker');
        }

        if (! file_exists($file)) {
            $file = str_replace($templateSource, $orginalFileSource, $file);
        }

        return $filesystem->get($file);
    }
}
