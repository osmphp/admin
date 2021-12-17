<?php

namespace Osm\Admin\Filters;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Interfaces\Module::class,
    ];
}