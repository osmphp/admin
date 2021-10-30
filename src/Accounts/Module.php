<?php

namespace Osm\Data\Accounts;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Base\Module::class,
        \Osm\Data\Tables\Module::class,
        \Osm\Data\Scopes\Module::class,
    ];
}