<?php

namespace Osm\Admin\All;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Accounts\Module::class,
        \Osm\Admin\Grids\Module::class,
        \Osm\Admin\Queries\Module::class,
        \Osm\Admin\Migrations\Module::class,
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Tables\Module::class,
    ];
}