<?php

namespace Osm\Admin\Overlay;

use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

#[Name('overlay')]
class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];
}