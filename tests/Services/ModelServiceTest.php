<?php

use Grafite\CrudMaker\Services\ModelService;

class ModelServiceTest extends TestCase
{
    protected $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = app(ModelService::class);
    }

    public function testPrepareModelRelationships()
    {
        $relationships = [
            ['hasOne','App\Author','author']
        ];
        $result = $this->service->prepareModelRelationships($relationships);

        $this->assertStringContainsString('this->hasOne', $result);
        $this->assertStringContainsString('App\Author', $result);
        $this->assertStringContainsString('author()', $result);
    }
}
