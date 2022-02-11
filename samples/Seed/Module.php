<?php

namespace Osm\Admin\Samples\Seed;

use Osm\Admin\Samples\App;
use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Admin\All\Module::class,
    ];
}