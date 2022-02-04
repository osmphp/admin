<?php

namespace Osm\Admin\Ui;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Admin\Api\Module::class,
    ];

}