<?php

namespace Osm\Admin\Icons;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('icons')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];
}