<?php

namespace Osm\Data\Migrations;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
        \Osm\Data\Schema\Module::class,
        \Osm\Data\Tables\Module::class,
        \Osm\Data\Scopes\Module::class,
    ];
}