<?php

namespace Osm\Admin\Queries;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Schema\Module::class,
    ];
}