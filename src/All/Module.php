<?php

namespace Osm\Data\All;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Accounts\Module::class,
        \Osm\Data\Queries\Module::class,
        \Osm\Data\Schema\Module::class,
        \Osm\Data\Tables\Module::class,
    ];
}