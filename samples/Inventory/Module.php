<?php

namespace Osm\Admin\Samples\Inventory;

use Osm\Core\BaseModule;
use Osm\Admin\Samples\App;

class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Admin\All\Module::class,
        \Osm\Admin\Samples\Products\Module::class,
    ];
}