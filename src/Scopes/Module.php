<?php

namespace Osm\Data\Scopes;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
    ];

}