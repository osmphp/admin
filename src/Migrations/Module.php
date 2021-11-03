<?php

namespace Osm\Admin\Migrations;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Tables\Module::class,
        \Osm\Admin\Scopes\Module::class,
    ];
}