<?php

namespace Osm\Data\Queries;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
        \Osm\Data\Schema\Module::class,
    ];
}