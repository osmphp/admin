<?php

namespace Osm\Admin\Interfaces;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Filters\Module::class,
        \Osm\Admin\Schema\Module::class,
    ];

}