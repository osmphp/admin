<?php

namespace Osm\Admin\Ui;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('ui')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Queries\Module::class,
    ];

}