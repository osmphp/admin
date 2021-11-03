<?php

namespace Osm\Admin\Grids;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('grids')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];

}