<?php

namespace Osm\Admin\Tables;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Queries\Module::class,
    ];

}