<?php

namespace Osm\Data\Grids;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('grids')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
    ];

}