<?php

namespace Osm\Admin\Storages;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Schema\Module::class,
        \Osm\Admin\Indexing\Module::class,
    ];
}