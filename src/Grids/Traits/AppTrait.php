<?php

namespace Osm\Admin\Grids\Traits;

use Osm\Admin\Grids\Grid;
use Osm\Core\App;
use Osm\Core\Attributes\UseIn;
use Osm\Core\Exceptions\NotImplemented;

#[UseIn(App::class)]
trait AppTrait
{
    public function grid(string $className, string $name = null): Grid {
        throw new NotImplemented($this);
    }
}