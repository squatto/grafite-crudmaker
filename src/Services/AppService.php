<?php

namespace Grafite\CrudMaker\Services;

class AppService
{
    public function getAppNamespace()
    {
        return app()->getNamespace();
    }
}
