<?php

namespace Osm\Admin\Accounts;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
        \Osm\Admin\Tables\Module::class,
        \Osm\Admin\Scopes\Module::class,
    ];
}