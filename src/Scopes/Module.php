<?php

namespace Osm\Admin\Scopes;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Tables\Module::class,
    ];
}