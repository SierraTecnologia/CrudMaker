<?php

use SierraTecnologia\CrudMaker\Services\AppService;

class AppServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(AppService::class);
    }

    public function testGetAppNamespace()
    {
        $result = $this->service->getAppNamespace();
        $this->assertEquals($result, 'App\\');
    }
}
