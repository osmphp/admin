<?php

namespace Osm\Data\Schema;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
    ];
}