<?php

namespace Osm\Admin\Messages;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('messages')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];
}