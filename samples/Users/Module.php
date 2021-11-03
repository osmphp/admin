<?php

namespace Osm\Admin\Samples\Users;

use Osm\Core\BaseModule;
use Osm\Admin\Samples\App;

class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Admin\All\Module::class,
    ];
}