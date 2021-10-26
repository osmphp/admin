<?php

declare(strict_types=1);

namespace Osm\Data\Base;

use Osm\App\App;
use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Framework\All\Module::class,
    ];
}