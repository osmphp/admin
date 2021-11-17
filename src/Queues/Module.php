<?php

namespace Osm\Admin\Queues;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Base\Module::class,
    ];
}