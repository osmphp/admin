<?php

namespace Osm\Admin\Queries;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Indexing\Module::class,
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Storages\Module::class,
    ];
}