<?php

namespace Osm\Admin\Forms;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('forms')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];

}