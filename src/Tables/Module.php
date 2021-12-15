<?php

namespace Osm\Admin\Tables;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Forms\Module::class,
        \Osm\Admin\Indexing\Module::class,
        \Osm\Admin\Queries\Module::class,
        \Osm\Admin\Storages\Module::class,
    ];

}