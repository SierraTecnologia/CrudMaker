<?php

if (!function_exists('app_namespace')) {
    function app_namespace()
    {
        return app('SierraTecnologia\CrudMaker\Services\AppService')
            ->getAppNamespace();
    }
}
