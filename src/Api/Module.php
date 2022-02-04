<?php

namespace Osm\Admin\Api;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Queries\Module::class,
    ];

}