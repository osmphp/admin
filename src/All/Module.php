<?php

namespace Osm\Admin\All;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Accounts\Module::class,
        \Osm\Admin\Forms\Module::class,
        \Osm\Admin\Formulas\Module::class,
        \Osm\Admin\Grids\Module::class,
        \Osm\Admin\Indexing\Module::class,
        \Osm\Admin\Icons\Module::class,
        \Osm\Admin\Queries\Module::class,
        \Osm\Admin\Messages\Module::class,
        \Osm\Admin\Queues\Module::class,
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Storages\Module::class,
        \Osm\Admin\Tables\Module::class,
    ];
}