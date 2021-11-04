<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Grids\Routes;
use Osm\Core\Attributes\UseIn;
use Osm\Framework\Http\Module;
use function Osm\merge;

#[UseIn(Module::class)]
trait HttpModuleTrait
{
    protected function around_loadRoutes(callable $proceed): void {
        /* @var Module|static $this */

        // first, find all statically defined routes, and put them into
        // `routes` and `dynamic_routes` properties. Note that these
        // properties are `#Cached`, so this only happens once
        $proceed();

        $this->routes = merge($this->routes, Routes::new()->all());
    }
}