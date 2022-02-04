<?php

namespace Osm\Admin\All;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Queries\Module::class,
        \Osm\Admin\Interfaces\Module::class,
    ];
}