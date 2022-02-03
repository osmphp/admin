<?php

declare(strict_types=1);

namespace Osm\Admin\Schema;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Framework\All\Module::class,
    ];
}